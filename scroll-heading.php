<?php
/*
Plugin Name: Scroll Heading PRO
Description: A plugin that makes a box stick to the top of the page when the user scrolls past it and allows for text input to be displayed in the box.
Version: 1.0
Author: Yasunori Abe
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require('delete-box-function.php');

// Enqueue scripts and styles
function scrollheading_scripts() {
wp_enqueue_style( 'scrollheading-style', plugin_dir_url( __FILE__ ) . 'scrollheading.css' );
wp_enqueue_script( 'scrollheading-script', plugin_dir_url( __FILE__ ) . 'scrollheading.js', array('jquery'), '1.0', true );
$background_color = get_option( 'scrollheading_background_color' );
$color = get_option( 'scrollheading_color' );
wp_localize_script( 'scrollheading-script', 'scrollheading_vars', array(
'background_color' => $background_color,
'color' => $color,
) );
}
add_action( 'wp_enqueue_scripts', 'scrollheading_scripts' );

// Add shortcode
function scrollheading_shortcode($atts, $content = null) {
    $atts = shortcode_atts( array(
    'class' => '',
    'id' => '',
    ), $atts, 'scrollheading' );
    $scrollheading_text = get_option( 'scrollheading_text_' . $atts['id'] );
    return '<div class="scrollheading-target ' . $atts['class'] . '" data-scrollheading-id="' . $atts['id'] . '">' . do_shortcode($content) . '<div class="scrollheading-box">' . $scrollheading_text . '</div></div>';
}
add_shortcode( 'lilseed', 'scrollheading_shortcode' );

// Add menu item
function scrollheading_menu() {
    add_menu_page( 'Box management menu', 'Box management menu', 'manage_options', 'scrollheading-menu', 'scrollheading_menu_page', '', 6 );
    add_submenu_page( 'scrollheading-menu', 'Box color', 'Box color', 'manage_options', 'scrollheading-menu-color', 'scrollheading_menu_page_color' );
    add_submenu_page( 'scrollheading-menu', 'Box setting', 'Box setting', 'manage_options', 'scrollheading-menu-sub', 'scrollheading_menu_page_sub' );
    add_submenu_page( 'scrollheading-menu', 'Box list', 'Box list', 'manage_options', 'scrollheading-menu-list', 'scrollheading_menu_page_list' );
}
add_action( 'admin_menu', 'scrollheading_menu' );

function scrollheading_menu_page() {
    echo 'Box management menu details';
}

function scrollheading_register_settings() {

register_setting( 'scrollheading-settings-group-color', 'scrollheading_background_color' );
register_setting( 'scrollheading-settings-group-color', 'scrollheading_color' );

}
add_action( 'admin_init', 'scrollheading_register_settings' );

function scrollheading_menu_page_color() {
    wp_enqueue_style( 'scrollheading-style', plugin_dir_url( __FILE__ ) . 'scrollheading.css' );
    $background_color = get_option( 'scrollheading_background_color' );
    $color = get_option( 'scrollheading_color' );
    echo '<form method="post" action="options.php">';

    settings_fields( 'scrollheading-settings-group-color' );
    do_settings_sections( 'scrollheading-settings-group-color' );

    echo '<div class="background-color-label">Background Color:</div>';
    echo '<div class="background-color-box">';
    echo '<input type="text" id="scrollheading-input-admin-background-color" name="scrollheading_background_color" value="' . $background_color . '"></input>';
    echo '</div>';
    echo '<div class="color-label">Color:</div>';
    echo '<div class="color-box">';
    echo '<input type="text" id="scrollheading-input-admin-color" name="scrollheading_color" value="' . $color . '"></input>';
    echo '</div>';
    submit_button();
    echo '</form>';
}

function scrollheading_menu_page_sub() {
    wp_enqueue_style( 'scrollheading-style', plugin_dir_url( __FILE__ ) . 'scrollheading.css' );
    echo '<form method="post" action="options.php">';
    settings_fields( 'scrollheading-settings-group' );
    do_settings_sections( 'scrollheading-settings-group' );
    echo '<div class="id-box">';
    echo '<label for="scrollheading-input-admin-id">ID:</label>';
    echo '<input type="text" id="scrollheading-input-admin-id" name="scrollheading_id"></input>';
    echo '</div>';
    echo '<div class="text-box">';
    echo '<label for="scrollheading-input-admin-text">Text:</label>';
    echo '<input type="text" id="scrollheading-input-admin-text" name="scrollheading_text"></input>';
echo '</div>';
submit_button();
echo '</div>';
echo '</form>';
}

function scrollheading_menu_page_list() {
    global $wpdb;
    $scrollheading_texts = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'scrollheading_text_%'");
    echo '<table>';
    echo '<tr><th>ID</th><th>Text</th><th>Action</th></tr>';
    foreach($scrollheading_texts as $key => $scrollheading_text) {
        echo '<tr>';
        echo '<td>' . str_replace('scrollheading_text_', '', $scrollheading_text->option_name) . '</td>';
        echo '<td>' . $scrollheading_text->option_value . '</td>';
        echo '<td>';
        echo '<form method="post" action="options.php">';
        settings_fields( 'scrollheading-settings-group' );
        echo '<input type="hidden" name="scrollheading_delete_id" value="' . str_replace('scrollheading_text_', '', $scrollheading_text->option_name) . '"></input>';
        submit_button('Delete');
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    unset($scrollheading_texts[$key]);
}

// Register settings
function scrollheading_settings() {
register_setting( 'scrollheading-settings-group', 'scrollheading_id' );
register_setting( 'scrollheading-settings-group', 'scrollheading_text' );
}
add_action( 'admin_init', 'scrollheading_settings' );

function scrollheading_settings_init() {
    register_setting( 'scrollheading-settings-group', 'scrollheading_id' );
    register_setting( 'scrollheading-settings-group', 'scrollheading_text' );
    register_setting( 'scrollheading-settings-group', 'scrollheading_delete_id', 'delete_scrollheading_data' );
}

add_action( 'admin_init', 'scrollheading_settings_init' );
// Update the box text with the text entered in the dashboard
function scrollheading_update_box() {
$scrollheading_id = get_option( 'scrollheading_id' );
$scrollheading_text = get_option( 'scrollheading_text' );
update_option( 'scrollheading_text_' . $scrollheading_id, $scrollheading_text );
}
add_action( 'admin_init', 'scrollheading_update_box' );

?>