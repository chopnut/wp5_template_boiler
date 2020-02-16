<?php

/* Debug variable */
if(!function_exists('debug')){
  function debug($data){
    $html  = '<pre style="padding: 15px; font-family: \'courier new\'; font-size: 12px; border: 1px dashed #800">';
    $html .= print_r($data, true);
    $html .= '</pre>';
    echo $html;
  }
}
/* Get post */
if(!function_exists('objectToArray')){
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
  function getPosts(
    $post_type='post', 
    $slug='',
    $acfFields = array(), 
    $categories = array(), 
    $perPage = -1, 
    $offset = 0,
    $metaArray = array(), 
    $orderby = 'menu_order', 
    $order = 'DESC'
    )
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
// Return or display the current template being included
if(!function_exists('getCurrentTemplate')){
  add_filter( 'template_include', 'var_template_include', 1000 );
  function var_template_include( $t ){
      $GLOBALS['current_theme_template'] = basename($t);
      return $t;
  }
  function getCurrentTemplate( $echo = false ) {
      if( !isset( $GLOBALS['current_theme_template'] ) )
          return false;
      if( $echo )
          echo $GLOBALS['current_theme_template'];
      else
          return $GLOBALS['current_theme_template'];
  }
}
// Return WP objects of children categories
if(!function_exists('getChildrenCategories')){
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

// getWidget based on the ID of the widget
if(!function_exists('getWidgetArray')){
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
      if(!isset($data['content'])){
        ob_start();
        do_shortcode(wpautop($data['text']));
        $content = ob_get_clean();
        $data['content'] = $content; 
      }
      $final[] = $data;
    }
    return $final;
  }
}
