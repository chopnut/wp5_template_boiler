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

$jumpID                = get_field('anchor_id');
$minimumHeight         = get_field('minimum_height');
$columns               = get_field('column_contents'); // repeater

$imageLeft             = get_field('left_image');
$imageLeftMargin       = get_field('left_image_margin');
$imageLeftBottomMargin = get_field('left_image_bottom_margin');
$imageLeftBGSize       = get_field('left_image_background_size');
$imageLeftZIndex       = get_field('left_z_index');
$imageLeftWidth        = get_field('left_image_width');

$imageRight             = get_field('right_image');
$imageRightMargin       = get_field('right_image_margin');
$imageRightBottomMargin = get_field('right_image_bottom_margin');
$imageRightBGSize       = get_field('right_image_background_size');
$imageRightZIndex       = get_field('right_z_index');
$imageRightWidth        = get_field('right_image_width');

//-------------------------------------------------------------------------
$blockStyle         = '';
$contentHolderStyle = '';
$leftStyle          = '';
$rightStyle         = '';

if($minimumHeight) {  $contentHolderStyle .= "min-height: $minimumHeight;";  }
if($imageLeft){ $leftStyle .= "background-image: url(".$imageLeft['url'].");"; }
if($imageLeftMargin){ $leftStyle .= "left: $imageLeftMargin;"; }
if($imageLeftBottomMargin){ $leftStyle .= "bottom: ;$imageLeftBottomMargin"; }
if($imageLeftZIndex){ $leftStyle .= "z-index: $imageLeftZIndex;"; }
if($imageLeftBGSize){ $leftStyle .= "background-size: $imageLeftBGSize;"; }
if($imageLeftWidth) { $leftStyle .= "width: $imageLeftWidth;"; }

if($imageRight){  $rightStyle .= "background-image: url(".$imageRight['url'].");";}
if($imageRightMargin){ $rightStyle .= "right: ".$imageRightMargin.";"; }
if($imageRightBottomMargin){ $rightStyle .= "bottom: ;$imageRightBottomMargin"; }
if($imageRightZIndex){ $rightStyle .= "z-index: $imageRightZIndex;"; }
if($imageRightWidth){ $rightStyle .= "width: $imageRightWidth;"; }

?>
<div class="urbosa-block <?=$className?> ratio <?=$jumpID?>" style="<?=$blockStyle?>">
  <div class="wrapper">
    <div class="content-holder" style="<?=$contentHolderStyle?>">
      <div class="content-wrapper">
        <div class="columns is-gapless">

          <?php 
            if(!empty($columns)){
              foreach($columns as $column){
                $content = $column['column_content'];
                ?>
                <div class="column">
                  <div class="content">
                    <?=wpautop($content)?>
                  </div>
                </div>
                <?php
              }
            }
          ?>

        </div>
      </div>
      <div class="image-left"  style="<?=$leftStyle?>"></div>
      <div class="image-right" style="<?=$rightStyle?>"></div>
    </div>
  </div>
</div>