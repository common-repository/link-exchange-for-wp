<?php
/**
 * @author Patrick Wagner
 * @copyright 2009
 * Author: Patrick Wagner
 * Author URI: htp://www.patrickwagner.com/ 
 * Plugin Name: Link Exchange WP Free
 * Description: This plugin is designed to manage link exchange directory and it allows visitors to apply for link swaps with your site. This will increase your exposure and ranking on many search engines. Link Exchanges are a vital part of any sites SEO and ranking strategy. | <a href="http://www.linkexchangewp.com/buy-now/">Buy Now</a> | <a href="http://www.linkexchangewp.com/wp-forum/">Support</a> | <a href="http://www.massivetraffic.com">WordPress Themes</a>
 * Version: 1.09
 * Plugin URI: http://www.linkexchangewp.com/
 */
?>
<?php

add_action('admin_menu', 	'lef_links_catalogue');
add_action('init', 			'lef_on_init'); 

$lef_categories_table 	= $wpdb->prefix."links_exchange_free_categories";
$lef_links_table 		= $wpdb->prefix."links_exchange_free_links";
$lef_currencies			= array('AUD'=>'$','CAD'=>'$','CHF'=>'Fr','CNY'=>'&#165;','EUR'=>'&#128;','GBP'=>'&#163;','JPY'=>'&#165;','MXN'=>'$','RUB'=>'p.','USD'=>'$','ZAR'=>'R');

function lscf_category_path_by_title($title)
{
	$path = strtolower($title);
	$path = str_replace(array(' ', '/', '&', '?', ':'), array('-', '-', 'and', '', '-'), $path);
	return $path;	
}
    //echo 'f:' . intval(add_filter('the_content', 'lef_links_catalogue_content'));
