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
	}

	/**
	 * Add options page.
	 */
	public function add_plugin_menu() {
		global $_registered_pages;
		if ( isset( $_registered_pages['settings_page_git-remote-updater'] ) ) {
			return;
		}

		$parent     = is_multisite() ? 'settings.php' : 'tools.php';
		$capability = is_multisite() ? 'manage_network' : 'manage_options';

		add_submenu_page(
			$parent,
			esc_html__( 'Git Remote Updater', 'git-remote-updater' ),
			esc_html__( 'Git Remote Updater', 'git-remote-updater' ),
			$capability,
			'git-remote-updater',
			[ $this, 'create_admin_page' ]
		);
	}

	/**
	 * Options page callback.
	 */
	public function create_admin_page() {
		$action = is_multisite() ? 'edit.php?action=git-remote-updater' : 'options.php';

		echo '<div class="wrap"><h2>';
		esc_html_e( 'Git Remote Updater', 'git-remote-updater' );
		echo '</h2>';

		$this->show_feedback();
		$this->repo_or_site_selector();

		echo '<form method="post" action="' . esc_attr( $action ) . '">';
		echo '<table class="form-table">';

		echo '<tbody class="git-remote-updater-repo">';
		( new Settings_Row() )->add_repo_rows();
		echo '</tbody>';

		echo '<tbody class="git-remote-updater-site">';
		( new Settings_Row() )->add_site_rows();
		echo '</tbody>';

		echo '</table></div>';
		echo '</form>';
	}

	/**
	 * Display update feedback.
	 *
	 * @return void
	 */
	private function show_feedback() {
		$feedback = get_site_transient( 'git_remote_updater_feedback' );
		if ( $feedback ) {
			echo '<div>';
			echo '<h3>' . esc_html__( 'Update Feedback', 'git-remote-updater' ) . '</h3>';
			foreach ( $feedback as $repo_feedback ) {
				echo '<div><p>';
				foreach ( $repo_feedback as $message ) {
					echo wp_kses_post( $message ) . '<br>';
				}
				echo '</p></div>';
			}
			echo '</div>';
		}
	}

	/**
	 * Repo or Site option.
	 */
	private function repo_or_site_selector() {
		$options = [
			'git-remote-updater-repo' => esc_html__( 'Show Repositories', 'git-remote-updater' ),
			'git-remote-updater-site' => esc_html__( 'Show Sites', 'git-remote-updater' ),
		]; ?>
		<label for="git-remote-updater">
			<select id="git-remote-updater" name="git-remote-updater">
				<?php foreach ( $options as $key => $value ) : ?>
						<option value="<?php esc_attr_e( $key ); ?>" <?php selected( $key ); ?> >
							<?php esc_html_e( $value ); ?>
						</option>
				<?php endforeach ?>
			</select>
		</label>
		<?php
	}

	/**
	 * Redirect to where we came from.
	 *
	 * @return void
	 */
	public function redirect() {
		$redirect_url = is_multisite() ? network_admin_url( 'settings.php' ) : admin_url( 'tools.php' );
		$location     = add_query_arg(
			[ 'page' => 'git-remote-updater' ],
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
}
