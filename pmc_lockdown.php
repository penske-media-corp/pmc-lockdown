<?php
/**
 * Plugin Name: PMC Lockdown
 * Plugin URI: http://engineering.pmc.com/
 * Description: Ability to enter lockdown mode: force-logout all non-administrators, prevent logins from non-administrators, and disable commenting.
 * Version: 1.0.0
 * Author: PMC
 * Author URI: http://engineering.pmc.com/
 * License: GPL2
 */

/*  Copyright 2014  PMC  (email : contact@pmc.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Adds a plugin settings page link to the plugin description.
 * 
 * @since 2014-06-17 Sachin Kumar
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page='.PMC_LOCKDOWN_I18N.'">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'pmc_lockdown_settings_link' );

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
 * Figure out whether we're in lockdown mode or not.  'plugins_loaded' is the
 * second action available, and it runs before authentication checks.
 * 1.0.0: Compatibility fix for the latest version of WordPress 3.9.1
 *
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 * @version 1.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_init() {
    $options = get_option('pmc_lockdown_settings', false);
    //check if pmc lockdown option is there in wordpress or not, this is very first time of the plugin install
    if(isset($options['pmc_lockdown']))
    {
        //if lockdown entry is available int the options page, then check whether it is enabled? 1=enabled, 0=disbaled
        if(($options['pmc_lockdown'] == 1))
        {
            //define the global value for pmc lockdown check
            define('PMC_LOCKDOWN', true);
        }
    }
}
add_action('plugins_loaded','pmc_lockdown_init');

/**
 * Adds a menu page under the general options section of the wordpress.
 * A new option item will be created with name 'PMC Lockdown' under
 * "Settings" section.
 * 
 * @since 2014-06-17 Sachin Kumar
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_add_admin_menu()
{
    add_options_page( 'PMC Lockdown', 'PMC Lockdown', 'manage_options', PMC_LOCKDOWN_I18N, 'pmc_lockdown_options_page' );
}
add_action('admin_menu', 'pmc_lockdown_add_admin_menu');

/**
 * Adds a plugin settings page link to the plugin description.
 * 
 * @since 1.0.0 2014-06-17 Sachin Kumar
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_settings_exist()
{
    if( false == get_option( 'pmc_lockdown_settings' ) ) { 
        $options = array("pmc_lockdown" => false);
        add_option( 'pmc_lockdown_settings', $options );
    }
}
add_action('admin_init', 'pmc_lockdown_settings_exist');


/**
 * Adds PMC Lockdown settings to its own options page.
 * Settings >> PMC Lockdown
 *
 * Previous method of adding settings into privacy 
 * section is renamed and updated.
 * Previous function name was pmc_lockdown_settings()
 * 
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_settings_init()
{
    register_setting( 'pmc_lockdown_page_group', 'pmc_lockdown_settings', 'pmc_sanitize' );

    add_settings_section(
        'pmc_lockdown_page_section', 
        __( '', PMC_LOCKDOWN_I18N ), 
        '', 
        'pmc_lockdown_page_group'
    );

    add_settings_field( 
        'pmc_lockdown', 
        __( 'Maintenance Lockdown', PMC_LOCKDOWN_I18N ), 
        'pmc_lockdown_render', 
        'pmc_lockdown_page_group', 
        'pmc_lockdown_page_section' 
    );
}
add_action('admin_init', 'pmc_lockdown_settings_init');

/**
 * Sanitize the checkbox value set by the user. 
 * Required: All input(s) should be sanitize before being used
 *
 * If there was nothing defined for the checkbox pmc_lockdown,
 * then set it to zero.
 *
 * @since 1.0.0 2014-06-17 Sachin Kumar
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_sanitize($pmc_lockdown_input_check) {
  if (!isset($pmc_lockdown_input_check['pmc_lockdown'])){
    $pmc_lockdown_input_check['pmc_lockdown'] = 0;
  }
  return $pmc_lockdown_input_check;
}

/**
 * Render the form inside html with PMC Lockdown checkbox
 *
 * @since 1.0.0 2014-06-17 Sachin Kumar
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_render(){
    $options = get_option( 'pmc_lockdown_settings' );
?>
     <label><input type='checkbox' id="pmc_lockdown" name='pmc_lockdown_settings[pmc_lockdown]' <?php if (isset($options['pmc_lockdown'])){ checked( $options['pmc_lockdown'], 1 );} ?> value='1'>Put this site into lockdown mode</label>
<?php
}

/**
 * Generate PMC Lockdown settings page
 *
 * @since 1.0.0 2014-06-17 Sachin Kumar
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_options_page()
{
?>
    <div class="wrap">
        <form action='options.php' method='post'>        
            <h2>PMC Lockdown</h2>
            <p>All comments will be closed. Non-administrators will be logged out and will not be able to log back in.</p>
            <?php
            settings_fields( 'pmc_lockdown_page_group' );
            do_settings_sections( 'pmc_lockdown_page_group' );
            submit_button();
            ?>
        </form>
    </div>
<?php
}

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
 * Show admin notice
 * 1.0.0: Notice message changed prior to previous version.
 * @since 0.9.0 2011-08-13 Gabriel Koen
 * @version 0.9.1 2011-08-16 Gabriel Koen
 * @version 1.0.0 2014-06-17 Sachin Kumar
 */
function pmc_lockdown_admin_notice() {
    if ( defined('PMC_LOCKDOWN') ) {
        ?><div id="message" class="updated fade">
            <p><?php printf( __('Site is running in maintenance lockdown mode. All comments will be closed. Non-administrators will be logged out and will not be able to log back in. <br/>Go to <a href="%s">PMC Lockdown settings</a> page to change.', PMC_LOCKDOWN_I18N), admin_url('options-general.php?page=pmc-lockdown') ); ?></p>
        </div><?php
    }
}
add_action( 'admin_notices', 'pmc_lockdown_admin_notice' );

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
?>