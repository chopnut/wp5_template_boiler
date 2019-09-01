<?php
function theme_setup()
{
  // NodeJS Build
  wp_enqueue_script('lib-js', get_template_directory_uri() . "/dist/js/bundle.js", array('wp-element'), null, true);
  wp_enqueue_style('lib-css', get_template_directory_uri() . "/dist/css/style.css?" . time(), array(), null, false);

  // Custom JS/CSS for non-node build
  wp_enqueue_style('custom-css', get_template_directory_uri() . "/css/custom.css?" . time(), array(), null, false);
  wp_enqueue_script('custom-js', get_template_directory_uri() . "/js/custom.js?" . time(), array(), null, true);

  // Inject local js
  wp_localize_script('custom-js', 'websiteData', array(
    'is_search' => is_search(),
  ));
}
add_action('wp_enqueue_scripts', 'theme_setup');
