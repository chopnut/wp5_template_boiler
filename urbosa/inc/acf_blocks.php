<?php 
/* Register blocks ACF only */
if(function_exists('acf_register_block')){
  //----------------------------------------------
  function register_blocks(){
    $acfBlocksLocation = 'inc/acf/blocks/';
    /* 
      Instruction: Copy the acf_register_block , 1 per each block set the render template.
      Create the template php file in the blocks folder. Icon should be dashicon of wordpress without the 'dashicons'
    */
    
    // 1. Urbosa Panel with options to add images left/right
    acf_register_block(array(
      'name'=> 'cb_content_panel',
      'title'=> __('Panel'),
      'description'=>__('Theme Panel'),
      'render_template'=> $acfBlocksLocation.'cb_content_panel.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'layout',
      'keywords'=> array( 'Theme Panel' ),
    ));
    // 2. Urbosa Theme Slider
    acf_register_block(array(
      'name'=> 'cb_theme_slider',
      'title'=> __('Slider'),
      'description'=>__('Theme Slider'),
      'render_template'=> $acfBlocksLocation.'cb_theme_slider.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'image-flip-horizontal',
      'keywords'=> array( 'Theme Slider' ),
    ));
    // 3. Repeater posts and CTA
    acf_register_block(array(
      'name'=> 'cb_repeater_posts',
      'title'=> __('Repeater Posts'),
      'description'=>__('Repeater Posts'),
      'render_template'=> $acfBlocksLocation.'cb_repeater_posts.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'grid-view',
      'keywords'=> array( 'Repeater Posts' ),
    ));
    // 4. ACF Google Map
    acf_register_block(array(
      'name'=> 'cb_google_map',
      'title'=> __('Google Map'),
      'description'=>__('Theme Google Map'),
      'render_template'=> $acfBlocksLocation.'cb_google_map.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'location',
      'keywords'=> array( 'Theme Google Map' ),
    ));
    // 5. Accordion
    acf_register_block(array(
      'name'=> 'cb_accordion',
      'title'=> __('Accordion'),
      'description'=>__('Theme Accordion'),
      'render_template'=> $acfBlocksLocation.'cb_accordion.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'list-view',
      'keywords'=> array( 'Theme Accordion' ),
    ));
    // 6. Search Result
    acf_register_block(array(
      'name'=> 'cb_search_results',
      'title'=> __('Seach Results'),
      'description'=>__('Theme Seach Results'),
      'render_template'=> $acfBlocksLocation.'cb_search_results.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'search',
      'keywords'=> array( 'Theme Seach Results' ),
    ));
  }

  add_action('acf/init', 'register_blocks');
  //----------------------------------------------
  function theme_category_block($categories,$post){
    return array_merge($categories, array(
      array(
        'slug'=>'urbosa-blocks',
        'title'=>__('Theme','urbosa-blocks')
      )
      ));
  }
  add_filter('block_categories','theme_category_block',10,2);
}



/* helper blocks */
function cb_search_results($data){
  if(isset($_POST['data'])){
    $data = json_decode(stripslashes($_POST['data']),true);

    // Prep variables

    $initialPage = $data['initial_page'];
    $page      = $data['page'];
    $postTypes = $data['post']['search_post_types'];
    $template  = $data['post']['template'];
    $perPage   = $data['per_page'];

    // Build query

    
    echo '<script>foundPosts=0</script>'; // always return overall total posts found
  }
  exit;
}
add_action('wp_ajax_cb_search_results', 'cb_search_results');
add_action('wp_ajax_nopriv_cb_search_results', 'cb_search_results');//for users that are not logged in.

?>