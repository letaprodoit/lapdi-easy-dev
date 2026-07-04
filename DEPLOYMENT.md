# WordPress.org deployment plan

## Current repository strategy

Use Git/GitHub for day-to-day development, code review, issues, and CI. WordPress.org still publishes plugins from its Subversion repository, so SVN remains the release target for the public plugin directory.

In practice this means:

1. Develop and review changes in Git.
2. Tag a release in Git when the plugin is ready.
3. Make sure the GitHub working copy inside the WordPress.org SVN `trunk/` folder is up to date.
4. Leave `trunk/` intact. Do not delete or replace its contents during tagging.
5. Copy the contents of `trunk/` into a new WordPress.org SVN `tags/<version>/` folder.
6. Remove the copied `.git/` folder from `tags/<version>/` before committing to SVN.
7. Commit the finished release to WordPress.org SVN.

The WordPress Plugin Handbook describes the Plugin Directory SVN repository as a release repository rather than a normal development repository, so only finished releases should be pushed there.

> **Do not keep the working Git checkout inside Dropbox (or any file-sync folder).** Dropbox syncs the `.git/` directory and creates "conflicted copy" duplicates of git's internal ref files, which corrupts the repository. A typical symptom is `git pull`/`git log` failing with `fatal: bad object origin/<branch> (... conflicted copy ...)`. If it happens, delete the stray files with `find .git -iname '*conflicted copy*' -delete`, prune the bad ref (`git remote prune origin`), and re-fetch — or just re-clone outside Dropbox, since everything is safe on GitHub. Keep the release checkout on a path Dropbox does not sync, or mark `.git` as ignored (`xattr -w com.dropbox.ignored 1 .git` on macOS).

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

- `trunk/`: contains the current GitHub working copy for the plugin. Keep this folder intact during the release tagging step.
- `tags/<version>/`: contains a frozen copy of the `trunk/` contents for that version, minus the copied `.git/` folder.
- `assets/`: contains WordPress.org directory assets that are reused by the readme/plugin listing, such as icons, banners, and screenshots.

## Local toolchain prerequisites

Generating `vendor/` requires a working local PHP and Composer. Releases have stalled here before, so verify the toolchain launches cleanly before starting:

```bash
php -v            # must print a version with no dyld/abort error
composer --version
```

Known breakages seen on macOS/Homebrew and their fixes:

- **PHP aborts with `dyld: Library not loaded: ...libicuio.NN.dylib`.** A Homebrew upgrade replaced `icu4c` and the PHP binary is still linked against the removed major version. Rebuild PHP against the current library:

  ```bash
  brew reinstall icu4c
  brew reinstall php
  php -v
  ```

- **`brew reinstall php` refuses with "Refusing to load formula from untrusted tap shivammathur/php".** PHP was installed from the third-party `shivammathur/php` tap and Homebrew gates untrusted taps. Trust it once, then reinstall:

  ```bash
  brew trust shivammathur/php
  brew reinstall php
  ```

- **Composer prints a wall of "Implicitly marking parameter ... as nullable is deprecated" notices.** The installed Composer is too old for the current PHP (e.g. Composer 2.0.9 on PHP 8.5). Update Composer itself — it still runs, but the noise buries real messages like security-audit warnings:

  ```bash
  composer self-update
  ```

Composer resolves against whatever local PHP you have. The published plugin targets **PHP 7.4**, and the current dependency set (Smarty 4.5, Bootstrap 4.6) supports 7.4, so a `vendor/` generated on a newer local PHP is still safe for customers. To confirm resolution against the 7.4 floor in a throwaway copy without editing the committed `composer.json`, run `composer update` after `composer config platform.php 7.4` in a scratch directory.

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

Harden the bundled dependencies before tagging. Keep the `composer.json` constraints on versions that clear known advisories while preserving the PHP 7.4 floor, then confirm with an audit:

```bash
composer update --no-dev
composer audit          # must report no advisories
```

The framework only uses stable Smarty APIs (`assign`, `display`, `setTemplateDir`, `setCompileDir`, `setCacheDir`), so staying within a single Smarty major line is safe across minor bumps. Current known-good floors: `smarty/smarty: ^4.5` (Smarty 3.x is EOL and carries CVE-2024-35226) and `twbs/bootstrap: ^4.6` (4.1.3 is affected by CVE-2019-8331). Do not move to Smarty 5 without also raising the `Requires PHP` header — Smarty 5 requires PHP 8.0.

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

