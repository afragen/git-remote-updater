<?php
/**
 * Git Remote Updater
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/git-remote-updater
 * @package git-remote-updater
 */

namespace Fragen\Git_Remote_Updater;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Bootstrap
 */
class Bootstrap {
	/**
	 * Run the bootstrap.
	 *
	 * @return void
	 */
	public function run() {
		add_action(
			'init',
			function () {
				load_plugin_textdomain( 'git-remote-updater' );
			}
		);

		( new Settings() )->load_hooks();
	}
}
