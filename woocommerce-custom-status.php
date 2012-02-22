<?php
/*
Plugin Name: WooCommerce Custom Status
Plugin URI: http://www.garmantech.com/wordpress-plugins/woocommerce-custom-status/
Description: Gives WooCommerce store administrators the ability to add custom order statuses.
Version: 1.0
Author: Garman Technical Services
Author URI: http://www.garmantech.com/wordpress-plugins/
License: GPLv2
*/

/*
Copyright 2012  Garman Technical Services  (email : contact@garmantech.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('woocommerce_custom_status')) return;
if (in_array('woocommerce/woocommerce.php',apply_filters('active_plugins',get_option('active_plugins')))) 
	add_action('plugins_loaded', 'woocommerce_custom_status_init', 0);
function woocommerce_custom_status_init() { $woocommerce_custom_status = new woocommerce_custom_status; }

class woocommerce_custom_status {
	function __construct() {
		add_action('admin_init',array(&$this,'admin_style'));
		add_action('admin_menu',array(&$this,'admin_menu'));
	}
	
	function admin_menu() {
		add_submenu_page('woocommerce', __('Order Statuses', 'wc_order_status'),  __('Order Statuses', 'wc_order_status') , 'manage_woocommerce', 'woocommerce_order_status', array(&$this,'options_page'));
	}
	
	function admin_style() {
		global $woocommerce;
		wp_enqueue_style('woocommerce_admin_styles', $woocommerce->plugin_url().'/assets/css/admin.css');	
	}
	
	function options_page() { 
		if(isset($_GET['action']) && $_GET['action']=='add') {
			if(isset($_POST['status']) && trim($_POST['status'])!='') {
				$result = wp_insert_term($_POST['status'], 'shop_order_status');
				if($result>0) {	$result = __('You have added a status!','wc_order_status'); }
				else { $result = __('Your status was not added.','wc_order_status'); }
			} else $result = __('You did not include a status name!','wc_order_status');
		} elseif(isset($_GET['remove']) && $_GET['remove'] > 0) {
			wp_delete_term($_GET['remove'],'shop_order_status');
			$result = __('The status has been removed.','wc_order_status');
		}
		
		$defaults = array('pending','failed','on-hold','processing','completed','refunded','cancelled');
		$statuses = (array) get_terms('shop_order_status', array('hide_empty' => 0, 'orderby' => 'id'));
		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" action="<?php echo admin_url('admin.php?page=woocommerce_order_status&action=add'); ?>">
				<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
				<h2><?php _e('Custom Order Statuses for WooCommerce','wc_order_status'); ?></h2>
				<?php if(isset($result)) { echo '<h3>'.$result.'</h3>'; } ?>
				<table class="widefat" style="width:auto; float:left; display:inline; clear:none;">
					<thead>
						<tr>
							<th style="min-width:30px;"><?php _e('ID','wc_order_status'); ?></th>
							<th style="min-width:120px;"><?php _e('Slug','wc_order_status'); ?></th>
							<th style="min-width:120px;"><?php _e('Name','wc_order_status'); ?></th>
							<th style="min-width:120px;"><?php _e('Actions','wc_order_status'); ?></th>
						</tr>
					</thead>
				<?php
					foreach($statuses as $status) {
						$style = (!in_array($status->slug,$defaults)) ? 'style="font-weight:bold;"' : '';
						echo '<tr '.$style.'><td>'.$status->term_id.'</td><td>'.$status->slug.'</td><td>'.$status->name.'</td><td>';
							if(!in_array($status->slug,$defaults)) {
								echo '<a href="'.add_query_arg('remove',$status->term_id,remove_query_arg('action')).'">'.__('Remove','wc_order_status').'</a>';
							} else {
								echo '<span style="font-style:italic;">'.__('Default Status','wc_order_status').'</span>';
							}
						echo '</td></tr>';
					}
				?>
				</table>
				<table class="widefat" style="width:auto; float:left; display:inline; clear:none; margin-left:20px;">
					<thead><tr><th colspan="2"><?php _e('Add a Status','wc_order_status'); ?></th></tr></thead>
					<tr><td><p><label for="status"><?php _e('Name','wc_order_status'); ?></label></p></td><td><p><input type="text" name="status" /></p></td></tr>
					<tr><td colspan="2"><p class="submit" style="clear:both;"><input type="submit" class="button-primary" value="<?php _e('Save','wc_order_status'); ?>" /></p></td></tr>
				</table>
			</form>
		</div>
	<?php }

}