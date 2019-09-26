# Git Bulk Updater

 * Plugin Name:       Git Bulk Updater
 * Plugin URI:        https://github.com/afragen/git-bulk-updater
 * Author:            Andy Fragen
 * Author URI:        https://github.com/afragen
 * Version:           0.0.1
 * License:           MIT
 * Domain Path:       /languages
 * Text Domain:       git-bulk-updater
 * GitHub Plugin URI: https://github.com/afragen/git-bulk-updater
 * Requires PHP:      7.1
 * Requires WP:       5.1

## Description

Allows you to easily update GitHub Updater repositories in bulk via RESTful endpoint updating.

## Setup

You must have a file `bulk-updates.json` at the root of this plugin in the following format.

```json
{
    "sites": [
        {
            "restful_start": "http://webhook1.test/wp-admin/admin-ajax.php?action=github-updater-update&key=99111ee0cc4876e473be9534b9d9d975",
            "slugs": [
                {
                    "slug": "test-plugin2",
                    "type": "plugin",
                    "branch": "master"
                },
                {
                    "slug": "test-bitbucket-child",
                    "type": "theme",
                    "branch": "master"
                }
            ]
        },
        {
            "restful_start": "http://webhook2.test/wp-admin/admin-ajax.php?action=github-updater-update&key=eabd2f85088619eb9f77a6b5b42b428c",
            "slugs": [
                {
                    "slug": "test-bitbucket-plugin",
                    "type": "plugin"
                },
                {
                    "slug": "sd-child",
                    "type": "theme"
                }
            ]
        }
    ]
}
```

The `restful_start` comes from you site's **GitHub Updater > Settings > Remote Management** tab.

The **branch** setting is optional, it will default to `master`.

Actions are currently populated in the `debug.log`.