function lef_on_init()
{	
	// -----------   FILTER for the selected page   ---------------
	add_filter('the_content', 'lef_links_catalogue_content');
	
	// -------------------  INSTALATION   -------------------------
	$links_exchange_free_build = 52;
	
	if (get_option('links_exchange_free_build') < $links_exchange_free_build)
	{	
		global $wpdb;
		global $lef_categories_table;
		global $lef_links_table;
		$install_error = false;
               
		$res = $wpdb->query("SHOW TABLE STATUS LIKE '$lef_categories_table'");
		if (! $res)
		{
			if (($wpdb->query("CREATE TABLE `$lef_categories_table` (
									  `id` int(11) NOT NULL auto_increment,
									  `parent` int(11) default NULL,
									  `title` varchar(250) NOT NULL,
									  `path` char(50) default NULL,
									  `description` varchar(250) NOT NULL,
									  UNIQUE KEY `id` (`id`)
									) ENGINE=InnoDB;") !== false) 
				&& ($wpdb->query("CREATE TABLE `$lef_links_table` (
									  `id` int(11) NOT NULL auto_increment,
									  `category` int(11) NOT NULL,
									  `url` char(100) collate cp1251_bin NOT NULL,
									  `title` char(100) collate cp1251_bin NOT NULL,
									  `description` varchar(250) collate cp1251_bin default NULL,
									  `owner_name` char(100) collate cp1251_bin default NULL,
									  `owner_email` char(50) collate cp1251_bin default NULL,
									  `add_date` timestamp NOT NULL default '0000-00-00 00:00:00',
									  `modified_date` timestamp NOT NULL default '0000-00-00 00:00:00',
									  `premium` int(11) default NULL,
									  `in` int(11) default '0',
									  `out` int(11) default '0',
									  `active` int(11) default '0',
									  PRIMARY KEY  (`id`),
									  UNIQUE KEY `id` (`id`)
									) ENGINE=InnoDB;") !== false))
			{      
				//$wpdb->query("INSERT INTO `$lef_categories_table` (`parent`, `title`, `description`) VALUES (0, 'Untitled', 'Some category');");	
				//$wpdb->query("INSERT INTO `$lef_links_table` (`category`, `url`, `title`, `description`, `owner_name`, `owner_email`, `add_date`, `modified_date`, `premium`, `in`, `out`) VALUES ('".$wpdb->insert_id."', 'http://somesite.com', 'Some Site', 'Link to Some Site', 'Name', 'somesite@email.com', NULL, '0000-00-00', 0, 0, 0);");
			}
			else
			{
				$install_error = 'Could not create tables '.$lef_categories_table.' and '.$lef_links_table.' <br/>'.$wpdb->lastError;
			}							
		}
		

		// --------------------------------   Default values   ---------------------------------------

		update_option('lscf_link_to_us', '<a href="'.get_bloginfo('url').'/">'.get_bloginfo('uri').'</a>');					  
		update_option('lscf_style', 'default.css');			  		  
		update_option('lscf_order_by', 'add_new_first');	
		update_option('lscf_links_on_page', 15);		  
		update_option('lscf_links_target', '_blank');			  
		update_option('lscf_no_show_if_inlessout', 0);
		update_option('lscf_activated_by_default', 0);	
		update_option('lscf_admin_email', get_option('admin_email'));	  
		update_option('lscf_insert_nofollow', 0);			  	  
		update_option('lscf_show_links_count', 1);	
		
		update_option('lscf_show_hits', 1);
		update_option('lscf_show_cached_pages', 1);
		update_option('lscf_show_backlinks', 1);
		update_option('lscf_show_other_links', 1);
				  
		update_option('lscf_field_site_title', 'Site Title:');			  
		update_option('lscf_show_field_date_and_time', 1);
		update_option('lscf_show_field_username', 1);
		update_option('lscf_show_field_site_url', 1);			  
		update_option('lscf_field_date_and_time_title', 'Date and Time:');			  
		update_option('lscf_field_username_title', 'Username:');			  
		update_option('lscf_field_category_title', 'Category:');			  
		update_option('lscf_field_site_url_title', 'Site URL:'); 
		update_option('lscf_field_description_title', 'Description:');
		update_option('lscf_field_email_title', 'Email:');  
		
		update_option('lscf_message_link_added', 'Link was added'); 
		update_option('lscf_message_invalid_code', 'Invalid code'); 
		update_option('lscf_message_invalid_url', 'Invalid URL'); 
		update_option('lscf_message_description_tolong', 'Description is to long'); 
			
		update_option('lscf_tab_links_title', 'Links');
		update_option('lscf_tab_linktous_title', 'Link To Us'); 
		update_option('lscf_tab_search_title', 'Search'); 
		update_option('lscf_tab_addyourlink_title', 'Add your Link'); 
		
		if ($install_error != false) 
		{
			update_option('links_exchange_free_install_error', $install_error);
		}
		else 
		{
			update_option('links_exchange_free_build', $links_exchange_free_build);
			update_option('links_exchange_free_install_error', false);	
		}	
	}
	
	// ---------------------------------  hook to the selected page  --------------------------------------
	$lscf_page_id = get_option('lscf_page_id');
	$_request_uri = $_SERVER['REQUEST_URI'];
	if ($lscf_page_id)
	{    	
		if (isset($_REQUEST['findlink']))
		{     	
			global $wpdb;
			global $lef_links_table;
			$id = intval($_GET['findlink']);

			$wpdb->query("UPDATE $lef_links_table SET `out` = (`out`+1) WHERE `id` = $id;");
			$URL = $wpdb->get_var("SELECT `url` FROM $lef_links_table WHERE `id` = $id;");
			if ($URL)
			{
				header("Location: $URL");
				exit;
			}
		}
		
		  
		$_permalink = get_permalink($lscf_page_id);
		$_permalink_request = substr($_permalink, strlen(get_bloginfo('url')), strlen($_permalink));
		   
		//echo '>'.$_request_uri.'<br>';
		//echo '>'.$_permalink_request.'<br>';

		if ($_permalink_request == substr($_request_uri, 0, strlen($_permalink_request)))
		{        
			$GLOBALS['linkdir_action'] = @$_GET['action'];
			$GLOBALS['linkdir_base_url'] = $_permalink;
			//echo 'linkdir_action:'.$GLOBALS['linkdir_action'].'<br>'.'linkdir_base_url:'.$GLOBALS['linkdir_base_url'];
			//query_posts('page_id='.$lscf_page_id);
		}
		
		
		if ($GLOBALS['linkdir_action'] == 'add')
		{
			session_start();
  			if(!empty($_POST['le_chapcha'])&&!empty($_SESSION['le_chapcha'])&&$_SESSION['le_chapcha']==$_POST['le_chapcha'])
          	{
               $GLOBALS['chapcha_send']=true;
          	}
			$GLOBALS['chapcha1']=rand (0,50);
			$GLOBALS['chapcha2']=rand (0,50);
			$_SESSION['le_chapcha']=$GLOBALS['chapcha1']+$GLOBALS['chapcha2'];
		}
		

	
		// --------------------------------  Counting backward transitions  --------------------------------------	
		if (! empty($_SERVER['HTTP_REFERER']))
		{
			global $wpdb;
			global $lef_links_table;
			$url = parse_url($_SERVER['HTTP_REFERER']);
			$url = $url['host'];
			$wpdb->query("UPDATE $lef_links_table SET `in` = (`in`+1) WHERE `url` LIKE '%$url%';");
		} 		
	} 
}

function lscf_get_styles()
{
	$styles_dirname = dirname(__FILE__).'/styles/';
	$file_list = array();
	if (is_dir($styles_dirname)) 
	{
	    if ($dh = opendir($styles_dirname)) 
		{
	        while (($file = readdir($dh)) !== false) 
	        {
	        	if (filetype($styles_dirname . $file) == 'file')
					array_push($file_list, $file);
			}
	        closedir($dh);
	    }
	}
	return $file_list;
}

function lef_get_styles($checked=0, $parent=0, $prefix='')
{
	$pages=get_pages('sort_column=menu_order&child_of='.$parent.'&');
	foreach($pages as $page)
	{
		if($page->post_parent==$parent)
		{
			echo('<label><input type="radio" '); 
			if ($checked == $page->ID) echo('checked="checked" '); 
			echo('name="lscf_page_id" value="'.$page->ID.'" /> '. $prefix . $page->post_title . '</label><br/>');
			if(get_children($page->ID))
				lef_get_styles($checked, $page->ID, $prefix.'-&rsaquo; ');
		}
	}
}



// -----------------------------------------  ADMIN PART  -----------------------------------------------

function lef_links_catalogue()
{		
	if (function_exists('add_submenu_page'))
	{
		add_menu_page('Link Exchange', 'Link Exchange', 8, __FILE__);
		add_submenu_page(__FILE__, 'Link Exchange - Configuration', 'Configuration', 8, __FILE__, 'lscf_configuration_submenu'); 
		add_submenu_page(__FILE__, 'Link Exchange - Select Catalogue Page', 'Select Page', 8, 'links_catalogue_select_page', 'lscf_select_page_submenu'); 
		add_submenu_page(__FILE__, 'Link Exchange - Categories', 'Categories', 8, 'links_catalogue_categories', 'lscf_categories_submenu');
		add_submenu_page(__FILE__, 'Link Exchange - Links', 'Links', 8, 'links_catalogue_links', 'lscf_links_submenu');
		add_submenu_page(__FILE__, 'Link Exchange - Import / Export Links', 'Import/Export', 8, 'links_import_export_links', 'lscf_import_export_links_submenu');  
	}	
}
//--------------------------------------------------------------------------------------------------------

function lscf_request_stripslashes($_array) 
{
	foreach ($_array as $kay => $val)
		$_array[$kay] = stripslashes($val);	
		
	return $_array;
}  

function lscf_print_info() 
{
	echo '
	  	<h2>LinkExchange WP Info</h2>
		<p>
			<strong>Free version description:</strong>This plugin is designed to manage link exchange directory and it allows visitors to apply for link swaps with your site. This will increase your exposure and ranking on many search engines. Link Exchanges are a vital part of any sites SEO and ranking strategy.
		</p>

		<p>You can buy the full version at <a href="http://www.linkexchangewp.com">linkexchangewp.com</a> - the price is $79.50 per domain licenses, with discounts for 2 domains.</p>

		<p>
			<strong>Paid version description:</strong>The only SEO friendly Link Exchange plugin for WordPress sites. Lot\'s of ways to customize and use this link exchange plugin for a link exchange directory, resource site, or service or product listing site. Get the best link exchange plugin - Link Exchange WP - will ensure you can handle and manage all your link exchanges while having SEO friendly links.
		</p>
		<p>
		The full version allows:<br/>
		- full SEO friendly links<br/>
		- you can remove the Powered by: info
		</p>';
} 
              

function lscf_configuration_submenu()
{
	$updated_message = false;
	if (! empty($_REQUEST['lscf_save_options']))
	{
		// ---------------------  magic_quotes_gpc  -------------------------
		if ( get_magic_quotes_gpc() ) { 
		   $_REQUEST = lscf_request_stripslashes($_REQUEST);
		}
		
		$lscf_link_to_us = $_REQUEST['lscf_link_to_us'];  
		update_option('lscf_link_to_us', $lscf_link_to_us);
		$lscf_style = $_REQUEST['lscf_style'];  
		update_option('lscf_style', $lscf_style);
		$lscf_order_by = $_REQUEST['lscf_order_by'];  
		update_option('lscf_order_by', $lscf_order_by);
		$lscf_links_on_page = intval($_REQUEST['lscf_links_on_page']);  
		update_option('lscf_links_on_page', $lscf_links_on_page);
		$lscf_links_target = $_REQUEST['lscf_links_target'];  
		update_option('lscf_links_target', $lscf_links_target);
		$lscf_no_show_if_inlessout = $_REQUEST['lscf_no_show_if_inlessout'];  		
		update_option('lscf_no_show_if_inlessout', $lscf_no_show_if_inlessout);
		$lscf_activated_by_default = $_REQUEST['lscf_activated_by_default']; 
		update_option('lscf_activated_by_default', $lscf_activated_by_default);
		$lscf_admin_email = $_REQUEST['lscf_admin_email'];        
		update_option('lscf_admin_email', $lscf_admin_email);

		$lscf_insert_nofollow = $_REQUEST['lscf_insert_nofollow'];  
		update_option('lscf_insert_nofollow', $lscf_insert_nofollow);
		$lscf_show_links_count = $_REQUEST['lscf_show_links_count'];  
		update_option('lscf_show_links_count', $lscf_show_links_count);
		
		$lscf_show_hits = $_REQUEST['lscf_show_hits'];  
		update_option('lscf_show_hits', $lscf_show_hits);
		$lscf_show_cached_pages = $_REQUEST['lscf_show_cached_pages'];  
		update_option('lscf_show_cached_pages', $lscf_show_cached_pages);
		$lscf_show_backlinks = $_REQUEST['lscf_show_backlinks'];  
		update_option('lscf_show_backlinks', $lscf_show_backlinks);
		$lscf_show_other_links = $_REQUEST['lscf_show_other_links'];  
		update_option('lscf_show_other_links', $lscf_show_other_links);
		
		$lscf_field_site_title = $_REQUEST['lscf_field_site_title'];  
		update_option('lscf_field_site_title', $lscf_field_site_title);
		$lscf_show_field_date_and_time = isset($_REQUEST['lscf_show_field_date_and_time']);  
		update_option('lscf_show_field_date_and_time', $lscf_show_field_date_and_time);
		$lscf_show_field_username = isset($_REQUEST['lscf_show_field_username']);  
		update_option('lscf_show_field_username', $lscf_show_field_username);
		$lscf_show_field_site_url = isset($_REQUEST['lscf_show_field_site_url']);  
		update_option('lscf_show_field_site_url', $lscf_show_field_site_url);
		
		$lscf_field_date_and_time_title = $_REQUEST['lscf_field_date_and_time_title'];  
		if ($lscf_show_field_date_and_time) update_option('lscf_field_date_and_time_title', $lscf_field_date_and_time_title);
		else $lscf_field_date_and_time_title = get_option('lscf_field_date_and_time_title');
		$lscf_field_username_title = $_REQUEST['lscf_field_username_title'];  
		if ($lscf_show_field_username) update_option('lscf_field_username_title', $lscf_field_username_title);
		else $lscf_field_username_title = get_option('lscf_field_username_title');
		$lscf_field_category_title = $_REQUEST['lscf_field_category_title'];  
		update_option('lscf_field_category_title', $lscf_field_category_title);
		$lscf_field_site_url_title = $_REQUEST['lscf_field_site_url_title'];  
		if ($lscf_show_field_site_url) update_option('lscf_field_site_url_title', $lscf_field_site_url_title);
		else $lscf_field_site_url_title = get_option('lscf_field_site_url_title');
		$lscf_field_description_title = $_REQUEST['lscf_field_description_title'];  
		update_option('lscf_field_description_title', $lscf_field_description_title);
		$lscf_field_email_title = $_REQUEST['lscf_field_email_title'];  
		update_option('lscf_field_email_title', $lscf_field_email_title);
		
		$lscf_tab_links_title = $_REQUEST['lscf_tab_links_title'];  
		update_option('lscf_tab_links_title', $lscf_tab_links_title);
		$lscf_tab_linktous_title = $_REQUEST['lscf_tab_linktous_title'];  
		update_option('lscf_tab_linktous_title', $lscf_tab_linktous_title);
		$lscf_tab_search_title = $_REQUEST['lscf_tab_search_title'];  
		update_option('lscf_tab_search_title', $lscf_tab_search_title);
		$lscf_tab_addyourlink_title = $_REQUEST['lscf_tab_addyourlink_title'];  
		update_option('lscf_tab_addyourlink_title', $lscf_tab_addyourlink_title);

		$lscf_message_link_added = $_REQUEST['lscf_message_link_added'];  
		update_option('lscf_message_link_added', $lscf_message_link_added);
		$lscf_message_invalid_code = $_REQUEST['lscf_message_invalid_code'];  
		update_option('lscf_message_invalid_code', $lscf_message_invalid_code);	
		$lscf_message_invalid_url = $_REQUEST['lscf_message_invalid_url'];  
		update_option('lscf_message_invalid_url', $lscf_message_invalid_url);	
		$lscf_message_description_tolong = $_REQUEST['lscf_message_description_tolong'];  
		update_option('lscf_message_description_tolong', $lscf_message_description_tolong);
		
		$updated_message = true;
	}
	else
	{
		$lscf_link_to_us = get_option('lscf_link_to_us');
		$lscf_style = get_option('lscf_style');
		$lscf_order_by = get_option('lscf_order_by');
		$lscf_links_on_page = get_option('lscf_links_on_page');
		$lscf_links_target = get_option('lscf_links_target');
		$lscf_no_show_if_inlessout = get_option('lscf_no_show_if_inlessout');
		$lscf_activated_by_default = get_option('lscf_activated_by_default');
		$lscf_admin_email = get_option('lscf_admin_email');
		
		$lscf_insert_nofollow = get_option('lscf_insert_nofollow');
		$lscf_show_links_count = get_option('lscf_show_links_count');
		
		$lscf_show_hits = get_option('lscf_show_hits');
		$lscf_show_cached_pages = get_option('lscf_show_cached_pages');
		$lscf_show_backlinks = get_option('lscf_show_backlinks');
		$lscf_show_other_links = get_option('lscf_show_other_links');
		
		$lscf_field_site_title = get_option('lscf_field_site_title');
		$lscf_show_field_date_and_time = get_option('lscf_show_field_date_and_time');
		$lscf_show_field_username = get_option('lscf_show_field_username');
		$lscf_show_field_site_url = get_option('lscf_show_field_site_url');
		
		$lscf_field_date_and_time_title = get_option('lscf_field_date_and_time_title');
		$lscf_field_username_title = get_option('lscf_field_username_title');
		$lscf_field_category_title = get_option('lscf_field_category_title');
		$lscf_field_site_url_title = get_option('lscf_field_site_url_title');
		$lscf_field_description_title = get_option('lscf_field_description_title');
		$lscf_field_email_title = get_option('lscf_field_email_title');
		
		$lscf_tab_links_title = get_option('lscf_tab_links_title');
		$lscf_tab_linktous_title = get_option('lscf_tab_linktous_title');                   
		$lscf_tab_search_title = get_option('lscf_tab_search_title');
		$lscf_tab_addyourlink_title = get_option('lscf_tab_addyourlink_title');
		
		$lscf_message_link_added = get_option('lscf_message_link_added');
		$lscf_message_invalid_code = get_option('lscf_message_invalid_code');
		$lscf_message_invalid_url = get_option('lscf_message_invalid_url');
		$lscf_message_description_tolong = get_option('lscf_message_description_tolong');
	}
	
	?>
	<div class="wrap">
		<?php lscf_print_info() ?>
	
		<h2>Configuration</h2>
		<?php
	     	$links_exchange_free_install_error = get_option('links_exchange_free_install_error');
	     	if ($links_exchange_free_install_error)
	     		echo('<div id="message" class="error"><p><strong>'.$links_exchange_free_install_error.'</strong></p></div>');
			
			if ($updated_message) echo '<div id="message" class="updated fade"><p><strong>Settings was Saved.</strong></p></div>';
		?>
		
		<form method="post" action="">  
			<br />
			<table class="form-table">
				<tr valign="top">
					<td width="30%">
						<b>"Link To Us" html:</b> 
					</td>
					<td>
						<textarea rows="2" name="lscf_link_to_us" style="width: 100%"><?php echo $lscf_link_to_us; ?></textarea>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Style:</b> 
					</td>
					<td>
						<select name="lscf_style" style="width: 100%">
							<?php
							 	$styles = lscf_get_styles();
							 	foreach ($styles as $file)
							 	{
							?>
							<option <?php if ($lscf_style == $file) echo ' selected="selected" '; ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
						    <?php						    
						    	}
						    ?>
						</select>
					</td> 
				</tr>
			
				
				<tr> <td colspan="2" align="right"><h3>Type:</h3></td> </tr>
				<tr valign="top">
					<td width="30%">
						<b>Do not show if 'input' less 'output' hits:</b> 
					</td>
					<td>
						<select name="lscf_no_show_if_inlessout" style="width: 100%">
							<option <?php if ($lscf_no_show_if_inlessout) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_no_show_if_inlessout) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Links activated by default:</b> 
					</td>
					<td>
						<select name="lscf_activated_by_default" style="width: 100%">
							<option <?php if ($lscf_activated_by_default) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_activated_by_default) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Admin email:</b> 
					</td>
					<td>
						<input type="text" name="lscf_admin_email" value="<?php echo attribute_escape($lscf_admin_email); ?>" style="width: 100%" />
					</td> 
				</tr>
				
				
				
				<tr> <td colspan="2" align="right"><h3>Links Display</h3></td> </tr>
				<tr valign="top">
					<td width="30%">
						<b>Links Order:</b> 
					</td>
					<td>
						<select name="lscf_order_by" style="width: 100%">
							<option <?php if ($lscf_order_by == 'add_new_first') echo ' selected="selected" '; ?> value="add_new_first">New First</option>
							<option <?php if ($lscf_order_by == 'add_new_last') 	echo ' selected="selected" '; ?> value="add_new_last">New Last</option>
							<option <?php if ($lscf_order_by == 'a_to_z') 		echo ' selected="selected" '; ?> value="a_to_z">Alphabetical A to Z</option>
							<option <?php if ($lscf_order_by == 'z_to_a') 		echo ' selected="selected" '; ?> value="z_to_a">Alphabetical Z to A</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Links count on page:</b> 
					</td>
					<td>
						<input type="text" name="lscf_links_on_page" value="<?php echo attribute_escape($lscf_links_on_page); ?>" style="width: 100%" />
					</td> 
				</tr>

				<tr valign="top">
					<td width="30%">
						<b>Links Target:</b> 
					</td>
					<td>
						<select name="lscf_links_target" style="width: 100%">
							<option <?php if ($lscf_links_target == '_blank') echo ' selected="selected" '; ?> value="_blank">New Window</option>
							<option <?php if ($lscf_links_target == '_self')  echo ' selected="selected" '; ?> value="_self">Current Window</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Insert NoFollow:</b> <br/>(attribute "rel=nofollow") 
					</td>
					<td>
						<select name="lscf_insert_nofollow" style="width: 100%">
							<option <?php if ($lscf_insert_nofollow) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_insert_nofollow) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>			
				<tr valign="top">
					<td width="30%">
						<b>Show Links Count:</b> <br/> Show links count in categories
					</td>
					<td>
						<select name="lscf_show_links_count" style="width: 100%">
							<option <?php if ($lscf_show_links_count) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_show_links_count) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				
				
				
				<tr> <td colspan="2" align="right"><h3>Links Info</h3></td> </tr>
				 
				<tr valign="top">
					<td width="30%">
						<b>Show "In/Out Hits":</b>
					</td>
					<td>
						<select name="lscf_show_hits" style="width: 100%">
							<option <?php if ($lscf_show_hits) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_show_hits) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Show "Cached Pages":</b>
					</td>
					<td>
						<select name="lscf_show_cached_pages" style="width: 100%">
							<option <?php if ($lscf_show_cached_pages) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_show_cached_pages) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Show "Backlinks":</b>
					</td>
					<td>
						<select name="lscf_show_backlinks" style="width: 100%">
							<option <?php if ($lscf_show_backlinks) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_show_backlinks) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Show "Other Links":</b>
					</td>
					<td>
						<select name="lscf_show_other_links" style="width: 100%">
							<option <?php if ($lscf_show_other_links) echo ' selected="selected" '; ?> value="1">Yes</option>
							<option <?php if (! $lscf_show_other_links) echo ' selected="selected" '; ?> value="0">No</option>
						</select>
					</td> 
				</tr>
				
				
				<tr> <td colspan="2" align="right"><h3>Fields Title</h3></td> </tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Site Title":</b> 
					</td>
					<td>
						<input type="text" name="lscf_field_site_title" value="<?php echo attribute_escape($lscf_field_site_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Date and Time":</b> 
					</td>
					<td>
						<label><input type="checkbox" value="1" <?php if ($lscf_show_field_date_and_time) echo ' checked="checked" '; ?> onchange="javascript: document.getElementById('lscf_field_date_and_time_title').disabled = ! this.checked;" name="lscf_show_field_date_and_time" id="lscf_show_field_date_and_time"/> Show this field</label><br/>
						<input type="text" name="lscf_field_date_and_time_title" value="<?php echo $lscf_field_date_and_time_title; ?>" id="lscf_field_date_and_time_title" style="width: 100%" />
						<script language="javascript">
							document.getElementById('lscf_field_date_and_time_title').disabled = ! document.getElementById('lscf_show_field_date_and_time').checked;
						</script>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Username":</b> 
					</td>                                
					<td>
						<label><input type="checkbox" value="1" <?php if ($lscf_show_field_username) echo ' checked="checked" '; ?> onchange="javascript: document.getElementById('lscf_field_username_title').disabled = ! this.checked;" name="lscf_show_field_username" id="lscf_show_field_username"/> Show this field</label><br/>
						<input type="text" name="lscf_field_username_title" id="lscf_field_username_title" value="<?php echo attribute_escape($lscf_field_username_title); ?>" style="width: 100%" />
						<script language="javascript">
							document.getElementById('lscf_field_username_title').disabled = ! document.getElementById('lscf_show_field_username').checked;
						</script>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Category":</b> 
					</td>
					<td>
						<input type="text" name="lscf_field_category_title" value="<?php echo attribute_escape($lscf_field_category_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Site URL":</b> 
					</td>
					<td>
						<label><input type="checkbox" value="1" <?php if ($lscf_show_field_site_url) echo ' checked="checked" '; ?> onchange="javascript: document.getElementById('lscf_field_site_url_title').disabled = ! this.checked;" name="lscf_show_field_site_url" id="lscf_show_field_site_url"/> Show this field</label><br/>
						<input type="text" name="lscf_field_site_url_title" id="lscf_field_site_url_title" value="<?php echo attribute_escape($lscf_field_site_url_title); ?>" style="width: 100%" />
						<script language="javascript">
							document.getElementById('lscf_field_site_url_title').disabled = ! document.getElementById('lscf_show_field_site_url').checked;
						</script>
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Description":</b> 
					</td>
					<td>
						<input type="text" name="lscf_field_description_title" value="<?php echo attribute_escape($lscf_field_description_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Field "Email":</b> 
					</td>
					<td>
						<input type="text" name="lscf_field_email_title" value="<?php echo attribute_escape($lscf_field_email_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				     
				<tr> <td colspan="2" align="right"><h3>Messages:</h3></td> </tr> 
				
				<tr valign="top">
					<td width="30%">
						<b>Message "Link was added":</b> 
					</td>
					<td>
						<input type="text" name="lscf_message_link_added" value="<?php echo attribute_escape($lscf_message_link_added); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Message "Invalid code":</b> 
					</td>
					<td>
						<input type="text" name="lscf_message_invalid_code" value="<?php echo attribute_escape($lscf_message_invalid_code); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Message "Invalid URL":</b> 
					</td>
					<td>
						<input type="text" name="lscf_message_invalid_url" value="<?php echo attribute_escape($lscf_message_invalid_url); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Message "Description is to long":</b> 
					</td>
					<td>
						<input type="text" name="lscf_message_description_tolong" value="<?php echo attribute_escape($lscf_message_description_tolong); ?>" style="width: 100%" />
					</td> 
				</tr>

				<tr> <td colspan="2" align="right"><h3>Tabs titles:</h3></td> </tr> 
				
				<tr valign="top">
					<td width="30%">
						<b>Tab "Links":</b> 
					</td>
					<td>
						<input type="text" name="lscf_tab_links_title" value="<?php echo attribute_escape($lscf_tab_links_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Tab "Link To Us":</b> 
					</td>
					<td>
						<input type="text" name="lscf_tab_linktous_title" value="<?php echo attribute_escape($lscf_tab_linktous_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Tab "Search":</b> 
					</td>
					<td>
						<input type="text" name="lscf_tab_search_title" value="<?php echo attribute_escape($lscf_tab_search_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				<tr valign="top">
					<td width="30%">
						<b>Tab "Add your Link":</b> 
					</td>
					<td>
						<input type="text" name="lscf_tab_addyourlink_title" value="<?php echo attribute_escape($lscf_tab_addyourlink_title); ?>" style="width: 100%" />
					</td> 
				</tr>
				
				<tr>
					<td colspan="2" align="right">
						<input type="hidden" name="lscf_save_options" value="1"/>
						<input type="submit" class="button" value="Save Changes" />
					</td> 
				</tr>
			</table>
		</form>
         
	</div>	
	<?php	
}
//--------------------------------------------------------------------------------------------------------

