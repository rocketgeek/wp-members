<?php
/**
 * WP-Members User API Functions
 * 
 * This file is part of the WP-Members plugin by Chad Butler
 * You can find out more about this plugin at https://rocketgeek.com
 * Copyright (c) 2006-2019  Chad Butler
 * WP-Members(tm) is a trademark of butlerblog.com
 *
 * @package WP-Members
 * @subpackage WP-Members API Functions
 * @author Chad Butler 
 * @copyright 2006-2019
 */

/**
 * Checks if a user exists.
 *
 * @since 3.2.5
 *
 * @param $user_id
 * @return boolean
 */
function wpmem_is_user( $user_id ) {
	$user = get_userdata( $user_id );
	return ( $user ) ? true : false;
}

/**
 * Returns the current user's current role.
 *
 * Note that users may have more than one role. This returns
 * whatever the internal pointer is set to. Usually, this will
 * be the first element in the array, but not always.
 * @see: https://www.php.net/manual/en/function.current.php
 *
 * @since 3.3.0
 *
 * @param  int     $user_id
 * @return mixed   If the user is set and has roles, the current user role, otherwise false.
 */
function wpmem_get_user_role( $user_id = false ) {
	$user = ( $user_id ) ? get_userdata( $user_id ) : wp_get_current_user();
	return ( $user ) ? current( $user->roles ) : false;
}

/**
 * Checks if user has a particular role.
 *
 * Utility function to check if a given user has a specific role. Users can
 * have multiple roles assigned, so it checks the role array rather than using
 * the incorrect method of current_user_can( 'role_name' ). The function can
 * check the role of the current user (default) or a specific user (if $user_id
 * is passed).
 *
 * @since 3.1.1
 * @since 3.1.6 Include accepting an array of roles to check.
 * @since 3.1.9 Return false if user is not logged in.
 * @since 3.2.0 Change return false to not logged in AND no user id.
 *
 * @global object        $current_user Current user object.
 * @global object        $wpmem        WP_Members object.
 * @param  string|array  $role         Slug or array of slugs of the role being checked.
 * @param  int           $user_id      ID of the user being checked (optional).
 * @return boolean       $has_role     True if user has the role, otherwise false.
 */
function wpmem_user_has_role( $role, $user_id = false ) {
	if ( ! is_user_logged_in() && ! $user_id ) {
		return false;
	}
	global $current_user, $wpmem;
	$has_role = false;
	if ( $user_id ) {
		$user = get_userdata( $user_id );
	} else {
		$user = ( isset( $current_user ) ) ? $current_user : wp_get_current_user();
	}
	if ( is_array( $role ) ) {
		foreach ( $role as $r ) {
			if ( in_array( $r, $user->roles ) ) {
				return true;
			}
		}
	} else {
		return ( in_array( $role, $user->roles ) ) ? true : $has_role;
	}
}

/**
 * Checks if a user has a given meta value.
 *
 * @since 3.1.8
 * @since 3.3.0 Added wpmem_user_has_meta filter.
 * @since 3.3.0 Added array check for multi-value fields (multicheckbox and multiselect).
 *
 * @global object  $wpmem     WP_Members object.
 *
 * @param  string  $meta      Meta key being checked.
 * @param  string  $value     Value the meta key should have (optional).
 * @param  int     $user_id   ID of the user being checked (optional).
 * @return boolean $has_meta  True if user has the meta value, otherwise false.
 */
function wpmem_user_has_meta( $meta, $value = false, $user_id = false ) {

	global $wpmem;
	
	// Get the user ID.
	$user_id = ( $user_id ) ? $user_id : get_current_user_id();
	
	// Get field type.
	$fields = wpmem_fields();
	$multi  = ( 'multicheckbox' == $fields[ $meta ]['type'] || 'multiselect' == $fields[ $meta ]['type'] ) ? true : false;
	
	// Get meta.
	$has_meta   = false;
	$user_value = get_user_meta( $user_id, $meta, true );
	
	// Check meta.
	if ( $value ) {
		if ( $multi ) {
			// Check array of values.
			$user_value = explode( $fields[ $meta ]['delimiter'], $user_value );
			$has_meta = ( in_array( $value, $user_value ) ) ? true : $has_meta;
		} else {
			// Straight comparison.
			$has_meta = ( $user_value == $value ) ? true : $has_meta;
		}
	} else {
		// Check if the user has any meta value (regardless of actual value).
		$has_meta = ( $user_value ) ? true : $has_meta;
	}
	
	/**
	 * Filter the user has meta result.
	 *
	 * @since 3.3.0
	 *
	 * @param bool   $has_meta   True if the user has the value, otherwise false.
	 * @param int    $user_id    The user ID being checked.
	 * @param string $user_value The user's stored meta value (false if none).
	 */
	return apply_filters( 'wpmem_user_has_meta', $has_meta, $user_id, $user_value );
}

