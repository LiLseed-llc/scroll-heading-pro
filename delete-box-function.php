<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function delete_scrollheading_data($id) {
global $wpdb;
$wpdb->delete( $wpdb->options, array( 'option_name' => 'scrollheading_text_' . $id ) );
// exclude background and text colors options
if ($id != 'scrollheading_background_color' && $id != 'scrollheading_color' && $id != 'scrollheading_text_' . $id) {
    $wpdb->delete( $wpdb->options, array( 'option_name' => $id ) );
}
}
?>