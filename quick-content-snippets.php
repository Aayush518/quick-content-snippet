<?php
/**
 * Plugin Name: Quick Content Snippets
 * Description: A simple WordPress plugin to manage and insert content snippets.
 * Version: 1.0
 * Author: Aayush Adhikari
 */

// Enqueue JavaScript and CSS for the admin interface
function qcs_enqueue_scripts() {
    wp_enqueue_script('qcs-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('qcs-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css', array(), '1.0');
    
    // Enqueue the snippet button block script
    wp_enqueue_script('qcs-snippet-button-block', plugin_dir_url(__FILE__) . 'js/snippet-button-block.js', array('wp-blocks', 'wp-components', 'wp-editor'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'qcs_enqueue_scripts');

// Create the Snippets Custom Post Type
function qcs_register_snippets_post_type() {
    register_post_type('qcs_snippet', array(
        'labels' => array(
            'name' => __('Snippets'),
            'singular_name' => __('Snippet'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor'),
    ));
}
add_action('init', 'qcs_register_snippets_post_type');

// Add a button to the post/page editor to insert snippets
function qcs_add_snippet_button() {
    if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
        add_filter('mce_external_plugins', 'qcs_add_snippet_plugin');
        add_filter('mce_buttons', 'qcs_register_snippet_button');
    }
}
add_action('admin_init', 'qcs_add_snippet_button');

function qcs_add_snippet_plugin($plugin_array) {
    $plugin_array['qcs_button'] = plugin_dir_url(__FILE__) . 'js/admin-script.js';
    return $plugin_array;
}

function qcs_register_snippet_button($buttons) {
    array_push($buttons, 'qcs_button');
    return $buttons;
}

// AJAX handler to fetch snippet options
function qcs_get_snippet_options_callback() {
    $args = array(
        'post_type'      => 'qcs_snippet',
        'posts_per_page' => -1,
    );

    $snippets = get_posts($args);
    $options = array();

    foreach ($snippets as $snippet) {
        $options[] = array(
            'text'  => $snippet->post_title,
            'value' => $snippet->ID,
        );
    }

    wp_send_json($options);
}
add_action('wp_ajax_qcs_get_snippet_options', 'qcs_get_snippet_options_callback');

// Shortcode handler for displaying snippets
function qcs_snippet_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts, 'qcs_snippet');

    $snippet_id = absint($atts['id']);
    $snippet_content = get_post_field('post_content', $snippet_id);

    return $snippet_content;
}
add_shortcode('qcs_snippet', 'qcs_snippet_shortcode');

// Register a custom block for Gutenberg editor
function qcs_register_blocks() {
    // Register the snippet block
    register_block_type('qcs/snippet-block', array(
        'render_callback' => 'qcs_render_snippet_block',
        'attributes'      => array(
            'snippetId' => array(
                'type' => 'number',
            ),
        ),
    ));

    // Register the snippet button block
    register_block_type('qcs/snippet-button-block', array(
        'render_callback' => 'qcs_render_snippet_button_block',
    ));
}
add_action('init', 'qcs_register_blocks');

// Callback function to render the snippet block content
function qcs_render_snippet_block($attributes) {
    $snippet_id = absint($attributes['snippetId']);
    $snippet_content = get_post_field('post_content', $snippet_id);

    return apply_filters('the_content', $snippet_content);
}

// Callback function to render the snippet button block content
function qcs_render_snippet_button_block() {
    return null; // This block is dynamic, and its content is handled on the client side
}

// Enqueue the Block Renderer JavaScript
function qcs_enqueue_block_renderer_script() {
    wp_enqueue_script('qcs-snippet-block-renderer', plugin_dir_url(__FILE__) . 'js/snippet-block-renderer.js', array('wp-blocks', 'wp-components', 'wp-editor'));
}
add_action('enqueue_block_editor_assets', 'qcs_enqueue_block_renderer_script');
