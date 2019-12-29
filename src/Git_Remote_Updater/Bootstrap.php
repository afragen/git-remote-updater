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
	 * Holds main plugin file.
	 *
	 * @var $file
	 */
	protected $file;

	/**
	 * Holds main plugin directory.
	 *
	 * @var $dir
	 */
	protected $dir;

	/**
	 * Holds JSON storage directory path.
	 *
	 * @var $storage
	 */
	protected $storage;

	/**
	 * Constructor.
	 *
	 * @param string $file Main plugin file.
	 *
	 * @return void
	 */
	public function __construct( $file ) {
		$this->file    = $file;
		$this->dir     = dirname( $file );
		$this->storage = WP_CONTENT_DIR . '/uploads/git-remote-updater';
	}

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

		define( 'GIT_REMOTE_UPDATER_DIR', $this->dir );
		define( 'GIT_REMOTE_UPDATER_JSON_PATH', $this->storage );

		// Check/create JSON storage location.
		if ( ! wp_mkdir_p( $this->storage ) ) {
			$error = __( 'Unable to create JSON storage folder for Git Remote Updater.', 'git-remote-updater' );
			wp_die( esc_html( $error ) );
		}

		( new Settings() )->load_hooks();
	}
}
