<?php
	
	class gcb
	{
		public  $table_name;
		
    public static $ENTRIES_KEY = "gcb_entries";		
		
		function  __construct() {
			global $wpdb;
			$this->table_name=$wpdb->prefix . "gcb";
		}
    
    public static function get_entries_map() {
      $map = get_option(self::$ENTRIES_KEY,"");
      if(strlen($map)) {
        $m = maybe_unserialize($map);
        if(is_array($m)) {
          return $m;
        }
      }
      return array();
      }
    
    
    public static function get_entries() {
      //get the map
      $map = self::get_entries_map();
      $entries = array();
      if(is_array($map)) {
        foreach($map as $entry) {
          $this_entry = get_option("gcb_".$entry,"");
          if(strlen($this_entry)) {
            $entries[] = maybe_unserialize($this_entry);
          }
        }
      }
      
      return $entries;
      
     }
     
     public static function get_entry_by_id($id) {
      return maybe_unserialize(get_option("gcb_".$id,""));       
     }
     
     public static function get_entry_by_custom_id($id) {
      $entries = self::get_entries();
      foreach($entries as $entry) {
        if($entry['custom_id'] == $id) {
          return $entry;
        }
      }
      //default
      return "";  
     }
     
     /**
     * Adds a new entry, returns the inserted ID
     */
     public static function add_entry($data) {
      
      $map = self::get_entries_map();
      
      if(count($map)) {
        $new_id = max($map) + 1;
      } else {
        $new_id = 1;
      }
      
      //add this id to the data
      $data["id"] = $new_id;
      
      update_option("gcb_".$new_id,serialize($data));
          
      //update the map as well
      $map[] = $new_id;
      update_option(gcb::$ENTRIES_KEY,serialize($map));      
      
      return $new_id;
     }
     
     /**
     * Updates an entry
     */
     public static function update_entry($data,$id) {
      //make sure the ID is in there
      $data["id"] = $id;
      
      update_option("gcb_".$id,serialize($data));
      
     }
     
     public static function delete_entry($id,$refresh_map = false) {
      
      delete_option("gcb_".$id);
      
      if($refresh_map) {
        self::refresh_map();
      }
      
     }
     
     public static function refresh_map() {
      $map = self::get_entries_map();
      $new_map = array();
     
      
        foreach($map as $entry) {
          $this_entry = get_option("gcb_".$entry,"");
          if(strlen($this_entry)) {
            $e = maybe_unserialize($this_entry);
            if(is_array($e)) {
              $new_map[] = $entry;
            }
          }
        }
        
      update_option(gcb::$ENTRIES_KEY,serialize($new_map));         
      
     }
     
     
     
     
    public static function get_available_types() {
      
      return array(
			"other"=>array("vname"=>"General","img"=>"gcb.png","help"=>"Content block"),
			"adsense"=>array("vname"=>"Adsense","img"=>"adsense.png","help"=>"Adsense code"),
			"code"=>array("vname"=>"Code","img"=>"code.png","help"=>"Programming code"),
			"form"=>array("vname"=>"Form","img"=>"form.png","help"=>"General form"),
			"html"=>array("vname"=>"HTML","img"=>"html.png","help"=>"Raw HTML code"),
			"iframe"=>array("vname"=>"Iframe","img"=>"iframe.png","help"=>"Iframe code"),
			"optin"=>array("vname"=>"Opt-In form","img"=>"optin.png","help"=>"Opt-in form code"),
			"php"=>array("vname"=>"PHP Code","img"=>"php.png","help"=>"PHP code (without <?php, <?, ?> tags)"),
			);
      
      
    }  



    public static function prompt_table_deletion() {
      ob_start();
      ?>
      <div class="wrap">
        <h2>Global Content Blocks</h2>
        <p>
						Welcome to the new version of Global Content Blocks!
            <br /><br />
            This update includes some significant improvements to the way content blocks are handled and stored.
            <br />
            Before you do the update, just to be on the safe side, we would <strong>STRONGLY RECOMMEND</strong> that you backup your existing content blocks.
            <br />
            Clicking on the button below will export your existing content blocks to a file on your computer from where they can be re-imported in the unlikely event that something goes wrong with the update.
            <br /><br /><br />
            
            <a class="button-primary" href="options-general.php?page=global-content-blocks&prompt_v2_download=1"> Get Backup of all entries </a>
            <br /><br />
            or <a href="options-general.php?page=global-content-blocks&prompt_v2=1">continue without backup</a>            
        </p>
      </div>
      <?php
      $r = ob_get_contents();
			ob_end_clean();
			return $r;
      
    }  

    public static function show_backup_download() {
      ob_start();
      ?>
      <div class="wrap">
        <h2>Global Content Blocks</h2>
        <meta http-equiv="refresh" content="3;URL='<?php echo WP_PLUGIN_URL;?>/global-content-blocks/gcb/gcb_export.php?gcb=all'">
        <p>
						Your download should start in a few seconds.
            <br />
            If it does not, please <a href="<?php echo WP_PLUGIN_URL;?>/global-content-blocks/gcb/gcb_export.php?gcb=all"> Click Here </a>
            <br /><br />
            Once downloaded, please click on the button below to continue the update.
            <br /><br />
            <a class="button-primary" href="options-general.php?page=global-content-blocks&prompt_v2=1">Continue to the plugin</a>            
        </p>
      </div>
      <?php
      $r = ob_get_contents();
			ob_end_clean();
			return $r;
      
    }
		
		public static function main_page($message = "") {
			global $wpdb;
			$view= isset($_GET['view']) ? $_GET['view']:"";
			$r = "";
			
			//define the available types,and their image
			$available_types = self::get_available_types();
			
			ob_start();
		?>
		<div class="wrap">
			
			<div class="gcb_sponsors" style="padding: 0.5em; margin-bottom: 0.5em; line-height: 1.1em; background-color: white; color: #F9F9F9;">
				<iframe src="http://wpxpert.com/gcbmessage/gcbtop.htm" align="middle" scrolling="no" width="744" frameborder="0" height="106"></iframe> 
			</div>
			
			<h2>Global Content Blocks</h2>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td style="vertical-align:top;">
						
						<p class="description">
						Global Content Blocks let you  add reusable code snippets, text, PHP or HTML including forms, opt-in boxes, Adsense,  etc, to pages and posts by either inserting a shortcode or the complete content block. It is ideal for inserting reusable objects into your content or to prevent the WordPress editor from stripping out your code or otherwise changing your formatting. </p>
						<p class="description">You can also use your own variables, WordPress global variables and other shortcodes within Content Blocks, modify the output, use shortcodes in themes and widgets, nest shorcodes and a whole lot more. See <a href="http://wpxpert.com/category/gcb-advanced-usage/" target="_blank">Advanced Usage Instructions</a> for more information.</p>
						<p class="description">You may also Export selected blocks and import them into Global Content Blocks on another site using the Import function. </p>
						<p class="description">Content Blocks are added to Pages and Posts by clicking on the <img border="0" src="<?php echo WP_PLUGIN_URL;?>/global-content-blocks/resources/tinymce/block20.gif" /> icon in the editor toolbar and selecting the block to insert, or by adding the shortcode  directly into the content in Visual or HTML mode.
						Using the icon also gives you the option to insert the complete content block instead of the shortcode.</p>
						<p class="description">Please let me know of any other uses you find for the plugin, suggestions for improvements or feature requests by leaving a comment on the <a href="http://wpxpert.com/global-content-blocks">plugin homepage</a>. </p>
<br />
						<div class="tablenav">
							<div class="alignleft actions">
								<a class="button" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=global-content-blocks&view=manage';?>">Manage Blocks</a>&nbsp;&nbsp;&nbsp;
                                <a class="button" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=global-content-blocks&view=addnew';?>">Add a New Content Block</a> 
							</div>	
							<div style="float:right">	
								<a href="http://wpxpert.com/category/gcb-advanced-usage/" target="_blank" class="button">Advanced Usage Instructions</a>
							</div>
							
						</div>
						<?php if ( strlen($message) ) : ?>
						<div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
						<?php endif; ?>
						<?php if ($view == "" OR $view == "manage") : ?>
						<?php
							
							$like_list = self::get_entries();
						?>
            
						<form action="" name="del_gcb" method="POST" onsubmit="return confirm('Are you sure?');">
							<table class="widefat" style="margin-top: .5em">
								<thead>
									<tr>
										<td colspan="4">
											
											<input tabindex="15" type="submit" name="gcb_delete" class="button-primary" value="Delete selected" />
											<a class="gcb_export button-primary" href="#">Export selected</a>
										</td>
									</tr>
									<tr>
										<th scope="col" class="check-column"><input type="checkbox" name="gcb_toggle" id="gcb_toggle" /></th>
										<th scope="col" width="2%"><center>ID</center></th>
										<th scope="col" width="30%">Name</th>
										<th scope="col" width="40%">Description</th>
										<th scope="col" width="10%">Type</th>
										<th scope="col" width="15%">Shortcode(s)</th>
									</tr>
								</thead>
								<?php if(count($like_list)): ?>
								<?php $class='';?>
								<?php foreach($like_list as $entry) : 
               
                  if(!is_array($entry)) continue;
                  
                  
                ?>
								<?php
									if($class != 'alternate') {$class = 'alternate';} else {$class = '';}
								?>
								<tr class='<?php echo $class; ?>'>
									<th scope="row" class="check-column"><input class="gcb_del_cb" type="checkbox" name="gcb_del[]" value="<?php echo $entry['id']; ?>" /></th>
									<td>                  
                    <center><?php echo $entry['id'];?></center>                    
                  </td>
									<td>
                    <strong>
                      <a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=global-content-blocks&view=update&edid='.$entry['id'];?>" title="Edit"><?php echo stripslashes($entry['name']);?></a>
                    </strong>
                    
                  
                  </td>
									<td><?php echo stripslashes(html_entity_decode($entry['description']));?></td>
									<td><?php echo $available_types[$entry['type']]["vname"];?></td>
									<td>
                  [contentblock id=<?php echo $entry['id']; ?>]
                  <?php if(strlen($entry['custom_id'])):?>
                   <br />
                  [contentblock id=<?php echo $entry['custom_id']; ?>]
                  <?php endif;?>
                  </td>
								</tr>
								<?php endforeach; ?>
								<tr>
									<td colspan="4">
										<input tabindex="15" type="submit" name="gcb_delete" class="button-primary" value="Delete selected" />
										<a class="gcb_export button-primary" href="#">Export selected</a>
									</td>
								</tr>
								<?php else: ?>
								<tr id='no-groups'>
									<th scope="row" class="check-column">&nbsp;</th>
									<td colspan="4"><em>No Content Blocks yet!</em></td>
								</tr>
								<?php endif;?>
							</table>
						</form>
						<div style="height:50px;">&nbsp;</div>
						
						<form name="gcb_import" action="" method="POST" enctype="multipart/form-data">
							<table class="widefat" style="margin-top: .5em">
								<thead>
									<tr valign="top">
										<th colspan="2" bgcolor="#DDD">Import Global Content Blocks</th>
									</tr>
									
									<tr>
										<td>
											<input tabindex="16" type="submit" name="gcb_import" class="button-primary" value="Import" />
										</td>
										<th  scope="row" width="85%" style="font-weight:normal;">
											<input type="file" name="gcb_import_file" />
										</th>
										
									</tr>
									
								</thead>
							</table>
						</form>
						
						<div style="height:50px;">&nbsp;</div>
						<form name="complete_uninstall" action="" method="POST">
							<table class="widefat" style="margin-top: .5em">
								<thead>
									<tr valign="top">
										<th colspan="2" bgcolor="#DDD">Uninstall Global Content Blocks</th>
									</tr>
									
									<tr>
										<td>
											<?php
												$check_uninstall =  (get_option("gcb_complete_uninstall","no")=="yes") ? "checked":"id='unin'";
											?>
											<input tabindex="15" type="submit" name="gcb_unin" class="button-primary" value="Update" />
											&nbsp;
											<input type="checkbox" name="ch_unin" value="1" <?php echo $check_uninstall;?> />
											
										</td>
										<th  scope="row" width="85%" style="font-weight:normal;">
											Checking this box will remove all content blocks and the table from the database.
											Only use if permanently uninstalling the Global Content Blocks plugin.
										</th>
										
									</tr>
									
								</thead>
							</table>
						</form>
						
						<?php elseif($view=="addnew" || $view=="update"): ?>
						<h3><?php echo $view=="addnew" ? "Add a New":"Edit";?> Content Block</h3>
												
						<form method="post" action="options-general.php?page=global-content-blocks">
							<?php $val = "";?>
							<?php if($view=="update"):?>
							<?php
								$edit_id = intval($_GET["edid"]);
								$record = maybe_unserialize(get_option("gcb_".$edit_id));
								
								//$val = $record->type="php" ? base64_decode($record->value):$record->value;
							?>
							<input type="hidden" name="update_it" value="<?php echo $edit_id;?>" />
							<?php else: ?>
							<?php
								//define a empty array(or one with default values)
								$record = array();
								$record['name'] = "";
								$record['type'] = "";
								$record['description'] = "";
								$record['value'] = "";
                $record['custom_id'] = "";
							?>
							<?php endif; ?>
							<p style="color:#ff0000;">* required</p>
							
						  <table class="widefat" style="margin-top: .5em">
								<thead>
									<tr valign="top">
										<th colspan="4" bgcolor="#DDD">Enter the details of your Content Block and click the Save button</th>
									</tr>
									
									<tr>
										<th scope="row" width="200">Name (short title) <span style="color:#ff0000;">*</span></th>
										<td colspan="3">
											
											<input tabindex="1" name="gcb_name" type="text" size="68" maxlength="36" class="search-input" value="<?php echo stripslashes($record['name']);?>" autocomplete="off" />
										</td>
									</tr>
                  
                  <tr>
										<th scope="row" width="200">Custom shortcode string</th>
										<td colspan="3">
											
											<input tabindex="2" name="gcb_custom_id" id="gcb_custom_id" type="text" size="68" maxlength="36" class="search-input" value="<?php echo stripslashes($record['custom_id']);?>" autocomplete="off" />
                      &nbsp;
                      (optional, if you want to use a custom name instead of a number)
										</td>
									</tr>
									
									<tr>
										<th scope="row" width="200">Type <?php
											$actual_type = $view=="addnew" ? "other":$record['type'];
										?>
										<img id="type_img" style="float:right;padding-top:0px;padding-right:10px;" border="0" src="<?php echo WP_PLUGIN_URL;?>/global-content-blocks/resources/i/<?php echo $available_types[$actual_type]["img"]?>" /></th>
										<td colspan="3">
											<select name="gcb_type" id="gcb_type">
												<?php foreach($available_types as $ak=>$av): ?>
												<option<?php echo ($record['type']==$ak ? " selected":""); ?> value="<?php echo $ak?>" img="<?php echo $av["img"]?>" help="<?php echo $av["help"]?>"><?php echo $av["vname"]?></option>
												<?php endforeach; ?>
											</select>
											&nbsp;
											
											&nbsp;
											<span id="type_help"><?php echo $available_types[$actual_type]["help"];?></span>
										</td>
									</tr>
									
									<tr>
										<th scope="row" valign="top" style="border-bottom:none;" width="200">Description
											<br />
											<span style="font-weight:normal;">
											(Optional)</span>
											
										</th>
										<td colspan="3" rowspan="1">
											
											<textarea tabindex="2" name="gcb_description" style="width:95%;height:150px;"><?php echo htmlspecialchars_decode(stripslashes($record['description']));?></textarea>
										</td>
									</tr>
									
									<tr>
										<th scope="row" valign="top" style="border-bottom:none;" width="200">
											Content <span style="color:#ff0000;">*</span>
											<br />
											<span style="font-weight:normal;">
												Enter or paste the content to appear in your content block
											</span>
										</th>
										<td colspan="3" rowspan="1">
											<div style="width:100%;">
											<?php
												if (function_exists('wp_editor')) {
													wp_editor(
													htmlspecialchars_decode(stripslashes($record['value'])),
													"gcbvalue",
													array(
														"wpautop"	=>	true,//we do not need auto paragraphs
													)
													
													);
													
												}
											?>	
											</div>
												
										</td>
									</tr>
									
								</thead>
							</table>
							<?php if($view=="update"):?>
							<?php endif; ?>
							<p class="submit">
								<input tabindex="15" type="submit" name="gcb_save" class="button-primary" value="Save" />
								<a href="options-general.php?page=global-content-blocks&view=manage" class="button">Cancel</a>
							</p>
							
					  </form>
							<?php endif; ?>
				  </td>
					
					
					
							<td width="210" style="width:210px;vertical-align:top;text-align:right; " valign="top">
							
							<div style="width:178px;margin-bottom:10px;margin-left:20px;background:#247AA2;border:1px solid #000;color: #FFF; padding:5px 0px;text-align:center;font-size:9pt;font-weight: bold;-moz-border-radius: 10px; -webkit-border-radius: 10px; -khtml-border-radius: 10px; border-radius: 10px;">
								If you like this plugin and find it useful please help keep it free, actively developed and supported by making a donation.
								<br />
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="VJZ5GFZUEEQ9Y">
							<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
							</form>
							</div>
							<div style="width:180px;height:600px;background-color: #F9F9F9;margin-top:10px;margin-left:20px;">
							<iframe src="http://wpxpert.com/gcbmessage/gcbside.htm" align="left" scrolling="no" width="180" frameborder="0" height="600"></iframe>
							</div>
							</td>
			  </tr>
							</table>
							
							
							
							</div>
							<?php
							$r = ob_get_contents();
							ob_end_clean();
							return $r;
							}
							
							
							}
							
							?>