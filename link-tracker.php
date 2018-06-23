<?php
/*
* Plugin Name: Inbound links
* Version: 0.0.1
* Description: A tool for tracking inbound links to your website
* Author: lktbl
* Author URI: https://lktbl.eu
* Text Domain: inbound-traffic
*/

if (!defined('ABSPATH')) exit;

// Adding plugin menupoint to dashboard
add_action('admin_menu', function(){
  add_submenu_page('options-general.php', 'Inbound links', 'Inbound links', 'manage_options', 'inbound-links', 'inbound_links_admin_page');
});

// Adding plugin settings page
function inbound_links_admin_page(){
  if ( !current_user_can( 'manage_options' ))  {
    wp_die( __( 'You do not have permissions to access this page.' ) );
  }

  include("inc/admin-page.php");
}

// Create data in database for plugin
add_action( 'admin_init', function() {
  register_setting( 'inbound-links-settings', 'inbound-links-get-parameter' );
});

add_action('wp_head', function(){
  // Get plugin settings
  $get_parameter = esc_attr(get_option('inbound-links-get-parameter'));

  if(isset($_GET[$get_parameter])){
    global $wp;
    $get_parameter_value = sanitize_text_field($_GET[$get_parameter]);
    $current_url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
    $current_user = is_user_logged_in() ? wp_get_current_user()->user_email : "unknown";
    $current_time = date("Y.m.d H:i:s");

    

  }

});
