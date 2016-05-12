<?php
/* 
Plugin Name: Product Attribute Tab for WooCommerce
Plugin URI: http://wordpress.org/plugins/woo-product-attribute-tab
Description: Adds the possibility to show an extra tab on the product page for configured product attributes.
Author: Michael Jarrett
Version: 0.0.1
Author URI: http://m.jarrett.ch
Text Domain: woo-product-attribute-tab
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

/**
 * Exit if accessed directly.
 */
defined('ABSPATH') or die();

/**
 * Initialize the plugin.
 */
require_once('components/class.core.php');
MJJ\WooProductAttributeTab\Core::instance();

?>