function lscf_select_page_submenu()
{
	$updated_message = false;
	if (! empty($_REQUEST['lscf_page_id']))
	{
		// ---------------------  magic_quotes_gpc  -------------------------
		if ( get_magic_quotes_gpc() ) { 
		   $_REQUEST = lscf_request_stripslashes($_REQUEST);
		}
		
		$lscf_page_id = $_REQUEST['lscf_page_id'];
		update_option('lscf_page_id', $lscf_page_id);	
		$updated_message = true;
	}
	else
	{
		$lscf_page_id = get_option('lscf_page_id');	
	}
	             
	?>
	<div class="wrap">   
		<?php lscf_print_info() ?>
		
		<h2>Select Catalogue Page</h2>
		<?php
	     	$links_exchange_free_install_error = get_option('links_exchange_free_install_error');
	     	if ($links_exchange_free_install_error)
	     		echo('<div id="message" class="error"><p><strong>'.$links_exchange_free_install_error.'</strong></p></div>');
			
			if ($updated_message) echo '<div id="message" class="updated fade"><p><strong>Settings was Saved.</strong></p></div>';
		?>
		<br/>
		<form method="post" action="">
			<table class="form-table">
				<tr valign="top">
					<td width="30%">
						<b>Catalogue page:</b> 
					</td>
					<td>
						<?php lef_get_styles($lscf_page_id); ?>	
					</td> 
				</tr>				
				
				<tr>
					<td colspan="2" align="right">
						<input type="submit" class="button" value="Save Changes"/>
					</td> 
				</tr>
			</table>
  		</form>
	</div>	
	<?php	
}
//--------------------------------------------------------------------------------------------------------

