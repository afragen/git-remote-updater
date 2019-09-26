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

use Fragen\Git_Bulk_Updater\Bootstrap;
use Fragen\Git_Bulk_Updater\Action_Row;

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
trait Webhooks {

	/**
	 * Holds data for sites.
	 *
	 * @var \stdClass
	 */
	public $sites;

	/**
	 * Holds data for repositories.
	 *
	 * @var \stdClass
	 */
	public $repos;

	/**
	 * Holds data for webhooks.
	 *
	 * @var \stdClass
	 */
	public $all_webhooks;

	/**
	 * Start processing JSON for webhooks.
	 *
	 * @param string $dir Directory path.
	 *
	 * @return void
	 */
	public function init( string $dir ) {
		$json = $this->process_json( $dir . '/jsons/' );
		$this->get_webhooks( $json );
		$this->get_all_webhooks();
	}

	/**
	 * Process bulk-updates.json.
	 *
	 * @param string $dir Directory path.
	 *
	 * @return \stdClass $configs
	 */
	public function process_json( string $dir ) {
		$jsons        = $this->list_directory( $dir );
		$configs      = [];
		$site_configs = [];
		foreach ( $jsons as $json ) {
			$config = null;
			if ( file_exists( $dir . $json ) ) {
				$config = file_get_contents( $dir . $json );
				if ( empty( $config ) ||
				null === ( $config = json_decode( $config ) )
				) {
					continue;
				}
			}
			$site_configs = array_merge( $site_configs, $config->sites );
		}

		foreach ( $site_configs as $site ) {
			$configs['site'][] = $site;
		}

		return (object) $configs;
	}

	/**
	 * Get directory listing of JSON files.
	 *
	 * @param string $dir Directory path the JSON files.
	 *
	 * @return array $arr_dir
	 */
	private function list_directory( $dir ) {
		$arr_dir = array();
		foreach ( glob( $dir . '*.{json}', GLOB_BRACE ) as $file ) {
			array_push( $arr_dir, basename( $file ) );
		}

		return $arr_dir;
	}
	/**
	 * Create array of webhooks (supports multiple instances).
	 *
	 * @param \stdClass $config JSON config as string.
	 *
	 * @return void
	 */
	public function get_webhooks( \stdClass $config ) {
		foreach ( $config as $sites ) {
			$parsed_sites = [];
			$repos        = [];
			foreach ( $sites as $site ) {
				foreach ( $site->slugs as $repo ) {
					$parsed_sites[ $site->site ][ $repo->type ][ $repo->slug ] = $this->get_endpoint( $site, $repo );
					$repos[ $repo->slug ][]                                    = [
						'site' => $site->site,
						'slug' => $repo->slug,
						'type' => $repo->type,
						'url'  => $this->get_endpoint( $site, $repo ),
					];
					$repos[ $repo->slug ]['sites'][]                           = $site->site;
				}
			}
			$this->sites = $parsed_sites;
			$this->repos = $repos;
		}
	}

	/**
	 * Get RESTful endpoint.
	 *
	 * @param \stdClass $site Object of site.
	 * @param \stdClass $repo Object of repo.
	 *
	 * @return string $endpoint
	 */
	private function get_endpoint( $site, $repo ) {
		$endpoint = add_query_arg( $repo->type, $repo->slug, "{$site->restful_start}" );
		$endpoint = isset( $repo->branch ) ? add_query_arg( 'tag', $repo->branch, $endpoint ) : $endpoint;
		$endpoint = add_query_arg( 'override', '', $endpoint );

		return $endpoint;
	}

	/**
	 * Parse the webhooks.
	 *
	 * @return void
	 */
	public function get_all_webhooks() {
		$parsed          = null;
		$all_webhooks    = null;
		$parsed_webhooks = null;
		foreach ( $this->sites as $site => $repos ) {
			foreach ( $repos as $repo ) {
				foreach ( $repo as $url ) {
					$all_webhooks[ $site ][] = $url;
				}
			}
		}

		$this->all_webhooks = $all_webhooks;
	}
}
