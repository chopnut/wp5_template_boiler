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
          $lowImage = $highImage = $alt = '';
          $ctaImage = $ctaPost['cta_image'];
          if($ctaImage){
            
            $lowImage = $ctaImage['sizes']['progressive_landscape'];
            $highImage = $ctaImage['sizes']['large'];
            $alt = $ctaImage['alt'];

            if(!$is_progressive){
              $lowImage = $highImage;
            }
          }
          $data = [
            'title' => $ctaPost['cta_title'],
            'excerpt' => $ctaPost['cta_description'],
            'image' => array(
              'high'=> $highImage,
              'low' => $lowImage,
              'alt' => $alt,
            ),
            'link' => ($ctaPost['cta_link']?$ctaPost['cta_link']: array(
              'url'=>'',
              'target' => '_self',
              'title'=> ''
            )),
            'date' => ''
          ];
          $tmp[]= $data;
        break;
        default: 
          $theID = $theTitle = $theExcerpt = $theDate =  $theContent = '' ;


          if(isset($post['object_type']) && $post['object_type']== 'post'){
            $thePost = $post['object_post'];
            $theID = $thePost->ID; 
            $theTitle = $thePost->post_title;
            $theExcerpt = $thePost->post_excerpt;
            $theDate = $thePost->post_date;
            $theContent = $thePost->post_content;

          }else{

            $theID = $post['ID']; 
            $theTitle = $post['post_title'];
            $theExcerpt = $post['post_excerpt']; 
            $theDate = $post['post_date'];
            $theContent = $post['post_content'];

          }

          if(empty($theExcerpt)){
            $theExcerpt = $theContent;
          }

          $lowImage = $highImage = $alt = '';
          $gatewayImage = get_field('gateway_image', $theID);

          if($gatewayImage){

            $lowImage = $gatewayImage['sizes']['progressive_landscape'];
            $highImage = $gatewayImage['sizes']['large'];
            $alt = $gatewayImage['alt'];

          }else{
            $lowImage = getFeaturedImage($theID,'progressive_landscape');
            $highImage = getFeaturedImage($theID,'large',$alt);
          }

          if(!$is_progressive){
            $lowImage = $highImage;
          }


          $data = [
            'title' =>   $theTitle,
            'excerpt' => $theExcerpt,
            'image' => array(
              'high'=> $highImage,
              'low' => $lowImage,
              'alt' => $alt,
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
$is_progressive = function_exists('urbosa_progressive');
$placeholder   = get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
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

      $postType = $autoOption['auto_post_type'];
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
    $columns        = get_field('style_columns');
    $wordsLimit     = get_field('style_words_limit');
    $styleType      = get_field('style_type');
    $styleSplitMode = get_field('style_split_mode');

    if(!empty($posts)){

      // Root properties
      $mainClasses = array();
      $mainAttr  = '';
      

      if(
        $styleType=='default' || 
        $styleType=='center'  ||
        ($styleType=='split' && $styleDirection=='row')
        ){
          
        // Do nothing
        $mainClasses[] = 'mt-0';
        $mainClasses[] = 'mb-0';
      }else{

        $mainClasses[] ='is-vcentered';
        $mainClasses[] ='is-gapless';

      }

      $mainClass = implode(' ', $mainClasses);

      ?>

      <div class="columns is-multiline <?=$mainClass?> <?=$styleType?> <?=$styleSplitMode?>" <?=$mainAttr?>>

      <?php

        if($styleDirection=='column' && $styleType=='split'){

          for($n=0; $n < count($posts); $n++){


            $post = $posts[$n];
            $excerpt = wp_trim_words($post['excerpt'], $wordsLimit);
            $title = $post['title'];

            $low = $post['image']['low'];
            $high = $post['image']['high'];
            $rawLow = $low;
            $placeHolderImage = $placeholder;

            if($low && $is_progressive){
              $low = encodeDataImage($low);
            }
            if($low){
              $placeHolderImage = $low;
            }
            
            
            $link = $post['link'];

            ?>
            <div class="column is-12 columns is-vcentered ">
              <div class="column pr-0 pl-0 pt-0 pb-0">
                <figure>
                  <a href="<?=$link['url']?>" target="<?=$link['target']?>">
                    <div class="image ratio square progressive <?=$block['id']?>" data-raw-low="<?=$rawLow?>" data-low="<?=$low?>" data-high="<?=$high?>" style="background-image:url(<?=$placeHolderImage?>)">
                  </div>
                  </a>
                </figure>
              </div>  
              <div class="column pr-0 pl-0">
                <div class="title"><?=$title?></div>
                <div class="desc pt-2 pb-4"><?=$excerpt?></div>
              </div>     
            </div>
            <?php

          }


        }else{

          ?>

          <?php
            $columnClass = 'is-12';
            if($styleDirection=='row'){
              
              $tmpColumn = 12/$columns;
              $columnClass = 'is-'.$tmpColumn;
            }

            for($n=0; $n < count($posts); $n++){

              $post = $posts[$n];
              $excerpt = wp_trim_words($post['excerpt'], $wordsLimit);
              $title = $post['title'];
              $image = $post['image'];
              
              $high = $image['high'];
              $alt  = $image['alt'];

              $link = $post['link'];

              if(empty($high)){
                $high = $placeholder;
              }

              $imgAttr = '';
              if(is_admin()){
                $imgAttr  = "src='$high'";
              }
              ?>
                <div class="column <?=$columnClass?>">
                  <div class="card">
                    <?php 
                      if($styleType=='center'){
                        ?>
                       <div class="title pt-4"><a href="<?=$link['url']?>" target="<?=$link['target']?>"><?=$title?></a></div>
                        <?php
                      }
                    ?>
                    <figure class="ratio wide force">
                      <a href="<?=$link['url']?>" target="<?=$link['target']?>">
                        <div class="img-bg cover">
                          <img class="urbosa-lazy-load" data-src="<?=$high?>" alt="<?=$alt?>" <?=$imgAttr?>/>
                        </div>
                      </a>
                    </figure>
                    <?php 
                      if($styleType!=='center'){
                        ?>
                        <div class="title pt-4"><a href="<?=$link['url']?>" target="<?=$link['target']?>"><?=$title?></a></div>
                        <?php
                      }
                    ?>
                    <div class="desc pt-4 pb-4"><?=$excerpt?></div>
                  </div>
                </div>
              <?php
            }
          ?>

          <?php

        }

      ?>

      </div>

      <?php
    } else {
      ?>
      <div class="no-posts">
        <div>No posts/object found.</div>
      </div>
      <?php
    }
  ?>
</div>
<?php 
  if(!is_admin()){
    ?>
    <script>
      $(document).ready(function(){
        initProgressive('<?=$block['id']?>')
      })
    </script>
    <?php
  }
?>