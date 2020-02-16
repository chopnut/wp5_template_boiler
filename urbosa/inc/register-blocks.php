<?php 
function register_blocks(){
  
  if(!function_exists('acf_register_block')){
    return;
  }
  
  // Register each of your blocks here. This will get added to your ACF block
  /* 
    Instruction: Copy the acf_register_block , 1 per each block set the render template.
    Create the template php file in the blocks folder. Icon should be dashicon of wordpress without the 'dashicons'
  */

  // 1. Custom block example
  acf_register_block(array(
    'name'=> 'cb_example',
    'title'=> __('Custom Block Example'),
    'description'=>__('My example block'),
    'render_template'=> 'inc/acf-blocks/cb_example.php',
    'category'=> 'urbosa-blocks',
    'icon'	=> 'layout',
    'keywords'=> array( 'Example' ),
  ));
}
add_action('acf/init', 'register_blocks');

function theme_category_block($categories,$post){
  return array_merge($categories, array(
    array(
      'slug'=>'urbosa-blocks',
      'title'=>__('Theme','urbosa-blocks')
    )
    ));
}
add_filter('block_categories','theme_category_block',10,2);
?>
