# Git Remote Updater

Plugin Name:       Git Remote Updater
Plugin URI:        https://github.com/afragen/git-remote-updater
Donate link:       https://thefragens.com/github-updater-donate
Author:            Andy Fragen
Author URI:        https://github.com/afragen
License:           MIT
Stable tag:        master
Requires PHP:      7.1
Requires at least: 5.2
Tested up to:      5.4

## Description

Allows you to easily update GitHub Updater repositories in bulk via [REST API endpoint updating](https://github.com/afragen/github-updater/wiki/Remote-Management---RESTful-Endpoints).

The **Git Remote Updater** page allows updating via repository or site.

## Setup

Requires [GitHub Updater](https://github.com/afragen/github-updater) v.9.0.0 or higher running on the sites you use with Git Remote Updater.

When the plugin is run it will create a directory at `wp-content/uploads/git-remote-updater`. This is the storage location for your JSON config files.

You must have one or more JSON files in the `wp-content/uploads/git-remote-updater/` directory.

You can download a JSON file from your `GitHub Updater > Remote Management > Make JSON file` button and then copy it to your `wp-content/uploads/git-remote-updater` folder. This JSON file contains the basic data for you site to allow for REST API updates. The site, REST API namespace/route, and the REST API key. Data regarding your plugins/themes are obtained via a REST API call back to your site. You will only need to update the site JSON file if your REST API key changes.

Update feedback will show at the top of the page. If you have debug logging set on your site, it is also added to the `debug.log`.

I recommend running Git Remote Updater from a local development environment installation of WordPress. It makes the collection and transfer of JSON files simpler, though you can run it from any WordPress site.
