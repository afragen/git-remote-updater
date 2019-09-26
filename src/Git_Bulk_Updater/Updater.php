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
			$site     = str_replace( '_', '.', array_search( 'Update', $_POST, true ) );
			$webhooks = ( new Webhooks() )->run( GIT_BULK_UPDATER_DIR );
			$sites    = ( new Webhooks() )->parse_webhooks( $webhooks );
			foreach ( $sites[ $site ]['all'] as $webhook ) {
				wp_remote_get( $webhook );
			}
			( new Actions() )->redirect();
		}
	}
}