/**
 * Checks if a user is activated.
 *
 * @since 3.1.7
 * @since 3.2.3 Now a wrapper for WP_Members_Users::is_user_activated().
 *
 * @global object $wpmem
 * @param  int    $user_id
 * @return bool
 */
function wpmem_is_user_activated( $user_id = false ) {
	global $wpmem;
	return $wpmem->user->is_user_activated( $user_id );
}

/**
 * Gets an array of the user's registration data.
 *
 * Returns an array keyed by meta keys of the user's registration data for
 * all fields in the WP-Members Fields.  Returns the current user unless
 * a user ID is specified.
 *
 * @since 3.2.0
 *
 * @global object  $wpmem
 * @param  integer $user_id
 * @param  bool    $all
 * @return array   $user_fields
 */
function wpmem_user_data( $user_id = false, $all = false ) {
	global $wpmem;
	return $wpmem->user->user_data( $user_id, $all );
}

/**
 * Updates a user's role.
 *
 * This is a wrapper for $wpmem->update_user_role(). It can add a role to a
 * user, change or remove the user's role. If no action is specified it will
 * change the role.
 *
 * @since 3.2.0
 *
 * @global object  $wpmem
 * @param  integer $user_id (required)
 * @param  string  $role    (required)
 * @param  string  $action  (optional add|remove|set default:set)
 */
function wpmem_update_user_role( $user_id, $role, $action = 'set' ) {
	global $wpmem;
	$wpmem->user->update_user_role( $user_id, $role, $action );
}

/**
 * A function for checking user access criteria.
 *
 * @since 3.2.0
 * @since 3.2.3 Reversed order of arguments.
 *
 * @param  mixed   $product 
 * @param  integer $user_id User ID (optional|default: false).
 * @return boolean $access  If user has access.
 */
function wpmem_user_has_access( $product, $user_id = false ) {
	global $wpmem; 
	return $wpmem->user->has_access( $product, $user_id );
}

/**
 * Sets product access for a user.
 *
 * @since 3.2.3
 * @since 3.2.6 Added $date to set a specific expiration date.
 *
 * @global object $wpmem
 * @param  string $product The meta key of the product.
 * @param  int    $user_id
 * @param  string $date    Expiration date (optional) format: MySQL timestamp
 * @return bool   $result
 */
function wpmem_set_user_product( $product, $user_id = false, $date = false ) {
	global $wpmem;
	return $wpmem->user->set_user_product( $product, $user_id, $date );
}

/**
 * Removes product access for a user.
 *
 * @since 3.2.3
 *
 * @global object $wpmem
 * @param  string $product
 * @param  int    $user_id
 */
function wpmem_remove_user_product( $product, $user_id = false ) {
	global $wpmem;
	$wpmem->user->remove_user_product( $product, $user_id );
	return;
}

/**
 * Sets a user as logged in.
 *
 * @since 3.2.3
 *
 * @global object $wpmem
 * @param  int    $user_id
 */
function wpmem_set_as_logged_in( $user_id ) {
	global $wpmem;
	$wpmem->user->set_as_logged_in( $user_id );
}

if ( ! function_exists( 'wpmem_login' ) ):
/**
 * Logs in the user.
 *
 * Logs in the the user using wp_signon (since 2.5.2). If login is
 * successful, it will set a cookie using wp_set_auth_cookie (since 2.7.7),
 * then it redirects and exits; otherwise "loginfailed" is returned.
 *
 * @since 0.1.0
 * @since 2.5.2 Now uses wp_signon().
 * @since 2.7.7 Sets cookie using wp_set_auth_cookie().
 * @since 3.0.0 Removed wp_set_auth_cookie(), this already happens in wp_signon().
 * @since 3.1.7 Now a wrapper for login() in WP_Members_Users Class.
 * @since 3.2.4 Moved to user API (could be deprecated).
 *
 * @global object $wpmem
 * @return string Returns "loginfailed" if the login fails.
 */
function wpmem_login() {
	global $wpmem;
	return $wpmem->user->login();
} // End of login function.
endif;

if ( ! function_exists( 'wpmem_logout' ) ):
/**
 * Logs the user out then redirects.
 *
 * @since 2.0.0
 * @since 3.1.6 Added wp_destroy_current_session(), removed nocache_headers().
 * @since 3.1.7 Now a wrapper for logout() in WP_Members_Users Class.
 * @since 3.2.4 Moved to user API (could be deprecated).
 *
 * @global object $wpmem
 * @param  string $redirect_to The URL to redirect to at logout.
 */
function wpmem_logout( $redirect_to = false ) {
	global $wpmem;
	$wpmem->user->logout( $redirect_to );
}
endif;

if ( ! function_exists( 'wpmem_change_password' ) ):
/**
 * Handles user password change (not reset).
 *
 * @since 2.1.0
 * @since 3.1.7 Now a wrapper for password_update() in WP_Members_Users Class.
 * @since 3.2.4 Moved to user API (could be deprecated).
 *
 * @global int $user_ID The WordPress user ID.
 *
 * @return string The value for $wpmem->regchk
 */
