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
								'first-path' => 'plugins.php',
								'contact'    => false,
								'support'    => false,
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
	}
}
