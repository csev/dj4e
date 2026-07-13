# DJ4E YouTube playlist data

Course-specific YouTube metadata for DJ4E. Shared tooling lives in
[`media-util`](../../media-util/README.md) (`/Users/csev/htdocs/media-util`).

## Files here

- `youtube-playlist.jsonl` — playlist dump used to fill YouTube fields

Course env (repo root): `../media.env`

## Typical workflow

```bash
source /Users/csev/htdocs/dj4e/media.env
cd /Users/csev/htdocs/dj4e

# 1. First: lessons.json <-> MEDIA_ROOT (does not touch media.yaml)
compare-lessons-root.py

# 2. Refresh playlist metadata
dump-youtube-playlist.sh

# 3. Build / refresh media.yaml from lessons + MEDIA_ROOT + playlist
bootstrap-media-yaml.py

# Later checks against media.yaml (optional)
# compare-media-root.py
# compare-youtube.py
# compare-lessons.py

# Transcribe (writes under whisper/)
whisper-media.sh

# Generate YouTube title/tags/description under whisper/desc/
cd whisper && whisper-desc
```

Sourcing `media.env` puts `media-util/bin` on your `PATH` and sets
`MEDIA_ROOT`, `OUTPUT_ROOT`, `YOUTUBE_DIR`, `YOUTUBE_PLAYLIST`, and `COURSE_HINT`.
No separate install step is required for day-to-day use.
