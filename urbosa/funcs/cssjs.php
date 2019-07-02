<?php
function theme_setup(){
  wp_enqueue_script('lib-js', get_template_directory_uri() . "/js/bundle.js", array(), null, true);
  wp_enqueue_style('custom-css', get_template_directory_uri() . "/css/custom.css", array(), null, false);
  wp_enqueue_script('custom-js', get_template_directory_uri() . "/js/custom.js?".time(), array(), null, true);
  
  // Inject local js
  wp_localize_script('custom-js', 'websiteData', array(
    'is_search' => is_search(),
  ));
}
add_action('wp_enqueue_scripts', 'theme_setup');

