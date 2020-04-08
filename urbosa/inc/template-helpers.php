<?php
if(!function_exists('debug')){  
  /**
   * debug
   * More presentational debug display of a variable
   * @param  mixed $data
   * @return void
   */
  function debug($data){
    $html  = '<pre style="padding: 15px; font-family: \'courier new\'; font-size: 12px; border: 1px dashed #800">';
    $html .= print_r($data, true);
    $html .= '</pre>';
    echo $html;
  }
}
if(!function_exists('objectToArray')){  
  /**
   * objectToArray
   * Convert object into an array
   * @param  mixed $object
   * @return array
   */
  function objectToArray($object)
  {
    if (!is_object($object) && !is_array($object)) {
      return $object;
    }
  
    if (is_object($object)) {
      $object = (array)$object;
    }
  
    return array_map('objectToArray', $object);
  }
}

if(!function_exists('getPosts')){  
  /**
   * getPosts - Return posts depending on parameters.
   * @param  mixed $post_type
   * @param  mixed $slug
   * @param  mixed $acfFields
   * @param  mixed $categories
   * @param  mixed $perPage
   * @param  mixed $offset
   * @param  mixed $metaArray
   * @param  mixed $orderby
   * @param  mixed $order
   * @return void
   */
  function getPosts( $post_type='post',  $slug='', $acfFields = array(),  $categories = array(),  $perPage = -1,  $offset = 0, $metaArray = array(),  $orderby = 'menu_order',  $order = 'DESC')
  {
    $args = array(
      'post_type' => $post_type,
      'post_status' => 'publish',
      'ignore_sticky_posts' => 0,
      'posts_per_page' => $perPage,
      'orderby' => $orderby,
      'order' => $order,
      'offset' => $offset,
    );
  
    if (count($categories) > 0) {
      $args['category_name'] = implode(',', $categories);
    }
    if(!empty($slug)){
      $args['name'] = $slug;
    }
  
    if (count($metaArray) > 0) {
      foreach ($metaArray as $meta) {
        if ($meta['value'] != '') {
          $args['meta_query'][] = array(
            'key' => $meta['key'],
            'value' => $meta['value'],
          );
        }
      }
    }
    
    $dataItems = new WP_Query($args);
    $query     = $dataItems;
    $dataItems = objectToArray($dataItems->posts);
    $dataArray = array();
  
    foreach ($dataItems as $data) {
      $thisID = $data['ID'];
      foreach ($acfFields as $field) {
        $data[$field] = get_field($field, $thisID);
        if (stripos($field, 'image') !== false) {
          $data[$field] = $data[$field];
        }
      }
      $dataArray[] = $data;
    }
    return 
      array(
        'posts' => $dataArray,
        'query' => $query,
      );  
    
  }
}

if(!function_exists('getWCPRoducts')){  
  /**
   * getWCPRoducts
   * 
   * @param  mixed $perPage
   * @param  mixed $page
   * @param  mixed $categorySlugs   eg: array('slug-1')
   * @param  mixed $attributeSlugs  eg: array('colour' => 'red','size'=> array('small','large'))
   * @param  mixed $minMaxPrice     eg: array(100,500)
   * @return void
   */
  function getWCPRoducts($perPage=10,$page=1,$categorySlugs=array(), $attributeSlugs=array(),$minMaxPrice=null){
    $offset       = ($page-1)*$perPage;
    $argCats      = array();
    $priceArgs    = array();

    if($minMaxPrice && count($minMaxPrice)==2){
      $priceArgs = array(
        'key' => '_price',
        'value' => array($minMaxPrice[0], $minMaxPrice[1]),
        'compare' => 'BETWEEN',
        'type' => 'NUMERIC'
      );
    }
    foreach($categorySlugs as $cat){
      $argCats[] = array(
        'taxonomy' => 'product_cat',
        'field' => 'slug',
        'terms'=> $cat
      );
    }
    foreach($attributeSlugs as $attSlug=>$attValue){
      $argCats[] = array(
        'taxonomy'        =>  'pa_'.$attSlug,
        'field'           => 'slug',
        'terms'           =>  $attValue,
        'operator'        => 'IN',
      );
    }
    $args = array(
      'post_type'             => 'product',
      'post_status'           => 'publish',
      'ignore_sticky_posts'   => 1,
      'posts_per_page'        => $perPage,
      'offset'                => $offset,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'tax_query' => array('relation' => 'AND', 
        $argCats),
      'meta_query' => array(
        $priceArgs
       ),
    );
    return new WP_Query($args); 
  }
}
if(!function_exists('getCurrentTemplate')){
  add_filter( 'template_include', 'var_template_include', 1000 );
  function var_template_include( $t ){
      $GLOBALS['current_theme_template'] = basename($t);
      return $t;
  }  
  /**
   * getCurrentTemplate
   * Display current template being used.
   * @param  mixed $echo eg: To echo or return
   * @return mixed
   */
  function getCurrentTemplate( $echo = false ) {
      if( !isset( $GLOBALS['current_theme_template'] ) )
          return false;
      if( $echo )
          echo $GLOBALS['current_theme_template'];
      else
          return $GLOBALS['current_theme_template'];
  }
}

