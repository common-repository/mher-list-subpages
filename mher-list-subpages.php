<?php
/**
 * Plugin Name:       mher list Subpages
 * Plugin URI:        https://mher.de/mher-list-subpages
 * Description:       Creates a list of subpages with featured image, title and the content of the blocks named 'teaser' or any other name provided in the shortcode
 * Version:           1.0.1
 * Requires at least: 6.5
 * Tested up to:      6.6
 * Requires PHP:      8.0
 * Author:            Michael H.E. Roth
 * Author URI:        https://mher.de
 * License:           GPL v2 or later
 * Licence URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mher-list-subpages
 * Domain Path:       /languages
 */

/*
"mher_list_subpages" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

"mher_list_subpages" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'MHER_LIST_SUBPAGES_PATH', plugin_dir_path( __FILE__ ) );

include_once( MHER_LIST_SUBPAGES_PATH . 'Helpers.php' );
include_once( MHER_LIST_SUBPAGES_PATH . 'ListSubpages.php' );
include_once( MHER_LIST_SUBPAGES_PATH . 'Options.php' );
include_once( MHER_LIST_SUBPAGES_PATH . 'Settings.php' );

new mher\listSubpages\ListSubpages();
new mher\listSubpages\Settings();