$lscf_categories_tree = null;

function lscf_make_categories_tree($parent = 0)
{
	global $wpdb;
	global $lef_categories_table;

	$categories = $wpdb->get_results("SELECT * FROM `$lef_categories_table` WHERE (`parent` = $parent)");
	if ($categories)
	{           
		$res = array();
		foreach($categories as $category)
		{
			$row = array();
			$row['id'] = $category->id;
			$row['title'] = $category->title;
			$row['path'] = $category->path;
			$row['parent'] = $category->parent;
			$row['description'] = $category->description;
			$row['children'] = lscf_make_categories_tree($category->id);
			array_push($res, $row);
		}
		return $res; 
	}
	else return null;
}

function lscf_get_cat_title($id, $subtree)
{
	foreach($subtree as $node)
	{
		if ($node['id'] == $id) return $node['title'];
		if ($node['children'])  return lscf_get_cat_title($id, $node['children']);	
	}	
	return '';	
}
//--------------------------------------------------------------------------------------------------------

function lscf_get_parents_categories($exclude, $subtree, $selected, $prefix = '')
{         
	if (! is_array($subtree)) return '';
	foreach($subtree as $node)
	{
		if ($node['id'] != $exclude)
		{
	 		echo '<option '.($node['id'] == $selected ? ' selected="selected" ' : '').' value="'.$node['id'].'">'.$prefix.$node['title'].'</option>';
			if ($node['children'])
				lscf_get_parents_categories($exclude, $node['children'], $selected, $prefix.'&rsaquo;');	
		}
	}	
}

function lscf_get_categories_select($exclude, $subtree, $selected, $prefix = '')
{         
	$content = '';
	if (! is_array($subtree)) return '';
	foreach($subtree as $node)
	{
		if ($node['id'] != $exclude)
		{
	 		$content .= '<option '.($node['id'] == $selected ? ' selected="selected" ' : '').' value="'.$node['id'].'">'.$prefix.$node['title'].'</option>';
			if ($node['children'])
				$content .= lscf_get_categories_select($exclude, $node['children'], $selected, $prefix.'&rsaquo;');	
		}
	}
	return $content;	
}

function lscf_print_categories($parent = 0)
{
	global $wpdb;
	global $lef_categories_table;
	global $lef_links_table;
	global $lscf_categories_tree;

	$categories = $wpdb->get_results("SELECT * FROM `$lef_categories_table` WHERE (`parent` = $parent)");
    $c = 0;
	foreach($categories as $category)
	{
		$c++;
		?>
			<div style="padding: 2px; margin: 2px 2px 2px 20px;">  
				<div id="show_cat_<?php echo $category->id; ?>" style="padding: 2px 0 2px 0;">
				    <a style="color: #4575AB; text-decoration: none;" href="#" onclick="javascript: edit_category_click(<?php echo $category->id; ?>); return false; ">
						<?php echo $category->title; ?>
                    </a>
				</div>
				<div class="edit_cat" id="edit_cat_<?php echo $category->id; ?>" style="display: none; border: 1px #ccc solid; border-top: 0px;">     
					<form action="" method="post">
						<table width="100%" class="form-table">
							<tr>
								<?php if ($padding) { ?>
								<td style="width: '.$padding.'px" valign="top">&nbsp;</td>
								<?php } ?> 
								
								<td width="200px" valign="top">
									<b>Title:</b> <br/>
									<input type="text" name="title" value="<?php echo attribute_escape($category->title); ?>" />
								</td>
								<td width="100px" valign="top">
									<b>Parent:</b> <br/>
									<select style="width: 100px" name="parent<?php echo $category->id; ?>">
		 								<option value="0" <?php if ($category->parent == 0) echo ' selected="selected" '?> >no parent</option>
										<?php lscf_get_parents_categories($category->id, $lscf_categories_tree, $category->parent, '&rsaquo;'); ?>
									</select>
								</td>
								<td>
									<b>Description:</b>  <br/>
									<textarea rows="3" name="description" style="width: 99%"><?php echo $category->description; ?></textarea>
								</td>	
								<td width="5%" align="center">
									<input type="submit" value="Save" class="button"/>
                                    <input type="button" value="Delete" class="button" onclick="if (confirm('Do you want to remove this category?')) document.getElementById('delete_form_<?php echo $category->id; ?>').submit();"/>
									<input type="button" value="Cancel" class="button" onclick="javascript: document.getElementById('edit_cat_<?php echo $category->id; ?>').style.display='none';   document.getElementById('show_cat_<?php echo $category->id; ?>').style.display='block';" />
								</td>
							</tr>
						</table> 
						<input type="hidden" name="id" value="<?php echo $category->id; ?>"/>
						<input type="hidden" name="save_form" value="1"/> 
					</form> 
					<form action="" id="delete_form_<?php echo $category->id; ?>" method="post">
						<input type="hidden" name="delete_category_id" value="<?php echo $category->id; ?>"/>
						<input type="hidden" name="delete_category_parent" value="<?php echo $category->parent; ?>"/>
					</form>
				</div>
				<?php $c += lscf_print_categories($category->id); ?>		
			</div>
		<?php
	}
	return $c;
}

function lscf_categories_submenu()
{       
		// ---------------------  magic_quotes_gpc  -------------------------
		if ( get_magic_quotes_gpc() ) { 
		   $_REQUEST = lscf_request_stripslashes($_REQUEST);
		}
	
	?>
	<script type="text/javascript">
	    function edit_category_click(element)
	    {
	    	var e = document.getElementById('edit_cat_' + element);	
	    	var close = (e.style.display == 'block');
	    	var divs = document.getElementsByTagName('div');
	    	for (i=0; i<divs.length; i++)
	    	{
	    		if (divs[i].className == 'edit_cat')
	    			divs[i].style.display = 'none';
	    	}
	    	
	    	if (close) e.style.display = 'none';
	    	else e.style.display = 'block';
	    }
		
	</script>
	<?php
	global $wpdb;
	global $lef_categories_table;
	global $lef_links_table;
     
 	$error = false;
	$updated_message = false;
	$updated_message_text = 'Settings was Saved.';
	
	if (! empty($_REQUEST['delete_category_id']))
	{     
		$id = intval($_REQUEST['delete_category_id']);
		$parent = intval($_REQUEST['delete_category_parent']);
		
		$wpdb->query("UPDATE `$lef_categories_table` SET `parent` = $parent WHERE (`parent` = $id)");
		$wpdb->query("UPDATE `$lef_links_table` SET `category` = 0 WHERE (`category` = $id)");
		$wpdb->query("DELETE FROM `$lef_categories_table` WHERE (`id` = $id)");
		
		$updated_message = true;
		$updated_message_text = 'Category was deleted.';
	}
	else
	{     
		if (! empty($_REQUEST['add_form']))
		{       
			$parent = intval($_REQUEST['parent']);
	 		$title = mysql_escape_string(trim($_REQUEST['title']));
	 		$description = mysql_escape_string(trim($_REQUEST['description']));

			if (strlen($title) >= 2)
			{
				$wpdb->query("INSERT INTO `$lef_categories_table` (`parent`, `title`, `path`, `description`) VALUES ($parent, '$title', '".lscf_category_path_by_title($title)."', '$description')");
				$updated_message = true;
			}
			else $error = 'Title of Category is to short';	
		}
		elseif (! empty($_REQUEST['save_form']))
		{       
			$id = intval($_REQUEST['id']);
			$parent = intval($_REQUEST['parent'.$id]);
	 		$title = mysql_escape_string(trim($_REQUEST['title']));
	 		$description = mysql_escape_string(trim($_REQUEST['description']));

			if (strlen($title) >= 2)
			{
				if ($wpdb->query("UPDATE `$lef_categories_table` SET `parent`= $parent, `title`= '$title', `description`= '$description' WHERE `id`=$id") !== false)
					$updated_message = true;
				else $error = 'Cannot change category.';	
			}
			else
			{
				$error = 'Title of Category is to short.';	
			}
		}
	}

	global $lscf_categories_tree;
	$lscf_categories_tree = lscf_make_categories_tree();
	        
	?>
	<div class="wrap">
		<?php lscf_print_info() ?>
		
		<h2>Edit Categories</h2>                                            
		<?php
	     	$links_exchange_free_install_error = get_option('links_exchange_free_install_error');
	     	if ($links_exchange_free_install_error)
	     		echo('<div id="message" class="error"><p><strong>'.$links_exchange_free_install_error.'</strong></p></div>');
			if ($updated_message) echo '<div id="message" class="updated fade"><p><strong>'.$updated_message_text.'</strong></p></div>';
			if ($error) echo('<div id="message" class="error"><p><strong>'.$error.'</strong></p></div>');
			
			echo '<h4>Existing categories:</h4>';
			echo '<ul>';
			if (lscf_print_categories())	echo '<br/><i>click to edit</i>';
			else						echo '<i>no one</i>';
			echo '</ul>';

		?>
		<br/>
		<h4>New Category</h4>   
		<form action="" method="post">
			<input type="hidden" name="id" value="<?php echo $category->id; ?>"/>
			<table width="100%" class="form-table">
				<tr>				
					<td width="200px" valign="top">
						<b>Title:</b>
						<input type="text" name="title" value="" style="width: 196px" />
					</td>
					<td width="155px" valign="top">
						<b>Parent:</b>
						<select style="width: 150px" name="parent">
							<option value="0">no parent</option>
							<?php  lscf_get_parents_categories(0, $lscf_categories_tree, 0, '&rsaquo;'); ?>
						</select>
					</td>
					<td>
						<b>Description:</b>
						<textarea rows="3" name="description" style="width: 99%"></textarea>
					</td>	
				</tr>
				<tr>
					<td align="right" colspan="3">
						<input type="submit" name="add_form" value="Add" class="button"/>
					</td>
				</tr>
			</table>  
		</form> 
	</div>	
	<?php		
}
//--------------------------------------------------------------------------------------------------------



