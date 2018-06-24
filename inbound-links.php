<?php
/*
* Plugin Name: Inbound links
* Version: 0.0.1
* Description: A tool for tracking inbound links to your website
* Author: lktbl
* Author URI: https://lktbl.eu
* Text Domain: inbound-links
*/

if (!defined('ABSPATH')) exit;

// Adding plugin menupoint to dashboard
add_action('admin_menu', function(){
  add_menu_page('Inbound links', 'Inbound links', 'manage_options', 'inbound-links', 'inbound_links_admin_page');
});

// Adding plugin settings page
function inbound_links_admin_page(){
  if ( !current_user_can( 'manage_options' ))  {
    wp_die( __( 'You do not have permissions to access this page.' ) );
  }

  include("inc/admin-page.php");
}

// Create data in database for plugin
add_action( 'admin_init', function(){
  register_setting( 'inbound-links-settings', 'inbound-links-get-parameter' );
  register_setting( 'inbound-links-settings', '  inbound-links-ignore-repeating' );
});

add_action('wp_head', function(){
  // Get plugin settings
  $get_parameter = esc_attr(get_option('inbound-links-get-parameter')) ? esc_attr(get_option('inbound-links-get-parameter')) : 'source' ;
  $ignore_repeating_value = get_option('inbound-links-ignore-repeating');

  if(isset($_GET[$get_parameter]) && $_GET[$get_parameter] != ''){
    global $wp;
    global $wpdb;
    $get_parameter_value = sanitize_text_field($_GET[$get_parameter]);
    $current_url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
    $current_user_email = is_user_logged_in() ? wp_get_current_user()->user_email : "unknown";
    $current_time = date("Y.m.d H:i:s");

    $table_name = $wpdb->prefix . 'inboundlinks';


     if(!$ignore_repeating_value || ($_COOKIE['inboundlinks'] != $get_parameter.':'.$get_parameter_value)){
       wp_enqueue_script('link-tracker-cookie-script', plugins_url('js/cookie_script.js', __FILE__));
       wp_localize_script( 'link-tracker-cookie-script', 'inboundlinks', array( 'parameter' => $get_parameter, 'value' => $get_parameter_value ));

       // Save data to db
       $wpdb->insert(
         $table_name,
         array(
           'parameter' => $get_parameter,
           'value' => $get_parameter_value,
           'time' => $current_time,
           'url' => $current_url,
           'email' => $current_user_email
         )
       );
    }
  }

});

add_action('admin_head', function(){
  wp_enqueue_style('link-tracker-admin-style', plugins_url('css/admin_style.css', __FILE__));
  wp_enqueue_style('link-tracker-table-style', plugins_url('css/table_style.css', __FILE__));
  wp_enqueue_script('link-tracker-admin-script', plugins_url('js/admin_script.js', __FILE__), array('jquery'));
  wp_enqueue_script('link-tracker-chart-script', plugins_url('js/Chart.min.js', __FILE__), array('jquery'));

  wp_localize_script( 'link-tracker-admin-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
});

// Add database to WP if the plugin is activated
register_activation_hook( __FILE__, 'inbound_links_create_db' );
function inbound_links_create_db(){
  global $wpdb;

	$table_name = $wpdb->prefix.'inboundlinks';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id int NOT NULL AUTO_INCREMENT,
    parameter tinytext NOT NULL,
    value tinytext NOT NULL,
		time datetime DEFAULT '0000.00.00 00:00:00' NOT NULL,
		url text DEFAULT '' NOT NULL,
    email tinytext NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

// Adding listeners for ajax requests
add_action('wp_ajax_inboundlinks_get_data', 'inboundlinks_serve_data');
function inboundlinks_serve_data(){
  global $wpdb;
  $parameter = sanitize_text_field($_POST['parameter']);
  $response = [];

  $result = $wpdb->get_results ( "SELECT DISTINCT value FROM wp_inboundlinks WHERE parameter='$parameter'" );

  foreach ($result as $value) {
    $count = $wpdb->get_results ( "SELECT * FROM wp_inboundlinks WHERE parameter='$parameter' AND value='$value->value'");
    $response[$value->value] = sizeof($count);
  }

  echo json_encode($response, true);
  die();
}
