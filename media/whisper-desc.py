#!/usr/bin/env python3
"""Generate YouTube title, tags, and description from whisper txt/ transcripts.

Run from a whisper folder that contains txt/... (for example dj4e/whisper).
Writes matching files under desc/... via the local Ollama API.

Output file format:

    title

    tag1, tag2, tag3

    two paragraph description
"""

from __future__ import annotations

import argparse
import json
import os
import pathlib
import re
import sys
import time
import urllib.error
import urllib.request


DEFAULT_MODEL = os.environ.get("OLLAMA_MODEL", "qwen3:4b")
DEFAULT_HOST = os.environ.get("OLLAMA_HOST", "http://localhost:11434").rstrip("/")

PROMPT_TEMPLATE = """\
/no_think
Read the following lecture transcript.

Write YouTube metadata in exactly this format:

TITLE
<one-line factual YouTube title>

TAGS
<comma-separated tags>

DESCRIPTION
<two factual paragraphs>

Requirements:
- Do not use hype.
- Do not invent information.
- Mention the main technical topics.
- Title should be concise and suitable for YouTube.
- Tags should be relevant technical keywords, comma-separated.
- Description must be exactly two paragraphs; each paragraph may be a few sentences covering the main points of the lecture.
- Return only the TITLE, TAGS, and DESCRIPTION sections — no other commentary.

Transcript:
---
{transcript}
---
"""


def fail(message: str, code: int = 1) -> None:
    print(f"ERROR: {message}", file=sys.stderr)
    raise SystemExit(code)


def find_whisper_root(start: pathlib.Path) -> pathlib.Path:
    """Prefer a directory that already contains txt/; otherwise use start."""
    candidate = start.resolve()
    if (candidate / "txt").is_dir():
        return candidate

    # Allow running from inside txt/ or a lesson subfolder.
    for parent in candidate.parents:
        if (parent / "txt").is_dir():
            return parent

    return candidate


def collect_txt_files(root: pathlib.Path, paths: list[str]) -> list[pathlib.Path]:
    txt_dir = root / "txt"
    if not txt_dir.is_dir():
        fail(f"txt/ not found under {root}")

    if not paths:
        return sorted(txt_dir.rglob("*.txt"))

    files = []
    for raw in paths:
        path = pathlib.Path(raw)
        if not path.is_absolute():
            path = (pathlib.Path.cwd() / path).resolve()
        if not path.is_file():
            fail(f"file not found: {raw}")
        if path.suffix.lower() != ".txt":
            fail(f"not a .txt file: {raw}")
        try:
            path.relative_to(txt_dir.resolve())
        except ValueError:
            fail(f"file is not under {txt_dir}: {raw}")
        files.append(path)
    return files


def desc_path_for(txt_path: pathlib.Path, root: pathlib.Path) -> pathlib.Path:
    rel = txt_path.resolve().relative_to((root / "txt").resolve())
    return root / "desc" / rel


def ollama_chat(host: str, model: str, prompt: str, timeout: int) -> str:
    url = f"{host}/api/chat"
    payload = {
        "model": model,
        "stream": False,
        "messages": [
            {
                "role": "user",
                "content": prompt,
            }
        ],
    }
    data = json.dumps(payload).encode("utf-8")
    request = urllib.request.Request(
        url,
        data=data,
        headers={"Content-Type": "application/json"},
        method="POST",
    )

    try:
        with urllib.request.urlopen(request, timeout=timeout) as response:
            body = json.load(response)
    except urllib.error.HTTPError as exc:
        detail = exc.read().decode("utf-8", errors="replace")
        fail(f"Ollama HTTP {exc.code}: {detail}")
    except urllib.error.URLError as exc:
        fail(f"cannot reach Ollama at {host}: {exc.reason}")
    except TimeoutError:
        fail(f"Ollama request timed out after {timeout}s")

    message = body.get("message") or {}
    content = (message.get("content") or "").strip()
    if not content:
        fail("Ollama returned an empty response")
    return content


def strip_fences(text: str) -> str:
    text = text.strip()
    if text.startswith("```"):
        text = re.sub(r"^```[a-zA-Z0-9_-]*\n?", "", text)
        text = re.sub(r"\n?```$", "", text)
    return text.strip()


