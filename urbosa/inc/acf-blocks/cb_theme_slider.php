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
//-------------------------------------------------------------------------
// Acf fields
$theme_slider           = get_field('selected_slider');
$sliders                = get_field('sliders',$theme_slider);
$slider_properties      = get_field('slider_properties',$theme_slider);

$proportion             = get_field('container_proportion');
$mobile_height_ratio    = get_field('mobile_height_ratio');
$desktopRatio           = get_field('ratio');

$theme_slider_id = 'theme_slider_'.$block['id'];
//-------------------------------------------------------------------------
// Slider
$show_arrows = $slider_properties['show_arrows'];
$arrow_icon  = $slider_properties['arrow_icon'];
$show_navigation  = $slider_properties['show_navigation'];
$auto_play  = $slider_properties['auto_play'];
$auto_play_speed  = $slider_properties['auto_play_speed'];

?>
<style>
  #<?=$theme_slider_id;?> .content-holder::before{
    <?php 
      if($proportion=='override'){
        echo "padding-bottom: $desktopRatio;";
      }
    ?>
  }
  @media (max-width: 768px){
    #<?=$theme_slider_id;?> .content-holder::before{
    <?php 
      if(!empty($mobile_height_ratio)){
        echo "padding-bottom: $mobile_height_ratio;";
      }
    ?>
  }
  }
</style>
<div class="urbosa-block <?=$className?> <?=$proportion?>  <?=(is_admin()?'admin':'')?>" id="<?=$theme_slider_id?>">



      <?php 
        if(!empty($sliders)){
          ?>
          <div class="slick-sliders <?=$show_navigation?'nav':''?>" id="<?=$theme_slider_id?>_slider">
          <?php 
              foreach ($sliders as $slider) {



                // Prep-vars
                $background_type        = $slider['background_type'];
                $image                  = $slider['image'];
                $video_type             = $slider['video_type'];
                
                $video_file_desktop     = $slider['video_file']['video_file_desktop'];
                $video_file_mobile      = $slider['video_file']['video_file_mobile'];

                $video_embed_desktop    = $slider['video_embed']['video_embed_desktop'];
                $video_embed_mobile     = $slider['video_embed']['video_embed_mobile'];

                $content                = $slider['content'];
                $content_position       = $slider['content_position'];

                // Style
                ?>
              <div class="each-slider ">
                
              
              
                <div class="wrapper">
                  <div class="content-holder ratio <?=$proportion?>">
                    
                    <div class="padders">
                      <div class="padders">

                <div class="actual-content">

                <?php
                //-----------------------------------------------------------
                if($background_type =='video'){

                  if($video_type == 'file'){ // File


                    $desktopSrc = $video_file_desktop['url'];
                    $mobileSrc  = $video_file_mobile['url']; 


                    $desktopType = $video_file_desktop['mime_type']; 
                    $mobileType  = $video_file_mobile['mime_type']; 
                    

                    if(!$video_file_mobile){
                      $mobileSrc = $desktopSrc;
                      $mobileType= $desktopType;
                    }
                    if(!$video_file_desktop){
                      $desktopSrc = $mobileSrc;
                      $desktopType= $mobileType;

                    }

                    if($video_file_desktop || $video_file_mobile){
                      
                      $videoClass ='';
                      if(empty($mobile_height_ratio)){
                        $videoClass = 'full';
                      }
                      ?>
                      <div class="video-wrapper desktop <?php ($video_file_desktop)?'':'no-desktop'; ?>">
                        <div class="html5-video-container">
                          <video loop autoplay muted class="<?=$videoClass?>" src="<?=$desktopSrc?>"  type="<?=$desktopType?>"></video>
                        </div>
                      </div>
                      <div class="video-wrapper mobile <?php ($video_file_mobile)?'':'no-mobile'; ?>">
                        <div class="html5-video-container">
                          <video loop autoplay muted class="<?=$videoClass?>" src="<?=$mobileSrc?>" type="<?=$mobileType?>"></video>
                        </div>
                      </div>
                      <?php
                    }


                  }else if(!empty($video_embed_desktop) || !empty($video_embed_mobile) ){   // Embed Youtube/Vimeo/ Etc

                    $desktopSrc = $video_embed_desktop;
                    $mobileSrc  = $video_embed_mobile; 


                    
                    if(!$video_embed_mobile){
                      $mobileSrc = $desktopSrc;
                    }
                    if(!$video_embed_desktop){
                      $desktopSrc = $mobileSrc;

                    }


                    ?>

                    <div class="youtube-wrapper desktop">
                      <?php youtubeEmbed($desktopSrc,'background'); ?>
                    </div>
                    <div class="youtube-wrapper mobile">
                      <?php youtubeEmbed($mobileSrc,'background'); ?>
                    </div>
                    <?php


                  }else{
                    ?>
                    <div class="no-video">
                      No video set for the slide.

                    </div>
                    <?php
                  }
            
                //-----------------------------------------------------------
                }else{ // Image
                  ?>
                  <div class="image-wrapper">

                  </div>
                  <?php
                }

                ?>
                </div><!--actual content-->


                      </div><!-- padders -->
                    </div><!-- padders -->


                    </div><!-- ratio -->
                  </div><!-- wrapper -->



                </div>
                <?php


                // Show only one to admin
                if(is_admin(  )){ break; }
              }
              
          ?>
          </div>
          <?php
        } else {

          ?>
          <div class="ratio wide">No slides has been set up yet.</div>
          <?php
        }
      ?>


 
</div>
<script>
  jQuery(document).ready(function($){
    var slickOptions = {
      slideToShow: 1,
    }

    <?php 
      if($show_arrows && $arrow_icon){
        ?>
        slickOptions['nextArrow'] = '<img src="<?=$arrow_icon?>" class="" width="50" height="50" />';
        slickOptions['prevArrow'] = '<img src="<?=$arrow_icon?>" class="" width="50" height="50" />';
        <?php
      }else if($show_arrows){
        ?>
        slickOptions['nextArrow'] = '<i class="dashicons dashicons-arrow-right-alt2 slick-nav-next"></i>';
        slickOptions['prevArrow'] = '<i class="dashicons dashicons-arrow-left-alt2 slick-nav-prev"></i>';
        <?php
      }

      if($show_navigation){
        ?>
        slickOptions['dots'] = true;
        <?php
      }
      if($auto_play){
        ?>
        slickOptions['autoplay'] = true;
        <?php
        
      }
      if($auto_play_speed){
        ?>
        slickOptions['autoplaySpeed'] = <?=$auto_play_speed?>;
        <?php
      }
    ?>

    $('#<?=$theme_slider_id?>_slider').slick(slickOptions);

  });
</script>