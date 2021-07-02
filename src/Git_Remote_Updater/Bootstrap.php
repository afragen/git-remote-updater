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

use Fragen\Git_Updater\Ignore;

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
		if ( ! gru_fs()->can_use_premium_code() ) {
			return;
		}
		add_action(
			'init',
			function () {
				load_plugin_textdomain( 'git-remote-updater' );
			}
		);

		( new Settings() )->load_hooks();
	}
}