### 3. Confirm SVN `trunk/` already has the current GitHub release contents

The `trunk/` folder stays intact during release tagging. Do not empty, delete, or replace `trunk/` as part of creating a version tag.

Before creating the SVN tag, make sure the GitHub repository inside `trunk/` is already updated to the release commit/version:

```bash
cd /path/to/PLUGIN_SLUG-svn/trunk
git status --short
git pull --ff-only
```

If `trunk/` is not a Git working copy in a particular checkout, update it using your normal GitHub-to-`trunk/` sync process first, then come back to these steps. The important rule is that the `tags/VERSION/` folder is created from the finalized contents of `trunk/`.

### 4. Generate the production `vendor/` folder, then copy `trunk/` into `tags/VERSION/`

The `vendor/` directory is **git-ignored** (see `.gitignore`) and is never committed to Git. It is generated at release time and lives only inside the frozen SVN tag. Generate it from inside the `trunk/` working copy first:

```bash
cd /path/to/PLUGIN_SLUG-svn/trunk
composer install --no-dev --optimize-autoloader
```

If `composer install` errors that the lock file does not satisfy the `composer.json` constraints (this happens after the dependency versions were bumped in step 1, because a stale `composer.lock` still pins the old versions), regenerate the lock instead — `install` only reads the lock, `update` rebuilds it:

```bash
composer update --no-dev --optimize-autoloader
```

`composer.lock` is git-ignored too, so it stays local; WordPress.org ships the resolved `vendor/` in the tag, not the lock.

Now create a new immutable tag folder for the release by copying the current `trunk/` contents (including the freshly generated `vendor/`):

```bash
cd /path/to/PLUGIN_SLUG-svn
rm -rf tags/VERSION
mkdir -p tags/VERSION
rsync -av trunk/ tags/VERSION/
```

After the tag is copied, remove the generated `vendor/` from `trunk/` so `trunk/` stays a clean mirror of the Git working copy (where `vendor/` is ignored). The tag keeps its own copy:

```bash
rm -rf trunk/vendor
```

After copying, remove the copied Git metadata from inside the tag folder so it is not committed with Subversion:

```bash
rm -rf tags/VERSION/.git
find tags/VERSION -name .git -type d -prune -exec rm -rf {} +
```

Do not remove `.git/` from `trunk/` if `trunk/` is intentionally maintained as the GitHub working copy. Only remove the copied `.git/` folder from `tags/VERSION/`.

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

- `trunk/` remains intact.
- A new `tags/VERSION/` folder exists and contains a copy of `trunk/`.
- `tags/VERSION/.git` has been removed before the SVN commit.
- Any reused readme/listing images are under `assets/`.
- No accidental local build/cache files are included in the SVN commit.

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
4. Leave SVN `trunk/` intact after it has been updated to the release contents.
5. Copy `trunk/` into `tags/<version>/`.
6. Remove `tags/<version>/.git` before committing to SVN.
7. Copy reused readme/listing images into SVN `assets/`.
8. Commit to SVN using WordPress.org credentials stored as GitHub repository secrets.

Do not enable this automation until SVN credentials are available as repository secrets and the manual workflow has been tested once.

## Compatibility target for this maintenance pass

The plugin keeps its existing backward-compatible WordPress floor of 4.5 while declaring compatibility with WordPress 7.0. WordPress 7.0 reports a PHP requirement of 7.4, so this plugin now declares PHP 7.4 as the supported runtime floor for current WordPress compatibility.

Bundled dependencies were reviewed for the 2.0.4 pass and pinned to advisory-free floors that still support PHP 7.4: `smarty/smarty: ^4.5` (clears CVE-2024-35226; Smarty 3.x is EOL) and `twbs/bootstrap: ^4.6` (clears CVE-2019-8331). Re-run `composer audit` before each future release and bump these floors if new advisories appear.

## Next hardening tasks before a public release

- Run the plugin under WordPress 4.5, 5.7, 6.9, and 7.0 test installs.
- Run PHP compatibility checks for PHP 7.4 through the newest PHP version used by target hosts.
- Re-run `composer audit` at each release and keep the bundled `vendor/` free of known advisories (dependency review for 2.0.4 is complete — see the Compatibility target section).
- Test the manual SVN workflow once before automating it with GitHub Actions.
