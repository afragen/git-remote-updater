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
 * Class Actions_Row
 */
class Actions_Row {

	/**
	 * Add row to Action page.
	 *
	 * @return void
	 */
	public function add_row() {
		$webhooks = ( new Webhooks() )->run( GIT_BULK_UPDATER_DIR );
		$sites    = ( new Webhooks() )->parse_webhooks( $webhooks );
		foreach ( $sites as $site => $hooks ) {
			wp_nonce_field( 'git_bulk_updater_nonce', 'git_bulk_updater_nonce' );
			echo '<tr valign="top">';
			echo '<th scope="row">';
			echo wp_kses_post( $site );
			echo '</th><td>';
			foreach ( $hooks['parsed'] as $slug => $type ) {
				$type     = key( $type );
				$dashicon = 'plugin' === $type ? '<span class="dashicons dashicons-admin-plugins"></span>&nbsp;&nbsp;' : '&nbsp;';
				$dashicon = 'theme' === $type ? '<span class="dashicons dashicons-admin-appearance"></span>&nbsp;&nbsp;' : $dashicon;
				echo '<p>' . wp_kses_post( $dashicon . '&nbsp;' . $slug ) . '</p>';
			}
			echo '<td>';
			echo '<input type="submit" class="button button-secondary" name="' . esc_attr( $site ) . '" value="' . esc_html__( 'Update', 'git-bulk-updater' ) . '">';
			echo '</td>';
			echo '</tr>';
		}
	}
}
