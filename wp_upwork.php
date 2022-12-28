<?php
/*
Plugin Name: Upwork Account Info
Description: A plugin to display information about your Upwork account.
Version: 1.0
Author: Walled Mahmoud Soliman
Author URI: https://github.com/WalledMahmoud/
*/

function get_upwork_account_info() {
  $api_key = 'YOUR_API_KEY';
  $response = wp_remote_get( "https://www.upwork.com/api/v3/me.json", array(
    'headers' => array(
      'Authorization' => "Bearer $api_key"
    )
  ) );
  if ( is_wp_error( $response ) ) {
    return $response;
  }
  $body = json_decode( $response['body'], true );
  return $body['profile'];
}


function upwork_info_shortcode() {
  $info = get_upwork_account_info();
  $output = '<div class="upwork-info">';
  $output .= '<p>Name: ' . $info['name'] . '</p>';
  $output .= '<p>Skills: ' . implode( ', ', $info['skills'] ) . '</p>';
  $output .= '</div>';
  return $output;
}
add_shortcode( 'upwork_info', 'upwork_info_shortcode' );


function upwork_info_settings_init() {
  add_settings_section(
    'upwork_info_section',
    'Upwork API Settings',
    'upwork_info_section_cb',
    'upwork_info'
  );
  add_settings_field(
    'upwork_api_key',
    'API Key',
    'upwork_api_key_render',
    'upwork_info',
    'upwork_info_section'
  );
  register_setting( 'upwork_info', 'upwork_api_key' );
}
add_action( 'admin_init', 'upwork_info_settings_init' );


function upwork_info_section_cb() {
  echo 'Enter your Upwork API key to use the plugin:';
}
function upwork_api_key_render() {
  $value = get_option( 'upwork_api_key', '' );
  echo '<input type="text" name="upwork_api_key" value="' . esc_attr( $value ) . '">';
}


function upwork_info_add_admin_menu() {
  add_options_page(
    'Upwork Info Settings',
    'Upwork Info',
    'manage_options',
    'upwork_info',
    'upwork_info_options_page'
  );
}
add_action( 'admin_menu', 'upwork_info_add_admin_menu' );


function upwork_info_options_page() {
  ?>
  <form action="options.php" method="post">
    <h2>Upwork Info Settings</h2>
    <?php
    settings_fields( 'upwork_info' );
    do_settings_sections( 'upwork_info' );
    submit_button();
    ?>
  </form>
  <?php
}
