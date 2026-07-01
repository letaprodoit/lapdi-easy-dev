# WordPress.org deployment plan

## Current repository strategy

Use Git/GitHub for day-to-day development, code review, issues, and CI. WordPress.org still publishes plugins from its Subversion repository, so SVN remains the release target for the public plugin directory.

In practice this means:

1. Develop and review changes in Git.
2. Tag a release in Git when the plugin is ready.
3. Copy the Git release contents into the WordPress.org SVN `trunk/` folder.
4. Copy the same release contents into a new WordPress.org SVN `tags/<version>/` folder.
5. Remove any Git-only files, especially `.git/`, from the SVN working copy before committing.
6. Commit the finished release to WordPress.org SVN.

The WordPress Plugin Handbook describes the Plugin Directory SVN repository as a release repository rather than a normal development repository, so only finished releases should be pushed there.

## WordPress.org SVN folder layout

A typical WordPress.org plugin SVN checkout has this shape:

```text
plugin-slug/
├── assets/
├── tags/
│   └── 2.0.4/
└── trunk/
```

Use each folder as follows:

- `trunk/`: contains the current release candidate/current stable plugin files copied from GitHub.
- `tags/<version>/`: contains a frozen copy of the exact release files for that version.
- `assets/`: contains WordPress.org directory assets that are reused by the readme/plugin listing, such as icons, banners, and screenshots.

## Manual release workflow

Replace `PLUGIN_SLUG` and `VERSION` in the commands below. For this plugin, `PLUGIN_SLUG` is likely `tsp-easy-dev` and the next release in this maintenance pass is `2.0.4`.

### 1. Prepare and verify the GitHub repository

```bash
git checkout main
git pull --ff-only
git status --short
```

Confirm the working tree is clean. Then update release metadata in Git:

- `README.txt`: `Stable tag`, `Tested up to`, `Requires at least`, `Requires PHP`, changelog, and upgrade notice.
- Main plugin file: `Version`, `Requires at least`, `Requires PHP`, and `Tested up to` plugin headers.
- Any docs needed for the release.

Run checks before tagging:

```bash
find . -maxdepth 2 -type f -name '*.php' -print0 | xargs -0 -n1 php -l
git diff --check
```

Commit the release metadata and create a Git tag:

```bash
git add README.txt tsp-easy-dev.php DEPLOYMENT.md
git commit -m "Release VERSION"
git tag VERSION
git push origin main
git push origin VERSION
```

### 2. Check out the WordPress.org SVN repository

Use a separate directory from the Git repository so Git-only files cannot accidentally be committed to SVN.

```bash
svn checkout https://plugins.svn.wordpress.org/PLUGIN_SLUG/ PLUGIN_SLUG-svn
cd PLUGIN_SLUG-svn
```

You should now have `assets/`, `tags/`, and `trunk/` in the SVN working copy.

### 3. Update the SVN `trunk/` folder from GitHub

Export the exact Git tag into a temporary folder. `git archive` is preferred because it does not include `.git/`.

```bash
cd /path/to/git/PLUGIN_SLUG
git archive --format=tar VERSION | tar -x -C /tmp/PLUGIN_SLUG-VERSION
```

Replace the SVN `trunk/` contents with the exported Git release:

```bash
cd /path/to/PLUGIN_SLUG-svn
rm -rf trunk/*
rsync -av --delete /tmp/PLUGIN_SLUG-VERSION/ trunk/
```

Important: if you copy with Finder, Explorer, `cp`, or `rsync` from a live Git checkout instead of `git archive`, remove Git-only files before committing:

```bash
find trunk -name .git -type d -prune -exec rm -rf {} +
find trunk -name .gitignore -type f -delete
```

### 4. Add a new version folder under SVN `tags/`

Create a new immutable tag folder for the release. The `tags/VERSION/` folder should contain the same plugin files as `trunk/`.

```bash
rm -rf tags/VERSION
mkdir -p tags/VERSION
rsync -av --delete trunk/ tags/VERSION/
```

Make sure no `.git/` folder exists in the new tag:

```bash
find tags/VERSION -name .git -type d -prune -exec rm -rf {} +
find tags/VERSION -name .gitignore -type f -delete
```

### 5. Store reusable readme/plugin listing images in SVN `assets/`

WordPress.org directory images that are referenced by the plugin readme/listing should live in the SVN `assets/` folder, not inside `trunk/` or `tags/`, when they are reused across releases.

Common filenames include:

- `assets/icon-128x128.png`
- `assets/icon-256x256.png`
- `assets/banner-772x250.png`
- `assets/banner-1544x500.png`
- `assets/screenshot-1.png`
- `assets/screenshot-2.png`

If a screenshot is referenced by `README.txt`, copy it to `assets/` and add it to SVN:

```bash
cp /path/to/reused-image.png assets/screenshot-1.png
svn add assets/screenshot-1.png
```

Only keep release-specific runtime images in the plugin package under `trunk/` and `tags/VERSION/`. Reused WordPress.org listing images belong in `assets/`.

### 6. Review SVN changes before commit

```bash
svn status
svn diff --summarize
```

Confirm the SVN changes include:

- Updated files under `trunk/`.
- A new `tags/VERSION/` folder.
- Any reused readme/listing images under `assets/`.
- No `.git/` folder and no accidental local build/cache files.

### 7. Commit the WordPress.org release

```bash
svn add --force trunk tags/VERSION assets
svn status
svn commit -m "Release VERSION"
```

After the SVN commit finishes, check the WordPress.org plugin page and confirm:

- The stable version matches `Stable tag` in `README.txt`.
- The changelog and upgrade notice render correctly.
- Icons, banners, and screenshots render correctly.
- The downloadable ZIP contains the expected release files.

## GitHub Actions option

A GitHub Actions workflow can automate the SVN copy/commit process, but it should still follow the same structure:

1. Trigger on a Git tag.
2. Check out the Git tag.
3. Check out the WordPress.org SVN repository.
4. Copy the Git tag contents into SVN `trunk/`.
5. Copy the same contents into `tags/<version>/`.
6. Remove `.git/` and other Git-only files from SVN paths.
7. Copy reused readme/listing images into SVN `assets/`.
8. Commit to SVN using WordPress.org credentials stored as GitHub repository secrets.

Do not enable this automation until SVN credentials are available as repository secrets and the manual workflow has been tested once.

## Compatibility target for this maintenance pass

The plugin keeps its existing backward-compatible WordPress floor of 4.5 while declaring compatibility with WordPress 7.0. WordPress 7.0 reports a PHP requirement of 7.4, so this plugin now declares PHP 7.4 as the supported runtime floor for current WordPress compatibility.

## Next hardening tasks before a public release

- Run the plugin under WordPress 4.5, 5.7, 6.9, and 7.0 test installs.
- Run PHP compatibility checks for PHP 7.4 through the newest PHP version used by target hosts.
- Review bundled dependencies and decide whether to commit a production `vendor/` directory for WordPress.org distribution.
- Test the manual SVN workflow once before automating it with GitHub Actions.
