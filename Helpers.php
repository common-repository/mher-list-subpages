<?php

namespace mher\listSubpages;
use function delete_option;

class Helpers {
	/**
	 * @param string $html
	 *
	 * @return string
	 */
	static function remove_html_comments( string $html ): string {
		return preg_replace( '/<!--(.|\s)*?-->/', '', $html );
	}

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	static function remove_scripts( string $html ): string {
		return preg_replace( '/<script(.|\s)*?>(.|\s)*?<\/script>/i', '', $html );
	}

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	static function remove_events( string $html ): string {
		return preg_replace( '/\s+on\w+\s*=\s*(["\'])(.|\s)*?\1/i', '', $html );
	}

	/**
	 * @param string|null $list
	 *
	 * @return array
	 */
	static function csv_list_to_int_array( string|null $list ): array {
		if ( is_null( $list ) ) {
			return [];
		}

		$values = explode( ',', $list );
		array_walk( $values, function ( &$item, $key ) {
			$item = intval( $item );
		} );

		return $values;
	}

	/**
	 * @param string|null $list
	 *
	 * @return string|null
	 */
	static function normalize_csv_list( string|null $list, bool $unique = false ): string|null {
		if ( is_null( $list ) ) {
			return null;
		}
		$array = self::csv_list_to_int_array( $list );

		if ( $unique ) {
			$array = array_unique( $array );
		}

		return implode( ',', $array );
	}

	static function validate_image_size( string $image_size ): string {

		if ( in_array( $image_size, self::get_all_images_sizes_names() ) ) {
			return $image_size;
		}

		return 'thumbnail';
	}

	/**
	 * Get all the registered image sizes along with their dimensions
	 *
	 * @return array $image_sizes The image sizes
	 * @link http://core.trac.wordpress.org/ticket/18947 Reference ticket
	 *
	 * @global array $_wp_additional_image_sizes
	 *
	 */
	static function get_all_image_sizes(): array {
		global $_wp_additional_image_sizes;

		$default_image_sizes = get_intermediate_image_sizes();

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ]['width']  = intval( get_option( "{$size}_size_w" ) );
			$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h" ) );
			$image_sizes[ $size ]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
		}

		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		return $image_sizes;
	}

	static function get_all_images_sizes_names(): array {
		return array_keys( self::get_all_image_sizes() );
	}


	/**
	 * @return void
	 */
	static function remove_plugin_options(): void {
		delete_option( 'mher_list_subpages_options' );
	}
}