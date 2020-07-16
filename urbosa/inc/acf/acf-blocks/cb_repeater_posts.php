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

$repeaterType = get_field('repeater_type');

$manual       = get_field('manual_objects');
$autoOption   = get_field('auto_post_options');

?>
<div class="urbosa-block <?=$className?>">
  <?php 
    $posts =[];

    if($repeaterType == 'auto'){ 
      // default
      $metaArray= [];
      $orderBy = 'date';
      $order   = 'DESC';
      $categories = [];
      $taxonomies = [];

      $postType = get_field('auto_post_type');
      $taxonomy = get_field('auto_taxonomy');
      $count    = get_field('auto_display_count');
      $orderBy  = get_field('auto_order_by');

      $catOption = get_field('auto_cat_option');
      $catType   = get_field('auto_cat_type');
      $autoCat   = get_field('auto_categories');
      $autoTax   = get_field('auto_taxonomies');

      if($catType == 'category' && !empty($autoCat)){
        $categories = array_map('trim', explode(',', $autoCat));
      }
      if($catType == 'taxonomy' && !empty($autoTax)){
        $taxonomies = array_map('trim', explode(',', $autoTax));
      }


      switch($orderBy){
        case 'dateasc':
          $order = 'ASC';
        break;
        case 'random':
          $orderBy = 'rand';
        break;
        case 'menuorder': 
          $orderBy = 'menu_order';
          $order = 'DESC';
        break;

      }

      $res = getPosts(
        $postType,
        '', 
        ['gateway_image'],// acf 
        $categories,// categories
        $taxonomies,// taxonomies
        $count, //per_page
        0, 
        $metaArray,
        $orderBy, 
        $order
      );
      debug($res['posts']);
    }else { // manual

    }

    $styleType = get_field('style_type');
    
    $styleDirection = get_field('style_direction');

  ?>
  Urbosa repeater posts
</div>