function lscf_print_links()
{      	
	?>
	<script type="text/javascript">
	    function edit_link_click(element)
	    {
	    	var e = document.getElementById('edit_link_' + element);	
	    	var close = (e.style.display == 'block');
	    	var divs = document.getElementsByTagName('div');
	    	for (i=0; i<divs.length; i++)
	    	{
	    		if (divs[i].className == 'edit_link')
	    			divs[i].style.display = 'none';
	    	}
	    	
	    	if (close) e.style.display = 'none';
	    	else e.style.display = 'block';
	    }
		   
	</script>
	<ul style="list-style: disc; padding-left: 20px;">  
	<?php
	global $wpdb;
	global $lef_links_table;
	global $lscf_categories_tree; 
	global $lef_links_table;
	
	$lcount = $wpdb->get_var("SELECT Count(*) FROM `$lef_links_table`"); 
	$lpage = intval(@$_REQUEST['lpage']);
	$lcounttopage = intval(get_option('lscf_links_on_page'));
	$lpages_count = ceil($lcount / $lcounttopage);
	$lim_start = $lpage * $lcounttopage; 

	$links = $wpdb->get_results("SELECT * FROM `$lef_links_table` LIMIT $lim_start, $lcounttopage");
	if ($links)
	{
		foreach($links as $link)
		{
			echo '<li><a href="#" style="color: #4575AB; text-decoration: none;" onclick="javascript: edit_link_click('.$link->id.'); return false; " style="text-decoration: none">';
			echo $link->title.'</a>';
			echo '
					Visits: <font color="#FA6525">'.$link->out.'</font>;
					Backwards: <font color="#12BC38">'.$link->in.'</font>;  
					Added By: '.$link->owner_name.' &lt;'.$link->owner_email.'&gt; <br/>';
			if ($link->active) 	echo '<font color="#333">'.$link->url.'</font>';		
			else    			echo '<font color="#bbb">'.$link->url.'</font>'
			?>
			<div class="edit_link" id="edit_link_<?php echo $link->id; ?>" style="display: none; border: 1px #ccc solid; border-top: 0px;">     
				<form action="" method="post">
					<input type="hidden" name="edit_link_id" value="<?php echo $link->id; ?>"/>
					<table width="100%" class="form-table">
						<tr>				
							<td width="170px" valign="top">
								<b>URL:</b>
							</td>
							<td valign="top">
								<input type="text" name="url" value="<?php echo attribute_escape($link->url); ?>" style="width: 270px" maxlength="100" />
							</td>

							<td width="170px" valign="top">
								<b>Title:</b>
							</td>
							<td valign="top">
								<input type="text" name="title" value="<?php echo attribute_escape($link->title); ?>" style="width: 270px" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td valign="top">
								<b>Username:</b>
							</td>
							<td valign="top">
								<input type="text" name="username" value="<?php echo attribute_escape($link->owner_name); ?>" style="width: 270px" maxlength="100" />
							</td>

							<td valign="top">
								<b>Email:</b>
							</td>
							<td valign="top">
								<input type="text" name="email" value="<?php echo attribute_escape($link->owner_email); ?>" style="width: 270px" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td valign="top">
								<b>Category:</b>
							</td>
							<td valign="top">

								<select style="width: 270px" name="category_<?php echo $link->id; ?>">
									<option value="0">no selected</option>
									<?php  lscf_get_parents_categories(0, $lscf_categories_tree, $link->category, '&rsaquo;'); ?>
								</select>
							</td>
							<td valign="top" colspan="2">
								<b>Activity:</b>
								<input type="checkbox" name="active" value="1" <?php if ($link->active) echo ' checked="checked" '; ?> maxlength="100" style="width: 50px" />
							</td>
						</tr>
						<tr>
							<td valign="top">
								<b>Description:</b>
							</td>
							<td valign="top">
                                <textarea rows="3" name="description" style="width: 270px"><?php echo attribute_escape($link->description); ?></textarea>
							</td>

							<td valign="top" colspan="2">
								<b>Visits:</b> <?php echo $link->out; ?>  <br/>
                                <b>Backward visits:</b> <?php echo $link->in; ?>  <br/>
								<b>Add date:</b> <?php echo $link->add_date; ?>  <br/>
                                <b>Last modified:</b> <?php echo $link->modified_date; ?>  <br/>
							</td>	
						</tr>					
						<tr>
							<td align="right" colspan="4">
								<input type="submit" name="add_form" value="Save" class="button"/>
								<input type="button" value="Delete" class="button" onclick="if (confirm('Do you want to remove this link?')) document.getElementById('delete_link_form_<?php echo $link->id; ?>').submit();"/>
								<input type="button" value="Cancel" class="button" onclick="javascript: document.getElementById('edit_link_<?php echo $link->id; ?>').style.display='none';" />
							</td>
						</tr>
					</table>  
				</form> 
				<form action="" id="delete_link_form_<?php echo $link->id; ?>" method="post">
					<input type="hidden" name="delete_link_id" value="<?php echo $link->id; ?>"/>
				</form>
			</div>
			<?php
			echo '</li>';
		}
		echo '<br/><i>click to edit</i>';
		
		if ($lpages_count > 1)
		{
			echo '<br/><div>Page: ';  
			$_request_uri = $_SERVER['REQUEST_URI'];
			if (isset($_REQUEST['lpage'])) 
				 $new_url_base = str_replace('&lpage='.$_REQUEST['lpage'], '', $_request_uri);
			else $new_url_base = $_request_uri;		
			for ($i = 0; $i < $lpages_count; $i++)
			{
				if ($i == $lpage) echo '['.($i+1).'] ';
				else echo '<a href="'.$new_url_base.'&lpage='.$i.'">'.($i+1).'</a> ';
			}
			echo '</div>';
		}
	}
	else
	{
		echo '<div><i>no one</i></div>';
	}
	echo '</ul>';		
}

function lscf_check_url($url)
{
	if (empty($url)) return false;   
	$pattern = "/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i";
	return preg_match ($pattern, $url);	
}

