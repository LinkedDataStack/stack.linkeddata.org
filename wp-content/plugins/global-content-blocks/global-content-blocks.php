<?php
	/*
		Plugin Name: Global Content Blocks
		Plugin URI: http://micuisine.com/content-block-plugins/
		Description: Create your own shortcodes to add HTML, PHP, forms, opt-ins, iframes, Adsense, code snippets, reusable objects, etc, to posts and pages. Ideal for adding reusable objects to your content or to preserve formatting of code that normally gets stripped out or reformatted by the editor. See the <a href="http://micuisine.com/content-block-plugins/global-content-blocks/" target="_blank">General Usage</a> page for complete instructions.
		Version: 2.1.4
		Author: Dave Liske
		Author URI: http://micuisine.com/content-block-plugins/
	*/
	
	define('GCB_VERSION','2.1.4');
	$current_version = get_option("gcb_db_version");
	
	require_once 'gcb/gcb.class.php';	
  
	gcb_check_update($current_version);
	
	/*
	* Installs the plugin
	*/
	function gcb_install() {
		//we do not remove the table, at least not yet    
		update_option("gcb_db_version", GCB_VERSION);    
	}
	
	function gcb_uninstall() {
		if(get_option("gcb_complete_uninstall","no")=="yes") {
		
			global $wpdb;
			$table_name = $wpdb->prefix . "gcb";
			
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				gcb_remove_table();				
			}
      
			  delete_option("gcb_db_version");
			  delete_option("gcb_complete_uninstall");
			  
			  //remove all entries
			  foreach(gcb::get_entries() as $entry) {gcb::delete_entry($entry['id']);}
			  
			  //remove the map
			  delete_option(gcb::$ENTRIES_KEY);
      
		}    
	}
  
	function gcb_remove_table() {
		//We are not removing the table for now
		return;
	}
	
	function gcb_check_update($current_version=0) {
		//make sure we have a value here
		if($current_version==0) {
			$current_version = get_option("gcb_db_version");
		}
		if(version_compare($current_version, GCB_VERSION)<0) {
			//prompt the user 
			gcb_migrate_to_options();
			gcb_remove_table();		  
			update_option("gcb_db_version",GCB_VERSION);
		}    
	}
  
	function gcb_migrate_to_options() {
		//do we have the table ?
		global $wpdb;
		$table_name = $wpdb->prefix . "gcb";
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			
			//did we alredy imported the old values from this table or some other source ?
			if(get_option(gcb::$ENTRIES_KEY) === FALSE) {
				//we did not, migrate
				//grab the current entries
				$entries = $wpdb->get_results("select * from ".$table_name );          
				$entries_map = array();
          
				if(is_array($entries) && count($entries)) {
					
					foreach($entries as $entry) {
						update_option("gcb_".$entry->id,serialize((array) $entry));
						$entries_map[] = $entry->id;
					}
				}          
				//save the map
				update_option(gcb::$ENTRIES_KEY,serialize($entries_map));
			}
		}
	}
	
	function gcb_add_submenu() {
		$gcb_page = add_menu_page( "Global Content Blocks", "Global Content Blocks", "publish_pages", "global-content-blocks", "gcb_submenu");
		add_action( "admin_print_scripts-$gcb_page", 'gcb_loadjs_admin_head',5 );
	}
	
	function gcb_loadjs_admin_head() {
		wp_enqueue_script('gcb_uni_script', plugins_url( 'resources/extra/extra.js', __FILE__ ));
	}
	
	/* function prefix_add_gcb_stylesheet() {
		wp_register_style( 'prefix-gcb-style', plugins_url( 'css/style.css', __FILE__ ));
		wp_register_style( 'prefix-gcb-custom-style', plugins_url( 'css/customstyle.css', __FILE__ ));
		wp_enqueue_style( 'prefix-gcb-style' );
		wp_enqueue_style( 'prefix-gcb-custom-style' );
	} */
	
	function gcb_submenu() {  
    	$msg = "";
		
		if(isset($_POST["gcb_delete"])) {
			if(isset($_POST["gcb_del"]) && is_array($_POST["gcb_del"])) {
      
				foreach($_POST["gcb_del"] as $bd) {
					gcb::delete_entry(intval($bd));
				}        
				//refresh the map
				gcb::refresh_map();        
				$msg = "Entry deleted.";
			}
		}		
		if(isset($_POST["gcb_unin"])) {
			
			if(isset($_POST["ch_unin"])) {
				update_option("gcb_complete_uninstall","yes");
			}
			else {
				update_option("gcb_complete_uninstall","no");
			}
		}		
		if(isset($_POST["gcb_import"])) {
			//importing files
			$msg = gcb_import();
		}		
		if(isset($_POST["gcb_save"])) {
			//saving files
			$msg = gcb_saverecord();	
		}
		
		if(isset($_POST["gcb_updateshortcode"])) {
			//saving files
			$msg = gcb_saverecord();
			// **** Set update flags
			$udr = "update";
			$udid = $_POST["update_it"] ;
		}		
		echo gcb::main_page($msg, $udr, $udid);
	}
	
	function gcb_settings_submenu() {  
		echo gcb::main_page($msg, "settings", $udid);
	}
	
	function gcb_saverecord() {		
		$name = $_POST["gcb_name"];
		$value = (htmlspecialchars($_POST['gcbvalue'])); 
      
		$entry_data = array(
			"name"        =>  $name,
			"description" =>  (htmlspecialchars($_POST['gcb_description'])),
			"value"       =>  $value,
			"custom_id"   =>  (htmlspecialchars(sanitize_title_with_dashes($_POST['gcb_custom_id']))),
			"type"        =>  (htmlspecialchars($_POST['gcb_type'])), 
		);

		if(strlen($name) && strlen($value)) {
			
			if(isset($_POST["update_it"])) {					
				gcb::update_entry($entry_data,intval($_POST["update_it"]));          
				$msg = "Entry updated.";
			}
			else {          
				gcb::add_entry($entry_data);					
				$msg = "Entry inserted.";
			}
		}
		else {
				$msg = "Name and Content are mandatory.";
			}		
		return $msg;
	}
	
	
	function gcb_import() {		
		if(isset($_FILES["gcb_import_file"]["tmp_name"]) && strlen($_FILES["gcb_import_file"]["tmp_name"])) {
    
			$text = file_get_contents($_FILES["gcb_import_file"]["tmp_name"]);
			$entries1 = explode("\r\n",$text);
			$entries = array();
			
			foreach($entries1 as $e1) {
				$row = explode("<;>",$e1);
				$entries[] = array(
					"name"=>  (base64_decode($row[0])),
					"description"=>  (base64_decode($row[1])),
					"value"=> (base64_decode($row[2])),
					"type"=>  (base64_decode($row[3]))
				);
      
				if(isset($row[4])) {
					$entries[count($entries)-1]["custom_id"] = (sanitize_title_with_dashes(base64_decode($row[4])));
				} else {
				$entries[count($entries)-1]["custom_id"] = "";
				}      
			}		
			foreach($entries as $e) {      
				gcb::add_entry($e);       
			}			
			return "Imported ".count($entries)." blocks.";			
		} else {
			return "Please Make sure you have a file uploaded.";
		}
	}
	
	function gcb_shortcode_replacer($atts, $content=null, $code="") {
		$a = shortcode_atts( array('id' => 0), $atts );
		
		if((is_numeric($a["id"]) && $a["id"]==0) || (!strlen($a["id"]))) return "";
		
		return gcb($a["id"],$atts);
	}
	
	/*
		* The processing function can also receive a number of arbitrary parameters, that we'll pass along and replace into the content
	*/
	function gcb($id,$attributes=array()) {	    
		//determine condition, since we can fetch blocks by 2 types of ids
		if(is_numeric($id)) {     
			$entry = gcb::get_entry_by_id(intval($id));
		} else {
			$entry = gcb::get_entry_by_custom_id($id);
		}		
    
		if(is_array($entry)) {			
			$content = htmlspecialchars_decode(stripslashes($entry['value']));
			
			//process the attributes
			if(is_array($attributes)&& count($attributes)){
				foreach($attributes as $attribute_key=>$attribute_value){
					$content = str_replace("%%".$attribute_key."%%",$attribute_value,$content);
				}
			}			
			
			if($entry['type'] == "php") {
				//execute the php code
				ob_start();
				$result = eval(" ".$content);
				$output = ob_get_contents();
				ob_end_clean();
				
				return apply_filters('gcb_block_output', do_shortcode($output . $result));//run the shortcodes as well
			}
			elseif($entry['type'] == "html") {   // alloyphoto: enable PHP code in < ?php ... ? > tags inside blocks
				ob_start();
				eval("?>$content<?php ");
				$output = ob_get_contents();
				ob_end_clean();
				
				return apply_filters('gcb_block_output', do_shortcode($output));//run the shortcodes as well
			}
			else {
				return apply_filters('gcb_block_output', do_shortcode($content));//make sure we also run the shortcodes in here
			}
		}
		else
		{   return "";    }
	}
		
	if (!function_exists("gcb_settingslink")) {
		
		function gcb_settingslink( $links, $file ){
			static $this_plugin;
			
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename(__FILE__);
			}	
			if ( $file == $this_plugin ){
				$settings_link = '<a href="options-general.php?page=global-content-blocks">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
	}
	
	// include settings page code
	
	/**
		* Hooks
	*/
	register_activation_hook(__FILE__,'gcb_install');
	register_deactivation_hook(__FILE__,'gcb_uninstall');
	add_action('admin_menu', 'gcb_add_submenu',5);
	// add_action( 'wp_enqueue_scripts', 'prefix_add_gcb_stylesheet' );
	add_shortcode('contentblock', 'gcb_shortcode_replacer');
	add_action('init', 'gcbStartSession', 1);
	add_action('wp_logout', 'gcbEndSession');
	add_action('wp_login', 'gcbStartSession');

	function gcbStartSession() {
		if(!session_id()) {
			session_start();
		}
	}

	function gcbEndSession() {
		if(session_id()) {
			session_destroy ();
		}
	}
	
	// Load the custom TinyMCE plugin
	function gcb_mce_external_plugins( $plugins ) {
		$plugins['gcbplugin'] = plugins_url( 'resources/tinymce/editor_plugin.js', __FILE__ );
		
		return $plugins;
	}
	
	function gcb_mce_buttons( $buttons ) {
		array_push( $buttons,"|","gcb");

		return $buttons;
	}
	
	function gcb_addbuttons() {
		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )

		return;

		// Register editor button hooks
		if ( get_user_option('rich_editing') == 'true') {     
			add_filter( 'mce_external_plugins','gcb_mce_external_plugins',3);
			add_filter( 'mce_buttons', 'gcb_mce_buttons',3);
		}
	}
	
	function myformatTinyMCE($in) {
		// Prevent editor cleanup, within HTNL5 specs
		$in['verify_html']=false;
		$opts = '*[*]';
		$in['paste_word_valid_elements'] = $opts;
		$in['valid_elements'] = $opts;
		$in['extended_valid_elements'] = $opts;
		
		return $in;
	}

	function gcb_my_refresh_mce($ver) {
		$ver += 3;
		return $ver;
	}  

	// init process for button control
	add_action('init', 'gcb_addbuttons',3);
	add_filter('tiny_mce_version', 'gcb_my_refresh_mce',3);
	add_filter('plugin_action_links', 'gcb_settingslink', 10, 2 ); 
	add_filter('tiny_mce_before_init', 'myformatTinyMCE' );