function wpmem_change_password() {
	global $wpmem;
	return $wpmem->user->password_update( 'change' );
}
endif;

if ( ! function_exists( 'wpmem_reset_password' ) ):
/**
 * Resets a forgotten password.
 *
 * @since 2.1.0
 * @since 3.1.7 Now a wrapper for password_update() in WP_Members_Users Class.
 * @since 3.2.4 Moved to user API (could be deprecated).
 *
 * @global object $wpmem The WP-Members object class.
 *
 * @return string The value for $wpmem->regchk
 */
function wpmem_reset_password() {
	global $wpmem;
	return $wpmem->user->password_update( 'reset' );
}
endif;

/**
 * Handles retrieving a forgotten username.
 *
 * @since 3.0.8
 * @since 3.1.6 Dependencies now loaded by object.
 * @since 3.1.8 Now a wrapper for $wpmem->retrieve_username() in WP_Members_Users Class.
 * @since 3.2.4 Moved to user API (could be deprecated).
 *
 * @global object $wpmem The WP-Members object class.
 *
 * @return string $regchk The regchk value.
 */
function wpmem_retrieve_username() {
	global $wpmem;
	return $wpmem->user->retrieve_username();
}

/**
 * Creates a membership number.
 *
 * @since 3.1.1
 * @since 3.2.0 Changed "lead" to "pad".
 *
 * @param  array  $args {
 *     @type string $option    The wp_options name for the counter setting (required).
 *     @type string $meta_key  The field's meta key (required).
 *     @type int    $start     Number to start with (optional, default 0).
 *     @type int    $increment Number to increment by (optional, default 1).
 *     @type int    $digits    Number of digits for the number (optional).
 *     @type boolen $pad       Pad leading zeros (optional, default true).
 * }
 * @return string $membersip_number
 */
function wpmem_create_membership_number( $args ) {
	global $wpmem;
	return $wpmem->api->generate_membership_number( $args );
}

/**
 * Activates a user.
 *
 * If registration is moderated, sets the activated flag 
 * in the usermeta. Flag prevents login when $wpmem->mod_reg
 * is true (1). Function is fired from bulk user edit or
 * user profile update.
 *
 * @uses  $wpdb WordPress Database object.
 *
 * @since 2.4
 * @since 3.1.6 Dependencies now loaded by object.
 * @since 3.2.4 Renamed from wpmem_a_activate_user().
 * @since 3.3.0 Moved to user API.
 *
 * @param int   $user_id
 */
function wpmem_activate_user( $user_id ) {

	global $wpmem;

	// Define new_pass.
	$new_pass = '';

	// If passwords are user defined skip this.
	if ( ! wpmem_user_sets_password() ) {
		// Generates a password to send the user.
		$new_pass = wp_generate_password();
		$new_hash = wp_hash_password( $new_pass );

		// Update the user with the new password.
		global $wpdb;
		$wpdb->update( $wpdb->users, array( 'user_pass' => $new_hash ), array( 'ID' => $user_id ), array( '%s' ), array( '%d' ) );
	}

	// @todo this should be taken out, use the wpmem_user_activated hook instead.
	// If subscriptions can expire, and the user has no expiration date, set one.
	if ( $wpmem->use_exp == 1 && ! get_user_meta( $user_id, 'expires', true ) ) {
		if ( function_exists( 'wpmem_set_exp' ) ) {
			wpmem_set_exp( $user_id );
		}
	}

	// Generate and send user approved email to user.
	wpmem_email_to_user( $user_id, $new_pass, 2 );

	// Set the active flag in usermeta.
	update_user_meta( $user_id, 'active', 1 );

	/**
	 * Fires after the user activation process is complete.
	 *
	 * @since 2.8.2
	 *
	 * @param int $user_id The user's ID.
	 */
	do_action( 'wpmem_user_activated', $user_id );

	return;
}

/**
 * Deactivates a user.
 *
 * Reverses the active flag from the activation process
 * preventing login when registration is moderated.
 *
 * @since 2.7.1
 * @since 3.2.4 Renamed from wpmem_a_deactivate_user().
 * @since 3.3.0 Moved to user API.
 *
 * @param int $user_id
 */
function wpmem_deactivate_user( $user_id ) {
	update_user_meta( $user_id, 'active', 0 );

	/**
	 * Fires after the user deactivation process is complete.
	 *
	 * @since 2.9.9
	 *
	 * @param int $user_id The user's ID.
	 */
	do_action( 'wpmem_user_deactivated', $user_id );
}

/**
 * Updates the user_status value in the wp_users table.
 *
 * @since Unknown
 * @since 3.3.0 Moved to User API.
 *
 * @global object $wpdb
 *
 * @param int    $user_id
 * @param string $status
 */
function wpmem_set_user_status( $user_id, $status ) {
	global $wpdb;
	$wpdb->update( $wpdb->users, array( 'user_status' => $status ), array( 'ID' => $user_id ) );
	return;
}

// End of file.