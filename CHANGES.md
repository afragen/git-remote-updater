#### [unreleased]
* get REST namespace from site
* fix settings page site query
* remove constant and require PHP 7+
* requires Git Updater v10+ **and** Git Updater PRO

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
