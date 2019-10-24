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
	 * @return void
	 */
	public function init() {
		$config = $this->process_json( GIT_REMOTE_UPDATER_JSON_PATH );
		$config = $this->get_site_data( $config );
		$this->get_webhooks( $config );
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
		$configs      = new \stdClass();
		$site_configs = [];

		foreach ( $jsons as $json ) {
			$config = null;
			if ( file_exists( "{$dir}/{$json}" ) ) {
				$config = file_get_contents( "{$dir}/{$json}" );
				if ( empty( $config ) ||
				null === ( $config = json_decode( $config ) )
				) {
					continue;
				}
			}
			$site_configs[] = $config;
		}

		return (object) $site_configs;
	}

	/**
	 * Get directory listing of JSON files.
	 *
	 * @param string $dir Directory path the JSON files.
	 *
	 * @return array $arr_dir
	 */
	private function list_directory( $dir ) {
		$arr_dir = [];
		foreach ( glob( "{$dir}/*.json" ) as $file ) {
			array_push( $arr_dir, basename( $file ) );
		}

		return $arr_dir;
	}

	/**
	 * Get remote site repo data via REST API endpoint.
	 *
	 * @param \stdClass $config Parsed JSON file created from GitHub Updater.
	 *
	 * @return \stdClass
	 */
	public function get_site_data( \stdClass $config ) {
		$json = [];
		foreach ( $config as $sites ) {
			$rest_url = $sites->site->host . '/wp-json/' . $sites->site->rest_namespace_route;
			$rest_url = add_query_arg( [ 'key' => $sites->site->rest_api_key ], $rest_url );
			$response = wp_remote_get( $rest_url );
			$code     = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $code ) {
				continue;
			}
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response );

			$json[ $sites->site->host ] = $response;
		}

		return (object) $json;
	}

	/**
	 * Create array of webhooks (supports multiple instances).
	 *
	 * @param \stdClass $config JSON config as string.
	 *
	 * @return void
	 */
	public function get_webhooks( \stdClass $config ) {
		$parsed_sites = [];
		$repos        = [];
		foreach ( $config as $sites ) {
			foreach ( $sites as $site ) {
				foreach ( $site->slugs as $repo ) {
					$repo_sites = [];
					$url        = [];

					$parsed_sites[ $site->site ][ $repo->type ][ $repo->slug ] = $this->get_endpoint( $site, $repo );

					$repos[ $repo->slug ] = [
						'slug' => $repo->slug,
						'type' => $repo->type,
						'url'  => $this->get_endpoint( $site, $repo ),
					];

					$url   = isset( $this->repos[ $repo->slug ]['urls'] ) ? $this->repos[ $repo->slug ]['urls'] : [];
					$url[] = $repos[ $repo->slug ]['url'];

					$repo_sites   = isset( $this->repos[ $repo->slug ]['sites'] ) ? $this->repos[ $repo->slug ]['sites'] : [];
					$repo_sites[] = $site->site;
					$repo_sites   = array_unique( $repo_sites );

					$repos[ $repo->slug ]['urls']  = $url;
					$repos[ $repo->slug ]['sites'] = isset( $repos[ $repo->slug ]['sites'] ) ? array_merge( $repos[ $repo->slug ]['sites'], $repo_sites ) : $repo_sites;
				}
				$parsed_sites = array_merge( (array) $this->sites, $parsed_sites );
				$repos        = array_merge( (array) $this->repos, $repos );
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
		$endpoint = isset( $repo->branch ) ? add_query_arg( 'branch', $repo->branch, $endpoint ) : $endpoint;
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
