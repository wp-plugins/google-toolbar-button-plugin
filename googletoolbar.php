<?php
/*
Plugin Name: Google Toolbar Button
Version: 1.2
Plugin URI: http://nothing.golddave.com/?page_id=78
Description: Creates an XML file for a Google Toolbar button and adds a link for it to your blog.
Author: David Goldstein
Author URI: http://nothing.golddave.com
Update:
*/

function googletoolbar($linktext){
	$current_options = get_option('google_toolbar_options');
	$xmllink = get_bloginfo('url').'/wp-content/'.$current_options["file_name"];
	
	echo '<a href="http://toolbar.google.com/buttons/add?url='.$xmllink.'">'.$linktext.'</a>';
}

function create_xml(){
	$current_options = get_option('google_toolbar_options');
	$xmlfile = ABSPATH.'wp-content/'.$current_options["file_name"];
	
	$xmlheader = '<?xml version="1.0" encoding="utf-8"?>';
	$webimage = $current_options["image_url"];
	if (!empty($webimage)) {
		$imagename =  ABSPATH.'wp-content/'.substr(strrchr($webimage, "/"), 1 );
		if (!copy($webimage, $imagename)) {
			echo "failed to copy $file....<br>";
		}
		$handle = fopen($imagename,'rb');
		$file_content = fread($handle,filesize($imagename));
		fclose($handle);
		$image = base64_encode($file_content);
	}
	ob_start();
	?>
<?php echo $xmlheader; ?>
	<custombuttons xmlns="http://toolbar.google.com/custombuttons/">
		<button>
			<search><?php bloginfo('url'); ?>/index.php?s={query}</search>
			<site><?php bloginfo('url'); ?></site>
			<feed refresh-interval="3800"><?php bloginfo('url'); ?>/?feed=rss2</feed>
			<title><?php bloginfo('name'); ?></title>
			<description><?php bloginfo('description'); ?></description>
			<?php if (!empty($webimage)){
				?>			<icon mode="base64" type="image/x-icon"><?php echo $image; ?></icon>
			<?php } ?>
		</button>
	</custombuttons>
	<?php
	$src = ob_get_contents();
	ob_end_clean();
	file_put_contents($xmlfile, $src);	
}

function file_put_contents($filename, $data, $file_append = false) {
	$fp = fopen($filename, (!$file_append ? 'w' : 'a'));
		if(!$fp) {
		trigger_error('file_put_contents cannot write in file.', E_USER_ERROR);
			return;
		}
		fwrite($fp, $data);
		fclose($fp);
}

function googletoolbar_add_pages() {
	add_options_page('GoogleToolbar', 'GoogleToolbar', 10, __FILE__, 'OptionsPage');
}

function OptionsPage() {
	$current_options = get_option('google_toolbar_options');
	if ($_POST['action']){ ?>
		<div class="updated"><p><strong>Options saved and XML regenerated.</strong></p></div>
	<?php } ?>
	<div class="wrap">
		<h2>Google Toolbar Options</h2>
		<p>These options will be used to generate the XML file that your Google Toolbar icon will be based on.</p>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>">
			<fieldset>
				<input type="hidden" name="action" value="generate_xml" />
				<table width="100%" cellspacing="2" cellpadding="5" class="editform">
					<tr>
						<th valign="top" scope="row"><label for="file_name">File name:</label></th>
						<td><input id="file_name" type="text" name="file_name" value="<?php echo $current_options["file_name"]; ?>" size="80"><br>
						The default filename for the XML file that will integrate to the Google Toolbar to become your<br>toolbar icon is <?php echo str_replace (" ", "_", get_bloginfo('name')).'.xml'; ?>.  If you would like to change the filname then type your<br>preferred filename above.</td>
					</tr>
					<tr>
						<th valign="top" scope="row"><label for="image_url">Image URL:</label></th>
						<td><input id="image_url" type="text" name="image_url" value="<?php echo $current_options["image_url"]; ?>" size="80"><br>
						Enter the URL of the image you'd like to use as the toolbar icon for your Google Toolbar item.<br>If you don't have one then the toolbar will use a default icon.</td>
					</tr>
					<?php
					$webimage = $current_options["image_url"];
					if (!empty($webimage)) { ?>
					<tr>
						<th valign="top" scope="row">Your current icon:</th>
						<td><img src="<?php echo bloginfo('url').'/wp-content/'.substr(strrchr($webimage, "/"), 1 ); ?>" ></td>
					<?php } ?>
				</table>
			</fieldset>
			<p class="submit">
				<input type="submit" name="Submit" value="Update Options and Regenerate XML &raquo;" />
			</p>
		</form>	
	</div>
<?php }

