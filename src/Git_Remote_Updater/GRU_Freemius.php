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

use Fragen\Git_Updater\Shim;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Freemius integration.
 * Freemius 'start.php' autoloaded via composer.
 */
class GRU_Freemius {

	/**
	 * Freemius integration.
	 *
	 * @return array|void
	 */
	public function init() {
		if ( ! function_exists( 'gru_fs' ) ) {

			/**
			 * Create a helper function for easy SDK access.
			 *
			 * @return \stdClass
			 */
			function gru_fs() {
				global $gru_fs;

				if ( ! isset( $gru_fs ) ) {

					// Init Freemius SDK.
					require_once Shim::dirname( __DIR__, 2 ) . '/vendor/freemius/wordpress-sdk/start.php';

					$gru_fs = fs_dynamic_init(
						[
							'id'               => '8312',
							'slug'             => 'git-remote-updater',
							'premium_slug'     => 'git-remote-updater',
							'type'             => 'plugin',
							'public_key'       => 'pk_c108f4c4bc31d332d1dce40c1e653',
							'is_premium'       => true,
							'is_premium_only'  => true,
							'has_addons'       => false,
							'has_paid_plans'   => true,
							'is_org_compliant' => false,
							'trial'            => [
								'days'               => 7,
								'is_require_payment' => true,
							],
							'menu'             => [
								'slug'    => 'git-remote-updater',
								'contact' => false,
								'support' => false,
							],
						]
					);

				}

				return $gru_fs;
			}

			// Init Freemius.
			gru_fs();
			// Signal that SDK was initiated.
			do_action( 'gru_fs_loaded' );
		}
		gru_fs()->add_filter( 'plugin_icon', [ $this, 'add_icon' ] );
	}

	/**
	 * Add custom plugin icon to update notice.
	 *
	 * @return string
	 */
	public function add_icon() {
		return Shim::dirname( __DIR__, 2 ) . '/assets/icon.svg';
	}
}
