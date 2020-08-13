<?php 

class Urbosa_Custom_Type  
{
  /* common */
  var $name = '';
  var $type = 'post';
  var $label = '';
  var $ucFirstKeys = array(
    'name',
    'singular_name',
    'add_new_item',
    'edit_item',
    'new_item',
    'all_items',
    'view_item',
    'search_items',
    'menu_name'
  );
  // ----- custom post type -------
  var $labels = array(
    'name'               =>  '{name}s',
    'singular_name'      =>  '{name}',
    'search_items'       =>  'Search {name}s',
    'add_new'            =>  'Add New',
    'add_new_item'       =>  'Add New {name}',
    'edit_item'          =>  'Edit {name}',
    'new_item'           =>  'New {name}',
    'all_items'          =>  '{name}s',
    'view_item'          =>  'View {name}',
    'not_found'          =>  'No {name}s found',
    'not_found_in_trash' =>  'No {name}s found in the Trash', 
    'parent_item_colon'  => ',',
    'menu_name'          => '{name}s'
  );
  var $args = array(
    'labels'        => array(),
    'description'   => 'Holds our {name}s and {name} specific data',
    'public'        => true,
    'publicly_queryable' => true,
    'hierarchical' => false, // parent/child relationship
    'menu_position' => 5,
    'supports'      => array( 
      'title', 
      'editor', 
      'thumbnail', 
      'excerpt', 
      'comments',
      'custom-fields',
      'author',
      'page-attributes' 
    ),
    'has_archive'   => true,
    'menu_icon' => '', // dashicons or URL to the icon image
    'exclude_from_search'=> true,
    'show_ui' => true,
    'show_in_menu' => true, // main menu to put it under
    'show_in_nav_menus' => true,
    'show_in_menu_string' => '',
    'show_in_rest' => true,// whether allow for REST API
  );
  // contextual help
  var $page1_main = '';
  var $page2_edit = '';

  //----- custom taxonomy ------.
  var $custom_taxonomy = false; 
  var $tax_label = '';
  var $tax_labels = array(
    'name'              => '{tax_name} Categories',
    'singular_name'     => '{tax_name} Category',
    'search_items'      => 'Search {tax_name} Categories',
    'add_new_item'      => 'Add New {tax_name} Category',
    'edit_item'         => 'Edit {tax_name} Category', 
    'all_items'         => 'All {tax_name} Categories',
    'parent_item'       => 'Parent {tax_name} Category',
    'parent_item_colon' => 'Parent {tax_name} Category:',
    'update_item'       => 'Update {tax_name} Category',
    'new_item_name'     => 'New {tax_name} Category',
    'menu_name'         => '{tax_name} Categories'
  );
  var $tax_args = array(
    'labels'        => array(),
    'description'   => 'Holds our {name}s and {name} specific data',
    'public'        => true,
    'publicly_queryable' => true,
    'hierarchical' => false,                // parent/child relationship
    'show_ui' => true,
    'show_in_menu' => true,                   // main menu to put it under
    'show_in_nav_menus' => true,
    'show_in_rest' => true,                 // whether allow for REST API
    'show_in_quick_edit' => true,
    'meta_box_cb' => false,                 // set a string for callback to show in metabox
    'capabilities' => array(
      'manage_terms' => 'manage_categories',
      'edit_terms' => 'manage_categories',
      'delete_terms' => 'manage_categories',
      'assign_terms' => 'edit_posts'),
    'rewrite' => true,                      // (bool|array) allow URL to change, flush after creating
    'query_var' => true,                    // will change {query_name}=term_slug
  );

