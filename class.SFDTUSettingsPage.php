<?php

/**
 * Creating a plugin settings page.
 */
class SFDTUSettingsPage {
	/**
	 * Settings fields.
	 */
	private $options;

	/**
	 * Adds a settings page, creates custom fields
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page_sfdtu' ) );
		add_action( 'admin_init', array( $this, 'page_init_sfdtu' ) );

		add_filter( 'plugin_action_links_' . SFDTU_PLUGIN_BASENAME, array( $this, 'plugin_settings_link' ) );
	}

	/**
	 * Created link option page.
	 */
	function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . SFDTU_ADMIN_PAGE . '">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Create options page in the settings menu of the main menu of the admin panel.
	 */
	public function add_plugin_page_sfdtu() {
		add_submenu_page(
			'options-general.php',
			'Sending form data to url',
			'SFDTU',
			'administrator',
			SFDTU_ADMIN_PAGE,
			array( $this, 'create_submenu_page_sfdtu' )
		);
	}

	/**
	 * Created html page structure options.
	 */
	public function create_submenu_page_sfdtu() {
		$this->options = get_option( SFDTU_OPTION );
		?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title(); ?></h1>
            <form method="post" action="options.php">
                <p><b><?php _e('To use the form, you must insert the shortcode [sfdtu] on the page!', SFDTU_TEXT_DOMAIN); ?></b></p>
				<?php
				settings_fields( SFDTU_OPTION_GROUP );
				do_settings_sections( SFDTU_ADMIN_PAGE );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Created groups and settings page fields.
	 */
	public function page_init_sfdtu() {
		register_setting(
			SFDTU_OPTION_GROUP,
			SFDTU_OPTION
		);

		add_settings_section(
			SFDTU_SETTINGS,
			'',
			null,
			SFDTU_ADMIN_PAGE
		);

		add_settings_field(
			'url',
			__( 'URL', SFDTU_TEXT_DOMAIN ),
			array( $this, 'url_callback_sfdtu' ),
			SFDTU_ADMIN_PAGE,
			SFDTU_SETTINGS
		);

		add_settings_field(
			'title',
			__( 'Title form', SFDTU_TEXT_DOMAIN ),
			array( $this, 'title_callback_sfdtu' ),
			SFDTU_ADMIN_PAGE,
			SFDTU_SETTINGS
		);

		add_settings_field(
			'successful_text',
			__( 'Successful text', SFDTU_TEXT_DOMAIN ),
			array( $this, 'successful_text_callback_sfdtu' ),
			SFDTU_ADMIN_PAGE,
			SFDTU_SETTINGS
		);
	}

	/**
	 * HTML 'url' display fields.
	 */
	public function url_callback_sfdtu () {
		isset( $this->options['url'] ) ? $value = esc_attr( $this->options['url'] ) : $value = '';
		echo '<input type="text" id="url" name="' . SFDTU_OPTION . '[url]" value="' . $value . '" size="100">';
	}

	/**
	 * HTML 'title' display fields.
	 */
	public function title_callback_sfdtu () {
		isset( $this->options['title'] ) ? $value = esc_attr( $this->options['title'] ) : $value = __( 'Sending data to URL', SFDTU_TEXT_DOMAIN );
		echo '<input type="text" id="title" name="' . SFDTU_OPTION . '[title]" value="' . $value . '" size="100">';
	}

	/**
	 * HTML 'successful_text' display fields.
	 */
	public function successful_text_callback_sfdtu () {
		isset( $this->options['successful_text'] ) ? $value = esc_attr( $this->options['successful_text'] ) : $value = __( 'Successfully sent!', SFDTU_TEXT_DOMAIN );
		echo '<input type="text" id="successful_text" name="' . SFDTU_OPTION . '[successful_text]" value="' . $value . '" size="100">';
	}
}