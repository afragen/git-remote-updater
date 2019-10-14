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
 * Description:       Allows you to easily update GitHub Updater repositories in bulk via RESTful endpoint updating.
 * Author:            Andy Fragen
 * Author URI:        https://github.com/afragen
 * Version:           0.3.0
 * License:           MIT
 * Domain Path:       /languages
 * Text Domain:       git-remote-updater
 * GitHub Plugin URI: https://github.com/afragen/git-remote-updater
 * Requires PHP:      7.1
 * Requires WP:       5.1
 */

namespace Fragen\Git_Remote_Updater;

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Setup plugin loading.
require_once __DIR__ . '/src/Git_Remote_Updater/Bootstrap.php';
( new Bootstrap( __FILE__ ) )->run();
