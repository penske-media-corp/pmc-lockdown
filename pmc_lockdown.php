<?php
/*
Plugin Name: PMC Lockdown
Plugin URI: http://engineering.pmc.com/
Description: Ability to enter lockdown mode: force-logout all non-administrators, prevent logins from non-administrators, and disable commenting.
Version: 0.9.5
Author: PMC
Author URI: http://engineering.pmc.com/
*/

/**
 * Internationalization stub
 *
 * @todo Needs language files
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 */
define('PMC_LOCKDOWN_I18N', 'pmc-lockdown');

/**
 * Figure out whether we're in lockdown mode or not.  'muplugins_loaded' is the
 * first action available, and it runs before authentication checks.
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 */
function pmc_lockdown_init() {
	if ( get_option('pmc_lockdown', false) ) {
		define('PMC_LOCKDOWN', true);
	}
}
add_action( 'muplugins_loaded','pmc_lockdown_init' );

/**
 * Prevent comments while we're on lockdown
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 */
function pmc_lockdown_close_comments( $open ) {
	if ( defined('PMC_LOCKDOWN') ) {
		return false;
	}

	return $open;
}
add_filter( 'comments_open', 'pmc_lockdown_close_comments', 99 );


/**
 * Show admin notice
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 */
function pmc_lockdown_admin_notice() {
	if ( defined('PMC_LOCKDOWN') ) {
		?><div id="message" class="updated fade">
			<p><?php printf( __('Site is on maintenance lockdown.  <a href="%s">Settings</a>', PMC_LOCKDOWN_I18N), admin_url('options-privacy.php') ); ?></p>
		</div><?php
	}
}
add_action( 'admin_notices', 'pmc_lockdown_admin_notice' );


/**
 * Only allow administrators to log in whilst in lockdown mode.  Hooking into
 * the athenticate filter so that we can return an error message during login.
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.5 2012-02-04 Daniel Convissor
 */
function pmc_lockdown_authentication( $user_data, $username ) {
	// If we're not in lockdown mode, bail.
	if ( !defined('PMC_LOCKDOWN') ) {
		return $user_data;
	}

	// If the user is not an administrator, error out.
	if ( !is_a($user_data, 'WP_Error') && 'administrator' !== pmc_lockdown_get_role($user_data, $username) ) {
		$error = new WP_Error();
		$error->add('pmc_maintenance', pmc_lockdown_maintenance_message());
		return $error;
	}

	// User is an administrator, return data as normal.
	return $user_data;
}
add_filter( 'authenticate', 'pmc_lockdown_authentication', 99, 2 );


/**
 * Force logged-in users who are not administrators to log out whilst in
 * lockdown mode.
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.4 2011-10-06 Gabriel Koen
 */
function pmc_lockdown_force_logout( $cookie_elements, $user ) {
	// If we're not in lockdown mode, bail.
	if ( !defined('PMC_LOCKDOWN') || defined('PMC_LOCKDOWN') && !is_admin() ) {
		return;
	}

	// The auth_cookie_valid action is run more than once on an admin page render, the first time $current_user is null, then it's populated by a WP_User object.  So don't do anything unless $current_user is a WP_User object.
	global $current_user;
	if ( !is_null($current_user) && 'administrator' !== pmc_lockdown_get_role() ) {
		wp_redirect(site_url('wp-login.php?reauth'));
		exit();
	}
}
add_action( 'auth_cookie_valid', 'pmc_lockdown_force_logout', 99, 2);


/**
 * Add PMC Lockdown settings to the Privacy page
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 */
function pmc_lockdown_settings() {
	// Only show the lockdown settings for admins

	if ( 'administrator' === pmc_lockdown_get_role() ) {
		add_settings_field('pmc_lockdown',
		__('Maintenance Lockdown', PMC_LOCKDOWN_I18N),
		'pmc_lockdown_setting_field',
		'privacy');

		register_setting( 'privacy', 'pmc_lockdown' );
	}
}
add_action( 'admin_init', 'pmc_lockdown_settings' );


/**
 * Produces the maintenance message above the login form if necessary
 *
 * @since 0.9.5 2012-02-04 Daniel Convissor
 * @version 0.9.5 2012-02-04 Daniel Convissor
 */
function pmc_lockdown_login_message() {
	if ( defined('PMC_LOCKDOWN') ) {
		return '<p class="login message">' . pmc_lockdown_maintenance_message() . '</p>';
	}
}
add_filter( 'login_message', 'pmc_lockdown_login_message' );


/**
 * Gets the (translated) "site is undergoing maintenance" message
 *
 * @since 0.9.5 2012-02-04 Daniel Convissor
 * @version 0.9.5 2012-02-04 Daniel Convissor
 */
function pmc_lockdown_maintenance_message() {
	return __('<strong>The site is undergoing maintenance.</strong>  Please try again later.', PMC_LOCKDOWN_I18N);
}


/**
 * PMC Lockdown settings field
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.0 2011-08-13 Gabriel Koen
 */
function pmc_lockdown_setting_field() {
	echo '<input name="pmc_lockdown" id="pmc_lockdown" type="checkbox" value="1" class="code" ' . checked( 1, get_option('pmc_lockdown', false), false ) . ' /> ' . __('Put site in maintenance lockdown', PMC_LOCKDOWN_I18N);
	_e('<br />&bull; All comments will be closed.', PMC_LOCKDOWN_I18N);
	_e('<br />&bull; Non-administrators will be logged out and will not be able to log back in.', PMC_LOCKDOWN_I18N);
}


/**
 * Get the current user's role
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.2 2011-08-17 Gabriel Koen
 * @version 0.9.3 2011-08-19  William Wallace
 *
 * @param null|obj $not_a_wp_user_object
 * @return string $user_role
 */
function pmc_lockdown_get_role( $not_a_wp_user_object = '', $username = '' ) {
	if ( !empty($not_a_wp_user_object) && is_a($not_a_wp_user_object, 'WP_User') ) {
		$current_user = $not_a_wp_user_object; // is a user object after all
	} else {
		$current_user = wp_get_current_user();
		if ( !($current_user instanceof WP_User) )
			return;
	}

	$roles = $current_user->roles;

	// We should have the user's roles in $user_data now, but sanity check anyway.
	if ( isset($roles) && is_array($roles) ) {
		$user_role = array_shift($roles);
	} else {
		$user_role = '';
	}

	return $user_role;
}

//EOF
