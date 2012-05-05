<?php
/*
Plugin Name: FoodStorm Sidebar Widget
Plugin URI: http://www.foodstorm.com
Description: Sidebar widget to display a link to your FoodStorm shopping cart.
Version: 1.0.0
Author: CaterXpress
Author URI: http://www.foodstorm.com
*/

/*	Copyright (c) 2012

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );

if ( !defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

// Define the plugin content url
define("FS_PLUGIN_URL", WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__) ) . "/");

function widget_FoodStorm_activate() {

	include_once('foodstorm.php');

	// INITIALISE OPTIONS
	// Get options
	$defaultOptions = array(
        'title'=>'Online Ordering',
		'shoppingcarturl'=>'yoururl.foodstorm.com',
        'linktitle'=>'Order Online',
        'newwindow'=>'_blank');
	$options = get_option('widget_FoodStorm');

	// options exist? if not set defaults
	if ( !is_array($options) ) {
		update_option('widget_FoodStorm', $defaultOptions);
	} else {
		// make sure the all the current available options are there
		foreach ($options as $i => $value) {
			$defaultOptions[$i] = $options[$i];
		}
		update_option('widget_FoodStorm', $defaultOptions);
	}

}

function widget_FoodStorm_init() {

	if (!function_exists('register_sidebar_widget')) return;

	function widget_FoodStorm($args) {
		include_once('foodstorm.php');

		// Extract title from options
		extract($args);
		$options = get_option('widget_FoodStorm');
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
		$shoppingCartUrl = htmlspecialchars($options['shoppingcarturl'], ENT_QUOTES);
		$shoppingCartUrl = (strrpos($shoppingCartUrl, 'http://') === false ? 'http://' . $shoppingCartUrl : $shoppingCartUrl);
		$linkTitle = htmlspecialchars($options['linktitle'], ENT_QUOTES);
		$newWindow = htmlspecialchars($options['newwindow'], ENT_QUOTES);

		// Render widget
		echo $before_widget . $before_title	. $title . $after_title;
?>
		<ul>
			<li><a target="<?php echo $newWindow; ?>" href="<?php echo $shoppingCartUrl; ?>"><?php echo $linkTitle; ?></a></li>
		</ul>
<?php
		echo $after_widget;
	}

	function widget_FoodStorm_control() {

		// Get options
		$options = get_option('widget_FoodStorm');

        // form posted?
		if ( $_POST['FoodStorm-submit'] ) {
			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['FoodStorm-title']));
            $options['shoppingcarturl'] = strip_tags(stripslashes($_POST['FoodStorm-shoppingcarturl']));
            $options['linktitle'] = strip_tags(stripslashes($_POST['FoodStorm-linktitle']));
            $options['newwindow'] = strip_tags(stripslashes($_POST['FoodStorm-newwindow']));
			update_option('widget_FoodStorm', $options);
		}

		// Get options for form fields to show
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$shoppingCartUrl = htmlspecialchars($options['shoppingcarturl'], ENT_QUOTES);
		$linkTitle = htmlspecialchars($options['linktitle'], ENT_QUOTES);
		$newWindow = htmlspecialchars($options['newwindow'], ENT_QUOTES);
?>
		<!-- The form field -->
		<p>
			<label for="FoodStorm-title"> <?php echo __('Title:'); ?></label>
			<input class='widefat' id="FoodStorm-title" name="FoodStorm-title" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="FoodStorm-shoppingcarturl"> <?php echo __('FoodStorm shopping cart URL:'); ?></label>
			<input class='widefat' id="FoodStorm-shoppingcarturl" name="FoodStorm-shoppingcarturl" type="text" value="<?php echo $shoppingCartUrl; ?>" />
		</p>
		<p>
			<label for="FoodStorm-linktitle"> <?php echo __('Link title:'); ?></label>
			<input class='widefat' id="FoodStorm-linktitle" name="FoodStorm-linktitle" type="text" value="<?php echo $linkTitle; ?>" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="FoodStorm-newwindow" name="FoodStorm-newwindow" value="_blank" <?php echo ($newWindow == '_blank' ? 'checked="checked"' : ''); ?> />
			<label for="FoodStorm-newwindow"> <?php echo __('Open in new window'); ?></label>
	    </p>
		<input type="hidden" id="FoodStorm-submit" name="FoodStorm-submit" value="1" />
<?php
	}

	// Register widget for use
	register_sidebar_widget(array('FoodStorm', 'widgets'), 'widget_FoodStorm');
	// Register settings for use, 300x100 pixel form
	register_widget_control(array('FoodStorm', 'widgets'), 'widget_FoodStorm_control');
}

// *** ACTIVATION  ***
register_activation_hook(__FILE__, 'widget_FoodStorm_activate');

// *** INITIALISE WIDGET ***
add_action('plugins_loaded', 'widget_FoodStorm_init');

?>