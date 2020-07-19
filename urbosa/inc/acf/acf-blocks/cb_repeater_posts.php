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
//    Normalise posts

if(!function_exists('cb_normalise_posts')){
  function cb_normalise_posts($posts){
    $is_progressive = function_exists('urbosa_progressive');
    $tmp = [];
    foreach($posts as $post){
      $type='post';
      if(isset($post['object_type']) && $post['object_type']== 'cta'){
        $type = 'cta';
        $ctaPost = $post['object_cta'];
      }

      switch($type){
        case 'cta':
          $data = [
            'title' => $ctaPost['cta_title'],
            'excerpt' => $ctaPost['cta_description'],
            'gateway_image'=> $ctaPost['cta_image'],
            'featured_image' => array(
              'normal'=> $ctaPost['cta_image']['sizes']['large'],
              'progressive' => ($ctaPost['cta_image'])?$ctaPost['cta_image']['sizes']['progressive_landscape']:'',
              'alt' => ($ctaPost['cta_image'])?$ctaPost['cta_image']['alt']:'',
            
            ),
            'link' => $ctaPost['cta_link'],
            'date' => ''
          ];
          $tmp[]= $data;
        break;
        default: 
          $theID = $theTitle = $theExcerpt = $theDate = '' ;
          $alt = '';

          if(isset($post['object_type']) && $post['object_type']== 'post'){
            $thePost = $post['object_post'];
            $theID = $thePost->ID; 
            $theTitle = $thePost->post_title;
            $theExcerpt = $thePost->post_excerpt;
            $theDate = $thePost->post_date;

          }else{

            $theID = $post['ID']; 
            $theTitle = $post['post_title'];
            $theExcerpt = $post['post_excerpt']; 
            $theDate = $post['post_date'];

          }
          $data = [
            'title' =>   $theTitle,
            'excerpt' => $theExcerpt,
            'gateway_image'=> get_field('gateway_image', $theID),
            'featured_image' => array(
              'normal'=> getFeaturedImage($theID,'large',$alt),
              'progressive' => ($is_progressive? getFeaturedImage($theID ,'progressive_landscape') :''),
              'alt' => $alt
            
            ),
            'link' => array(
              'title'=> $theTitle,
              'url'=> get_permalink($theID),
              'target'=> '_self'
            ),
            'date' =>  $theDate
          ];
          $tmp[]= $data;
      }
    }
    return $tmp;
  }
}
//--------------------------------------------------------------------------
$repeaterType = get_field('repeater_type');
$manual       = get_field('manual_objects');

?>
<div class="urbosa-block <?=$className?>">
  <?php 
    $posts =[];

    if($repeaterType == 'auto'){ 
      $autoOption     = get_field('auto_post_options');

      // default
      $metaArray= [];
      $orderBy = 'date';
      $order   = 'DESC';
      $categories = [];
      $taxonomies = [];

      $postType = get_field('auto_post_type');
      $count    = $autoOption['auto_display_count'];
      $orderBy  = $autoOption['auto_order_by'];
      $catOption = $autoOption['auto_cat_option'];

      $catType   = $catOption['auto_cat_type'];
      $autoCat   = $catOption['auto_categories'];
      $autoTax   = $catOption['auto_taxonomies'];

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
      
      $posts = cb_normalise_posts($res['posts']);

    }else { // manual
      $posts = cb_normalise_posts($manual);

    }
    $styleDirection = get_field('style_direction');
    $columns        = get_field('columns');
    $wordsLimit     = get_field('words_limit');
    $styleType      = get_field('style_type');

    debug($styleType);
    debug($styleDirection);


  ?>
  Urbosa repeater posts
</div>