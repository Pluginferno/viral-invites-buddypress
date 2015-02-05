<?php 
/*
	Plugin Name: Viral Invites BuddyPress 
	Plugin URI: http://pluginferno.com/products/viral-invites/
	Description: BuddyPress integration for the Viral Invites plugin.
	Author: Pluginferno
	Version: 1.0
	Author URI: http://pluginferno.com/
 */

//registration form

function viralinvites_buddypress_register_form()
{
	echo '<label for="signup_invitation_code">Invitation Code</label>';
	do_action( 'bp_signup_invitation_code_errors' );
	echo '<input type="text" name="signup_invitation_code" id="invitation_code" value="'. viralinvites_signup_invitation_code_value() .'">';
}
add_action('bp_account_details_fields', 'viralinvites_buddypress_register_form');

function viralinvites_signup_invitation_code_value() {
	$value = '';
	if ( isset( $_POST['signup_invitation_code'] ) )
		$value = $_POST['signup_invitation_code'];

	return apply_filters( 'signup_invitation_code', $value );
}

//end registration form

//validate, save, update register form
function viralinvites_buddypress_check_fields() {

	global $bp;
	
	$invitation_code = $_POST['signup_invitation_code'];

	if ( empty( $invitation_code ) ) {
		$bp->signup->errors['signup_invitation_code'] = "Please enter invitation code";
	}
	
	if( !empty( $invitation_code ) ) {
		//check if code valid or not used
		global $wpdb;
		$table_name = $wpdb->prefix . "viralinvites";			
		$data = $wpdb->get_results( "SELECT * FROM $table_name where invite_code='$invitation_code' and used=0" );	
			
		if( count( $data ) != 1 ) {
		
			// Invalid code
			$bp->signup->errors['signup_invitation_code'] = "This invite code is invalid.";
		
		}
		
		if ( empty( $bp->signup->errors ) ) {
		
			// If valid code, and no other errors, mark as used.
			$wpdb->update( $table_name, array( 'used' => 1 ), array( 'invite_code' => $invitation_code ) );
		
		}
		//end check
	}
	
}
add_action('bp_signup_validate', 'viralinvites_buddypress_check_fields');
//end validation

