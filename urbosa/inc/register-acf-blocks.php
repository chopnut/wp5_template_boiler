<?php 
/* Register blocks ACF only */
if(function_exists('acf_register_block')){
  //----------------------------------------------
  function register_blocks(){
    /* 
      Instruction: Copy the acf_register_block , 1 per each block set the render template.
      Create the template php file in the blocks folder. Icon should be dashicon of wordpress without the 'dashicons'
    */
    // 1. Urbosa Panel with options to add images left/right
    acf_register_block(array(
      'name'=> 'cb_content_panel',
      'title'=> __('Theme Panel'),
      'description'=>__('Theme Panel'),
      'render_template'=> 'inc/acf-blocks/cb_content_panel.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'layout',
      'keywords'=> array( 'Theme Panel' ),
    ));
    // 2. Urbosa Theme Slider
    acf_register_block(array(
      'name'=> 'cb_theme_slider',
      'title'=> __('Theme Slider'),
      'description'=>__('Theme Slider'),
      'render_template'=> 'inc/acf-blocks/cb_theme_slider.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'image-flip-horizontal',
      'keywords'=> array( 'Theme Slider' ),
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
