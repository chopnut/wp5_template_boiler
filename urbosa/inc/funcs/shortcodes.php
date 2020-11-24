<?php 

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

add_action( 'admin_init', 'wpse_136058_debug_admin_menu' );

function wpse_136058_debug_admin_menu() {
  global $submenu;
  // unset($submenu['#urbosa_resources'][0]);
    // echo '<pre>' . print_r($submenu['#urbosa_resources']) . '</pre>';
    // remove_submenu_page('#urbosa_resources','#urbosa_resources');
    // exit;
}



?>