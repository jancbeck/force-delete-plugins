<?php
/*
Plugin Name: Force Delete Plugins
Plugin URI:  https://wordpress.org/plugins/force-delete-plugins/
Description: Changes the default behavior of the bulk delete plugin actions so it deletes plugins regardless whether they are active or not. Helpful for site developers.
Version:     1.0.2
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

add_action( 'admin_init', function(){

	if ( ! current_user_can('activate_plugins') )
		wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

	$wp_list_table = _get_list_table('WP_Plugins_List_Table');

	$action = $wp_list_table->current_action();

	if ( 'delete-selected' !== $action ) {
		return;
	}

	check_admin_referer('bulk-plugins');

	$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

	deactivate_plugins( $plugins, false, is_network_admin() );

} );