function lscf_links_submenu()
{
		// ---------------------  magic_quotes_gpc  -------------------------
		if ( get_magic_quotes_gpc() ) { 
		   $_REQUEST = lscf_request_stripslashes($_REQUEST);
		}
	
	global $wpdb;
	global $lef_links_table;
     
 	$error = false;
	$updated_message = false;
	$updated_message_text = 'Settings was Saved.';
	
	if (! empty($_REQUEST['edit_link_id']))
	{     
		$id = intval($_REQUEST['edit_link_id']);
		$category = intval($_REQUEST['category_'.$id]); 
		$premium = 0;    
		$url = mysql_escape_string(trim($_REQUEST['url']));
		$title = mysql_escape_string(trim($_REQUEST['title']));
		$owner_name = mysql_escape_string(trim($_REQUEST['username']));
		$owner_email = mysql_escape_string(trim($_REQUEST['email']));
		$description = mysql_escape_string(trim($_REQUEST['description']));
		$active = intval(@$_REQUEST['active']);   
		if (strlen($title) == 0) $title = $url; 

		
		if (lscf_check_url($url))
		{
			if ($wpdb->query("UPDATE `$lef_links_table` 
							SET 
								`url` = '$url', 
								`title` = '$title', 
								`description` = '$description',
								`owner_name` = '$owner_name', 
								`owner_email` = '$owner_email', 
								`category` = '$category',
								`premium` = 0,  
								`active` = '$active', 
								`modified_date` = NOW() 
								 
							WHERE (`id` = $id)") !== false)
			{
				$updated_message = true;
			}	
			else 
			{
				$error = 'Cannot add link.';	
			}	
		}
		else 
		{
			$error = 'Invalid URL.';	
		}
	}
	elseif (! empty($_REQUEST['new_link_form']))
	{     
		$category = intval($_REQUEST['category']);   
		$premium = 0;   
		$url = mysql_escape_string(trim($_REQUEST['url']));
		$title = mysql_escape_string(trim($_REQUEST['title']));
		$owner_name = mysql_escape_string(trim($_REQUEST['username']));
		$owner_email = mysql_escape_string(trim($_REQUEST['email']));
		$description = mysql_escape_string(trim($_REQUEST['description']));
		$active = intval(@$_REQUEST['active']);   
		if (strlen($title) == 0) $title = $url; 
		
		if (lscf_check_url($url))
		{		
			$res = $wpdb->get_var("SELECT `id` FROM $lef_links_table WHERE `url` LIKE '$url%';");
			if ($res)
			{
				$error = '<div class="le_error">This link already exists.</div>';
			}
			else
			{
				if ($wpdb->query("INSERT INTO `$lef_links_table` 
										SET 
											`url` = '$url', 
											`title` = '$title', 
											`description` = '$description',
											`owner_name` = '$owner_name', 
											`owner_email` = '$owner_email', 
											`category` = '$category', 
											`premium` = 0, 
											`active` = '$active', 
											`add_date` = NOW(),
											`modified_date` = NOW()") !== false)
				{
					$updated_message = true;
					$updated_message_text = 'Link was added';
				}	                         
				else $error = 'Cannot add link.';
			}	
		}
		else 
		{
			$error = 'Invalid URL';	
		}

	} 
	elseif (! empty($_REQUEST['delete_link_id']))
	{     
		$id = intval($_REQUEST['delete_link_id']);

		$wpdb->query("DELETE FROM `$lef_links_table` WHERE (`id` = $id)");
		
		$updated_message = true;
		$updated_message_text = 'Link was deleted.';
	} 
	
	global $lscf_categories_tree;
	$lscf_categories_tree = lscf_make_categories_tree();
	
	?>
	<div class="wrap">
		<?php lscf_print_info() ?>
		
		<h2>Edit Links</h2>   
		<?php
	     	$links_exchange_free_install_error = get_option('links_exchange_free_install_error');
	     	if ($links_exchange_free_install_error)
	     		echo('<div id="message" class="error"><p><strong>'.$links_exchange_free_install_error.'</strong></p></div>');
			if ($updated_message) echo '<div id="message" class="updated fade"><p><strong>'.$updated_message_text.'</strong></p></div>';
			if ($error) echo('<div id="message" class="error"><p><strong>'.$error.'</strong></p></div>');
		?>
		
		<h4>Existing link</h4>  
		<?php 
			lscf_print_links(); 
		?>
		<br/>
		
		<h4>New link</h4>  
		<form action="" method="post">
			<input type="hidden" name="new_link_form" value="1"/>
			<table width="100%" class="form-table">
				<tr>				
					<td width="170px" valign="top">
						<b>URL:</b>
					</td>
					<td valign="top">
						<input type="text" name="url" value="http://" style="width: 270px" maxlength="100" />
					</td>

					<td width="170px" valign="top">
						<b>Title:</b>
					</td>
					<td valign="top">
						<input type="text" name="title" value="" style="width: 270px" maxlength="100" />
					</td>
				</tr>
				<tr>
					<td valign="top">
						<b>Username:</b>
					</td>
					<td valign="top">
						<input type="text" name="username" value="" style="width: 270px" maxlength="100" />
					</td>

					<td valign="top">
						<b>Email:</b>
					</td>
					<td valign="top">
						<input type="text" name="email" value="" style="width: 270px" maxlength="100" />
					</td>
				</tr>
				<tr>
					<td valign="top">
						<b>Category:</b>
					</td>
					<td valign="top">
						<select style="width: 270px" name="parent">
							<option value="0">no selected</option>
							<?php  lscf_get_parents_categories(0, $lscf_categories_tree, 0, '&rsaquo;'); ?>
						</select>
					</td>
					<td valign="top" colspan="2">
						<b>Activity:</b>
						<input type="checkbox" name="active" checked="checked" value="1" maxlength="100" style="width: 50px" />
					</td>	
				</tr>
				<tr>
					<td valign="top">
						<b>Description:</b>
					</td>	
					<td colspan="3">
						<textarea rows="3" name="description" style="width: 99%"></textarea>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="4">
						<input type="submit" name="add_form" value="Add" class="button"/>
					</td>
				</tr>
			</table>  
		</form> 
	</div>	
	<?php		
}
//--------------------------------------------------------------------------------------------------------

function lscf_import_export_links_submenu()
{
	?>
	<div class="wrap">
		<?php lscf_print_info() ?>
     	
          <h2>Import / Export Links</h2>
          <?php
               $links_exchange_free_install_error = get_option('links_exchange_free_install_error');
               if ($links_exchange_free_install_error)
                    echo('<div id="message" class="error"><p><strong>'.$links_exchange_free_install_error.'</strong></p></div>');
          ?>
          <br/>         
           <?php if(empty($_REQUEST['action_link'])) : ?>
                <h3>Export</h3>
				<a href="<?php echo($_SERVER['REQUEST_URI'].'&action_link=export') ?>">Click for export Links and Categories</a><br />
                <br/>
				
				<h3>Import</h3>
				<form method="post" action="<?php echo($_SERVER['REQUEST_URI'].'&action_link=import') ?>" enctype="multipart/form-data"> 
                     <input type="file" size="25" name="import"/>
                     <input type="submit" class="button" value="Import" />
                </form>
          <?php else : ?>
                <?php if($_REQUEST['action_link']=='export') : ?>
                         <?php $linc=lef_get_damp_link(); ?>
                         <a href="<?php echo($linc['url']);?>">Download</a>
               <?php endif ; ?>
               <?php if($_REQUEST['action_link']=='import') : ?>
                <?php
                     if(!empty($_FILES['import']))
                     {
                          lef_insert_damp_link();
                          echo "Impotr successfully completed";
                     }
                ?>
                    

               <?php endif ; ?>
          <?php endif ; ?>
     </div>     
     <?php          
}


function lef_get_damp_link()
{
     global $wpdb;
     global $lef_links_table;
     global $lscf_categories_tree; 
     global $lef_links_table;
     $links = $wpdb->get_results("SELECT * FROM `$lef_links_table`");
     $categories=lscf_make_categories_tree();
     $damp=array();
     $damp['categories']=$categories;
     $damp['links']=$links;
     
     
     $damp=serialize($damp);
     $damp=wp_upload_bits( 'damp'.date('d_n_y').'.zip', '',$damp);
     return $damp;
}

function lef_insert_damp_link()
{
     global $wpdb;
     global $lef_links_table;
     $file=file($_FILES['import']['tmp_name']);
     if(isset($file[0]))
     {
          $damp=unserialize($file[0]);
          $categories=$damp['categories'];
          $newarray=lef_damp_add_categories($categories);
          $reindex=$newarray[0];
          $newindex=$newarray[1];
          $links=$damp['links'];
          foreach($links as $link)
          {
               $url=$link->url;
               $title=$link->title;
               $description=$link->description;
               $owner_name=$link->owner_name;
               $owner_email=$link->owner_email;
               $category=$link->category;
               $premium=$link->premium;
               $add_date=$link->add_date;
               $modified_date=$link->modified_date;
               $active=$link->active;
               if(in_array($category,$reindex))
               {
                    $category=$newindex[array_search($category,$reindex)];
               }
               $wpdb->query("INSERT INTO `$lef_links_table` 
                                             SET 
                                                  `url` = '$url', 
                                                  `title` = '$title', 
                                                  `description` = '$description',
                                                  `owner_name` = '$owner_name', 
                                                  `owner_email` = '$owner_email', 
                                                  `category` = '$category', 
                                                  `premium` = '$premium',       
                                                  `add_date` = '$add_date',
                                                  `modified_date` = '$modified_date',
												  `active` = '$active'");
          }
     }
}


function lef_damp_add_categories($categories,$reindex=array(),$newindex=array())
{
     global $wpdb;
     global $lef_categories_table;
     foreach($categories as $categori)
     {
          $parent=$categori['parent'];
          if(in_array($categori['parent'],$reindex))
          {
               $parent=$newindex[array_search($categori['parent'],$reindex)];
          }
          $title=$categori['title'];
          $description=$categori['description'];
          //echo $title . '# '.("INSERT INTO `$lef_categories_table` (`parent`, `title`, `path`, `description`) VALUES ($parent, '$title', '".lscf_category_path_by_title($title)."', '$description')")."\n";
          $wpdb->query("INSERT INTO `$lef_categories_table` (`parent`, `title`, `path`, `description`) VALUES ($parent, '$title', '".lscf_category_path_by_title($title)."', '$description')");     
          if($wpdb->insert_id!=$categori['id'])
          {
               $reindex[]=$categori['id'];
               $newindex[]=$wpdb->insert_id;
          }
          if(is_array($categori['children']))
          {
               $newarray=lef_damp_add_categories($categori['children'],$reindex,$newindex);
               $reindex=$newarray[0];
               $newindex=$newarray[1];
          }
     }
     return array($reindex,$newindex);
}
//--------------------------------------------------------------------------------------------------------









// ------------------------------   Hook To the Content   --------------------------------
function lscf_get_header()
{
	$act = $GLOBALS['linkdir_action'];
	$base = $GLOBALS['linkdir_base_url'];

	if (strpos($base, '?') === false) $base .= '?';	
	else $base .= '&';
	
	$styles_dirname = dirname(__FILE__).'/styles/'; 
	$content = '<style type="text/css">'.file_get_contents($styles_dirname.get_option('lscf_style')).'</style>';

	$content .= '
		<div id="le_header">
	         <a id="ls_home_link" 	 class="le_tab'.(($act == '')?				' le_tab_current':'').'" href="'.$base.'">'.get_option('lscf_tab_links_title').'</a>
	         <a id="ls_linktous" 	 class="le_tab'.(($act == 'linktous')?		' le_tab_current':'').'" href="'.$base.'action=linktous">'.get_option('lscf_tab_linktous_title').'</a>
	         <a id="ls_searchlink" 	 class="le_tab'.(($act == 'search')?		' le_tab_current':'').'" href="'.$base.'action=search">'.get_option('lscf_tab_search_title').'</a>
	         <a id="ls_add_link" 	 class="le_tab'.(($act == 'add')?			' le_tab_current':'').'" href="'.$base.'action=add">'.get_option('lscf_tab_addyourlink_title').'</a>      
		</div>';
		           
	return $content;
}

function lscf_get_category_url($cat_id, $link=null, $vars='')
{
	$current = 0;
	if ($cat_id)
	{
		global $wpdb, $lef_categories_table;
		$current = $wpdb->get_row("SELECT * FROM `$lef_categories_table` WHERE (`id` = $cat_id)");
		if (!$current) return '#';
	}
	
	// $href = $GLOBALS['linkdir_base_url'].'category-'.$l->category.'/link-'.$host.'-'.$l->id.'/';
	if (! $link)
	{
		$href = $GLOBALS['linkdir_base_url'];
		$vars .= 'catid='.$cat_id;
		if ($vars) $href .= '?&'.$vars;
		return $href;
	}
	else
	{
		$url = parse_url($link->url);
		$host = str_replace('.', '', $url['host']);
		$href = $GLOBALS['linkdir_base_url'];
		$vars .= 'linkid='.$link->id;
		if ($vars) $href .= '?&'.$vars;
		return $href;
	}				
}

function lscf_get_links_count($cat_id)
{
	global $wpdb;           
	global $lef_links_table;
	
	$WHERE = "(`category` = $cat_id) AND (`active` = 1)";
	if (get_option('lscf_no_show_if_inlessout')) $WHERE = "$WHERE AND ((`in` > `out`) OR (`premium` = 1))";
	$lcount = $wpdb->get_var("SELECT Count(*) FROM `$lef_links_table` WHERE ($WHERE);"); 
	
	return $lcount;	
}

function lscf_content_get_category($cat_id)
{
	$content = '';
	global $wpdb;
	global $lef_categories_table;
	global $lef_links_table;
	
	if ($cat_id)
	{
		// Print title of current category
		$current = $wpdb->get_row("SELECT * FROM `$lef_categories_table` WHERE (`id` = $cat_id)");
		if ((!$current) && (!count($categories))) return 'There is no category for this ID';
		$content .= '<h4 class="le_current_cat">'.$current->title.'</h4>';
		$content .= '<div class="le_current_cat_description">'.$current->description.'</div>';
	}

	$categories = $wpdb->get_results("SELECT * FROM `$lef_categories_table` WHERE (`parent` = $cat_id)");
	
	if (($categories) && (count($categories)))
	{
		$content .= '<ul class="le_categories">';
		if ($cat_id) $content .= '<b>Sub categories:</b>';
		else 		 $content .= '<b>Categories:</b>';
		foreach($categories as $cat)
		{
			$content .= '<li class="le_category">
							<a href="'.lscf_get_category_url($cat->id).'">'.$cat->title.'</a>';  
			if (get_option('lscf_show_links_count')) $content .= ' ('.lscf_get_links_count($cat->id).')';
			$content .= '<br/>'.$cat->description.'</li>';	
		}
		$content .= '</ul>';
	}
		
	$WHERE = "(`category` = $cat_id) AND (`active` = 1)";
	if (get_option('lscf_no_show_if_inlessout')) $WHERE = "$WHERE AND ((`in` > `out`) OR (`premium` = 1))";
	
	$lcount = $wpdb->get_var("SELECT Count(*) FROM `$lef_links_table` WHERE ($WHERE);"); 
	$lpage = intval(@$_REQUEST['lpage']);
	$lcounttopage = intval(get_option('lscf_links_on_page'));
	if ($lcounttopage) $lpages_count = ceil($lcount / $lcounttopage);
	else $lpages_count = 1;
	$lim_start = $lpage * $lcounttopage; 
	
	$lscf_order_by = get_option('lscf_order_by');
	switch ($lscf_order_by)
	{
		case 'add_new_first':
			$q_order_by = ' ORDER BY `add_date` DESC ';
			break;	
			
		case 'add_new_last':
			$q_order_by = ' ORDER BY `add_date` ';
			break;	
			
		case 'a_to_z':
			$q_order_by = ' ORDER BY `title` ';
			break;	
			
		case 'z_to_a':
			$q_order_by = ' ORDER BY `title` DESC ';
			break;	
	}
	
	$links = $wpdb->get_results("SELECT * FROM `$lef_links_table` WHERE ($WHERE) $q_order_by LIMIT $lim_start, $lcounttopage");
	if (($links) && (count($links)))
	{
		$content .= '<ul class="le_links"><b>Links:</b>';
		foreach($links as $link)
		{
			$content .= '<li class="le_link"><a target="'.get_option('lscf_links_target').'" href="'.lscf_get_category_url($cat_id, 0, $vars='findlink='.$link->id).'">'.$link->title.'</a> '.$link->description;
			$content .= ' <a href="'.lscf_get_category_url($cat_id, $link).'">More Info</a>'; 
			$content .= '</li>'; 			
		}
		if ($lpages_count > 1)
		{
			$content .= '<div class="le_pages">Page: ';  
			for ($i = 0; $i < $lpages_count; $i++)
			{
				if ($i == $lpage) $content .= '['.($i+1).'] ';
				else $content .= '<a href="'.lscf_get_category_url($cat_id, 0, 'lpage='.$i).'">'.($i+1).'</a> ';
			}
			$content .= '</div>';
		}
		$content .= '</ul>';
	}
	else
	{
		$content .= '<div class="le_no_one">No links</div>'; 	
	}
	
	return $content;
}

function lscf_get_main_content()
{
	global $wpdb, $lef_categories_table, $lef_links_table;
	$content = '';
	$base = $GLOBALS['linkdir_base_url']; 
	$action = $GLOBALS['linkdir_action'];
	
	$show_cat = intval(@$_GET['catid']); 
	$show_link = intval(@$_GET['linkid']); 

	//echo "show_cat: $show_cat<br>";
	//echo "show_link: $show_link<br>";
	
	if ($show_link)
	{
		$link = $wpdb->get_row("SELECT * FROM `$lef_links_table` WHERE ((`id` = $show_link) AND (`active` = 1));");

		if ($link)
		{
			$cat = $wpdb->get_row("SELECT * FROM `$lef_categories_table` WHERE (`id` = ".$link->category.");"); 
			if ($cat) $cat_id = $cat->id;
			else $cat_id = 0;
			
			$content .= '<div class="le_single">';
			$content .= '<b>'.get_option('lscf_field_site_title').			  '</b> <a target="'.get_option('lscf_links_target').'" href="'.lscf_get_category_url($cat_id, 0, $vars='findlink='.$link->id).'">'.$link->title.'</a><br/>';
			if (get_option('lscf_show_field_site_url'))	
				$content .= '<b>'.get_option('lscf_field_site_url_title').		  '</b> '.$link->url.'<br/>'; 
            $content .= '<b>'.get_option('lscf_field_description_title'). 	  '</b> '.$link->description.'<br/>'; 
            if (get_option('lscf_show_field_username'))	
				$content .= '<b>'.get_option('lscf_field_username_title').		  '</b> '.$link->owner_name.'<br/>';
			if (get_option('lscf_show_field_date_and_time'))			
				$content .= '<b>'.get_option('lscf_field_date_and_time_title').'</b> '.$link->add_date.'<br/>'; 
			
			if (get_option('lscf_show_hits'))
			{
				$content .= '<b>Hits In:</b> '.$link->in.'<br/>';
				$content .= '<b>Hits Out:</b> '.$link->out.'<br/>';
			}
           
   			$_parsed_url = parse_url($link->url);
      		$host = $_parsed_url['host'];
      		
            $content .= '<table class="le_cached">';
            
            if (get_option('lscf_show_cached_pages')) $content .= '
								<tr><td>Cached Pages:</td>
								<td><a href="http://www.google.com/search?ie=UTF-8&amp;q=site:'.$host.'" target="_blank">Google</a>, <a href="http://search.yahoo.com/search?p=site:'.$host.'" target="_blank">Yahoo!</a>, <a href="http://search.msn.com/results.aspx?q=site:'.$host.'" target="_blank">MSN</a>, <a href="http://www.altavista.com/web/results?q=site:'.$host.'" target="_blank">AltaVista</a></td>
								</tr>';
								
			if (get_option('lscf_show_backlinks')) $content .= '
								<tr><td>Backlinks:</td>
								<td><a href="http://www.google.com/search?ie=UTF-8&amp;q=link:'.$host.'" target="_blank">Google</a>, <a href="http://search.yahoo.com/search?p=link:'.$host.'" target="_blank">Yahoo!</a>, <a href="http://search.msn.com/results.aspx?q=link:'.$host.'" target="_blank">MSN</a>, <a href="http://www.altavista.com/web/results?q=links:'.$host.'" target="_blank">AltaVista</a></td>
								</tr>';
								
			if (get_option('lscf_show_other_links')) $content .= '
								<tr><td>Other Links:</td>
								<td><a href="http://www.alexa.com/data/details/main/'.$host.'" target="_blank">Alexa.com</a>, <a href="http://whois.net/whois_new.cgi?d=&amp;tld='.$host.'" target="_blank">Whois.net</a></td>
								</tr>';
			$content .= '</table>';
            
            $content .= '</div>';		
		}
		else $content .= '<div class="le_no_one">Not found</div>';
	}
	else
	{
		$content .=	lscf_content_get_category($show_cat);	
	}
	
	return $content;	
} 

function lscf_get_linktous_content()
{
	$base = $GLOBALS['linkdir_base_url'];
	if (strpos($base, '?') === false) $base .= '?';	
	else $base .= '&';
	
	$linktous = get_option('lscf_link_to_us');
	$content = 'First of all, you should add a link to our blog in your site:<br/> 
				<textarea id="ls_linktous_textarea">'.$linktous.'</textarea>
				<br/>  				
				After that, add a link on our site: <a href="'.$base.'action=add">Add link</a>.';
	
	
	return $content;	
}   

function lscf_get_search_content()
{
	$content = '';
	$searchtext = '';
	$searchtype = 'in_all';
	if (!empty($_REQUEST['searchtext']))
	{
		$searchtext = mysql_escape_string(strtolower(trim($_REQUEST['searchtext']))); 
		if (strlen($searchtext))
		{
			$searchtype = $_REQUEST['searchtype'];
			global $wpdb;
			global $lef_links_table;
			$where = '(`active`=1)AND';
			switch ($searchtype)
			{
				case 'in_title':
				    $where .= "(lower(`title`) like '%$searchtext%')";
					break; 
				case 'in_url':
				    $where .= "(lower(`url`) like '%$searchtext%')";
					break; 
				case 'in_description':
				    $where .= "(lower(`description`) like '%$searchtext%')";
					break; 
					
				default :
				    $where .=    "((lower(`title`) like '%$searchtext%') 
								OR (lower(`url`) like '%$searchtext%') 
								OR (lower(`description`) like  '%$searchtext%'))";
					break;				
			}
			
			$links = $wpdb->get_results("SELECT * FROM `$lef_links_table` WHERE $where;");
			
			$content .= '<ul id="le_search_results"><b>Search results:</b>'; 
			if ((! is_array($links)) || (! count($links)))
			{
				$content .= '<div class="le_no_one">No one</div>'; 	
			}
			else
			{
				foreach($links as $l)
					$content .= '<li><a href="'.lscf_get_category_url($l->category, $l).'">'.$l->title.'</a> ('.$l->url.')</li>';	         
			} 
			$content .= '</ul>';
		}	
	}

	$content .= '<form name="search" action="" method="post">
					<div id="le_main">
						<div id="le_search_left">
							<b>Search For: </b>
							<br/>
							<input type="text" name="searchtext" value="'.attribute_escape($searchtext).'" name="Search"/>
						</div>
				        <div id="le_search_right">
							<b>Search: </b>  
							<br/>
							<select name="searchtype">
								<option '.(($searchtype == 'in_all')? 'selected="selected" ': '').' value="in_all">In All</option>
								<option '.(($searchtype == 'in_title')? 'selected="selected" ': '').' value="in_title">In Title</option>
								<option '.(($searchtype == 'in_url')? 'selected="selected" ': '').' value="in_url">In URL</option>
								<option '.(($searchtype == 'in_description')? 'selected="selected" ': '').' value="in_description">In Description</option>
							</select>
						</div>
					</div>
					<div id="le_search_submit">
						<input type="submit" value="Search" name="submit"/>
					</div>
				</form>';	
	
	return $content;	
} 

function lef_send_mime_mail($name_from, $email_from, $name_to, $email_to, 
						$data_charset, $send_charset, $subject, $body, 
						$file_data=null, $file_type='', $file_name='') 
{
	$to = lef_mime_header_encode($name_to, $data_charset, $send_charset) . ' <' . $email_to . '>';
	$subject = lef_mime_header_encode($subject, $data_charset, $send_charset);
	$from =  lef_mime_header_encode($name_from, $data_charset, $send_charset).' <' . $email_from . '>';

	if ($data_charset != $send_charset) {
		$body = iconv($data_charset, $send_charset, $body);
	}
	$headers = "From: ".$from; 
	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	$headers .= "\nMIME-Version: 1.0\n" . 
	            "Content-Type: multipart/mixed;\n" . 
	            " boundary=\"{$mime_boundary}\""; 
	$email_message = $body;
	$email_message .= "This is a multi-part message in MIME format.\n\n" . 
	                "--{$mime_boundary}\n" . 
	                "Content-Type:text/html; charset=\"$send_charset\"\n" . 
	                "Content-Transfer-Encoding: 7bit\n\n" . 
	$email_message . "\n\n"; 
	
	if ($file_data)
	{ 
		$file_data = chunk_split(base64_encode($file_data)); 
		
		$email_message .= "--{$mime_boundary}\n" . 
		                  "Content-Type: {$file_type};\n" . 
		                  " name=\"{$file_name}\"\n" . 
		                  "Content-Transfer-Encoding: base64\n\n" . 
		                  $file_data . "\n\n" . 
		                  "--{$mime_boundary}--\n"; 
	}

	return mail($to, $subject, $email_message, $headers);
}
function lef_mime_header_encode($str, $data_charset, $send_charset) 
{
	if ($data_charset != $send_charset) {
		$str = iconv($data_charset, $send_charset, $str);
	}
	return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}

function lscf_get_add_content()
{
	// ---------------------  magic_quotes_gpc  -------------------------
	if ( get_magic_quotes_gpc() ) { 
	   $_REQUEST = lscf_request_stripslashes($_REQUEST);
	}
		
     $content = '';
     $complit = false;
     $def_category = 0;
     if (! empty($_REQUEST['add_link_form']))
     {     
          global $wpdb;
          global $lef_links_table;
          
          $category = intval($_REQUEST['category']);   
          $url = mysql_escape_string(trim($_REQUEST['url']));
          $title = mysql_escape_string(trim($_REQUEST['title']));
          $owner_name = mysql_escape_string(trim($_REQUEST['username']));
          $owner_email = mysql_escape_string(trim($_REQUEST['email']));
          $description = mysql_escape_string(trim($_REQUEST['description']));
          $active = intval(get_option('lscf_activated_by_default'));   
          if (strlen($title) == 0) $title = $url; 
                                                   
          if (strlen($description) > 250)
          {
               $content .= '<div class="le_error">'.get_option('lscf_message_description_tolong').'</div>';     
          }
          else
          {
               if(!empty($_POST['le_chapcha'])&&$GLOBALS['chapcha_send'])
               {
               		if (substr($url, 0, 4) != 'http') $url = 'http://'.$url;
                    if (lscf_check_url($url))
                    {
                         $res = $wpdb->get_var("SELECT `id` FROM $lef_links_table WHERE `url` LIKE '$url%';");
                         if ($res)
                         {
                              $content .= '<div class="le_error">This link already exists.</div>';
                         }
                         else
                         {
                              if ($wpdb->query("INSERT INTO `$lef_links_table` 
                                                            SET 
                                                                 `url` = '$url', 
                                                                 `title` = '$title', 
                                                                 `description` = '$description',
                                                                 `owner_name` = '$owner_name', 
                                                                 `owner_email` = '$owner_email', 
                                                                 `category` = '$category', 
                                                                 `premium` = 0, 
                                                                 `active` = $active, 
                                                                 `add_date` = NOW(),
                                                                 `modified_date` = NOW();") !== false)
                              {
                                   $content .= '<div class="le_message">'.get_option('lscf_message_link_added').'</div>';
                                   $complit = true;
                                   
                                   // send link to administrator for activation
                                   if (! $active)
                                   {
                                   		$key = md5($url.$title.$owner_email.$owner_name.$wpdb->insert_id.'key');
                                   		$email_message = 'New link was added.<br/>
										   <br/>
											Url: '.$url.'<br/> 
											Site Title: '.$title.'<br/> 
											Description: '.$description.'<br/>
											Username: '.$owner_name.'<br/> 
											Email: '.$owner_email.'<br/> 	
											<br/> 
											Link is not active now. 
											<a href="'.$GLOBALS['linkdir_base_url'].'activate/?id='.$wpdb->insert_id.'&k='.$key.'">Activate</a>';
                                   	
										lef_send_mime_mail('Link Exchange WP', get_option('lscf_admin_email'), 'WP Admin', 
											get_option('lscf_admin_email'), 'UTF-8', 'UTF-8', 'New Link', $email_message);
                                   	
                                   }                                                                    
                              }                              
                              else 
                              {
                                   $content .= '<div class="le_error">Cannot add link.</div>';
                              }     
                         }
                    }
                    else 
                    {
                         $content .= '<div class="le_error">'.get_option('lscf_message_invalid_url').'</div>';
                    }
               }
               else
               {
                    $content .= '<div class="le_error">'.get_option('lscf_message_invalid_code').'</div>';
               }
          }
     } 
     else
     {
		if (preg_match ('#/category-([0-9]*)/#i', $_SERVER['HTTP_REFERER'], $res))
			$def_category = $res[1];
     }

     if (! $complit)
     {
          $lscf_categories_tree = lscf_make_categories_tree();
          $content .= '
          <form id="le_add_link_form" action="" method="post">
               <input type="hidden" name="add_link_form" value="1"/>
               <table width="100%" class="form-table">
                    <tr>                    
                         <td width="170px" valign="top">
                              <b>'.get_option('lscf_field_site_url_title').'</b>
                         </td>
                         <td valign="top">
                              <input type="text" name="url" value="http://" style="width: 270px" maxlength="100" />
                         </td>
                    </tr>
                   <tr>
                         <td width="170px" valign="top">
                              <b>'.get_option('lscf_field_site_title').'</b>
                         </td>
                         <td valign="top">
                              <input type="text" name="title" value="" style="width: 270px" maxlength="100" />
                         </td>
                    </tr>
                    <tr>
                         <td valign="top">
                              <b>'.get_option('lscf_field_username_title').'</b>
                         </td>
                         <td valign="top">
                              <input type="text" name="username" value="" style="width: 270px" maxlength="100" />
                         </td>
                    </tr>
                   <tr>
                         <td valign="top">
                              <b>'.get_option('lscf_field_email_title').'</b>
                         </td>
                         <td valign="top">
                              <input type="text" name="email" value="" style="width: 270px" maxlength="100" />
                         </td>
                    </tr>
                    <tr>
                         <td valign="top">
                              <b>'.get_option('lscf_field_category_title').'</b>
                         </td>
                         <td valign="top">
                              <select style="width: 270px" name="category">
                                   <option value="0">no selected</option>
                                   '.lscf_get_categories_select(0, $lscf_categories_tree, $def_category, '&rsaquo;').'
                              </select>
                         </td>
                    </tr>
                    <tr>
                         <td valign="top">
                              <b>'.get_option('lscf_field_description_title').'</b>
                         </td>     
                         <td colspan="3">
                              <textarea rows="3" name="description" style="width: 99%"></textarea>
                         </td>
                    </tr>
                    <tr>
                         <td class="le_right" colspan="2">
                              <lable for="chapcha">'.$GLOBALS['chapcha1'].'+'.$GLOBALS['chapcha2'].'=</lable>
                              <input type="text" clsss="le_chapcha" name="le_chapcha" value=""/>
                              <input type="submit" name="add_form" value="Add" class="button"/>
                         </td>
                    </tr>
               </table>  
          </form>'; 
     }
      
     return $content;     	
} 

function lef_links_catalogue_content($content)
{     
	//	$GLOBALS['linkdir_base_url'] = $GLOBALS['linkdir_base_url'].'/';
	if (empty($GLOBALS['linkdir_action'])) $GLOBALS['linkdir_action'] = ''; 	
	if (get_the_ID() != get_option('lscf_page_id')) return $content;

	$pnum = 0;
	switch ($GLOBALS['linkdir_action'])
	{
		case 'linktous':             
			$tab_content = lscf_get_linktous_content(); 
			break;	
		case 'search':
			$tab_content = lscf_get_search_content();   
			break;
		case 'add':
			$tab_content = lscf_get_add_content();  
			break;
		case 'activate':
			$tab_content = lscf_get_activate_content(); 
			break;
			
		default:
			$tab_content = lscf_get_main_content(); 
			break;
	} 
	
	return $content.lscf_get_header().'<div id="le_tabcontent">'.$tab_content.'</div><div id="le_poweredby"> <a target="_blank" href="http://www.linkexchangewp.com/">Link Exchange WP Free</a> by <a target="_blank" href="http://www.patrickwagner.com/">Patrick Wagner</a> </div>';
}


//http://plugin/plugin/activate/?id=15&k=8c066dc6be04641a61863647d77dee75
function lscf_get_activate_content()
{
	if ((! empty($_REQUEST['id'])) && (! empty($_REQUEST['k'])))
	{
		$id = intval($_REQUEST['id']);
		$k = $_REQUEST['k'];
		$ok = false;
		global $wpdb, $lef_categories_table, $lef_links_table;
              
		$link = $wpdb->get_row("SELECT * FROM `$lef_links_table` WHERE `id` = $id;");  
		$key = md5($link->url.$link->title.$link->owner_email.$link->owner_name.$link->id.'key');
		if ($key == $k)
		{
			$ok = ($wpdb->query("UPDATE `$lef_links_table` SET `active`= 1 WHERE `id`=$id;") !== false);	
		}
		
		if ($ok) return '<div class="le_message">Link was activated</div>';
		else return '<div class="le_message">Error on link activation</div>';
	}	
}












   


?>