if(!function_exists('getChildrenCategories')){  
  /**
   * getChildrenCategories
   * Get all children category under the main category
   * @param  mixed $parent_slug_or_id - Category slug or ID
   * @param  mixed $hide_empty        - To hide category with no posts
   * @return void
   */
  function getChildrenCategories($parent_slug_or_id, $hide_empty = false)
  {
    $parent_id = 0;
    $parent = null;

    if (is_numeric($parent_slug_or_id)) {
      $parent_id = $parent_slug_or_id;
    } else {
      $parent = get_category_by_slug($parent_slug_or_id);
      if ($parent) {
        $parent_id = $parent->term_id;
      }
    }


    if ($parent_id) {
      // Category by parent
      $args = array(
        'type' => 'post',
        'child_of' => $parent_id,
        'hierarchical' => 1,
        'taxonomy' => 'category',
        'hide_empty' => $hide_empty
      );
      $children = get_categories($args);
      return $children;
    }
    return null;
  }
}

if(!function_exists('getWidgetArray')){
    
  /**
   * Returns widgets into an array of widgets for display
   *
   * @param  mixed $widget_id
   * @return array
   */
  function getWidgetArray($widget_id){
    global $wp_registered_widgets;
    $sidebars_widgets = wp_get_sidebars_widgets();

    if(!isset($sidebars_widgets[$widget_id])){
      return array();
    }
    $widgets = $sidebars_widgets[$widget_id]; 
    
    $final  = [];
   
    foreach ($widgets as $widget) {

      $option_name = $wp_registered_widgets[$widget]['callback'][0]->option_name;
      $key = $wp_registered_widgets[$widget]['params'][0]['number'];
      $widget_instances = get_option($option_name);
      
      // make sure "content" has values cause sometimes , "text" has it.
      $data    = $widget_instances[$key];
      $content = '';
      
      if(!isset($data['content']) && !isset($data['nav_menu'])){
        ob_start();
        echo do_shortcode(wpautop($data['text']));
        $content = ob_get_clean();

      }else if(isset($data['nav_menu'])){
        // Grab the menu object by slug as to make sure you are grabbing the correct one
        $menuID = $data['nav_menu'];
        ob_start();
        $menuObject = wp_get_nav_menu_object($menuID);
        wp_nav_menu(array('menu'=>$menuObject->slug));
        $content = ob_get_clean();
      }
      $menuTitle = strtolower($data['title']);
      ob_start();
      ?>
      <div class="widget <?=$menuTitle?>">
        <div class="widget-title"><?=$data['title']?></div>
        <div class="widget-content"><?=$content?></div>
      </div>
      <?php
      $content = ob_get_clean();
      $data['content'] = $content; 
      $final[] = $data;
    }
    return $final;
  }
}
if(!function_exists('pregMatchGrouping')){  
  /**
   * Preg_match_all replacement as this groups matches in their own array instead of separate.
   *
   * @param  mixed $exp
   * @param  mixed $subject
   * @return array
   */
  function pregMatchGrouping($exp, $subject){
    preg_match_all($exp,$subject, $matches);
    $tmp = array();
    if($matches && count($matches)>0){
      for ($i=0; $i < count($matches); $i++) { 
        $match = $matches[$i];
  
        for ($n=0; $n < count($match); $n++) { 
          if(!isset($tmp[$n])){
            $tmp[$n] = array();
            $tmp[$n][] = $match[$n];
          }else{
            $tmp[$n][] = $match[$n];
          }
        }
      }
    }
    return $tmp;
  }
}
if(!function_exists('getFeatureImage')){  
  /**
   * getFeatureImageSrc of a post
   *
   * @param  mixed $postID
   * @param  mixed $size eg: 'thumbnail','medium','large','full'
   * @return string
   */
  function getFeatureImage($postID=null, $size='medium'){
    $pID = 0;
    if($postID){
      $pID = $postID;
    } else {
      $pID = get_the_ID();
    }
    $url = wp_get_attachment_image_src( get_post_thumbnail_id( $pID), $size );
    if(is_array($url) && count($url)>0){
      return $url[0];
    }
    return '';
  }
}

if(!function_exists('youtubeEmbed')){
  
  /**
   * Display embeded video as normal (being outputted anywhere on the page) or background as a full width browser bg.
   *
   * @param  mixed $embed_video eg: raw iframe code from youtube
   * @param  mixed $mode eg: 'normal' or 'background'
   * @param  mixed $properties
   * @param  mixed $srcquery
   * @return void
   */
  function youtubeEmbed($embed_video,$mode='normal',$properties="width='100%' height='100%' frameborder='0' allowfullscreen",$srcquery="&autoplay=1&controls=0&html5=1&loop=1&mute=1&rel=0"){
    $new_embed = $embed_video;
    if($mode=='normal'){
      echo '<div class="video-container">';
      echo $new_embed;
      echo '</div>';
    }else if($mode=='background'){
      $new_embed = preg_replace('/'.'width=".*?"'.'/','', $new_embed);
      $new_embed = preg_replace('/'.'height=".*?"'.'/','', $new_embed);
      $new_embed = preg_replace('/'.'<iframe'.'/','<iframe '.$properties, $new_embed);
    
      preg_match('/'.'(src=".*?")'.'/',$embed_video,$match);
      if($match){
        $src = $match[1];
        $src = substr($src,0,-1);
        $src = $src.$srcquery.'"';
        $new_embed = preg_replace('/'.'src=".*?"'.'/',$src, $new_embed);
      }
      echo '<div class="video-background">';
        echo '<div class="video-foreground">';
        echo $new_embed;
        echo '</div>';
      echo '</div>';
    }
  }
}