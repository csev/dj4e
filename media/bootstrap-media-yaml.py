#!/usr/bin/env python3
"""Bootstrap and refresh media.yaml from lessons.json, media-files.txt, and media files.

Preserves manually edited description / youtube_id / kaltura_id on rerun via ruamel.yaml.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import re
import shutil
import subprocess
import sys
import warnings
from pathlib import Path
from typing import Any

from ruamel.yaml import YAML
from ruamel.yaml.comments import CommentedMap


SCRIPT_DIR = Path(__file__).resolve().parent
REPO_ROOT = SCRIPT_DIR.parent

ENTRY_KEYS = (
    "title",
    "youtube_id",
    "kaltura_id",
    "size",
    "md5",
    "duration",
    "description",
)

PRESERVE_KEYS = ("description", "youtube_id", "kaltura_id")


def build_yaml() -> YAML:
    yaml = YAML()
    yaml.preserve_quotes = True
    yaml.default_flow_style = False
    yaml.width = 4096
    yaml.indent(mapping=2, sequence=4, offset=2)

    def represent_none(representer, _data):
        return representer.represent_scalar("tag:yaml.org,2002:null", "null")

    yaml.representer.add_representer(type(None), represent_none)
    return yaml


def parse_args(argv: list[str] | None = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Bootstrap or refresh media.yaml from lessons and media files."
    )
    parser.add_argument(
        "--lessons",
        type=Path,
        default=REPO_ROOT / "lessons.json",
        help=f"Path to lessons.json (default: {REPO_ROOT / 'lessons.json'})",
    )
    parser.add_argument(
        "--files",
        type=Path,
        default=SCRIPT_DIR / "media-files.txt",
        help=f"Path to media filename list (default: {SCRIPT_DIR / 'media-files.txt'})",
    )
    parser.add_argument(
        "--media-root",
        type=Path,
        default=Path("/Users/csev/Desktop/teach/dj4e-media"),
        help="Root directory containing media files",
    )
    parser.add_argument(
        "--www-root",
        type=Path,
        default=REPO_ROOT,
        help=f"Web/document root for this site (default: {REPO_ROOT})",
    )
    parser.add_argument(
        "--output",
        type=Path,
        default=REPO_ROOT / "media.yaml",
        help=f"Output YAML path (default: {REPO_ROOT / 'media.yaml'})",
    )
    parser.add_argument(
        "--force-title",
        action="store_true",
        help=(
            "Always overwrite title from lessons.json (or stem fallback). "
            "Without this flag, existing titles are preserved for unmatched files."
        ),
    )
    parser.add_argument(
        "--youtube-playlist",
        type=Path,
        default=SCRIPT_DIR / "youtube-playlist.jsonl",
        help=(
            "yt-dlp JSONL dump of the course playlist "
            f"(default: {SCRIPT_DIR / 'youtube-playlist.jsonl'})"
        ),
    )
    parser.add_argument(
        "--force-youtube",
        action="store_true",
        help=(
            "Overwrite existing youtube_id and description from the playlist. "
            "Without this flag, only null/empty values are filled."
        ),
    )
    return parser.parse_args(argv)


def load_lessons_media_map(
    lessons_path: Path,
    relevant: set[str] | None = None,
) -> tuple[dict[str, str], dict[str, str]]:
    """Return (title_map, youtube_id_map) from lessons.json.

    Schema (inspected): top-level ``modules`` list; each module has ``items``.
    Items may include ``media``, ``title``, and ``youtube``.

    Identical reuses are allowed. Conflicting titles/youtube IDs for the same
    media path are an error when that path is in ``relevant``.
    """
    try:
        text = lessons_path.read_text(encoding="utf-8")
    except OSError as exc:
        raise SystemExit(f"Error: cannot read lessons file {lessons_path}: {exc}") from exc

    try:
        data = json.loads(text)
    except json.JSONDecodeError as exc:
        raise SystemExit(
            f"Error: malformed JSON in {lessons_path}: {exc.msg} "
            f"(line {exc.lineno}, column {exc.colno})"
        ) from exc

    if not isinstance(data, dict) or "modules" not in data:
        raise SystemExit(
            f"Error: unexpected lessons schema in {lessons_path}: "
            "expected a top-level object with a 'modules' list"
        )

    modules = data["modules"]
    if not isinstance(modules, list):
        raise SystemExit(
            f"Error: unexpected lessons schema in {lessons_path}: "
            "'modules' must be a list"
        )

    title_map: dict[str, str] = {}
    youtube_map: dict[str, str] = {}
    title_conflicts: dict[str, set[str]] = {}
    youtube_conflicts: dict[str, set[str]] = {}

    for module in modules:
        if not isinstance(module, dict):
            continue
        items = module.get("items") or []
        if not isinstance(items, list):
            continue
        for item in items:
            if not isinstance(item, dict):
                continue
            media = item.get("media")
            if not media:
                continue
            if not isinstance(media, str):
                raise SystemExit(
                    f"Error: non-string media path in {lessons_path}: {media!r}"
                )
            title = item.get("title")
            if not isinstance(title, str) or not title.strip():
                raise SystemExit(
                    f"Error: media {media!r} in {lessons_path} has missing/empty title"
                )
            title = title.strip()
            if media in title_map:
                if title_map[media] != title:
                    title_conflicts.setdefault(media, {title_map[media]}).add(title)
            else:
                title_map[media] = title

            youtube = item.get("youtube")
            if isinstance(youtube, str) and youtube.strip():
                youtube = youtube.strip()
                if media in youtube_map:
                    if youtube_map[media] != youtube:
                        youtube_conflicts.setdefault(media, {youtube_map[media]}).add(
                            youtube
                        )
                else:
                    youtube_map[media] = youtube

    if relevant is not None:
        title_conflicts = {k: v for k, v in title_conflicts.items() if k in relevant}
        youtube_conflicts = {k: v for k, v in youtube_conflicts.items() if k in relevant}

    if title_conflicts:
        lines = [
            f"Error: duplicate filename mappings with conflicting titles "
            f"in {lessons_path}:"
        ]
        for media, titles in sorted(title_conflicts.items()):
            lines.append(f"  {media}:")
            for title in sorted(titles):
                lines.append(f"    - {title}")
        raise SystemExit("\n".join(lines))

    if youtube_conflicts:
        lines = [
            f"Error: duplicate filename mappings with conflicting youtube IDs "
            f"in {lessons_path}:"
        ]
        for media, ids in sorted(youtube_conflicts.items()):
            lines.append(f"  {media}:")
            for youtube_id in sorted(ids):
                lines.append(f"    - {youtube_id}")
        raise SystemExit("\n".join(lines))

    return title_map, youtube_map


def normalize_title(title: str) -> str:
    """Normalize titles for comparison (drop DJ nn.mm prefix and trailing times)."""
    text = title.strip()
    text = re.sub(r"^DJ\s+\d+\.\d+\s+", "", text, flags=re.IGNORECASE)
    while True:
        match = re.search(r"\s*\((\d{1,2}:\d{2}|\d+\.\d{2})\)\s*$", text)
        if not match:
            break
        text = text[: match.start()].rstrip()
    text = text.replace("'", "").replace('"', "")
    text = re.sub(r"[^a-z0-9]+", " ", text.lower())
    return " ".join(text.split())


def titles_compatible(lesson_title: str, youtube_title: str) -> bool:
    left = normalize_title(lesson_title)
    right = normalize_title(youtube_title)
    if not left or not right:
        return False
    if left == right:
        return True
    # Allow wording drift (e.g. "Django Data Models" vs "Introduction to Django Models")
    if left in right or right in left:
        return True
    left_tokens = set(left.split())
    right_tokens = set(right.split())
    if not left_tokens or not right_tokens:
        return False
    overlap = left_tokens & right_tokens
    return len(overlap) >= min(3, len(left_tokens), len(right_tokens))


def load_youtube_playlist(playlist_path: Path) -> list[dict[str, Any]]:
    if not playlist_path.exists():
        warnings.warn(
            f"YouTube playlist file not found: {playlist_path}; "
            "youtube_id/description will not be filled from YouTube",
            UserWarning,
            stacklevel=2,
        )
        return []

    entries: list[dict[str, Any]] = []
    try:
        lines = playlist_path.read_text(encoding="utf-8").splitlines()
    except OSError as exc:
        raise SystemExit(
            f"Error: cannot read YouTube playlist {playlist_path}: {exc}"
        ) from exc

    for lineno, raw in enumerate(lines, start=1):
        line = raw.strip()
        if not line:
            continue
        try:
            obj = json.loads(line)
        except json.JSONDecodeError as exc:
            raise SystemExit(
                f"Error: malformed JSONL in {playlist_path} line {lineno}: {exc.msg}"
            ) from exc
        if not isinstance(obj, dict) or not obj.get("id"):
            warnings.warn(
                f"Skipping playlist line {lineno}: missing id",
                UserWarning,
                stacklevel=2,
            )
            continue
        entries.append(obj)
    return entries


def index_youtube_playlist(
    playlist: list[dict[str, Any]],
) -> tuple[dict[str, dict[str, Any]], dict[str, dict[str, Any]], dict[str, dict[str, Any]]]:
    """Index playlist entries by id, normalized title, and basename (no folder)."""
    by_id: dict[str, dict[str, Any]] = {}
    by_title: dict[str, dict[str, Any]] = {}
    by_basename: dict[str, dict[str, Any]] = {}

    for entry in playlist:
        youtube_id = str(entry["id"])
        by_id[youtube_id] = entry

        title = entry.get("title") or entry.get("fulltitle") or ""
        if isinstance(title, str) and title.strip():
            by_title.setdefault(normalize_title(title), entry)

        for key in ("filename", "_filename"):
            value = entry.get(key)
            if not isinstance(value, str) or not value.strip():
                continue
            # Folder may be absent in YouTube/yt-dlp filenames.
            basename = Path(value).name
            by_basename.setdefault(basename, entry)
            by_basename.setdefault(Path(basename).stem, entry)

    return by_id, by_title, by_basename


def match_youtube_entry(
    rel_name: str,
    *,
    lesson_title: str | None,
    lesson_youtube_id: str | None,
    by_id: dict[str, dict[str, Any]],
    by_title: dict[str, dict[str, Any]],
    by_basename: dict[str, dict[str, Any]],
) -> dict[str, Any] | None:
    """Match a media file to a playlist entry via id, basename, then title."""
    if lesson_youtube_id and lesson_youtube_id in by_id:
        entry = by_id[lesson_youtube_id]
        yt_title = entry.get("title") or ""
        if lesson_title and isinstance(yt_title, str) and yt_title.strip():
            if not titles_compatible(lesson_title, yt_title):
                warnings.warn(
                    f"YouTube title differs for {rel_name!r}: "
                    f"lesson={lesson_title!r} youtube={yt_title!r} "
                    f"(keeping youtube id {lesson_youtube_id})",
                    UserWarning,
                    stacklevel=2,
                )
        return entry

    basename = Path(rel_name).name
    stem = Path(rel_name).stem
    for key in (basename, stem):
        if key in by_basename:
            return by_basename[key]

    if lesson_title:
        normalized = normalize_title(lesson_title)
        if normalized in by_title:
            return by_title[normalized]
        for yt_norm, entry in by_title.items():
            if normalized and (normalized in yt_norm or yt_norm in normalized):
                return entry

    return None


def empty_value(value: Any) -> bool:
    return value is None or value == ""


def choose_field(existing: Any, incoming: Any, *, force: bool) -> Any:
    if force or empty_value(existing):
        return incoming if not empty_value(incoming) else existing
    return existing


def load_media_files(files_path: Path) -> list[str]:
    try:
        lines = files_path.read_text(encoding="utf-8").splitlines()
    except OSError as exc:
        raise SystemExit(f"Error: cannot read file list {files_path}: {exc}") from exc

    result: list[str] = []
    seen: set[str] = set()
    for raw in lines:
        name = raw.strip()
        if not name or name.startswith("#"):
            continue
        if name in seen:
            raise SystemExit(f"Error: duplicate media filename in {files_path}: {name}")
        seen.add(name)
        result.append(name)
    return result


def title_from_stem(rel_path: str) -> str:
    stem = Path(rel_path).stem
    return stem.replace("_", " ").replace("-", " ").strip()


def resolve_title(
    rel_path: str,
    title_map: dict[str, str],
    existing_title: str | None,
    *,
    force_title: bool,
    is_new: bool,
) -> str:
    """Return the title to store for this media file.

    Matched lessons titles are always applied (requirement: update title on rerun).
    Unmatched files warn and use a stem-derived title for new entries, or when
    ``--force-title`` is set; otherwise an existing title is preserved.
    """
    if rel_path in title_map:
        return title_map[rel_path]

    if existing_title and not force_title and not is_new:
        warnings.warn(
            f"No lesson title for {rel_path!r}; keeping existing title",
            UserWarning,
            stacklevel=2,
        )
        return existing_title

    stem_title = title_from_stem(rel_path)
    warnings.warn(
        f"No lesson title for {rel_path!r}; using filename stem {stem_title!r}",
        UserWarning,
        stacklevel=2,
    )
    return stem_title


def file_md5(path: Path) -> str:
    digest = hashlib.md5()
    with path.open("rb") as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def require_ffprobe() -> str:
    path = shutil.which("ffprobe")
    if not path:
        raise SystemExit(
            "Error: ffprobe not found on PATH. "
            "Install ffmpeg (which provides ffprobe) and try again."
        )
    return path


def probe_duration(ffprobe: str, path: Path) -> int:
    cmd = [
        ffprobe,
        "-v",
        "error",
        "-show_entries",
        "format=duration",
        "-of",
        "default=noprint_wrappers=1:nokey=1",
        str(path),
    ]
    try:
        completed = subprocess.run(
            cmd,
            check=False,
            capture_output=True,
            text=True,
        )
    except OSError as exc:
        raise SystemExit(f"Error: failed to run ffprobe on {path}: {exc}") from exc

    if completed.returncode != 0:
        err = (completed.stderr or completed.stdout or "").strip()
        raise SystemExit(
            f"Error: ffprobe failed for {path} "
            f"(exit {completed.returncode}): {err or 'no details'}"
        )

    raw = completed.stdout.strip()
    try:
        return round(float(raw))
    except ValueError as exc:
        raise SystemExit(
            f"Error: ffprobe returned non-numeric duration for {path}: {raw!r}"
        ) from exc


def load_existing(output_path: Path, yaml: YAML) -> CommentedMap:
    if not output_path.exists():
        data = CommentedMap()
        data["media_root"] = None
        data["www_root"] = None
        data["entries"] = CommentedMap()
        return data

    try:
        with output_path.open("r", encoding="utf-8") as handle:
            loaded = yaml.load(handle)
    except Exception as exc:  # noqa: BLE001 - surface YAML parse errors clearly
        raise SystemExit(f"Error: cannot parse existing YAML {output_path}: {exc}") from exc

    if loaded is None:
        data = CommentedMap()
        data["media_root"] = None
        data["www_root"] = None
        data["entries"] = CommentedMap()
        return data

    if not isinstance(loaded, dict):
        raise SystemExit(
            f"Error: existing {output_path} must be a mapping with media_root and entries"
        )

    if not isinstance(loaded, CommentedMap):
        data = CommentedMap()
        for key, value in loaded.items():
            data[key] = value
        loaded = data

    entries = loaded.get("entries")
    if entries is None:
        loaded["entries"] = CommentedMap()
    elif not isinstance(entries, dict):
        raise SystemExit(f"Error: 'entries' in {output_path} must be a mapping")
    elif not isinstance(entries, CommentedMap):
        converted = CommentedMap()
        for key, value in entries.items():
            converted[key] = value
        loaded["entries"] = converted

    return loaded


def ensure_entry_map(entry: Any) -> CommentedMap:
    if entry is None:
        return CommentedMap()
    if isinstance(entry, CommentedMap):
        return entry
    if isinstance(entry, dict):
        converted = CommentedMap()
        for key, value in entry.items():
            converted[key] = value
        return converted
    raise SystemExit(f"Error: entry must be a mapping, got {type(entry).__name__}")


def ordered_entry(
    existing: CommentedMap,
    *,
    title: str,
    size: int,
    md5: str,
    duration: int,
    youtube_id: Any = None,
    description: Any = None,
    force_youtube: bool = False,
) -> CommentedMap:
    """Update an entry in place so ruamel comments/formatting are preserved."""
    preserved = {key: existing.get(key, None) for key in PRESERVE_KEYS}
    preserved["youtube_id"] = choose_field(
        preserved["youtube_id"], youtube_id, force=force_youtube
    )
    preserved["description"] = choose_field(
        preserved["description"], description, force=force_youtube
    )
    # Always preserve manually set kaltura_id.
    preserved["kaltura_id"] = existing.get("kaltura_id", None)

    # Ensure known keys exist in the required order by rebuilding only when needed.
    known = [key for key in existing.keys() if key in ENTRY_KEYS]
    needs_reorder = known != list(ENTRY_KEYS)
    if needs_reorder or not existing:
        extras = [(key, value) for key, value in existing.items() if key not in ENTRY_KEYS]
        existing.clear()
        existing["title"] = title
        existing["youtube_id"] = preserved["youtube_id"]
        existing["kaltura_id"] = preserved["kaltura_id"]
        existing["size"] = size
        existing["md5"] = md5
        existing["duration"] = duration
        for key, value in extras:
            existing[key] = value
        existing["description"] = preserved["description"]
        return existing

    existing["title"] = title
    existing["youtube_id"] = preserved["youtube_id"]
    existing["kaltura_id"] = preserved["kaltura_id"]
    existing["description"] = preserved["description"]
    existing["size"] = size
    existing["md5"] = md5
    existing["duration"] = duration
    return existing


def rebuild_entries(
    old_entries: CommentedMap,
    media_files: list[str],
    updated: dict[str, CommentedMap],
) -> tuple[CommentedMap, list[str]]:
    """Return entries in media-files.txt order, then any orphaned keys."""
    media_set = set(media_files)
    orphans = [key for key in old_entries.keys() if key not in media_set]

    # If order already matches and there are no inserts/moves, update in place.
    expected = list(media_files) + orphans
    if list(old_entries.keys()) == expected:
        for name in media_files:
            old_entries[name] = updated[name]
        return old_entries, orphans

    new_entries = CommentedMap()
    for name in media_files:
        new_entries[name] = updated[name]
    for key in orphans:
        new_entries[key] = old_entries[key]
    return new_entries, orphans


def main(argv: list[str] | None = None) -> int:
    args = parse_args(argv)
    ffprobe = require_ffprobe()
    media_files = load_media_files(args.files)
    title_map, lesson_youtube_map = load_lessons_media_map(
        args.lessons, relevant=set(media_files)
    )
    media_root: Path = args.media_root

    if not media_root.is_dir():
        raise SystemExit(f"Error: media root is not a directory: {media_root}")

    playlist = load_youtube_playlist(args.youtube_playlist)
    by_id, by_title, by_basename = index_youtube_playlist(playlist)

    yaml = build_yaml()
    data = load_existing(args.output, yaml)
    data["media_root"] = str(media_root)
    data["www_root"] = str(args.www_root)

    old_entries = data["entries"]
    updated: dict[str, CommentedMap] = {}
    youtube_matched = 0
    youtube_unmatched: list[str] = []

    for rel_name in media_files:
        media_path = media_root / rel_name
        if not media_path.is_file():
            raise SystemExit(f"Error: missing media file: {media_path}")

        existing = ensure_entry_map(old_entries.get(rel_name))
        existing_title = existing.get("title")
        if not isinstance(existing_title, str):
            existing_title = None

        is_new = rel_name not in old_entries
        title = resolve_title(
            rel_name,
            title_map,
            existing_title,
            force_title=args.force_title,
            is_new=is_new,
        )

        yt_entry = match_youtube_entry(
            rel_name,
            lesson_title=title_map.get(rel_name) or title,
            lesson_youtube_id=lesson_youtube_map.get(rel_name),
            by_id=by_id,
            by_title=by_title,
            by_basename=by_basename,
        )
        youtube_id = None
        description = None
        if yt_entry is not None:
            youtube_matched += 1
            youtube_id = yt_entry.get("id")
            description = yt_entry.get("description")
            if isinstance(description, str):
                description = description.strip() or None
            else:
                description = None
        elif playlist:
            youtube_unmatched.append(rel_name)
            warnings.warn(
                f"No YouTube playlist match for {rel_name!r}",
                UserWarning,
                stacklevel=2,
            )

        updated[rel_name] = ordered_entry(
            existing,
            title=title,
            size=media_path.stat().st_size,
            md5=file_md5(media_path),
            duration=probe_duration(ffprobe, media_path),
            youtube_id=youtube_id,
            description=description,
            force_youtube=args.force_youtube,
        )

    new_entries, orphans = rebuild_entries(old_entries, media_files, updated)
    data["entries"] = new_entries

    # Keep media_root / www_root / entries first without rebuilding the root map
    # when possible so document-level comments survive.
    root_keys = list(data.keys())
    expected_prefix = ["media_root", "www_root", "entries"]
    if root_keys[:3] != expected_prefix:
        ordered = CommentedMap()
        ordered["media_root"] = data["media_root"]
        ordered["www_root"] = data.get("www_root")
        ordered["entries"] = data["entries"]
        for key, value in data.items():
            if key in ("media_root", "www_root", "entries"):
                continue
            ordered[key] = value
        data = ordered

    try:
        with args.output.open("w", encoding="utf-8") as handle:
            yaml.dump(data, handle)
    except OSError as exc:
        raise SystemExit(f"Error: cannot write {args.output}: {exc}") from exc

    print(f"Wrote {len(media_files)} media entries to {args.output}")
    if playlist:
        print(
            f"YouTube matches: {youtube_matched}/{len(media_files)} "
            f"from {args.youtube_playlist}"
        )
        if youtube_unmatched:
            print(f"Unmatched media files ({len(youtube_unmatched)}):")
            for name in youtube_unmatched:
                print(f"  {name}")
    if orphans:
        print(f"Orphaned YAML entries ({len(orphans)}) not in {args.files}:")
        for name in orphans:
            print(f"  {name}")
    else:
        print("No orphaned YAML entries.")
    return 0


if __name__ == "__main__":
    def _showwarning(message, category, filename, lineno, file=None, line=None):
        sys.stderr.write(f"Warning: {message}\n")

    warnings.showwarning = _showwarning  # type: ignore[assignment]
    raise SystemExit(main())
