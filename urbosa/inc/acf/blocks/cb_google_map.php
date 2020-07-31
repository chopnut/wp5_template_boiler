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

  </div>
</div>
