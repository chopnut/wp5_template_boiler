<?php
/*
Plugin Name: Urbosa Custom Block
Description: Custom block for Urbosa template.
Author: Danny Danting
Text Domain: urbosa-cb
version: 1.0
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}



// =========================================
// Loading all scripts/styles
function urbosa_custom_blocks()
{
  wp_register_script(
    'urbosa_custom_blocks',
    plugins_url('dist/blocks.build.js', __FILE__),
    array('wp-blocks', 'wp-element', 'wp-data', 'wp-editor', 'wp-i18n')
  );
  // Both front-end and admin
  wp_register_style(
    'urbosa_custom_blocks_front',
    plugins_url('dist/blocks.style.build.css',  __FILE__),
    array()
  );
  // Admin only
  wp_register_style(
    'urbosa_custom_editor_css',
    plugins_url('dist/blocks.editor.build.css',  __FILE__),
    array()
  );

  require_once('src/Blocks/blocks.php');
}
add_action('init', 'urbosa_custom_blocks');

// This create a category for the custom-block
function urbosa_block_category($categories, $post)
{
  return array_merge(
    $categories,
    array(
      array(
        'slug' => 'urbosa-blocks',
        'title' => 'Urbosa Blocks',
      ),
    )
  );
}
add_filter('block_categories', 'urbosa_block_category', 10, 2);
