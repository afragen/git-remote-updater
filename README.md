# Git Bulk Updater

 * Plugin Name:       Git Bulk Updater
 * Plugin URI:        https://github.com/afragen/git-bulk-updater
 * Author:            Andy Fragen
 * Author URI:        https://github.com/afragen
 * License:           MIT
 * Domain Path:       /languages
 * Text Domain:       git-bulk-updater
 * GitHub Plugin URI: https://github.com/afragen/git-bulk-updater
 * Requires PHP:      7.1
 * Requires WP:       5.1

## Description

Allows you to easily update GitHub Updater repositories in bulk via [RESTful endpoint updating](https://github.com/afragen/github-updater/wiki/Remote-Management---RESTful-Endpoints).

The **Git Bulk Updater** page allows updating via repository or site.

## Setup

When the plugin is run it will create a directory at `wp-content/uploads/git-bulk-updater`. This is the storage location for your JSON config files.

You must have one or more JSON files in the `wp-content/uploads/git-bulk-updater/` directory.

You can download a JSON file from your `GitHub Updater > Remote Management > Make JSON file` button and then copy it to your `wp-content/uploads/git-bulk-updater` folder.

Feedback will show at the top of the page and if you have debug logging set on your site it is also currently populated in the `debug.log`.
