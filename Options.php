<?php
namespace mher\listSubpages;
class Options {
	private static Options|null $instance = null;

	private $options = [];

	/**
	 * fetches the option array for this plugin and stores them for further reference
	 * and registers the function to remove the options when uninstalling the plugin
	 */
	private function __construct() {
		$this->options = \get_option( 'mher_list_subpages_options' );
		\load_plugin_textdomain('mher-list-subpages', false, plugin_basename(dirname(__FILE__)) . '/languages');
		\register_uninstall_hook(__FILE__, 'Helpers::remove_plugin_options');
	}

	/**
	 * Returns the singular instance of this Class
	 *
	 * @return Options
	 */
	public static function getInstance(): Options {
		if ( self::$instance == null ) {
			self::$instance = new Options();
		}

		return self::$instance;
	}

	/**
	 * Returns the named suboption of the options for this plugin
	 *
	 * @param string $optionname
	 *
	 * @return string|int|array|null
	 */
	public function get_option( string $optionname ): string|int|array|null {
		switch ( $optionname ) {
			case 'image-id':
				return $this->get_image_id();
			case 'templates':
				return $this->get_templates();
			default:
				return null;
		}
	}

	/**
	 * Returns the id of the configured fallback image
	 *
	 * @return string|int|null
	 */
	public function get_image_id(): string|int|null {
		$image_id = null;
		if ( isset( $this->options['image-id'] ) ) {
			$image_id = $this->options['image-id'];
		}

		return ( is_null( $image_id ) || $image_id === '' ) ? null : $image_id;
	}

	/**
	 * Returns array of all configured templates
	 *
	 * @return array
	 */
	public function get_templates(): array {
		$templates = [];
		if ( isset( $this->options['templates'] ) ) {
			$templates = $this->options['templates'];
		}

		return $templates;
	}

	/**
	 * Returns the - by id or name - identified template
	 *
	 * @param string $templateidentifier
	 *
	 * @return array|null
	 */
	public function get_template( string|null $templateidentifier ): array|null {
		$templates = $this->get_templates();
		foreach ( $templates as $key => $template ) {
			if ( strval( $key ) === $templateidentifier || $template['name'] === $templateidentifier ) {
				return $template;
			}
		}

		return null;
	}

	/**
	 * Returns the next free id for a new template
	 *
	 * @return int
	 */
	public function get_next_template_key(): int {
		$templates = $this->get_templates();
		if ( count( $templates ) ) {
			return max( array_keys( $templates ) ) + 1;
		}

		return 1;
	}
}