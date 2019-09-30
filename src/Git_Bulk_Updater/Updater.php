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
	 * Use wp_remote_get() on individual webhooks.
	 *
	 * @return void
	 */
	public function update() {
		if ( isset( $_POST['git_bulk_updater_nonce'] ) ) {
			if ( ! check_admin_referer( 'git_bulk_updater_nonce', 'git_bulk_updater_nonce' ) ) {
				return;
			}
			$this->init( GIT_BULK_UPDATER_DIR );
			$message  = null;
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
				$response  = wp_remote_get( $webhook );
				$message[] = $this->parse_response( $response, $webhook );
			}
			if ( null !== $message ) {
				set_site_transient( 'git_bulk_updater_feedback', $message, 10 );
			}
			( new Actions() )->redirect();
		}
	}

	/**
	 * Parse wp_remote_get() response for feedback reporting.
	 *
	 * @param string $response JSON response to wp_remote_get().
	 * @param string $webhook URL of webhook.
	 *
	 * @return array $message
	 */
	private function parse_response( $response, $webhook ) {
		$site = parse_url( $webhook, PHP_URL_HOST );
		$site = "<strong>{$site}</strong>  ";
		if ( is_wp_error( $response ) ) {
			$error     = $response->errors;
			$message[] = isset( $error['http_request_failed'] ) ? 'WP_Error: ' . $error['http_request_failed'][0] : [];
		} else {
			$message = wp_remote_retrieve_body( $response );
			$message = json_decode( $message, true );
			$message = isset( $message['data']['messages'] ) ? $message['data']['messages'] : [];
		}
		if ( ! is_wp_error( $response ) &&
			( isset( $response['response']['code'] ) && 200 !== (int) $response['response']['code'] )
		) {
			$message[] = "{$response['response']['code']} {$response['response']['message']}";
		}
		array_unshift( $message, $site );
		return $message;
	}
}
