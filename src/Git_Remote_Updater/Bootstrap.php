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

		/**
		 * Simple filter to turn on normal API checks, etc.
		 *
		 * @since Git Updater 10.2.0
		 */
		if ( ! \apply_filters( 'gu_test_premium_plugins', false ) ) {
			$this->load_hooks();
		}
	}

	/**
	 * Hooks to use Freemius for updating and display properly in GitHub subtab.
	 *
	 * @return void
	 */
	public function load_hooks() {
		add_filter(
			'gu_github_api_no_check',
			function( $false, $repo ) {
				return 'git-remote-updater/git-remote-updater.php' === $repo->file;
			},
			10,
			2
		);

		add_filter(
			'gu_github_api_no_wait',
			function( $repos ) {
				unset( $repos['git-remote-updater'] );

				return $repos;
			},
			10,
			1
		);

		add_filter(
			'gu_display_repos',
			function( $type_repos ) {
				if ( isset( $type_repos['git-remote-updater'] ) ) {
					$type_repos['git-remote-updater']->is_private     = true;
					$type_repos['git-remote-updater']->remote_version = true;
				}

				return $type_repos;
			},
			10,
			1
		);

		add_filter(
			'gu_add_repo_setting_field',
			function( $arr, $token ) {
				if ( 'git-remote-updater/git-remote-updater.php' === $token->file ) {
					$arr = [];
				}

				return $arr;
			},
			15,
			2
		);
	}
}
