<?php

if (!defined('ABSPATH')) exit;

// Get plugin settings
$get_parameter_value = esc_attr(get_option('inbound-links-get-parameter'));
$ignore_repeating_value = get_option('inbound-links-ignore-repeating');

$current_tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'general';

function page_tabs( $current = 'first' ) { // This function prints ot the tabs for the plugin settings page
  $tabs = array(
    'general' => __( 'Inbound links', 'inbound-links' ),
    'stats' => __('Stats', 'inbound-links'),
    'logs' => __( 'Logs', 'inbound-links' )
  );
  $html = '<h2 class="nav-tab-wrapper">';
  foreach( $tabs as $tab => $name ){
    $class = ( $tab == $current ) ? 'nav-tab-active' : '';
    $html .= '<a class="nav-tab ' . $class . '" href="?page=inbound-links&tab=' . $tab . '">' . $name . '</a>';
  }
  $html .= '</h2>';
  echo $html;
}

page_tabs( $current_tab );

if ( $current_tab == 'general' ) {
  ?>
  <div class="wrap">
    <h1>General settings</h1>
    <p>This plugin will log every incoming traffic wich has '<?php echo $get_parameter_value ? $get_parameter_value : "source" ?>' GET parameter in it's link.</p>
    <p>For example: <?php echo home_url('', '') ?>/index.php?<?php echo $get_parameter_value ? $get_parameter_value : "source" ?>=facebook</p>

    <form action="options.php" method="post">

      <?php
      settings_fields( 'inbound-links-settings' );
      do_settings_sections('inbound-links-settings');
      ?>

      <table class="form-table">

        <tr valign="top">
          <th scope="row">GET parameter to track</th>
          <td>
            <input type="text" class="regular-text" name="inbound-links-get-parameter" value="<?php echo $get_parameter_value ? $get_parameter_value : "source" ?>"/>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row">Ignore repeating requests</th>
          <td>
              <label><input name="inbound-links-ignore-repeating" type="radio" value="1" <?php echo $ignore_repeating_value ? 'checked' : 'unchecked' ?>> Enabled</label>&nbsp;&nbsp;
              <label><input name="inbound-links-ignore-repeating" type="radio" value="0" <?php echo (!$ignore_repeating_value || $ignore_repeating_value == '') ? 'checked' : 'unchecked' ?>> Disabled</label></p>
              <p class="description">If this otion is enabled, the plugin will ignore identical requests from users for 5 minutes.</p>
          </td>
        </tr>

      </table>
      <?php submit_button(); ?>
      </form>
    </div>
  <?php
}
elseif( $current_tab == 'logs' ){
  global $wpdb;
  $parameter_to_list = sanitize_text_field($_GET['parameter']);
  ?>
  <div class="wrap">
    <h1>Logs</h1>
    <br>
    <span>Parameter: &nbsp;</span>
    <select id="select_parameter">
      <option id="all" selected="selected">all</option>
      <?php
        $exists_parameter = false;
        $result = $wpdb->get_results ( "SELECT DISTINCT parameter FROM wp_inboundlinks" );
          foreach ( $result as $value ){
            if ($parameter_to_list == $value->parameter) {
              $exists_parameter = true;
              echo "<option id='$value->parameter' selected='selected'>$value->parameter</option>";
            }
            else echo "<option id='$value->parameter'>$value->parameter</option>";
          }
      ?>
    </select>
    <br><br>
    <table class="pure-table pure-table-bordered striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Parameter</th>
          <th>Value</th>
          <th>URL</th>
          <th>User Email</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
      <?php
        if ($parameter_to_list && $exists_parameter) {
          $result = $wpdb->get_results ( "SELECT * FROM wp_inboundlinks WHERE parameter='$parameter_to_list' ORDER BY id DESC" );
        }
        else $result = $wpdb->get_results ( "SELECT * FROM wp_inboundlinks ORDER BY id DESC" );
        foreach ( $result as $value )   {
        ?>
          <tr>
          <td><?php echo $value->id;?></td>
          <td><?php echo $value->parameter;?></td>
          <td><?php echo $value->value;?></td>
          <td><?php echo $value->url;?></td>
          <td><?php echo $value->email;?></td>
          <td><?php echo $value->time;?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <?php
}
elseif($current_tab == 'stats'){
  global $wpdb;
  $parameter_to_list = sanitize_text_field($_GET['parameter']);
  ?>
    <div class="wrap" id="inboundlinks_stats">
      <h1>Stats</h1>
      <br>
      <span>Parameter: &nbsp;</span>
      <select id="select_parameter">
        <?php
          $exists_parameter = false;
          $result = $wpdb->get_results ( "SELECT DISTINCT parameter FROM wp_inboundlinks" );
            foreach ( $result as $value ){
              if ($parameter_to_list == $value->parameter) {
                $exists_parameter = true;
                echo "<option id='$value->parameter' selected='selected'>$value->parameter</option>";
              }
              else echo "<option id='$value->parameter'>$value->parameter</option>";
            }
        ?>
      </select>
      <br><br>
      <div class="chart_container">
        <canvas id="general_stats" width="400" height="400"></canvas>
      </div>
      <div class="stats_table">
        <table class="pure-table pure-table-bordered striped">
          <thead>
            <tr>
              <th>Value</th>
              <th>Number of requests</th>
            </tr>
          </thead>
          <tbody id="stats_table">

          </tbody>
        </table>
      </div>
    </div>
  <?php
}
?>
