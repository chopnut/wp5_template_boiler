<?php 

class Urbosa_Custom_Type  
{
  // ----- custom post type -------
  var $name = '';
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
    'menu_position' => 5,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments','custom-fields','author','page-attributes' ),
    'has_archive'   => true,
    'menu_icon' => '', // dashicons or URL to the icon image

    //defaults below
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_nav_menus' => true,
    'show_in_rest' => true,// whether allow for REST API
    'exclude_from_search'=> true,
    'show_in_menu' => '', // main menu to put it under
    'show_in_menu_string' => '',
    'hierarchical' => false, // parent/child relationship
  );
  // contextual help
  var $page1_main = '';
  var $page2_edit = '';

  //----- custom taxonomy ------.
  var $custom_taxonomy = false; 
  var $tax_label = '';
  var $taxLabels = array(
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
  var $taxArgs = array(
    'labels' => array(),
    'hierarchical' => true,
  );

  function __construct($name){
    $this->name = strtolower($name);
  }
  function initialize(){
    if(!empty($this->name)){
      $this->merge();

      register_post_type( $this->name, $this->args );

      if($this->custom_taxonomy){
        register_taxonomy( $this->name.'_category', $this->name, $this->taxArgs );
      }

      add_action( 'contextual_help', array($this,'contextual_help'), 10, 3 );
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
    $tmpTaxLabels = $this->taxLabels;
    $tmpArgs      = $this->taxArgs;
    foreach($this->taxLabels as $key=>$value){
      $name = $this->name;
      if(!empty($this->taxLabel)) $name = $this->taxLabel;

      foreach($this->ucFirstKeys as $ucKey){
        if($ucKey == $key){
          $name = ucfirst($name);
          break;
        }
      }
      $tmpTaxLabels[$key] = str_replace('{tax_name}', $name, $this->taxLabels[$key]);
    }
    $tmpArgs['labels'] = $tmpTaxLabels;
    $this->taxArgs = $tmpArgs;

  }
  function getArgs(){
    $this->merge();
    return array('custom' => $this->args, 'taxonomy'=> $this->taxArgs);
  }
  function contextual_help( $contextual_help, $screen_id, $screen ) { 
    if ( $this->name == $screen->id ) {
      $contextual_help = $this->page1_main;
    } elseif ( 'edit-'.$this->name == $screen->id ) {
      $contextual_help = $this->page2_edit;
    }
    return $contextual_help;
  }
  /* semantic callable settings */

  function set_archive($val){ $this->args['has_archive'] = $val;}
  function set_label($val){ $this->label = $val;}
  function set_tax_label($val){ $this->tax_label = $val;}
  function set_menu_position($val){ $this->args['menu_position'] = $val;}
  function set_description($val){ $this->args['description'] = $val;}
  function set_supports($val){ $this->args['supports'] = $val;}
  function set_help($page1_main, $page2_edit){ 
    $this->page1_main = $page1_main;
    $this->page2_edit = $page2_edit;
  }
  function set_menu_under($val) { $this->args['show_in_menu']= $val;}
  function set_icon($val) { $this->args['menu_icon'] = $val; }
  function with_taxonomy(){ $this->custom_taxonomy = true; }
}