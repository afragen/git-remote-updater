<?php
/**
 * Git Bulk Updater
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/git-bulk-updater
 * @package git-bulk-updater
 */

/**
 * Plugin Name:       Git Bulk Updater
 * Plugin URI:        https://github.com/afragen/git-bulk-updater
 * Description:       Allows you to easily update GitHub Updater repositories in bulk via RESTful endpoint updating.
 * Author:            Andy Fragen
 * Author URI:        https://github.com/afragen
 * Version:           0.0.2
 * License:           MIT
 * Domain Path:       /languages
 * Text Domain:       git-bulk-updater
 * GitHub Plugin URI: https://github.com/afragen/git-bulk-updater
 * Requires PHP:      7.1
 * Requires WP:       5.1
 */

namespace Fragen\Git_Bulk_Updater;

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Setup plugin loading.
require_once __DIR__ . '/src/Git_Bulk_Updater/Bootstrap.php';
( new Bootstrap( __FILE__ ) )->run();
