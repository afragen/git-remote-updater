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
 * Class Actions
 */
class Actions {
	use Webhooks;

	/**
	 * Load hooks.
	 *
	 * @return void
	 */
	public function load_hooks() {
		add_action( 'admin_menu', [ $this, 'add_plugin_menu' ] );
		add_action( 'admin_init', [ new Updater(), 'update' ] );
	}

	/**
	 * Add options page.
	 */
	public function add_plugin_menu() {
		global $_registered_pages;
		if ( isset( $_registered_pages['settings_page_git-bulk-updater'] ) ) {
			return;
		}

		$parent     = is_multisite() ? 'settings.php' : 'tools.php';
		$capability = is_multisite() ? 'manage_network' : 'manage_options';

		add_submenu_page(
			$parent,
			esc_html__( 'Git Bulk Updater', 'git-bulk-updater' ),
			esc_html__( 'Git Bulk Updater', 'git-bulk-updater' ),
			$capability,
			'git-bulk-updater',
			[ $this, 'create_admin_page' ]
		);
	}

	/**
	 * Options page callback.
	 */
	public function create_admin_page() {
		$action = is_multisite() ? 'edit.php?action=git-bulk-updater' : 'options.php';
		?>
		<div class="wrap">
			<h2>
				<?php esc_html_e( 'Git Bulk Updater', 'git-bulk-updater' ); ?>
			</h2>
			<form method='post' action="<?php esc_attr_e( $action ); ?>">
			<table class='form-table'>
			<tbody>
		<?php
		( new Actions_Row() )->add_site_row();
		( new Actions_Row() )->add_repo_row();
		echo '</tbody></table></div>';
		echo '</form>';
	}

	/**
	 * Redirect to where we came from.
	 *
	 * @return void
	 */
	public function redirect() {
		$redirect_url = is_multisite() ? network_admin_url( 'settings.php' ) : admin_url( 'tools.php' );
		$location     = add_query_arg(
			[ 'page' => 'git-bulk-updater' ],
			$redirect_url
		);
		wp_safe_redirect( $location );
		exit();
	}
}
