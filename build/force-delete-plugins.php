<?php
/*
Plugin Name: Force Delete Plugins
Plugin URI:  https://wordpress.org/plugins/force-delete-plugins/
Description: Changes the default behavior of the bulk delete plugin actions so it deletes plugins regardless whether they are active or not. Helpful for site developers.
Version:     1.0.3
Author:      Jan Beck
Author URI:  http://jancbeck.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: force-delete-plugins
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 *  Hooks into WordPress
 */
add_action( 'current_screen', function() {

	$user_can   = current_user_can( 'activate_plugins');
	$action     = _get_list_table('WP_Plugins_List_Table')->current_action();
	$plugins    = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	$referer    = wp_verify_nonce( isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : null, 'bulk-plugins' );
	
	$can_force_delete_plugins = can_force_delete_plugins( $user_can, $referer, $action, $plugins );
	
	if ( ! is_wp_error( $can_force_delete_plugins ) ) {
		deactivate_plugins( $plugins, false, is_network_admin() );
	}

} );

/**
 *  Checks if user tried to deactivate plugins
 *
 *  @param   bool  $user_can
 *  @param   bool  $referer
 *  @param   string  $action
 *  @param   array  $plugins
 *
 *  @return  bool|WP_Error
 */
function can_force_delete_plugins( $user_can, $referer, $action, $plugins = array() ) {

	if ( ! $user_can ) {
		return new WP_Error( 'user_can', 'You are not allowed to deactivate plugins.', $user_can );
	}

	if ( ! $referer ) {
		return new WP_Error( 'referer', 'Invalid request.', $referer );
	}

	if ( 'delete-selected' !== $action ) {
		return new WP_Error( 'action', 'Invalid action: ', $action );
	}

	if ( empty( $plugins )) {
		return new WP_Error( 'plugins', 'No plugins to deactivate.', $plugins );
	}

	return true;
}