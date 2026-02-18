# en-php Setup and Usage

en-php is a **self-contained** PHP version of the Django Girls Tutorial. All README.md files and images are copied here from `en/`.

## Sync from en/

To refresh the Markdown and images from the main `en/` folder:

```bash
# From tutorial root
./setup_en_php.sh
```

## Serve

```bash
# From tutorial root
php -S localhost:8000 -t en-php
```

Then open http://localhost:8000

## Edit Content

Edit the `.md` files directly in **en-php/** (e.g. `en-php/how_the_internet_works/README.md`). Refresh the browser to see changes.

To pull updates from the main `en/` repo, run `./setup_en_php.sh` again.
