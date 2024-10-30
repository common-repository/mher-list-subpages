<?php
namespace mher\listSubpages;
class Settings {

	private Options $options;

	/**
	 *
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'options_page' ] );
		add_action( 'admin_init', [ $this, 'setup_sections' ] );
		add_action( 'admin_init', [ $this, 'setup_fields' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		$this->options = Options::getInstance();
	}

	/**
	 * Adding the settings page to the WP menu
	 *
	 * @return void
	 */
	public function options_page(): void {
		add_menu_page( 'mher list subpages', 'mher list Subpages Options', 'manage_options', 'mher_list_subpages', [
				$this,
				'options_page_html',
			]
		// icon
		// position
		);
	}

	/**
	 * Displays the settings page
	 *
	 * @return void
	 */
	public function options_page_html(): void {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( wp_verify_nonce( 'mher_list_subpages-options' ) && isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'mher_list_subpages_messages', 'mher_list_subpages_message', esc_html__( 'Settings Saved', 'mher-list-subpages' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'mher-list-subpag_messages' );

		?>
    <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
        <?php
		// output security fields for the registered setting "mher_list_subpages"
		settings_fields( 'mher_list_subpages' );
		// output setting sections and their fields
		// (sections are registered for "mher_list_subpages", each field is registered to a specific section)
		do_settings_sections( 'mher_list_subpages' );
		// output save settings button
		submit_button( __( 'Save Settings', 'mher-list-subpages' ) );
		?>
    </form>
    </div>
        <?php
	}

	/**
	 * Setting up the sections
	 *
	 * @return void
	 */
	public function setup_sections(): void {
		add_settings_section( 'mher_list_subpages_fallback_image_section', esc_html__( 'Select a fallback image.', 'mher-list-subpages' ), [
				$this,
				'section_callback',
			], 'mher_list_subpages' );
		add_settings_section( 'mher_list_subpages_templates_section', esc_html__( 'Define your own templates', 'mher-list-subpages' ), [
				$this,
				'section_callback',
			], 'mher_list_subpages' );
	}

	/**
	 * @param array $arguments
	 *
	 * @return void
	 */
	public function section_callback( array $arguments ): void {
		switch ( $arguments['id'] ) {
			case 'mher_list_subpages_fallback_image_section':
				?>
                <p><?php esc_html_e( 'Here you can define your own default fallback image', 'mher-list-subpages' ); ?></p>
                <p><?php esc_html_e( 'You can override this in the shortcode, i.e ', 'mher-list-subpages' ); ?><code>[mher_subpages
                        image_id=123]</code><br>
					<?php esc_html_e( '(if a featured image is present for the subpage this still takes precedence)', 'mher-list-subpages' ); ?>
                </p>
				<?php
				break;
			case 'mher_list_subpages_templates_section':
				?>
                <p>
					<?php esc_html_e( 'Here you can define your own templates for the listings', 'mher-list-subpages' ); ?>
                    <br>
					<?php esc_html_e( 'You can use the following placeholders for data from the subpages in the template for the subpages', 'mher-list-subpages' ); ?>
                    <br>
                    <code>{{ title }}</code> <?php esc_html_e( 'for the subpage\'s title', 'mher-list-subpages' ); ?>
                    <br>
                    <code>{{ url }}</code> <?php esc_html_e( 'for the subpage\'s url', 'mher-list-subpages' ); ?><br>
                    <code>{{ img
                        }}</code> <?php esc_html_e( 'for the complete html for the  subpage\'s image', 'mher-list-subpages' ); ?>
                    <br>
                    <code>{{ img_url
                        }}</code> <?php esc_html_e( 'for the subpage\'s image url', 'mher-list-subpages' ); ?><br>
                    <code>{{ blocks
                        }}</code> <?php esc_html_e( 'for the subpage\'s blocks you selected via their block name', 'mher-list-subpages' ); ?>
                    <br>
                </p>
                <p><?php esc_html_e( 'You can select the template in the shortcode by name or by number. I.e. ', 'mher-list-subpages' ); ?>
                    <code>[mher_subpages template=myTemplate]</code></p>
				<?php
				break;
		}
	}

	/**
	 * @return void
	 */
	public function setup_fields(): void {
		add_settings_field( 'fallback_image_field', // As of WP 4.6 this value is used only internally.
			// Use $args' label_for to populate the id inside the callback.
			esc_html__( 'Fallback image', 'mher-list-subpages' ), [
				$this,
				'fallback_image_field_callback',
			], 'mher_list_subpages', 'mher_list_subpages_fallback_image_section', );

		add_settings_field( 'templates_fields', esc_html__( 'Templates', 'mher-list-subpages' ), [
				$this,
				'templates_fields_callback',
			], 'mher_list_subpages', 'mher_list_subpages_templates_section', );

		register_setting( 'mher_list_subpages', 'mher_list_subpages_options' );
	}

	/**
	 * Display the field to set the fallback image
	 *
	 * @param array $arguments
	 *
	 * @return void
	 */
	public function fallback_image_field_callback( array $arguments ): void {
		/* Image */
		$image_id = $this->options->get_image_id();

		?>
        <div class="mher-list-subpages-image-picker">
            <label><?php esc_html_e( 'Select Image:', 'mher-list-subpages' ); ?></label><br>
            <input type="hidden" id="mher-list-subpages-image-id" name="mher_list_subpages_options[image-id]"
                   value="<?php echo esc_html( $image_id ); ?>"/>
            <button type="button" class="button mher-list-subpages-media-button"
                    style="margin:5px 0 10px 0"><?php esc_html_e( 'Use Media Library', 'mher-list-subpages' ); ?></button>
            <br>
            <img id="mher-list-subpages-image-preview"
                 src="<?php echo esc_url( wp_get_attachment_image_url( get_option( 'image-id' ), 'thumbnail' ) ); ?>"
                 alt=""/>
        </div>
        <div style="margin-top: 1em;">
            <p style="font-weight: bold"><?php esc_html_e( 'currently selected:', 'mher-list-subpages' ); ?></p>
            <div style="position: relative; width: 150px; height: 150px; margin:0; display: inline-block;">
				<?php
				if ( $image_id ) {
					?>
                    <img id="mher-list-subpages-image-selected"
                         src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'thumbnail' ) ); ?>" alt=""/>
                    <button id="mher-list-subpages-delete-image"
                            style="color: red; position:absolute; bottom: 0; right: 0"
                            data-fallback-image-url="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>images/fallback-image.webp">
                        <span class="dashicons dashicons-dismiss"></span></button>
					<?php
				} else {
					?>
                    <img id="mher-list-subpages-image-selected"
                         src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ) ?>images/fallback-image.webp" alt=""/>
					<?php
				}
				?>
            </div>
        </div>
		<?php
	}

	/**
	 * Display the fields to define the custom templates
	 *
	 * @param array $arguments
	 *
	 * @return void
	 */
	public function templates_fields_callback( array $arguments ): void {
		$templates = $this->options->get_templates();

		$next_key = $this->options->get_next_template_key();

        $formrow_scaffold = '
        <div class="mher-list-subpages-template" id="mher-list-subpages-template-%d-row" style="margin:3px 0">
        
        <p style="font-weight: bold">' . /* translators: add %d where the number of the template should be included */
		           esc_html__( 'Template No. %d', 'mher-list-subpages' ) . '</p>
        <label for="mher-list-subpages-template-%d-name">' . __( 'Template Name', 'mher-list-subpages' ) . '</label><br>
        <input id="mher-list-subpages-template-%d-name" name="mher_list_subpages_options[templates][%d][name]" type="text" value="" /><br><br>
        <label for="mher-list-subpages-template-%d-templatehead">' . __( 'Template Head', 'mher-list-subpages' ) . '</label><br>
        <textarea id="mher-list-subpages-template-%d-templatehead" name="mher_list_subpages_options[templates][%d][templatehead]" style="resize:both" class="large-text code" rows="4" cols="50"></textarea><br>
        <label for="mher-list-subpages-template-%d-templaterow">' . __( 'Template for subpage elements', 'mher-list-subpages' ) . '</label><br>
        <textarea id="mher-list-subpages-template-%d-templaterow" name="mher_list_subpages_options[templates][%d][templaterow]" style="resize:both" class="large-text code" rows="4" cols="50"></textarea><br>
        <label for="mher-list-subpages-template-%d-templatefoot">' . __( 'Template Foot', 'mher-list-subpages' ) . '</label><br>
        <textarea id="mher-list-subpages-template-%d-templatefoot" name="mher_list_subpages_options[templates][%d][templatefoot]" style="resize:both" class="large-text code" rows="4" cols="50"></textarea><br>
        <button type="button" class="mher-list-subpages-template-delete-button" style="color: rgba(255,0,0,0)"><span class="dashicons dashicons-dismiss"></span></button>
        </div>
        ';
		?>
        <div id="mher-list-subpages-templates">
            <div class="mher-list-subpages-template">
                <button type="button" class="button" style="margin:5px 0 10px 0"
                        id="mher-list-subpages-template-add-button" data-scaffold='<?php echo esc_html( $formrow_scaffold );  ?>' data-next-key="<?php echo esc_attr( $next_key ); ?>"><span
                            class="dashicons dashicons-plus"
                            style="margin-top:5px"></span><?php esc_html_e( 'add tempate', 'mher-list-subpages' ); ?>
                </button>
            </div>
			<?php
			foreach ( $templates as $key => $template ) {
				if ( ! is_array( $template ) ) {
					continue;
				}
                #foreach ( $template as $key => $value ) {echo "$key: $value<br>"; }
                ?>
                <div class="mher-list-subpages-template" id="mher-list-subpages-template-%d-row" style="margin:3px 0">
                    <p style="font-weight: bold"><?php  /* translators: add %d where the number of the template should be included */ echo esc_html(str_replace('%d', $key, __(  'Template No. %d', 'mher-list-subpages' ))); ?></p>
                    <label for="mher-list-subpages-template-<?php echo esc_attr($key); ?>-name"><?php esc_html_e( 'Template Name', 'mher-list-subpages' ) ?></label><br>
                    <input id="mher-list-subpages-template-<?php echo esc_attr($key); ?>-name" name="mher_list_subpages_options[templates][<?php echo esc_attr($key); ?>][name]" type="text" value="<?php echo esc_attr($template['name']); ?>" /><br><br>
                    <label for="mher-list-subpages-template-<?php echo esc_attr($key); ?>-templatehead"><?php esc_html_e( 'Template Head', 'mher-list-subpages' ) ?></label><br>
                    <textarea id="mher-list-subpages-template-<?php echo esc_attr($key); ?>-templatehead" name="mher_list_subpages_options[templates][<?php echo esc_attr($key); ?>][templatehead]" style="resize:both" class="large-text code" rows="4" cols="50"><?php echo esc_textarea($template['templatehead']); ?></textarea><br>
                    <label for="mher-list-subpages-template-<?php echo esc_attr($key); ?>-templaterow"><?php esc_html_e( 'Template for subpage elements', 'mher-list-subpages' ) ?>'</label><br>
                    <textarea id="mher-list-subpages-template-<?php echo esc_attr($key); ?>-templaterow" name="mher_list_subpages_options[templates][<?php echo esc_attr($key); ?>][templaterow]" style="resize:both" class="large-text code" rows="4" cols="50"><?php echo esc_textarea($template['templaterow']); ?></textarea><br>
                    <label for="mher-list-subpages-template-<?php echo esc_attr($key); ?>-templatefoot"><?php esc_html_e( 'Template Foot', 'mher-list-subpages' ) ?></label><br>
                    <textarea id="mher-list-subpages-template-<?php echo esc_attr($key); ?>-templatefoot" name="mher_list_subpages_options[templates][<?php echo esc_attr($key); ?>][templatefoot]" style="resize:both" class="large-text code" rows="4" cols="50"><?php echo esc_textarea($template['templatefoot']); ?></textarea><br>
                    <button type="button" class="mher-list-subpages-template-delete-button" style="color: red"><span class="dashicons dashicons-dismiss"></span></button>
                </div>
                <?php
			}
			?>
        </div>
		<?php
	}

	/**
	 * Enqueue the JS Script
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_script( 'mher-list-subpages-mher-script', plugin_dir_url( __FILE__ ) . 'js/script.js', array( 'jquery' ), '1.0.0', true );
	}
}