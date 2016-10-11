<?php
/*
Plugin Name: Multiple Roles
Description: Allow users to have multiple roles on one site.
Version: 1.1
Author: Michael Dance, Florian Tiar
Author URI: http://mikedance.com
Text Domain: multiple-roles
*/

define( 'MDMR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MDMR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load files and add hooks to get things rolling.
 */
function md_multiple_roles() {

	require_once( MDMR_PATH . 'model.php' );
	require_once( MDMR_PATH . 'controllers/checklist.php' );
	require_once( MDMR_PATH . 'controllers/column.php' );

	$model = new MDMR_Model();

	$checklist = new MDMR_Checklist_Controller( $model );
	add_action( 'admin_enqueue_scripts', array( $checklist, 'remove_dropdown' ) );
	add_action( 'show_user_profile',     array( $checklist, 'output_checklist' ) );
	add_action( 'edit_user_profile',     array( $checklist, 'output_checklist' ) );
	add_action( 'user_new_form',         array( $checklist, 'output_checklist' ) );
	add_action( 'profile_update',        array( $checklist, 'process_checklist' ) );

	// For new user form (in Backoffice)
	// In multisite, user_register hook is too early so wp_mu_activate_user add user role after
	if ( is_multisite() ) {
		add_action( 'wpmu_activate_user',    array( $checklist, 'process_checklist' ) ); // Handle Multisite
	} else {
		add_action( 'user_register',         array( $checklist, 'process_checklist' ) );
	}

	$column = new MDMR_Column_Controller( $model );
	add_filter( 'manage_users_columns',       array( $column, 'replace_column' ), 11 );
	add_filter( 'manage_users_custom_column', array( $column, 'output_column_content' ), 10, 3 );

    add_action( 'init', function() {
        load_plugin_textdomain( 'multiple-roles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    } );
}

md_multiple_roles();