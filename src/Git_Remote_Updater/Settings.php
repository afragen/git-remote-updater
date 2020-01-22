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
 * Class Actions
 */
class Settings {

	/**
	 * Load hooks.
	 *
	 * @return void
	 */
	public function load_hooks() {
		$this->load_js();
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', [ $this, 'add_plugin_menu' ] );
		add_action( 'admin_init', [ new Updater(), 'update' ] );
		add_action( 'admin_init', [ $this, 'update_settings' ] );
		add_action( 'network_admin_edit_git-remote-updater', [ $this, 'update_settings' ] );
	}

	/**
	 * Add options page.
	 */
	public function add_plugin_menu() {
		$capability = is_multisite() ? 'manage_network' : 'manage_options';

		add_menu_page(
			esc_html__( 'Git Remote Updater', 'git-remote-updater' ),
			esc_html_x( 'Git Remote Updater', 'Menu item', 'git-remote-updater' ),
			$capability,
			'git-remote-updater',
			[ $this, 'create_admin_page' ],
			'dashicons-update',
			null
		);

		add_submenu_page(
			'git-remote-updater',
			esc_html__( 'Update', 'git-remote-updater' ),
			esc_html__( 'Update', 'git-remote-updater' ),
			$capability,
			'git-remote-updater',
			[ $this, 'create_admin_page' ],
		);

		add_submenu_page(
			'git-remote-updater',
			esc_html__( 'Settings', 'git-remote-updater' ),
			esc_html__( 'Settings', 'git-remote-updater' ),
			$capability,
			'git-remote-updater-settings',
			[ $this, 'create_admin_page' ],
		);

	}

	/**
	 * Options page callback.
	 */
	public function create_admin_page() {
		$action = is_multisite() ? 'edit.php?action=git-remote-updater' : 'options.php';
		$page   = isset( $_GET['page'] ) ? esc_attr( ( $_GET['page'] ) ) : 'git-remote-updater';

		// Kludge for "redirect" after WP_List_Table bulk actions.
		if ( isset( $_REQUEST['_wpnonce_list'] ) ) {
			$page = 'git-remote-updater-settings';
		}

		echo '<div class="wrap"><h2>';
		esc_html_e( 'Git Remote Updater', 'git-remote-updater' );
		echo '</h2>';

		if ( 'git-remote-updater' === $page ) {
			( new Actions() )->display( $action );
		}
		if ( 'git-remote-updater-settings' === $page ) {
			( new Site_List_Table() )->render_list_table();
			$this->register_settings();
			echo '<form class="settings" method="post" action="' . esc_attr__( $action ) . '">';
			settings_fields( 'git_remote_updater' );
			do_settings_sections( 'git_remote_updater' );
			submit_button( esc_html__( 'Add Site', 'git-remote-updater' ) );
			echo '</form>';
		}
	}

	/**
	 * Update settings for single site or network activated.
	 *
	 * @link http://wordpress.stackexchange.com/questions/64968/settings-api-in-multisite-missing-update-message
	 * @link http://benohead.com/wordpress-network-wide-plugin-settings/
	 */
	public function update_settings() {
		$options   = get_site_option( 'git_remote_updater', [] );
		$duplicate = false;
		if ( isset( $_POST['option_page'], $_POST['git_remote_updater_site'], $_POST['git_remote_updater_key'] ) &&
			'git_remote_updater' === $_POST['option_page']
		) {
			$new_options = [
				'site'    => $_POST['git_remote_updater_site'],
				'api_key' => $_POST['git_remote_updater_key'],
			];
			$new_options = $this->sanitize( $new_options, $options );
			$empty_add   = empty( $_POST['git_remote_updater_site'] );

			foreach ( $options as $option ) {
				$duplicate = in_array( $new_options[0]['ID'], $option, true );
				if ( $duplicate ) {
					break;
				}
			}
			if ( ! $duplicate && ! $empty_add ) {
				$options = array_merge( $options, $new_options );
				update_site_option( 'git_remote_updater', $options );
			}
			$this->redirect();
		}
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array
	 */
	public function sanitize( $input, $options ) {
		$new_input = [];

		foreach ( (array) $input as $key => $value ) {
			$new_input[0][ $key ] = 'site' === $key ? untrailingslashit( esc_url_raw( $value ) ) : sanitize_text_field( $value );
			$new_input[0]['ID']   = md5( $new_input[0]['site'] );
		}

		return $new_input;
	}

	/**
	 * Redirect to where we came from.
	 *
	 * @return void
	 */
	public function redirect() {
		$redirect_url = is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' );
		$query        = isset( $_POST['_wp_http_referer'] ) ? parse_url( $_POST['_wp_http_referer'], PHP_URL_QUERY ) : null;
		parse_str( $query, $arr );

		$location = add_query_arg(
			[
				'page'    => $arr['page'],
				'updated' => true,
			],
			$redirect_url
		);
		wp_safe_redirect( $location );
		exit();
	}

	/**
	 * Load javascript for Install.
	 *
	 * @return void
	 */
	public function load_js() {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_GET['page'] ) && 'git-remote-updater' === $_GET['page'] ) {
			add_action(
				'admin_enqueue_scripts',
				function () {
					// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
					wp_register_script( 'git-remote-updater-actions', plugins_url( basename( GIT_REMOTE_UPDATER_DIR ) . '/js/git-remote-updater-switcher.js' ), [], false, true );
					wp_enqueue_script( 'git-remote-updater-actions' );
				}
			);
		}
	}

	/**
	 * Add settings sections.
	 */
	public function register_settings() {
		register_setting(
			'git_remote_updater',
			'git_remote_updater',
			[ $this, 'sanitize' ]
		);

		add_settings_section(
			'git_remote_updater',
			esc_html__( 'Add Site Data', 'git_remote_updater' ),
			[],
			'git_remote_updater'
		);

		add_settings_field(
			'site',
			esc_html__( 'Site URL', 'git_remote_updater' ),
			[ $this, 'get_site' ],
			'git_remote_updater',
			'git_remote_updater'
		);

		add_settings_field(
			'api_key',
			esc_html__( 'REST API key', 'git_remote_updater' ),
			[ $this, 'get_api_key' ],
			'git_remote_updater',
			'git_remote_updater'
		);
	}

	/**
	 * Site setting.
	 */
	public function get_site() {
		?>
		<label for="git_remote_updater_site">
			<input type="text" style="width:50%;" id="git_remote_updater_site" name="git_remote_updater_site" value="" autofocus>
			<br>
			<span class="description">
				<?php esc_html_e( 'URI is case sensitive.', 'git-remote-updater' ); ?>
			</span>
		</label>
		<?php
	}

	/**
	 * API key setting.
	 */
	public function get_api_key() {
		?>
		<label for="git_remote_updater_key">
			<input type="text" style="width:50%;" id="git_remote_updater_key" name="git_remote_updater_key" value="">
		</label>
		<?php
	}

}
