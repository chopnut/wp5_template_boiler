<?php

//===============================================
if (!function_exists('widget_theme_feature')) {
  function widget_theme_feature()
  {
    // Adding a widget area
    register_sidebar(
      array(
        'name' => 'Footer section',
        'id' => 'footer_section',
      )
    );
     // This will enable excerpt on a page type.
    add_post_type_support( 'page', 'excerpt' );
  }
  add_action('widgets_init', 'widget_theme_feature');
}

//===============================================
function urbosa_theme_setup()
{
  add_theme_support('align-wide');
  add_theme_support('post-thumbnails'); // enable feature image
  add_theme_support('title-tag' );
  add_theme_support('woocommerce' );

  $logoDefaults = array(
    'height'      => 100,
    'width'       => 400,
    'flex-height' => true,
    'flex-width'  => true,
    
    );
  add_theme_support( 'custom-logo', $logoDefaults );
  add_image_size( 'urbosa_size', 2000, 2000 );
  add_feature('progressive');
  register_nav_menu('main', __('My main menu'));
}
add_action('after_setup_theme', 'urbosa_theme_setup');
function urbosa_menu_by_location($themeLocation = 'main', $containerClass=''){
  wp_nav_menu(array('theme_location' => $themeLocation,'container_class'=>$containerClass)); 
}

/* Add all shortcodes here */
function add_theme_shortcodes(){
  add_shortcode( 'the_social_icons', 'theme_social_icons' );
}

function theme_social_icons($att){

  ob_start();
  $socials = get_field('socials', 'options');
  $iconClass = isset($att['icon'])?$att['icon']:'';
  $ulClass   = isset($att['class'])?$att['class']:'';
  $gap   = isset($att['gap'])?$att['gap']:'';

  $styleGap = '';
  if(!empty($gap)){
    $styleGap = "padding: 0 $gap;";
  }

  if(!empty($socials)){
    ?>
    <ul class="the_social_icons <?=$ulClass?>">
      <?php
         foreach ($socials as $social ) {
            $link = $social['social_link'];
            $iconImg = $social['icon'];
            $class = $social['class'];
           ?>
           <li>
            <a href="<?=$link?>" style="<?=$styleGap?>">
              <?php 
                if(!empty($iconImg)){
                  echo "<img src='$iconImg' class='$class'/>";
                }else{
                  echo "<i class='urbosa-icon $iconClass $class'></i>";
                }
              ?>
            </a>
           </li>
           <?php
         }
      ?>
    </ul>
    <?php

  }
  
  return ob_get_clean();
}