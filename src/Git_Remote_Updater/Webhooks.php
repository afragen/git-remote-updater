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
		$options = get_site_option( 'git_remote_updater', [] );
		$config  = $this->process_options( $options );
		$config  = $this->get_site_data( $config );
		$this->get_webhooks( $config );
		$this->get_all_webhooks();
	}

	/**
	 * Process options for config object.
	 *
	 * @param array $options Site options.
	 *
	 * @return \stdClass
	 */
	private function process_options( $options ) {
		$config    = [];
		$namespace = 'github-updater/v1';
		$route     = 'repos';

		foreach ( $options as $option ) {
			$site                             = new \stdClass();
			$site->site                       = new \stdClass();
			$site->site->host                 = $option['site'];
			$site->site->rest_namespace_route = "$namespace/$route/";
			$site->site->rest_api_key         = $option['api_key'];
			$config[]                         = $site;
		}

		return (object) $config;
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
		$json    = get_site_transient( 'git_remote_updater_repo_data' );
		$message = null;

		if ( ! $json ) {
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

				if ( is_object( $response ) && \property_exists( $response, 'error' ) ) {
					$message[] = "{$sites->site->host}<br>{$response->error}";
					continue;
				}

				$json[ $sites->site->host ] = $response;
			}
			/**
			 * Filter to add slugs to be removed from updating.
			 *
			 * @since 0.3.6
			 *
			 * @param array Array of slugs to remove from site data.
			 */
			$remove_slugs = \apply_filters( 'git_remote_updater_remove_site_data', [] );
			if ( ! empty( $remove_slugs ) ) {
				$json = $this->remove_slugs_from_json( $json, $remove_slugs );
			}

			/**
			 * Filter the transient timeout.
			 * Useful for testing. Default is 600 seconds.
			 *
			 * @since 0.4.7
			 * @return int
			 */
			$timeout = \apply_filters( 'git_remote_updater_repo_transient_timeout', 600 );
			set_site_transient( 'git_remote_updater_repo_data', $json, $timeout );

			// Display error feedback.
			if ( null !== $message ) {
				echo '<div class="error notice is-dismissible"><p>';
				foreach ( $message as $feedback ) {
					echo wp_kses_post( $feedback ) . '<br>';
				}
				echo '</p></div>';
			}
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
			if ( empty( $sites ) ) {
				return;
			}
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
		if ( isset( $repo->branch, $repo->primary_branch, $repo->tag )
			&& $repo->primary_branch === $repo->branch
			&& $repo->tag
		) {
			$endpoint = remove_query_arg( 'branch', $endpoint );
			$endpoint = add_query_arg( 'tag', $repo->tag, $endpoint );
		}
		$endpoint = add_query_arg( 'override', true, $endpoint );

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
		foreach ( (array) $this->sites as $site => $repos ) {
			foreach ( $repos as $repo ) {
				foreach ( $repo as $url ) {
					$all_webhooks[ $site ][] = $url;
				}
			}
		}

		$this->all_webhooks = $all_webhooks;
	}

	/**
	 * Filter JSON data to remove specific slugs from updating.
	 *
	 * @param array $json         Array of sites data.
	 * @param array $remove_slugs Array of slugs to remove from updating.
	 *
	 * @return array
	 */
	private function remove_slugs_from_json( $json, $remove_slugs ) {
		if ( ! $json ) {
			return false;
		}
		foreach ( $json as $sites ) {
			if ( \property_exists( $sites, 'sites' ) ) {
				foreach ( $sites->sites->slugs as $key => $slug ) {
					if ( in_array( $slug->slug, $remove_slugs, true ) ) {
						unset( $sites->sites->slugs[ $key ] );
					}
				}
			}
		}

		return $json;
	}
}
