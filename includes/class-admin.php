<?php


class EDD_HS_Admin {

	/**
	 * Disable greedy listening if a new person is activating the plugin
	 */
	public static function plugin_activation() {
		update_option( 'edd_hs_greedy_listening', 0 );
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		// maybe run upgrade routine
		$this->upgrade_routine();

		if( isset( $_POST['edd_hs_disable_greediness'] ) ) {
			update_option( 'edd_hs_greedy_listening', 0 );
		}

		// show notice if greedy listening is enabled
		add_action( 'admin_notices', array( $this, 'show_notice_about_greedy_listening' ) );

	}

	/**
	 * Upgrade routine, only runs when needed
	 */
	private function upgrade_routine() {

		$db_version = get_option( 'edd_hs_version', 0 );

		// only run if db version is lower than actual code version
		if( ! version_compare( $db_version, EDD_HS::VERSION, '<' ) ) {
			return false;
		}

		update_option( 'edd_hs_version', EDD_HS::VERSION );
	}

	/**
	 * Shows a notice telling the user they should upgrade their HelpScout App URL
	 */
	public function show_notice_about_greedy_listening() {

		// only show notice when greedy listening is enabled
		if( ! get_option( 'edd_hs_greedy_listening', 1 ) ) {
			return false;
		}

		?>
		<div class="update-nag">
			<p><?php printf( __( 'The EDD HelpScout plugin is greedy and currently listening to all frontend requests. You should update your HelpScout App Url to %s and disable the greediness.', 'edd-helpscout' ), '<code>' . site_url( '/edd-helpscout-api/customer-data.json' ) . '</code>' ); ?></p>
			<form action="<?php echo admin_url(); ?>" method="post">
				<input type="submit" class="button" value="I updated my HelpScout App URL" />
				<input type="hidden" name="edd_hs_disable_greediness" value="1" />
			</form>
		</div>
		<?php
	}

}