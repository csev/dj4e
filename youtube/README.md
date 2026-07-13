# DJ4E YouTube playlist data

Course-specific YouTube metadata for DJ4E. Shared tooling and the full workflow
live in [`media-util`](../../media-util/README.md)
(`/Users/csev/htdocs/media-util`).

## Files here

- `youtube-playlist.jsonl` — playlist dump used when building `media.yaml`
- `client_secret.json` — OAuth Desktop client (gitignored; for push updates)
- `youtube-oauth-token.json` — cached OAuth token after first consent

Course env (repo root): `../media.env`

## Workflow (summary)

```bash
source /Users/csev/htdocs/dj4e/media.env
cd /Users/csev/htdocs/dj4e

# 1. Download existing YouTube playlist
dump-youtube-playlist.sh

# 2. Align lessons.json <-> MEDIA_ROOT
compare-lessons-root.py

# 3. Remove orphan whisper artifacts
compare-whisper-root.py --remove

# 4. Transcribe missing media (all of MEDIA_ROOT, or only lessons.json)
whisper-media.sh
# whisper-lessons

# 5. Generate titles/tags/descriptions (Ollama must be running)
whisper-desc

# 6. Build media.yaml
bootstrap-media-yaml.py

# 7. Push titles/descriptions to YouTube (optional; OAuth required)
update-youtube-from-media.py         # dry-run
# update-youtube-from-media.py --apply
```

See the media-util [INSTALL.md](../../media-util/INSTALL.md) and
[README.md](../../media-util/README.md) for install notes and the full workflow.
