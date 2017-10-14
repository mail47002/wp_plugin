<?php
/*
Plugin Name: MyAriPlugin
Plugin URI: http://ari.in.ua
Description: Test plugin
Version: 1.0.0
Author: Ari
Author URI: http://ari.in.ua
*/


/*  Copyright ГОД  Ari  (email: mail47002@gmail.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class MyAriPlugin
{
	public function __construct()
	{
		add_option('myariplugin_app_key', '140086723287465');
		add_option('myariplugin_app_secret', 'a19e277f2ea09d85669b1e75769b5015');
		add_option('myariplugin_page_id', '185550645139549');

		if (function_exists ('add_shortcode') )
		{
			add_shortcode('myariplugin', array (&$this, 'myariplugin_shortcode'));

			add_action('admin_menu',  array (&$this, 'admin') );
		}
	}

	public function admin ()
	{
		if ( function_exists('add_options_page'))	{
			add_options_page( 'Myariplugin Options', 'Myariplugin', 8, basename(__FILE__), array (&$this, 'myariplugin_form') );
		}
	}

	public function myariplugin_form()
	{

		$myariplugin_app_key = get_option('myariplugin_app_key');
		$myariplugin_app_secret = get_option('myariplugin_app_secret');
		$myariplugin_page_id = get_option('myariplugin_page_id');

		if ( isset($_POST['submit']) ) {
		   if (function_exists('current_user_can') && !current_user_can('manage_options') )
			  die ( _e('Hacker?', 'myariplugin') );

			if (function_exists ('check_admin_referer') ) {
				check_admin_referer('myariplugin_form');
			}

			update_option('myariplugin_app_key', $_POST['myariplugin_app_key']);
			update_option('myariplugin_app_secret', $_POST['myariplugin_app_secret']);
			update_option('myariplugin_page_id', $_POST['myariplugin_page_id']);
		}

		?>
		<div class='wrap'>
			<h2><?php _e('MyAriPlugin settings', 'myariplugin'); ?></h2>
			<form name="myariplugin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=myariplugin.php&amp;updated=true">
				<?php
					if (function_exists ('wp_nonce_field')) {
						wp_nonce_field('myariplugin_form');
					}
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('App key:', 'myariplugin'); ?></th>
						<td>
							<input type="text" name="myariplugin_app_key" size="80" value="<?php echo $myariplugin_app_key; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('App secret:', 'myariplugin'); ?></th>
						<td>
							<input type="text" name="myariplugin_app_secret" size="80" value="<?php echo $myariplugin_app_secret; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Id page:', 'myariplugin'); ?></th>
						<td>
							<input type="text" name="myariplugin_page_id" size="80" value="<?php echo $myariplugin_page_id; ?>" />
						</td>
					</tr>
				</table>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="myariplugin_app_key,myariplugin_app_secret,myariplugin_page_id" />
				<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
		<?
	}

	public function myariplugin_shortcode ($atts, $content = null)
	{

		$urls = 'https://graph.facebook.com/v2.8/'. get_option('myariplugin_page_id') . '?fields=fan_count&access_token='. get_option('myariplugin_app_key') . '|' . get_option('myariplugin_app_secret');
	  $data = @file_get_contents($urls);
	  if($data) {
	    $fan_count = json_decode($data);
	   	$content =  'Users: '.$fan_count->fan_count;
	  } else {
	  	$content = 'error';
	  }

	  return "<span style='white-space: nowrap; display: inline !important;'><b>$content</b></span>";

	}

}

$test = new MyAriPlugin();
