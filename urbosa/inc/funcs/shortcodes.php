<?php 

/* Add all shortcodes here */
function add_theme_shortcodes(){
  
  add_shortcode( 'the_social_icons', 'theme_social_icons' );
  add_shortcode( 'the_slider', 'theme_the_slider' );

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

function theme_the_slider($att){

  global $scSlider, $block;

  // Prep variables
  $sliderID   = (isset($att['slider'])?$att['slider']:0);
  $className  = (isset($att['class'])?$att['class']:'');
  $id         = (isset($att['id'])?$att['id']:'');
  $align         = (isset($att['align'])?$att['align']:'full');
  

  $scSlider = array(
    'slider' => $sliderID,
    'container_proportion' => (isset($att['proportion'])?$att['proportion']:'wide'),
    'desktop_ratio'        => (isset($att['ratio'])?$att['ratio']:''),
    'mobile_height_ratio'  => (isset($att['mobile_ratio'])?$att['mobile_ratio']:''),
  );

  if($sliderID){
    $block = array(
      'className' => 'cb_theme_slider '.$className,
      'id' => $id,
      'align' => $align
    );
    require(__DIR__.'/../acf/blocks/cb_theme_slider.php');
    
  }
}
?>