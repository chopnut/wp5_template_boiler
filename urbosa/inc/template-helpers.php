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

if(!function_exists('getWCProducts')){  
  /**
   * getWCProducts
   * 
   * @param  mixed $perPage
   * @param  mixed $searchTerm      eg: 'cool product'
   * @param  mixed $categorySlugs   eg: array('slug-1')
   * @param  mixed $attributeSlugs  eg: array('colour' => 'red','size'=> array('small','large'))
   * @param  mixed $minMaxPrice     eg: array(100,500)
   * @param  mixed $orderby         eg: array('orderby'=>'meta_value_num','order'=>'desc','meta_key'=>'order')
   * @param  mixed $inStock         true or false | returns all product in stuck
   * @return void
   */
  function getWCProducts(
    $perPage=10,
    $searchTerm='',
    $categorySlugs=array(), 
    $attributeSlugs=array(),
    $minMaxPrice=null,
    $orderby=null,
    $inStock=false){
    
    global $paged;
  
    $offset       = ($paged-1)*$perPage;
    if($offset<=0){
      $offset = 0;
    }
    $argCats      = array();
    $metaArgs     = array();
  
    $argCats['relation']    = 'AND';
    $metaArgs['relation']   = 'AND';
  
  
  
    if($minMaxPrice && count($minMaxPrice)==2){
      $metaArgs[] = array(
        'key' => '_price',
        'value' => array($minMaxPrice[0], $minMaxPrice[1]),
        'compare' => 'BETWEEN',
        'type' => 'NUMERIC'
      );
    }
    if($inStock){
      $metaArgs[]= array(
        'key' => '_stock_status',
        'value' => 'instock',
        'compare' => '=',
      );
    }
    foreach($categorySlugs as $cat){
      if(!empty($cat)){
        $argCats[] = array(
          'taxonomy' => 'product_cat',
          'field' => 'slug',
          'terms'=> $cat
        );
      }
    }
    foreach($attributeSlugs as $attSlug=>$attValue){
      if(!empty($attValue)){
        $argCats[] = array(
          'taxonomy'        =>  'pa_'.$attSlug,
          'field'           => 'slug',
          'terms'           =>  $attValue,
          'operator'        => 'IN',
        );
      }
    }
    $args = array(
      'post_type'             => 'product',
      'post_status'           => 'publish',
      'ignore_sticky_posts'   => 1,
      'posts_per_page'        => $perPage,
      'offset'                => $offset,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'meta_key' => '',
      'tax_query' => $argCats,
      'meta_query'=> $metaArgs,
      's'=>$searchTerm,
      'paged'=>$paged
    );
    if($orderby){
      if(isset($orderby['orderby']) && isset($orderby['order'])){
        $args['orderby']  = $orderby['orderby'];
        $args['order']    = $orderby['order'];
  
        if(isset($orderby['meta_key'])){
          $args['meta_key'] = $orderby['meta_key'];
        }
      }
    }
  
  
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
if(!function_exists('getContentBlocks')){
    
  /**
   * getContentBlocks
   *
   * @param  mixed $postID
   * @param  mixed $blockNames eg: array or string
   * @param  mixed $getAllOther
   * @return void
   */
  function getContentBlocks( $postID, $blockNames, &$getAllOther= null ) {
    $post_content = get_post( $postID )->post_content;
  
    //get all blocks of requested type
    $otherBlocks  = array();
    $blocks       = array();
    $parseBlocks =  parse_blocks($post_content);

    foreach($parseBlocks as $parseBlock){
      $tmpBlock = $parseBlock;
      if(is_array($blockNames)){
        $found = 0;
        foreach ($blockNames as $eachBlockName) {
          $blockID = str_replace('_','-', $eachBlockName);
          $acfBlockID = 'acf/'.$blockID;
          if($blockID==$parseBlock['blockName'] || $acfBlockID==$parseBlock['blockName']){
            $found = 1;
          }
        }
        // Check for reusable block
        if( $parseBlock['blockName'] === 'core/block' && ! empty( $parseBlock['attrs']['ref'] ) ){
          $blockContent = parse_blocks( get_post( $parseBlock['attrs']['ref'] )->post_content );
          if(is_array($blockContent) && count($blockContent)>0){
            $blockID       = str_replace('acf/','', $blockContent[0]['blockName']);
            $blockID       = str_replace('-','_', $blockID);
  
            if(in_array($blockID, $blockNames)){
              $found = 1;
              $tmpBlock = $blockContent[0];
            }
          }

        }
        if($found){
          $blocks[] = $tmpBlock;
          continue;
        }else{
          if(!empty($parseBlock['blockName'])){
            $otherBlocks[] = $tmpBlock;
          }
        }
      }else{
        $blockID = str_replace('_','-', $blockNames);
        $acfBlockID = 'acf/'.$blockID;
        
        if($blockID==$parseBlock['blockName'] || $acfBlockID==$parseBlock['blockName']){
          $blocks[] = $parseBlock;
        }else{
          if(!empty($parseBlock['blockName'])){
            $otherBlocks[] = $parseBlock;
          }
        }
      }

    }

    if(is_array($getAllOther)){
      foreach ($otherBlocks as $otherBlock) {
        $getAllOther[] = $otherBlock;
      }
    }
    return $blocks;
  }
}

if(!function_exists('getVar')){  
  /**
   * get post/get/request if available return default otherwise
   *
   * @param  mixed $name
   * @param  mixed $default
   * @return void
   */
  function getVar($name,$default=''){
    if(isset($_REQUEST[$name])){
      return $_REQUEST[$name];
    }
    if(isset($_GET[$name])){
      return $_GET[$name];
    }
    if(isset($_POST[$name])){
      return $_POST[$name];
    }
    return $default;
  }
}
if(!function_exists('getACFImage')){  
  
  /**
   * Get the image and return default if acf is not available
   *
   * @param  mixed $acfField
   * @param  mixed $postID
   * @param  mixed $default
   * @param  mixed $size
   * @return void
   */
  function getACFImage($acfField,$postID=0,$default='',$size='large'){
    $acfImage = ($postID)? get_field($acfField,$postID):get_field($acfField);
    if(!$acfImage || !isset($acfImage['sizes']) || !isset($acfImage['url'])){
      return $default;
    }
    if($size=='full' || $size=='url'){
      return $acfImage['url'];
    }
    if(isset($acfImage['sizes'][$size])){
      return $acfImage['sizes'][$size];
    }
    return $default;
  }
}
if(!function_exists('do_curl')){
  function doCurl($path,$postParams= null){
    /* Prep-variables */
    $ch       = curl_init();
    /* Initialize CURL set-up */
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING , "");
  
    if($postParams){
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
    }
    $result = json_decode(curl_exec($ch));
    curl_close($ch);
    return $result;
  }
}
