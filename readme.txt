=== Plugin Name ===
Tags: google toolbar
Requires at least: 1.5
Tested up to: 2.3

Adds a link to the sidebar (or other location) to install a button for your blog in the Google Toolbar.

== Description ==
This plugin gives you a link in your sidebar (or wherever you want it in your theme) that installs a button to the Google Toolbar.  The button gives the ability to monitor the RSS feed of your blog from the Google Toolbar.  It also adds your blog to the list of possible places to search when running a search from the Google Toolbar.

If this plugin is used with Wordpress versions earlier than 2.2 and a widget implementation will be used then the Sidebar Widgets plugin (http://automattic.com/code/widgets/) and Widgetized theme is required.

== Installation ==
1. Upload the googletoolbar.php to to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== How To Use ==
#Template Tag (Non Widget)
To use the plugin simply put the following code in the place you want the link to appear:
* <?php if(function_exists(googletoolbar)) : googletoolbar(link text); endif; ?>
Replace link text with the words you want to appear as your link.  For example, if you want your link to read "Add to Google Toolbar" you would put in the following:
* <?php if(function_exists(googletoolbar)) : googletoolbar('Add to Google Toolbar'); endif; ?>

#Widget
1. Go to Presentation->Sidebar Widgets in your admin interface.
2. Drag and drop the Google Toolbar widget from the Available Widgets section to where you want it on your sidebar.
3. Click the configure icon to open the options for this widget.
4. There are 2 options:
	* Title - The title for this widget that you want to display in your sidebar.  "Google Toolbar Icon Link" is the default.
	* Link text - The words you want to appear as your link.  "Add to Google Toolbar" is the default.
5. After selecting your options click the X in the top right corner.
6. Click the Save Changes button.

After the page reloads view your site.  The Google Toolbar link will appear in your sidebar with your selected options.

#Options Page
A page exists under Options->Google Toolbar.  This page allows you to change the name of the XML file that the plugin creates to add your blog to the Google Toolbar and/or to attach a specific image file to use as an icon for your blog in the toolbar (if you don't specify an icon then the Google Toolbar will use a generic icon).