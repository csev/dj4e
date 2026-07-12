# Media tooling

## Generate `media.yaml`

1. Refresh the YouTube playlist metadata (recommended before generating).
   Keep only the fields needed for matching and descriptions:

```bash
PLAYLIST_URL='https://www.youtube.com/playlist?list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z'
yt-dlp --skip-download --ignore-errors --print '{"id":%(id)j,"title":%(title)j,"description":%(description)j,"duration":%(duration)j,"playlist_index":%(playlist_index)j}' "$PLAYLIST_URL" > youtube-playlist.jsonl
```

Each JSONL line has: `id`, `title`, `description`, `duration`, `playlist_index`.

2. Generate / refresh `../media.yaml`:

From the repository root:

```bash
python3 media/bootstrap-media-yaml.py
```

Or from this directory:

```bash
python3 bootstrap-media-yaml.py
```

Defaults:

- `--lessons` → `../lessons.json`
- `--files` → `media-files.txt`
- `--youtube-playlist` → `youtube-playlist.jsonl`
- `--media-root` → `/Users/csev/Desktop/teach/dj4e-media`
- `--www-root` → repository root (parent of `media/`)
- `--output` → `../media.yaml`

Requires `ffprobe` (from ffmpeg), `yt-dlp` (for the playlist dump), and the
`ruamel.yaml` Python package.

### What gets updated

On each run the script refreshes `size`, `md5`, `duration`, and lesson `title`.

YouTube fields are filled from `youtube-playlist.jsonl` by matching:

1. `youtube` id from `lessons.json` when present in the playlist
2. title comparison (YouTube titles omit the `DJ nn.mm` prefix; that is OK)
3. optional media basename, if a `filename` field is present in the JSONL

`youtube_id` and `description` are filled when null/empty. Manually edited values
are preserved unless you pass `--force-youtube`. `kaltura_id` is always preserved.

Use `--force-title` to overwrite titles that were filled from filename stems when
no lesson match exists.
