<?php 
//--------------------------------------------------------------------------
// Create class attribute allowing for custom "className" and "align" values.
// This will check alignment as well
$className = basename(__FILE__, '.php'); 
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}
//--------------------------------------------------------------------------

$image  = get_field('initial_image');
$image2 = get_field('secondary_image');

?>


<div class="urbosa-block <?=$className?>">
  <?php 
  
    if(!empty($image)){
      $blockID = $block['id'];
      $caption = $image['description'];
      $alt     = $image['alt'];
      $lightboxAlt =  $alt;
      $imgSrc  = $image['url'];
      $link    = get_field('link');
      $isDimmer = get_field('is_dimmer');
      $attr     = "";
      $youtubeID = getYoutubeIdFromUrl($link);

      // if there is 2nd image use it instead
      if($image2){
        $imgSrc = $image2['url'];
        $lightboxAlt = $image2['alt'];
      }

      if($isDimmer){
        $content = "<img src='$imgSrc' alt='$lightboxAlt' />";
        $ytSrc = 'https://www.youtube.com/embed/'.$youtubeID.'?autoplay=1&controls=1&html5=1&loop=0&mute=1&rel=0&playlist='.$youtubeID;
        
        if($youtubeID){
          $content = '<div class="video-container">
              <iframe src="'.$ytSrc.'" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
            </div>';

            // Add playicon button
            ?>
            <div class="play-button">
              <i class="urbosa-icon play-button"></i>
            </div>
            <?php
        }
        
        ?>
        <div class="ui page dimmer" id="dimmer_<?=$blockID?>">
          <div class="content">
            <div class="wrapper">
              <?=$content?>
            </div>
          </div>
        </div>
        <?php
      }

      // put the original image back for initial render
      $imgSrc = $image['url'];
      if(is_admin()){
        $attr = "src='$imgSrc'";
        $link = "javascript:;";
      }
      ?>
      <a href="<?=$link?>" 
        class="media-dimmer-trigger"
        data-dimmer-id="dimmer_<?=$blockID?>"
      >
        <figure>
          <div class="image">
            <img data-src="<?=$imgSrc?>" alt="<?=$alt?>" class="urbosa-lazy-load" <?=$attr?>/>
          </div>
          <?php 
            if(!empty($caption)){
              ?>
              <figcaption>
                <?=$caption?>
              </figcaption>
              <?php
            }
          ?>
        </figure>
      </a>
      <?php

    }else{
      cb_no_resource_set('Media Dimmer', 'No items yet.');

    }
  ?>
</div>