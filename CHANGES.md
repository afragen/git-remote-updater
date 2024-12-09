#### [unreleased]
* add GA to generate POT
* add GA to add release asset
* load in `init` for `_load_textdomain_just_in_time`

#### 3.1.1 / 2023-09-08
* update to WPCS 3.0.0

#### 3.1.0 / 2022-10-24
* require WP 5.9+
* check for `is_plugins_active` function and load `wp-admin/includes/plugin.php` if needed
* remove Freemius SDK

#### 3.0.1 / 2022-08-27
* update Freemius/wordpress-sdk

#### 3.0.0 / 2022-04-24
* require PHP7.2+

#### 2.4.3 / 2022-03-10
* composer update

#### 2.4.2 / 2022-03-02
* update `class Site_List_Table` and `Settings` nonces
* move check for Git Updater to hook
* update Freemius/wordpress-sdk to 2.4.3

#### 2.4.1 / 2021-11-16
* remove checkbox from Settings List table
* use `sanitize_title_with_dashes()` as `sanitize_file_name()` maybe have attached filter that changes output

#### 2.4.0 / 2021-07-05
* remove Freemius from the autoloader
* utilize new `class Ignore` from Git Updater
* uses new `class Fragen\Git_Updater\Shim` for PHP 5.6 compatibility, will remove when WP core changes minimum requirement

#### 2.3.1 / 2021-06-14
* utilize new `class Ignore` in Git Updater

#### 2.3.0 / 2021-06-02
* add filter to skip updating from Git Updater
* add filter to display this plugin in GitHub subtab without errors

#### 2.2.3 / 2021-05-21
* add language pack updating

#### 2.2.2 / 2021-05-18
* ensure custom icon shows in update notice from Freemius

#### 2.2.0 / 2021-05-11
* update assets

#### 2.1.0 / 2021-05-03
* add Freemius integration

#### 2.0.0 / 2021-04-15
* get REST namespace from site
* fix settings page site query
* remove constant and require PHP 7+
* requires Git Updater v10+ **and** Git Updater PRO, now just Git Updater v12+

#### 1.0.1 / 2020-08-07
* add some error checking

#### 1.0.0 / 2020-07-31 - 💥
* initial pass
* add rows for both update entire site or update all sites with specific repository
* add ability to use multiple JSON files.
* update for use with downloadable JSON files from GitHub Updater
* add javascript to switch display from repos/sites
* add update feedback messaging
* create JSON storage directory in `wp-content/uploads/git-remote-updater`
* sort repositories by name
* more error checking during update process
* rebrand to Git Remote Updater
* keep retrying if remote response is WP_Error
* added filter and function to remove slugs from appearing for updates
* added a Settings tab to add site data, removed JSON storage directory
* added nonce checks for WP_List_Table
* move Git Remote Updater a top level menu
* added `git_remote_updater_repo_transient_timeout` to set transient timeout, returns int with default of 600 seconds. I use this for testing.
* escape, sanitize & ignore
* add error messaging when getting repo data
* increase `wp_remote_get` timeout for updating, helps to avoid a timeout loop
* with GitHub Updater v 9.7.2 or later, repos with tags and set to primary branch will use tag for remote update
