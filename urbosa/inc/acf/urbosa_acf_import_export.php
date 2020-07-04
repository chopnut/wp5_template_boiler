<?php 

class Urbosa_ACF_Import_Export{
  var $back_up_folder = '';
  var $copy_folder = '';
  function __construct(){
    $this->copy_folder    = get_stylesheet_directory().'/inc/acf/copy';
    $this->back_up_folder = get_stylesheet_directory().'/inc/acf/backup';
  }
   function init(){
    if(function_exists('acf_get_field_group')){
      add_filter('acf/settings/save_json',  array($this,'urbosa_acf_save_json_folder'));
      add_action( 'acf/update_field_group', array($this,'urbosa_acf_field_group_copy'));
      add_action( 'acf/trash_field_group',  array($this,'urbosa_acf_field_group_trash'));
      add_action( 'acf/untrash_field_group', array($this,'urbosa_acf_field_group_untrash'));
    }
  }
  function process($actioned){
    if(function_exists('acf_get_field_group')){
      $themeStatus  = get_option('urbosa_theme_status');
      $themePath =  get_stylesheet_directory();
      $phpFile = $themePath.'/inc/acf/acf-auto.php';
      //---------------------------------------------------
      if($actioned){
        if(function_exists('acf_get_field_group')){
          if($themeStatus){ // to live   
          
            
            // output php file
            $content = $this->urbosa_acf_write_php();
            file_put_contents($phpFile, $content);
  
            // delete all fields permanently
            $this->urbosa_acf_delete_all_fields();

            if(isset($_GET['acf'])){
              wp_redirect( '/wp-admin');
              die;
            }
          }else{ // to dev
            // remove php file
            @unlink($phpFile);
  
            // reinitialize from copy/activate
            $this->urbosa_acf_import_from_json_to_db($this->copy_folder);
          }
        }
        
        if(!is_admin()){
          wp_redirect( home_url('/') );
          die();
        }
      }
      //---------------------------------------------------
      // This disable ACF
      if($themeStatus){
      }
    }
  }
  /* Helper function below */

  function  urbosa_acf_field_group_untrash($field_group){
    $this->urbosa_acf_write_json_field_group($field_group, $this->copy_folder);
    $this->urbosa_acf_write_json_field_group($field_group, $this->back_up_folder);

    // check for trash and remove it as new one been added.
    $trashJSON = $this->back_up_folder.'/'.$field_group['key'].'__trashed.json';
    if(file_exists($trashJSON)){
      @unlink($trashJSON);
    }
  }
  function  urbosa_acf_field_group_trash($field_group){
    $keyFile = str_replace('__trashed','',$field_group['key']).'.json';
    $path         = $this->copy_folder.'/'.$keyFile;
    @unlink($path);

    // check for active ones and remove it as this is now trash.
    $activeJSON = $this->back_up_folder.'/'.str_replace('__trashed','',$field_group['key']).'.json';
    if(file_exists($activeJSON)){
      @unlink($activeJSON);
    }
    $this->urbosa_acf_write_json_field_group($field_group, $this->back_up_folder);

  }
  function urbosa_acf_field_group_copy($field_group){
    $this->urbosa_acf_write_json_field_group($field_group, $this->copy_folder);
    $this->urbosa_acf_write_json_field_group($field_group, $this->back_up_folder);
  }
  function urbosa_acf_save_json_folder( $path ) {
    $path = get_stylesheet_directory() . '/inc/acf/default';
    return $path;
  }
  function urbosa_acf_write_php(){

    // // remove all jsons first
    $acfGroups =acf_get_field_groups();
		$str = "<?php if( function_exists('acf_add_local_field_group') ):" . "\r\n" . "\r\n";
    foreach ($acfGroups as $acf) {
      $field_group = acf_get_field_group( $acf['key'] );
      if( empty($field_group) ) continue;
        $field_group['fields'] = acf_get_fields( $field_group );
        $field_group = acf_prepare_field_group_for_export( $field_group );
        $code = var_export($field_group, true);			
        $str .= "acf_add_local_field_group({$code});" . "\r\n" . "\r\n";
    }
    $str .= "endif; ?>";
    return $str;
  }

  function urbosa_acf_write_json_field_group( $field_group, $path='' ) {
    $file = $field_group['key'] . '.json';
    $path = untrailingslashit( $path );
    if( !is_writable($path) ){

      return false;
    } 
		$field_group['fields'] = acf_get_fields( $field_group );
    $id = acf_extract_var( $field_group, 'ID' );
    $field_group = acf_prepare_field_group_for_export( $field_group );
    $field_group['modified'] = get_post_modified_time('U', true, $id, true);
    $pathFile = "{$path}/{$file}";
    $f = fopen($pathFile, 'w');
    fwrite($f, acf_json_encode( $field_group ));
    fclose($f);
    return true;
  }
  function urbosa_acf_delete_all_fields(){
    $acfGroups =acf_get_field_groups();
    foreach ($acfGroups as $acf) {
      acf_delete_field_group($acf['ID']);
    }
  }
  function urbosa_json_export_all_json($folder){
    $field_groups = acf_get_field_groups();
    foreach ($field_groups as $field_group) {
      $this->urbosa_acf_write_json_field_group($field_group, $folder);
    }
  }
  function urbosa_acf_trash_all_fields(){
    $acfGroups =acf_get_field_groups();
    foreach ($acfGroups as $acf) {
      acf_trash_field_group($acf['ID']);
    }
  }
  function urbosa_acf_import_from_json_to_db($path){
		$path = untrailingslashit( $path );
		if( !is_dir($path) ) return false;
		$dir = opendir( $path );
		if( !$dir ) return false;
		
	    while(false !== ( $file = readdir($dir)) ) {
			if( pathinfo($file, PATHINFO_EXTENSION) !== 'json' ) continue;

	    	$json = file_get_contents("{$path}/{$file}");
	    	if( empty($json) ) continue;
	    	$json = json_decode($json, true);
	    	$json['local'] = 'json';
        acf_import_field_group($json);
	        
	    }
	    return true;
  }
}

?>