  function __construct($name){
    $this->name = strtolower($name);
  }
  function init(){
    if(!empty($this->name)){
      $this->merge();
      switch($this->type){
        case 'post':
          register_post_type( $this->name, $this->args );
          if($this->custom_taxonomy){
            // (taxonomy_name , post_type , args)
            register_taxonomy( $this->name.'_category', $this->name, $this->tax_args );
          }
          if(!empty($this->page1_main) && !empty($this->page2_edit)){
            add_action( 'contextual_help', array($this,'contextual_help'), 10, 3 );
          }
        break;
        case 'taxonomy':
          if($this->custom_taxonomy){
            // if set as taxonomy type this becomes the post
            register_taxonomy( $this->name, $this->custom_taxonomy, $this->tax_args );
          }
        default:
      }
    }else{
      echo 'Urbosa_Custom_Type: Name is empty.';
    }
  }
  function merge(){
    /* custom post */
    $tmpLabels = $this->labels;
    $tmpArgs   = $this->args;
    
    foreach($this->labels as $key=>$value){
      $name = $this->name;
      if(!empty($this->label)) $name = $this->label;
      foreach($this->ucFirstKeys as $ucKey){
        if($ucKey == $key){
          $name = ucfirst($name);
          break;
        }
      }
      $tmpLabels[$key] = str_replace('{name}', $name, $this->labels[$key]);
    }
    $tmpArgs['labels'] = $tmpLabels;
    $tmpArgs['description'] =  str_replace('{name}', $this->name, $this->args['description'] );
    $this->args = $tmpArgs;

    /* taxonomy */
    $tmpTaxLabels = $this->tax_labels;
    $tmpArgs      = $this->tax_args;
    foreach($this->tax_labels as $key=>$value){
      $name = $this->name;
      if(!empty($this->tax_label)) $name = $this->tax_label;

      foreach($this->ucFirstKeys as $ucKey){
        if($ucKey == $key){
          $name = ucfirst($name);
          break;
        }
      }
      $tmpTaxLabels[$key] = str_replace('{tax_name}', $name, $this->tax_labels[$key]);
    }
    $tmpArgs['labels'] = $tmpTaxLabels;
    $this->tax_args = $tmpArgs;

  }
  function getArgs(){
    $this->merge();
    return array('custom' => $this->args, 'taxonomy'=> $this->tax_args);
  }
  function contextual_help( $contextual_help, $screen_id, $screen ) { 
    if ( $this->name == $screen->id ) {
      $contextual_help = $this->page1_main;
    } elseif ( 'edit-'.$this->name == $screen->id ) {
      $contextual_help = $this->page2_edit;
    }
    return $contextual_help;
  }
  /* helpers */
  function _set_args($val,$key){
    $argsName = 'args';
    if($this->type=='taxonomy'){
      $argsName = 'tax_args';
    }
    $this->$argsName[$key]=$val;
  }
  function _prefix_disable_gutenberg($current_status, $post_type){
      // Use your post type key instead of 'product'
      if ($post_type === $this->name) return false;
      return $current_status;
  }
  /* semantic callable settings */
  function set_as($type){$this->type=$type; }

  /* common */
  function set_label($val){ $this->label = $val;}
  function set_taxonomy($val=true){ $this->custom_taxonomy = $val; }
  function set_tax_label($val){ $this->tax_label = $val;}
  
  function set_archive($val){ $this->_set_args($val,'has_archive');}
  function set_menu_position($val){ $this->_set_args($val,'menu_position');}
  function set_description($val){ $this->_set_args($val,'description');}
  function set_menu_under($val){ $this->_set_args($val,'show_in_menu');}

  /* post */
  function set_support($val){ $this->args['supports'] = $val;}
  function set_help($page1_main, $page2_edit){ 
    $this->page1_main = $page1_main;
    $this->page2_edit = $page2_edit;
  }
  function set_icon($val) { $this->args['menu_icon'] = $val; }
  function disable_editor(){
    add_filter('use_block_editor_for_post_type', array($this,'_prefix_disable_gutenberg'), 10, 2);
    $tmpSupports = $this->args['supports'];
    if (($key = array_search('editor', $tmpSupports)) !== false) {
      unset($tmpSupports[$key]);
    }
    $this->set_support($tmpSupports);
  }
  
}