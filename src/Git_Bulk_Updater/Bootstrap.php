<?php
/**
 * Git Bulk Updater
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/git-bulk-updater
 * @package git-bulk-updater
 */

namespace Fragen\Git_Bulk_Updater;

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
	 * @param  string $file Main plugin file.
	 *
	 * @return void
	 */
	public function __construct( $file ) {
		$this->file    = $file;
		$this->dir     = dirname( $file );
		$this->storage = WP_CONTENT_DIR . '/uploads/git-bulk-updater';
	}

	/**
	 * Run the bootstrap.
	 *
	 * @return void
	 */
	public function run() {
		add_action(
			'init',
			function() {
				load_plugin_textdomain( 'git-bulk-updater' );
			}
		);

		define( 'GIT_BULK_UPDATER_DIR', $this->dir );
		define( 'GIT_BULK_UPDATER_JSON_PATH', $this->storage );

		// Load Autoloader.
		require_once $this->dir . '/vendor/autoload.php';

		// Check/create JSON storage location.
		if ( ! wp_mkdir_p( $this->storage ) ) {
			$error = __( 'Unable to create JSON storage folder for Git Bulk Updater.', 'git-bulk-updater' );
			wp_die( esc_html( $error ) );
		}

		( new Actions() )->load_hooks();
	}

}
