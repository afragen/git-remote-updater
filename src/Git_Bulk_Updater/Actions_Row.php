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
	use Webhooks;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Add row to Action page for sites.
	 *
	 * @return void
	 */
	public function add_site_rows() {
		echo '<tr><th>';
		esc_html_e( 'Site', 'git-bulk-updater' );
		echo '</th><th>';
		esc_html_e( 'Repositories', 'git-bulk-updater' );
		echo '</th><th>';
		esc_html_e( 'Action', 'git-bulk-updater' );
		echo '</th></tr>';

		foreach ( $this->sites as $site => $elements ) {
			wp_nonce_field( 'git_bulk_updater_nonce', 'git_bulk_updater_nonce' );
			echo '<tr valign="top">';
			echo '<th scope="row">';
			echo wp_kses_post( $site );
			echo '</th><td>';

			foreach ( $elements as $type => $repo ) {
				$dashicon = 'plugin' === $type ? '<span class="dashicons dashicons-admin-plugins"></span>&nbsp;&nbsp;' : '&nbsp;';
				$dashicon = 'theme' === $type ? '<span class="dashicons dashicons-admin-appearance"></span>&nbsp;&nbsp;' : $dashicon;
				foreach ( $repo as $slug => $url ) {
					echo '<p>' . wp_kses_post( $dashicon . '&nbsp;' . $slug ) . '</p>';
				}
			}

			echo '<td style="vertical-align:top">';
			echo '<input type="submit" class="button button-secondary" name="' . esc_attr( $site ) . '" value="' . esc_html__( 'Update', 'git-bulk-updater' ) . '">';
			echo '</td>';
			echo '</tr>';
		}
	}

	/**
	 * Add row to Actions page for repositories.
	 *
	 * @return void
	 */
	public function add_repo_rows() {
		echo '<tr><th>';
		esc_html_e( 'Repository', 'git-bulk-updater' );
		echo '</th><th>';
		esc_html_e( 'Sites', 'git-bulk-updater' );
		echo '</th><th>';
		esc_html_e( 'Action', 'git-bulk-updater' );
		echo '</th></tr>';

		ksort( $this->repos );
		foreach ( $this->repos as $slug => $elements ) {
			wp_nonce_field( 'git_bulk_updater_nonce', 'git_bulk_updater_nonce' );
			$sites = $elements['sites'];
			unset( $elements['sites'] );
			$type     = $elements['type'];
			$dashicon = 'plugin' === $type ? '<span class="dashicons dashicons-admin-plugins"></span>&nbsp;&nbsp;' : '&nbsp;';
			$dashicon = 'theme' === $type ? '<span class="dashicons dashicons-admin-appearance"></span>&nbsp;&nbsp;' : $dashicon;
			echo '<tr valign="top">';
			echo '<th scope="row">';
			echo wp_kses_post( $dashicon . '&nbsp;' . $slug );
			echo '</th><td>';

			foreach ( $sites as $site ) {
				echo '<p>' . wp_kses_post( $site ) . '</p>';
			}

			echo '</td><td style="vertical-align:top">';
			echo '<input type="submit" class="button button-secondary" name="' . esc_attr( $type ) . ' ' . esc_attr( $slug ) . '" value="' . esc_html__( 'Update', 'git-bulk-updater' ) . '">';
			echo '</td></tr>';
		}
	}
}
