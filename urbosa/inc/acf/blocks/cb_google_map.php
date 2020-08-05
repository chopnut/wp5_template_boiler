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

$gmType     = get_field('gm_type');
$gmFunction = get_field('function_name');
$addresses  = get_field('addresses');
$gmMarker   = get_field('image_marker');
$proportion   = get_field('proportion');
$overrideProportion   = get_field('override_proportion');
$googleMapID = 'google_map_'.$block['id'];

if($proportion=='override'){
  ?>
  <style>
    #<?=$googleMapID?>::before{
      padding-bottom: <?=$overrideProportion?>;
    }
  </style>
  <?php
}

?>

<div class="urbosa-block <?=$className?> ratio <?=$proportion?>" id="<?=$googleMapID?>">
  <div class="content-holder">
    <?php 
        if($gmType=='custom'){
          $addresses = array();
          if(function_exists($gmFunction)){
            $addresses = $gmFunction();
          }
        }
        if(!empty($addresses)){
          ?>
          <div class="acf-map">
          <?php
          $n = 0;
          foreach ($addresses as $address ) {

            $windowID = 'window-info-'.$n;
            $lat = $address['address']['lat'];
            $lng = $address['address']['lng'];
            $content = isset($address['content'])?$address['content']:'';
            
            ?>
            
            <div class="marker" 
              data-lat="<?=$lat?>" 
              data-lng="<?=$lng?>" 
              data-window-info="<?=$windowID?>"
              data-custom-marker="<?=$gmMarker?>"
            ></div>
            
            <?php 
              $content = 'hello';

              // Custom content window
              if(!empty($content)){
                ?>
                <div class="window-property" id="<?=$windowID?>">
                  <i class="dashicons dashicons-no-alt close"></i>
                  <div class="window-content"><?=$content?></div>
                </div>
                <?php
              }
            
            $n++;
          }

          ?>
          </div>
          <?php
        } else{
          ?>
          <div class="no-resource-set">
            No location found.
          </div>
          <?php
        }
        ?>
      <?php 
          if(!empty($addresses)){

            $n = 0;
            foreach ($addresses as $address ) {
  
              $windowID = 'window-info-'.$n;
              $content = isset($address['content'])?$address['content']:'';
  
                // Custom content window
                if(!empty($content)){
                  ?>
                  <div class="window-property" id="<?=$windowID?>">
                    <div class="close">
                      <img src="<?=get_stylesheet_directory_uri().'/assets/img/icons/close.svg';?>" />
                    </div>
                    <div class="window-content"><?=$content?></div>
                  </div>
                  <?php
                }
              
              $n++;
            }
          }
      ?>

  </div>
</div>
