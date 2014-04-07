<?php
/**
 * Ollie Ford & Co Social Connect.
 *
 * Plugin that uses the OAuth Library to help you connect with Social Network (currently only Twitter) and registers widgets and shortcodes to use on the front * end of your website.
 *
 * @package   Ollie Ford & Co Social Connect
 * @author    Rubén Madila (for Ollie Ford & Co) <ruben@ollieford.co.uk>
 * @license   GPL-2.0+
 * @link      http://www.ollieford.co.uk
 * @copyright 2014 Ollie Ford & Co
 *
 * @wordpress-plugin
 * Plugin Name: Ollie Ford & Co Social Connect
 * Plugin URI: http://www.ollieford.co.uk
 * Description: Plugin that uses the OAuth Library to help you connect with Social Network (currently only Twitter) and registers widgets and shortcodes to use * on the front end of your website.
 * Version: 0.1.0
 * Author: Ruben Madila
 * Author Email: ruben@ollieford.co.uk
 * Text Domain:       of_social_connect
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/OllieFordandCo/ollieford-social-connect/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-of-social-connect.php' );
require_once (plugin_dir_path( __FILE__ ) . 'includes/OAuth/bootstrap.php');

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
register_activation_hook( __FILE__, array( 'OF_Social_Connect', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'OF_Social_Connect', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'OF_Social_Connect', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-of-social-connect-admin.php' );
	add_action( 'plugins_loaded', array( 'of_social_connect_admin', 'get_instance' ) );

}