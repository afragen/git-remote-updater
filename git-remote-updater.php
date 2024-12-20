<?php
/**
 * Git Remote Updater
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/git-remote-updater
 * @package git-remote-updater
 */

/**
 * Plugin Name:       Git Remote Updater
 * Plugin URI:        https://github.com/afragen/git-remote-updater
 * Description:       Allows you to easily update Git Updater repositories in bulk via REST API endpoint updating. Requires Git Updater PRO.
 * Author:            Andy Fragen
 * Author URI:        https://github.com/afragen
 * Version:           3.2.0
 * License:           MIT
 * Network:           true
 * Domain Path:       /languages
 * Text Domain:       git-remote-updater
 * GitHub Plugin URI: https://github.com/afragen/git-remote-updater
 * GitHub Languages:  https://github.com/afragen/git-remote-updater-translations
 * Requires PHP:      7.2
 * Requires at least: 5.9
 */

namespace Fragen\Git_Remote_Updater;

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load Autoloader.
require_once __DIR__ . '/vendor/autoload.php';

add_action(
	'init',
	function () {
		// Make sure `is_plugin_active()` is available.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		// Don't load if Git Updater not running.
		if ( ! is_plugin_active( 'git-updater/git-updater.php' ) ) {
			return;
		}

		( new Settings() )->load_hooks();
	}
);
