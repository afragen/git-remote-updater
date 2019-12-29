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
		if ( isset( $_POST['git_remote_updater_nonce'] ) ) {
			if ( ! check_admin_referer( 'git_remote_updater_nonce', 'git_remote_updater_nonce' ) ) {
				return;
			}
			$this->init();
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

			// Try again if response is WP_Error.
			foreach ( $webhooks as $webhook ) {
				do {
					$response  = wp_remote_get( $webhook );
					$message[] = $this->parse_response( $response, $webhook );
				} while ( is_wp_error( $response ) );
			}
			if ( null !== $message ) {
				set_site_transient( 'git_remote_updater_feedback', $message, 10 );
			}
			( new Settings() )->redirect();
		}
	}

	/**
	 * Parse wp_remote_get() response for feedback reporting.
	 *
	 * @param string $response JSON response to wp_remote_get().
	 * @param string $webhook  URL of webhook.
	 *
	 * @return array $message
	 */
	private function parse_response( $response, $webhook ) {
		$parsed = parse_url( $webhook );
		$site   = $parsed['host'];
		parse_str( $parsed['query'], $query );
		$repo = isset( $query['plugin'] ) ? $query['plugin'] : $query['theme'];

		switch ( $response ) {
			case is_wp_error( $response ):
				$error     = $response->errors;
				$message[] = isset( $error['http_request_failed'] ) ? 'WP_Error - ' . $error['http_request_failed'][0] : [];
				break;
			default:
				$message = wp_remote_retrieve_body( $response );
				$message = json_decode( $message, true );
				$message = isset( $message['data']['messages'] ) ? $message['data']['messages'] : [];
				if ( 200 !== (int) $response['response']['code'] ) {
					$message = is_array( $message ) ? $message : (array) $message;
					array_unshift( $message, "{$response['response']['code']} {$response['response']['message']}" );
				}
				break;
		}
		array_unshift( $message, "<strong>{$site}: {$repo}</strong>" );

		return $message;
	}
}
