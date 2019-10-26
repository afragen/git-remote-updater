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
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Actions_Row
 */
class Settings_Row {
	use Webhooks;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
		$this->can_run();
	}

	/**
	 * Are there basic data to run?
	 *
	 * @return void
	 */
	private function can_run() {
		if ( empty( $this->sites ) || empty( $this->repos ) ) {
			wp_die( esc_html__( 'Please add JSON config files to your JSON storage folder.', 'git-remote-updater' ) );
		}
	}

	/**
	 * Add row to Action page for sites.
	 *
	 * @return void
	 */
	public function add_site_rows() {
		$this->can_run();
		echo '<tr><th>';
		esc_html_e( 'Site', 'git-remote-updater' );
		echo '</th><th>';
		esc_html_e( 'Repositories', 'git-remote-updater' );
		echo '</th><th>';
		esc_html_e( 'Action', 'git-remote-updater' );
		echo '</th></tr>';

		foreach ( $this->sites as $site => $elements ) {
			wp_nonce_field( 'git_remote_updater_nonce', 'git_remote_updater_nonce' );
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
			echo '<input type="submit" class="button button-secondary" name="' . esc_attr( $site ) . '" value="' . esc_html__( 'Update', 'git-remote-updater' ) . '">';
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
		$this->can_run();
		echo '<tr><th>';
		esc_html_e( 'Repository', 'git-remote-updater' );
		echo '</th><th>';
		esc_html_e( 'Sites', 'git-remote-updater' );
		echo '</th><th>';
		esc_html_e( 'Action', 'git-remote-updater' );
		echo '</th></tr>';

		ksort( $this->repos );
		foreach ( $this->repos as $slug => $elements ) {
			wp_nonce_field( 'git_remote_updater_nonce', 'git_remote_updater_nonce' );
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
			echo '<input type="submit" class="button button-secondary" name="' . esc_attr( $type ) . ' ' . esc_attr( $slug ) . '" value="' . esc_html__( 'Update', 'git-remote-updater' ) . '">';
			echo '</td></tr>';
		}
	}
}
