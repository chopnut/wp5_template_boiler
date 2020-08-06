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
   * @param  mixed $withFeaturedImage (book)
   * @return void
   */
  function getPosts( 
    $postType='post',  
    $slug='', 
    $acfFields = array(),  
    $categories = array(),  
    $taxonomies = array(),
    $perPage = -1,  
    $offset = 0, 
    $metaArray = array(),  
    $orderby = 'menu_order',  
    $order = 'DESC',
    $withFeaturedImage= true
  )
  {
    $args = array(
      'post_type' => $postType,
      'post_status' => 'publish',
      'ignore_sticky_posts' => 0,
      'posts_per_page' => $perPage,
      'orderby' => $orderby,
      'order' => $order,
      'offset' => $offset,
    );
  
    if(count($categories)) {
      $args['category_name'] = implode(',', $categories);
    }
    if(count($taxonomies)){
      $args['tax_query'] = array_map(function($v){
        return array(
          'taxonomy' => 'fabric_building_types',
          'field' => 'name',
          'terms' => $v,
        );
      }, $taxonomies);
    }
    if(!empty($slug)){
      $args['name'] = $slug;
    }
  
    if (count($metaArray)) {
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
      if($withFeaturedImage){
        $alt = '';
        $featImage = array(
          'normal' => getFeaturedImage($thisID,'large',$alt),
          'progressive' => (function_exists('urbosa_progressive')? getFeaturedImage($thisID,'progressive_landscape') :''),
          'alt' => $alt
        );
        $data['featured_image'] = $featImage;
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
   * @param  bool $unique = if values needs to be unique
   * @return array
   */
  function pregMatchGrouping($exp, $subject, $unique= true){
    preg_match_all($exp,$subject, $matches);
    $tmp = $unique = array();

    if($matches && count($matches)>0){
      for ($i=0; $i < count($matches); $i++) { 
        $match = $matches[$i];
        for ($n=0; $n < count($match); $n++) { 
          if(!isset($unique[$match[$n]])){
            $unique[$match[$n]] = true;
            if(!isset($tmp[$n])){
              $tmp[$n] = array();
              $tmp[$n][] = $match[$n];
            }else{
              $tmp[$n][] = $match[$n];
            }
          }
        }
      }
    }
    return $tmp;
  }
}
if(!function_exists('getFeaturedImage')){  
  /**
   * getFeaturedImage of a post
   *
   * @param  mixed $postID
   * @param  mixed $size eg: 'thumbnail','medium','large','full'
   * @return string
   */
  function getFeaturedImage($postID=null, $size='medium', &$alt=NULL){
    $pID = 0;
    if($postID){
      $pID = $postID;
    } else {
      $pID = get_the_ID();
    }
    
    $thumbID = get_post_thumbnail_id( $pID);
    $alt = get_post_meta($thumbID, '_wp_attachment_image_alt', true); 
    $url = wp_get_attachment_image_src( $thumbID, $size );

    if($alt!==NULL) $alt = $alt;
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
  function youtubeEmbed($embed_video,$mode='normal',$properties="width='100%' height='100%' frameborder='0' allowfullscreen",$srcquery="autoplay=1&controls=0&html5=1&loop=1&mute=1&rel=0"){
    $new_embed = $embed_video;
    $tmp = array(
      'render' => '',
      'src' => '',
      'id' => ''
    );


    $new_embed = preg_replace('/'.'width=".*?"'.'/','', $new_embed);
    $new_embed = preg_replace('/'.'height=".*?"'.'/','', $new_embed);

    if(strpos($new_embed,'?')==false){

      $srcquery = '?'.$srcquery;
    }else{
      $srcquery = '&'.$srcquery;
    }
  
    preg_match('/'.'(src=".*?")'.'/',$embed_video,$match);

    if($match){
      $src = $match[1];

      $url = str_replace('src=','', $src);
      $url = str_replace('"','', $url);

      $tmp['src'] = $url;

      $yID = getYoutubeIdFromUrl($src);
      $tmp['id'] = $yID;

      $srcquery .= '&playlist='.$yID; // this allow it to loop

      $src = substr($src,0,-1);
      $src = $src.$srcquery.'"';
      $new_embed = preg_replace('/'.'src=".*?"'.'/',$src, $new_embed);
    }

    ob_start();
    if($mode=='background'){

      echo '<div class="video-background">';
      echo '<div class="video-foreground">';
      echo $new_embed;
      echo '</div>';
      echo '</div>';
      
    }else {

      echo '<div class="video-container">';
      echo $new_embed;
      echo '</div>';
      
    }
    $tmp['render'] = ob_get_clean();
    return $tmp;
  }


  function getYoutubeIdFromUrl($url) {
    $parts = parse_url($url);
    if(isset($parts['query'])){
        parse_str($parts['query'], $qs);
        if(isset($qs['v'])){
            return $qs['v'];
        }else if(isset($qs['vi'])){
            return $qs['vi'];
        }
    }
    if(isset($parts['path'])){
        $path = explode('/', trim($parts['path'], '/'));
        return $path[count($path)-1];
    }
    return false;
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
if(!function_exists('doCurl')){
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
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}

if(!function_exists('progressiveBG') && !function_exists('enableProgressiveBG')){

  function progressiveBG(){
    ?>
<script>

function initProgressive(className='progressive'){

  $elements = $('.'+className+':not(.enhanced)');
  for (let n = 0; n < $elements.length; n++) {
    initProgressiveSingle($('.'+ className)[n])
  }
}
function initProgressiveSingle(jEl){
    if(!jEl) return;

    var $el = $(jEl);
    var high = $el.data('high')

    if(high!==''){

      // Load hi-res
      var imgHigh = new Image();
  
      imgHigh.onload = function() {
        if($el.prop('tagName')=='IMG'){
          $el.attr('src',high);
        }else{
          $el.css('background-image',`url(${high})`)
        }
        $el.addClass('enhanced')
      };
      if (high) { imgHigh.src = high; }
    }else{
      $el.addClass('enhanced')
    }
  
}
</script>
<style>
.progressive { background-position: center;background-repeat: no-repeat; background-size: cover; transform: translateZ(0); will-change: transform;}
</style>
    <?php
  }
  function enableProgressiveBG(){
    add_action('wp_footer', 'progressiveBG');
    if(!function_exists('urbosa_progressive')){ function urbosa_progressive(){}}
  }

}

if(!function_exists('urbosaLoader')){
  function urbosaLoader($color='#000',$width="20"){
    $svg = '<svg width="'.$width.'" height="'.$width.'" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="'.$color.'">
          <g fill="none" fill-rule="evenodd">
              <g transform="translate(5 5)" stroke-width="5">
                  <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
                  <path d="M36 18c0-9.94-8.06-18-18-18">
                      <animateTransform
                          attributeName="transform"
                          type="rotate"
                          from="0 18 18"
                          to="360 18 18"
                          dur="1s"
                          repeatCount="indefinite"/>
                  </path>
              </g>
          </g>
      </svg>';
    return "data:image/svg+xml;base64,".base64_encode($svg);
  }
}
if(!function_exists('removePortFromPath')){
  function removePortFromPath($tmpPath){
    $p     = parse_url($tmpPath);
    $path = $p['scheme'].'://'.$p['host'].$p['path'];
    if(!empty($p['query'])) $path .= '?'.$p['query'];
    return $path;
  }
}
if(!function_exists('encodeDataImage')){
  function encodeDataImage($path,$removePort=true){
    
    $tmpPath = $path;
    if(empty($path)) return '';

    if($removePort) $tmpPath = removePortFromPath($tmpPath);
    $type = pathinfo($tmpPath, PATHINFO_EXTENSION);
    $data = file_get_contents($tmpPath);

    
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
  }
}
/* Allows you to set AJAX content loading.*/
if(!function_exists('ajaxContent')){
  function ajaxContent(
      $action,
      $contentSelector,
      $loadMoreSelector, 
      $postData = array(),
      $labels= array(
        'label_loading'=> 'Loading',
        'label_not_found'=> 'Nothing has been found',
      )){
    $defaultData = array(
      'initial_page' => isset($_GET['pg'])?$_GET['pg']:0,
      'action' => $action,
      'content_container_selector'=> $contentSelector,
      'load_more_selector' => $loadMoreSelector,
      'page' => 1, // this value will be changed
      'post'=>$postData,
      'busy'=> false,
      'found'=> null,
      'per_page'=> get_option('posts_per_page'),
      'label_loading' => $labels['label_loading'],
      'label_not_found' => $labels['label_not_found'],
    );
    ?>
    <script>var <?=$contentSelector?>_optionData= <?=json_encode($defaultData)?></script>
    <?php
  }
}
if(!function_exists('setQueryURL')){
  function setQueryURL( $params, $path='') {
    $url = $path;
    if(empty($path)){ $url = getCurrentURL(); }
    $url_parts = parse_url($url);
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $tmpParams);
        $params = $params + $tmpParams;
    } else if(empty($params)){
        $params = array();
    }
    $url_parts['query'] = http_build_query($params);
    $port = '';
    if(isset($url_parts['port'])){
      $port = ':'.$url_parts['port'];
    }
   
    if(function_exists('http_build_url')){
      $str = http_build_url($url_parts);
    }else{
      $str = $url_parts['scheme'] . '://' . $url_parts['host'] .$port. $url_parts['path'] . (empty($params)?'':'?') . $url_parts['query'];
    }
    return $str;
  }
}
if(!function_exists('getCurrentURL')){
  function getCurrentURL(){
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  }
}
/* 
  Mailchimp embedded form modification for custom response handling 
  MCJS callback is: mcFormCallback(result)
*/
if(!function_exists('mcForm')){
  function mcForm($htmlForm,$formID='mc_form_signup',$jsCallback='mcFormCallback'){
    $html = str_replace('/post?','/post-json?c=?&',$htmlForm);
    $html = str_replace('method="post','/method="get',$html);
    $html = str_replace('mc-embedded-subscribe-form',$formID,$html);
    $html = str_replace('&amp;','&',$html);
    $html = str_replace('class="validate"','class="validate '.$formID.'"',$html);
    echo $html;
    ?>
    <script>
    jQuery(document).ready(function($){
      $('#<?=$formID?>, .<?=$formID?>').submit(function(e){
        e.preventDefault()
        register_<?=$formID?>($('#<?=$formID?>'))
      })
    })
    function register_<?=$formID?>($form) {
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            cache       : false,
            dataType    : 'json',
            contentType: "application/json; charset=utf-8",
            error       : function(err) { 
              console.log("MC Error: Could not connect to the registration server. Please try again later."); 
            },
            success     : function(data) {
              <?=$jsCallback?>(data)
            }
        });
    }
    </script>
    <?
  }
}
if(!function_exists('urbosa_custom_logo')){
  function urbosa_custom_logo($is_src=true){
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    if(has_custom_logo()){
      if($is_src) return $logo[0]; 
      return '<img src="' . esc_url( $logo) . '" alt="' . get_bloginfo( 'name' ) . '">';
    }
    return '';
  }
}
if(!function_exists('urbosa_copy_folder')){
  function urbosa_copy_folder($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                urbosa_copy_folder($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
  } 
}

if(!function_exists('getVideoExtMimeType')){
    function getVideoExtMimeType($path) {
      $ext = pathinfo($path, PATHINFO_EXTENSION);
      $mime_map = [
          '3g2' => 'video/3gpp2'        ,                             
          '3gp' => 'video/3gp'          ,                             
          '3gp' => 'video/3gpp'         ,                             
          'avi' => 'video/avi'          ,                             
          'flv' => 'video/x-flv'        ,                             
          'mov' => 'video/quicktime'    ,                             
          'movie' => 'video/x-sgi-movie',                             
          'mp4' => 'video/mp4'          ,                             
          'mpeg' => 'video/mpeg'        ,                             
          'ogg' => 'video/ogg'          ,                             
          'webm' => 'video/webm'        ,                             
          'wmv' => 'video/x-ms-asf'     ,                         
      ];

      return isset($mime_map[$ext]) ? $mime_map[$ext] : '';
  }
}