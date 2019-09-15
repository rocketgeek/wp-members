<?php
/**
 * WP-Members API Functions
 * 
 * This file is part of the WP-Members plugin by Chad Butler
 * You can find out more about this plugin at https://rocketgeek.com
 * Copyright (c) 2006-2019  Chad Butler
 * WP-Members(tm) is a trademark of butlerblog.com
 *
 * @package    WP-Members
 * @subpackage WP-Members API Functions
 * @author     Chad Butler 
 * @copyright  2006-2019
 */

if ( ! function_exists( 'wpmem_login_form' ) ):
/**
 * Invokes a login form.
 *
 * Note: The original pluggable version of this function used a $page param
 * and an array. This function should (1) no longer be considered pluggable
 * and (2) should pass all arguments in the form if a single array. The previous
 * methods are maintained for legacy reasons, but should be updated to apply
 * to the current function documentation.
 *
 * @since 2.5.1
 * @since 3.1.7 Now a wrapper for $wpmem->forms->login_form()
 * @since 3.3.0 Added to API.
 *
 * @global object $wpmem
 * @param  array  $args {
 *     Possible arguments for creating the form.
 *
 *     @type string id
 *     @type string tag
 *     @type string form
 *     @type string redirect_to
 * }
 * @param  array  $arr {
 *     Maintained only for legacy reasons.
 *     The elements needed to generate the form (login|reset password|forgotten password).
 *
 *     @type string $heading     Form heading text.
 *     @type string $action      The form action (login|pwdchange|pwdreset).
 *     @type string $button_text Form submit button text.
 *     @type array  $inputs {
 *         The form input values.
 *
 *         @type array {
 *
 *             @type string $name  The field label.
 *             @type string $type  Input type.
 *             @type string $tag   Input tag name.
 *             @type string $class Input tag class.
 *             @type string $div   Div wrapper class.
 *         }
 *     }
 *     @type string $redirect_to Optional. URL to redirect to.
 * }
 * @return string $form  The HTML for the form as a string.
 */
function wpmem_login_form( $args, $arr = false ) {
	global $wpmem;
	// Convert legacy values.
	if ( ! is_array( $args ) && is_array( $arr ) ) {
		$page = $args;
		$args = $arr;
		$args['page'] = $page;
	}
	return $wpmem->forms->login_form( $args );
}
endif;

/**
 * Invokes a registration or user profile update form.
 *
 * @since 3.2.0
 *
 * @global object $wpmem
 * @param  array  $args {
 *     Possible arguments for creating the form.
 *
 *     @type string id
 *     @type string tag
 *     @type string form
 *     @type string product
 *     @type string include_fields
 *     @type string exclude_fields
 *     @type string redirect_to
 *     @type string heading
 * }
 * @return string $html
 */
function wpmem_register_form( $args ) {
  global $wpmem;
  return $wpmem->forms->register_form( $args );
}

/**
 * Change Password Form.
 *
 * @since 3.3.0 Replaces wpmem_inc_changepassword().
 * @since 3.3.0 Added $action argument.
 *
 * @global stdClass $wpmem   The WP_Members object.
 *
 * @param  string   $action  Determine if it is password change or reset.
 * @return string   $str     The generated html for the change password form.
 */
function wpmem_change_password_form( $form = 'pwdchange' ) {
	global $wpmem;
	return $wpmem->forms->do_changepassword_form( $form );
}

/**
 * Reset Password Form.
 *
 * @since 3.3.0 Replaced wpmem_inc_resetpassword().
 *
 * @global object $wpmem The WP_Members object.
 * @return string $str   The generated html fo the reset password form.
 */
function wpmem_reset_password_form() { 
	global $wpmem;
	return $wpmem->forms->do_resetpassword_form();
}

/**
 * Forgot Username Form.
 *
 * @since 3.3.0 Replaced wpmem_inc_forgotusername().
 *
 * @global object $wpmem The WP_Members object class.
 * @return string $str   The generated html for the forgot username form.
 */
function wpmem_forgot_username_form() {
	global $wpmem;
	return $wpmem->forms->do_forgotusername_form();
}

