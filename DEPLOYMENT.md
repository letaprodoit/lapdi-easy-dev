# WordPress.org deployment plan

## Current repository strategy

Use Git/GitHub for day-to-day development, code review, issues, and CI. WordPress.org still publishes plugins from its Subversion repository, so SVN remains the release target for the public plugin directory.

In practice this means:

1. Develop and review changes in Git.
2. Tag a release in Git when the plugin is ready.
3. Deploy that finished release artifact to the WordPress.org SVN repository.
4. Do not use the WordPress.org SVN repository as the working development history.

The WordPress Plugin Handbook describes the Plugin Directory SVN repository as a release repository rather than a normal development repository, so only finished releases should be pushed there.

## Recommended release flow

1. Update compatibility metadata in `README.txt` and the main plugin header.
2. Run local checks against the supported PHP and WordPress versions.
3. Build/install production dependencies with Composer if the release package includes `vendor/`.
4. Create a Git tag matching the plugin version.
5. Deploy the tagged package to WordPress.org SVN, either manually with `svn` or through a GitHub Actions workflow that commits the Git tag contents to SVN.
6. Verify the WordPress.org plugin page shows the new stable tag, readme metadata, and assets correctly.

## Compatibility target for this maintenance pass

The plugin keeps its existing backward-compatible WordPress floor of 4.5 while declaring compatibility with WordPress 7.0. WordPress 7.0 reports a PHP requirement of 7.4, so this plugin now declares PHP 7.4 as the supported runtime floor for current WordPress compatibility.

## Next hardening tasks before a public release

- Run the plugin under WordPress 4.5, 5.7, 6.9, and 7.0 test installs.
- Run PHP compatibility checks for PHP 7.4 through the newest PHP version used by target hosts.
- Review bundled dependencies and decide whether to commit a production `vendor/` directory for WordPress.org distribution.
- Add a GitHub Actions deployment workflow only after SVN credentials are available as repository secrets.
