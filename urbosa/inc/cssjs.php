<?php
function theme_setup()
{
  wp_enqueue_script('lib-js', get_template_directory_uri() . "/assets/dist/js/bundle.js", array('wp-element'), null, true);
  wp_enqueue_script('custom-js', get_template_directory_uri() . "/assets/js/custom.js?" . time(), array(), null, true);
  wp_enqueue_style('lib-css', get_template_directory_uri() . "/assets/dist/css/style.css?" . time(), array(), null, false);
  wp_enqueue_style('custom-css', get_template_directory_uri() . "/assets/css/custom.css?" . time(), array(), null, false);

  // Inject local js
  wp_localize_script('custom-js', 'websiteData', array(
    'is_search' => is_search(),
  ));
}
add_action('wp_enqueue_scripts', 'theme_setup');

function load_admin_style(){
  wp_enqueue_script('jquery');
  wp_enqueue_style('admin-lib-css', get_template_directory_uri() . "/assets/lib/semantic-ui/semantic.min.css?" . time(), array(), null, false);
  wp_enqueue_style('admin-template-css', get_template_directory_uri() . "/assets/dist/css/template.css?" . time(), array(), null, false);

  // Block specifics
  wp_enqueue_style('admin-blocks-css', get_template_directory_uri() . "/assets/dist/css/blocks.css?" . time(), array(), null, false);
  wp_enqueue_script('admin-blocks-js', get_template_directory_uri() . "/assets/js/blocks.js?" . time(), array(), null, true);

  // Inject local js
  wp_localize_script('admin-blocks-js', 'websiteData', array(
    'is_admin' => is_admin(),
  ));

}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