add_action('admin_menu', 'googletoolbar_add_pages');

function google_toolbar_save_options() {
	$current_options = get_option('google_toolbar_options');
	if (($current_options["file_name"]) != ($_POST["file_name"])) {
		$dir = ABSPATH."wp-content";
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				unlink($dir . "/" . $current_options["file_name"]);
				closedir($dh);
			}
 		}
 	}
	
 	if (!empty($current_options["image_url"])) {
 		if (empty($_POST["image_url"])) {
 			$dir = ABSPATH."wp-content/";
 			if (is_dir($dir)) {
 				if ($dh = opendir($dir)) {
 					unlink($dir . "/" . substr(strrchr($current_options["image_url"], "/"), 1 ));
 					closedir($dh);
 				}
 			}
 		}
 	}
 	
 	// create array
 	if (!empty($_POST["file_name"])) {
 		$google_toolbar_options["file_name"] = $_POST["file_name"];
 	} else {
 		$google_toolbar_options["file_name"] = str_replace (" ", "_", get_bloginfo('name')).'.xml';
 	}
	$google_toolbar_options["image_url"] = $_POST["image_url"];
	update_option('google_toolbar_options', $google_toolbar_options);
	$options_saved = true;
	create_xml();
}

if (!get_option('google_toolbar_options')){
	// create default options
	$google_toolbar_options["file_name"] = str_replace (" ", "_", get_bloginfo('name')).'.xml';
	$google_toolbar_options["image_url"] = '';
	
	update_option('google_toolbar_options', $google_toolbar_options);
}

if ($_POST['action'] == 'generate_xml'){
	google_toolbar_save_options();
}

function widget_googletoolbar_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;
		
		function widget_googletoolbar($args) {
			extract($args);
			$options = get_option('widget_googletoolbar');
			$title = empty($options['title']) ? __('Google Toolbar Icon Link') : $options['title'];
			$linktext = empty($options['linktext']) ? __('Add to Google Toolbar') : $options['linktext'];
			echo $before_widget . $before_title . $title . $after_title;
			googletoolbar($linktext);
			echo $after_widget;
	}
	
	function widget_googletoolbar_control() {
		$options = $newoptions = get_option('widget_googletoolbar');
		if ( $_POST["googletoolbar-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["googletoolbar-title"]));
			$newoptions['linktext'] = strip_tags(stripslashes($_POST["googletoolbar-linktext"]));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_googletoolbar', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$linktext = htmlspecialchars($options['linktext'], ENT_QUOTES);
		?>
		<p style="text-align:right;"><label for="googletoolbar-title"><?php _e('Title:'); ?><input style="width: 200px;" id="googletoolbar-title" name="googletoolbar-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<p style="text-align:right;"><label for="googletoolbar-linktext"><?php _e('Link text:'); ?><input style="width: 200px;" id="googletoolbar-linktext" name="googletoolbar-linktext" type="text" value="<?php echo $linktext; ?>" /></label></p>
		<input type="hidden" id="googletoolbar-submit" name="googletoolbar-submit" value="1" />
		<?php
	}
	
	register_sidebar_widget('Google Toolbar', 'widget_googletoolbar');
	register_widget_control('Google Toolbar', 'widget_googletoolbar_control', 300, 150);
}

add_action('widgets_init', 'widget_googletoolbar_init');
?>