def parse_metadata(raw: str) -> tuple[str, str, str]:
    text = strip_fences(raw)
    text = re.sub(r"<think>.*?</think>", "", text, flags=re.DOTALL | re.IGNORECASE)
    text = text.strip()

    match = re.search(
        r"TITLE\s*\n(?P<title>.+?)\n+"
        r"TAGS\s*\n(?P<tags>.+?)\n+"
        r"DESCRIPTION\s*\n(?P<description>.+)\Z",
        text,
        flags=re.DOTALL | re.IGNORECASE,
    )
    if not match:
        # Fallback: treat the whole response as blank-line separated sections.
        parts = [p.strip() for p in re.split(r"\n\s*\n", text) if p.strip()]
        if len(parts) >= 3:
            title = parts[0]
            tags = parts[1]
            description = "\n\n".join(parts[2:])
        else:
            fail(
                "could not parse Ollama response into title/tags/description:\n"
                f"{text[:500]}"
            )
    else:
        title = match.group("title").strip()
        tags = match.group("tags").strip()
        description = match.group("description").strip()

    # Drop accidental labels if the model repeated them on the value line.
    title = re.sub(r"^(TITLE|Title)\s*[:\-]\s*", "", title).strip()
    tags = re.sub(r"^(TAGS|Tags)\s*[:\-]\s*", "", tags).strip()
    description = re.sub(
        r"^(DESCRIPTION|Description)\s*[:\-]\s*",
        "",
        description,
        count=1,
    ).strip()

    if not title or not tags or not description:
        fail(f"incomplete metadata parsed from response:\n{text[:500]}")

    # Normalize tags to a single comma-separated line.
    tags = re.sub(r"\s*,\s*", ", ", tags.replace("\n", " "))
    tags = re.sub(r"\s+", " ", tags).strip(" ,")

    return title, tags, description


def format_output(title: str, tags: str, description: str) -> str:
    return f"{title}\n\n{tags}\n\n{description.rstrip()}\n"


def process_file(
    txt_path: pathlib.Path,
    root: pathlib.Path,
    host: str,
    model: str,
    timeout: int,
    force: bool,
    dry_run: bool,
) -> str:
    out_path = desc_path_for(txt_path, root)
    rel = out_path.relative_to(root)

    if out_path.exists() and not force:
        return f"SKIP existing: {rel}"

    transcript = txt_path.read_text(encoding="utf-8").strip()
    if not transcript:
        return f"SKIP empty transcript: {txt_path.relative_to(root)}"

    if dry_run:
        return f"DRY-RUN would write: {rel}"

    txt_rel = txt_path.relative_to(root)
    print(f"START: {txt_rel}", flush=True)

    prompt = PROMPT_TEMPLATE.format(transcript=transcript)
    started = time.monotonic()
    raw = ollama_chat(host, model, prompt, timeout)
    elapsed = time.monotonic() - started
    title, tags, description = parse_metadata(raw)
    output = format_output(title, tags, description)

    out_path.parent.mkdir(parents=True, exist_ok=True)
    tmp_path = out_path.with_suffix(out_path.suffix + ".tmp")
    tmp_path.write_text(output, encoding="utf-8")
    tmp_path.replace(out_path)
    print(f"DONE:  {rel} ({elapsed:.1f}s)", flush=True)
    print(f"TITLE: {title}", flush=True)
    return f"WROTE: {rel}"


def main() -> None:
    parser = argparse.ArgumentParser(
        description=(
            "Generate YouTube title/tags/description files under desc/ "
            "from whisper txt/ transcripts using Ollama."
        )
    )
    parser.add_argument(
        "paths",
        nargs="*",
        help="Optional txt files under txt/. Default: all txt/**/*.txt",
    )
    parser.add_argument(
        "--root",
        default=".",
        help="Whisper folder containing txt/ (default: current directory)",
    )
    parser.add_argument(
        "--model",
        default=DEFAULT_MODEL,
        help=f"Ollama model (default: {DEFAULT_MODEL})",
    )
    parser.add_argument(
        "--host",
        default=DEFAULT_HOST,
        help=f"Ollama host (default: {DEFAULT_HOST})",
    )
    parser.add_argument(
        "--timeout",
        type=int,
        default=int(os.environ.get("OLLAMA_TIMEOUT", "600")),
        help="HTTP timeout in seconds (default: 600)",
    )
    parser.add_argument(
        "--force",
        "-f",
        action="store_true",
        help="Overwrite existing desc/ files",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="List files that would be processed without calling Ollama",
    )
    args = parser.parse_args()

    root = find_whisper_root(pathlib.Path(args.root))
    files = collect_txt_files(root, args.paths)
    if not files:
        fail(f"no .txt files found under {root / 'txt'}")

    print(f"Root: {root}")
    print(f"Model: {args.model}")
    print(f"Host: {args.host}")
    print(f"Files: {len(files)}")
    print()

    errors = 0
    for txt_path in files:
        try:
            message = process_file(
                txt_path=txt_path,
                root=root,
                host=args.host,
                model=args.model,
                timeout=args.timeout,
                force=args.force,
                dry_run=args.dry_run,
            )
            print(message)
        except SystemExit:
            raise
        except Exception as exc:  # noqa: BLE001 - keep batch going
            errors += 1
            print(f"ERROR: {txt_path}: {exc}", file=sys.stderr)

    if errors:
        fail(f"finished with {errors} error(s)", code=1)


if __name__ == "__main__":
    main()