/**
 * Wrapper for $wpmem->create_form_field().
 *
 * @since 3.1.2
 * @since 3.2.0 Accepts wpmem_create_formfield() arguments.
 *
 * @global object $wpmem    The WP_Members object class.
 * @param string|array  $args {
 *     @type string  $name        (required) The field meta key.
 *     @type string  $type        (required) The field HTML type (url, email, image, file, checkbox, text, textarea, password, hidden, select, multiselect, multicheckbox, radio).
 *     @type string  $value       (optional) The field's value (can be a null value).
 *     @type string  $compare     (optional) Compare value.
 *     @type string  $class       (optional) Class identifier for the field.
 *     @type boolean $required    (optional) If a value is required default: true).
 *     @type string  $delimiter   (optional) The field delimiter (pipe or comma, default: | ).
 *     @type string  $placeholder (optional) Defines the placeholder attribute.
 *     @type string  $pattern     (optional) Adds a regex pattern to the field (HTML5).
 *     @type string  $title       (optional) Defines the title attribute.
 *     @type string  $min         (optional) Adds a min attribute (HTML5).
 *     @type string  $max         (optional) Adds a max attribute (HTML5).
 *     @type string  $rows        (optional) Adds rows attribute to textarea.
 *     @type string  $cols        (optional) Adds cols attribute to textarea.
 * }
 * @param  string $type     The field type.
 * @param  string $value    The default value for the field.
 * @param  string $valtochk Optional for comparing the default value of the field.
 * @param  string $class    Optional for setting a specific CSS class for the field.
 * @return string           The HTML of the form field.
 */
//function wpmem_form_field( $args ) {
function wpmem_form_field( $name, $type=null, $value=null, $valtochk=null, $class='textbox' ) {
	global $wpmem;
	if ( is_array( $name ) ) {
		$args = $name;
	} else {
		$args = array(
			'name'     => $name,
			'type'     => $type,
			'value'    => $value,
			'compare'  => $valtochk,
			'class'    => $class,
		);
	}
	return $wpmem->forms->create_form_field( $args );
}

/**
 * Wrapper for $wpmem->create_form_label().
 *
 * @since 3.1.7
 *
 * @global object $wpmem
 * @param array  $args {
 *     @type string $meta_key
 *     @type string $label
 *     @type string $type
 *     @type string $id         (optional)
 *     @type string $class      (optional)
 *     @type string $required   (optional)
 *     @type string $req_mark   (optional)
 * }
 * @return string The HTML of the form label.
 */
function wpmem_form_label( $args ) {
	global $wpmem;
	return $wpmem->forms->create_form_label( $args );
}

/**
 * Wrapper to get form fields.
 *
 * @since 3.1.1
 * @since 3.1.5 Checks if fields array is set or empty before returning.
 * @since 3.1.7 Added wpmem_form_fields filter.
 *
 * @global object $wpmem  The WP_Members object.
 * @param  string $tag    The action being used (default: null).
 * @param  string $form   The form being generated.
 * @return array  $fields The form fields.
 */
function wpmem_fields( $tag = '', $form = 'default' ) {
	global $wpmem;
	// Load fields if none are loaded.
	if ( ! isset( $wpmem->fields ) || empty( $wpmem->fields ) ) {
		$wpmem->load_fields( $form );
	}
	
	// @todo Review for removal.
	$tag = $wpmem->convert_tag( $tag );
	
	/**
	 * Filters the fields array.
	 *
	 * @since 3.1.7
	 *
	 * @param  array  $wpmem->fields
	 * @param  string $tag (optional)
	 */
	return apply_filters( 'wpmem_fields', $wpmem->fields, $tag );
}

/**
 * Sanitizes classes passed to the WP-Members form building functions.
 *
 * This generally uses just sanitize_html_class() but allows for 
 * whitespace so multiple classes can be passed (such as "regular-text code").
 * This is an API wrapper for WP_Members_Forms::sanitize_class().
 *
 * @since 3.2.9
 *
 * @global  object $wpmem
 *
 * @param	string $class
 * @return	string sanitized_class
 */
function wpmem_sanitize_class( $class ) {
	global $wpmem;
	return $wpmem->forms->sanitize_class( $class );
}

/**
 * Sanitizes the text in an array.
 *
 * This is an API wrapper for WP_Members_Forms::sanitize_array().
 *
 * @since 3.2.9
 *
 * @global  object $wpmem
 *
 * @param  array $data
 * @return array $data
 */
function wpmem_sanitize_array( $data ) {
	global $wpmem;
	return $wpmem->forms->sanitize_array( $data );
}

/**
 * A multi use sanitization function.
 *
 * @since 3.3.0
 *
 * @global  object  $wpmem
 *
 * @param   string  $data
 * @param   string  $type
 * @return  string  $sanitized_data
 */
function wpmem_sanitize_field( $data, $type = 'text' ) {
	global $wpmem;
	return $wpmem->forms->sanitize_field( $data, $type );
}