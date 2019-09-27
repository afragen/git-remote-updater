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
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Updater
 */
class Updater {
	use Webhooks;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init( GIT_BULK_UPDATER_DIR );
	}

	/**
	 * Use wp_remote_get() on individual webhooks.
	 *
	 * @return void
	 */
	public function update() {
		if ( isset( $_POST['git_bulk_updater_nonce'] ) ) {
			if ( ! check_admin_referer( 'git_bulk_updater_nonce', 'git_bulk_updater_nonce' ) ) {
				return;
			}
			$webhooks = [];
			$update   = array_search( 'Update', $_POST, true );
			$type     = false !== strpos( $update, 'plugin_' ) ? 'plugin' : null;
			$type     = false !== strpos( $update, 'theme_' ) ? 'theme' : $type;
			$update   = 'plugin' === $type ? str_replace( 'plugin_', '', $update ) : $update;
			$update   = 'theme' === $type ? str_replace( 'theme_', '', $update ) : $update;
			$site     = null === $type ? str_replace( '_', '.', $update ) : null;
			$webhooks = null !== $site ? $this->all_webhooks[ $site ] : $webhooks;
			if ( null === $site ) {
				foreach ( $this->repos[ $update ]['urls'] as $url ) {
					$webhooks[] = $url;
				}
			}

			foreach ( $webhooks as $webhook ) {
				$response = wp_remote_get( $webhook );
			}
			( new Actions() )->redirect();
		}
	}
}
