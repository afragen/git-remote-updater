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
class Actions {

	/**
	 * Display Actions page.
	 *
	 * @param string $action Form action.
	 *
	 * @return void
	 */
	public function display( $action ) {
		echo '<br>';
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

}
