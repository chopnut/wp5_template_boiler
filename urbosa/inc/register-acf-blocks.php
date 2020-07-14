<?php 
/* Register blocks ACF only */
if(function_exists('acf_register_block')){
  //----------------------------------------------
  function register_blocks(){
    $acfBlocksLocation = 'inc/acf/acf-blocks/';
    /* 
      Instruction: Copy the acf_register_block , 1 per each block set the render template.
      Create the template php file in the blocks folder. Icon should be dashicon of wordpress without the 'dashicons'
    */
    
    // 1. Urbosa Panel with options to add images left/right
    acf_register_block(array(
      'name'=> 'cb_content_panel',
      'title'=> __('Theme Panel'),
      'description'=>__('Theme Panel'),
      'render_template'=> $acfBlocksLocation.'cb_content_panel.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'layout',
      'keywords'=> array( 'Theme Panel' ),
    ));
    // 2. Urbosa Theme Slider
    acf_register_block(array(
      'name'=> 'cb_theme_slider',
      'title'=> __('Theme Slider'),
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
      'title'=> __('Theme Google Map'),
      'description'=>__('Theme Google Map'),
      'render_template'=> $acfBlocksLocation.'cb_google_map.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'grid-view',
      'keywords'=> array( 'Theme Google Map' ),
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

?>
