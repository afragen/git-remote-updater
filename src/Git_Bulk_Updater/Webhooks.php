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
 * Class Webhooks
 */
class Webhooks {

	/**
	 * Start processing JSON for webhooks.
	 *
	 * @param string $dir Directory path.
	 * @return array $webhooks
	 */
	public function run( string $dir ) {
		$json     = $this->process_json( $dir );
		$webhooks = $this->get_webhooks( $json );
		return $webhooks;
	}

	/**
	 * Process bulk-updates.json.
	 *
	 * @param string $dir Directory path.
	 */
	public function process_json( string $dir ) {
		if ( file_exists( $dir . '/bulk-updates.json' ) ) {
			$config = file_get_contents( $dir . '/bulk-updates.json' );
			if ( empty( $config ) ||
				null === ( $config = json_decode( $config ) )
			) {
				return;
			}
			return $config;
		}
	}

	/**
	 * Create array of webhooks (supports multiple instances).
	 *
	 * @param \stdClass $config JSON config as string.
	 * @return array $webhooks
	 */
	public function get_webhooks( \stdClass $config ) {
		$webhooks = [];
		foreach ( $config as $sites ) {
			foreach ( $sites as $site ) {
				foreach ( $site->slugs as $slug ) {
					$host    = parse_url( "{$site->restful_start}", PHP_URL_HOST );
					$webhook = "{$site->restful_start}";
					$webhook = add_query_arg( $slug->type, $slug->slug, $webhook );
					$webhook = isset( $slug->branch ) ? add_query_arg( 'tag', $slug->branch, $webhook ) : $webhook;
					$webhook = add_query_arg( 'override', '', $webhook );
					$webhooks[ $host ][ $slug->type ][ $slug->slug ] = $webhook;
				}
			}
			return $webhooks;
		}
	}

	/**
	 * Parse the webhooks.
	 *
	 * @param array $webhooks Array of webhooks.
	 *
	 * @return array $parsed Array of parsed webhooks.
	 */
	public function parse_webhooks( array $webhooks ) {
		$parsed = null;
		foreach ( $webhooks as $site => $repos ) {
			$all_webhooks    = null;
			$parsed_webhooks = null;
			foreach ( $repos['plugin'] as $repo => $plugin_webhook ) {
				$parsed_webhooks[ $repo ] = [ 'plugin' => $plugin_webhook ];
				$all_webhooks[]           = $plugin_webhook;
			}
			foreach ( $repos['theme'] as $repo => $theme_webhook ) {
				$parsed_webhooks[ $repo ] = [ 'theme' => $theme_webhook ];
				$all_webhooks[]           = $theme_webhook;
			}
			$parsed[ $site ] = [
				'parsed' => $parsed_webhooks,
				'all'    => $all_webhooks,
			];
		}

		return $parsed;
	}

}
