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

You must have one or more JSON files in the `\jsons\` directory of this plugin in the following format.

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
        }
    ]
}
```

The `restful_start` comes from you site's **GitHub Updater > Settings > Remote Management** tab.

The **branch** setting is optional, it will default to `master`.

Actions are currently populated in the `debug.log`.
