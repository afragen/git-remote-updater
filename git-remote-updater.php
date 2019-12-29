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
 * Description:       Allows you to easily update GitHub Updater repositories in bulk via REST API endpoint updating.
 * Author:            Andy Fragen
 * Author URI:        https://github.com/afragen
 * Version:           0.3.6
 * License:           MIT
 * Network:           true
 * Domain Path:       /languages
 * Text Domain:       git-remote-updater
 * GitHub Plugin URI: https://github.com/afragen/git-remote-updater
 * Requires PHP:      7.1
 * Requires at least: 5.2
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

( new Bootstrap( __FILE__ ) )->run();
