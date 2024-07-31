<?php
/*
Plugin Name: Related Categories for WooCommerce
Plugin URI: https://wpfactory.com/item/related-categories-for-woocommerce/
Description: Add "Related categories" section to single product and/or shop pages in WooCommerce.
Version: 1.9.7
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: related-categories-for-woocommerce
Domain Path: /langs
WC tested up to: 9.1
Requires Plugins: woocommerce
*/

defined( 'ABSPATH' ) || exit;

if ( 'related-categories-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.9.0
	 * @since   1.9.0
	 */
	$plugin = 'related-categories-for-woocommerce-pro/related-categories-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_RELATED_CATEGORIES_VERSION' ) || define( 'ALG_WC_RELATED_CATEGORIES_VERSION', '1.9.7' );

defined( 'ALG_WC_RELATED_CATEGORIES_FILE' ) || define( 'ALG_WC_RELATED_CATEGORIES_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-related-categories.php' );

if ( ! function_exists( 'alg_wc_related_categories' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Related_Categories to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_related_categories() {
		return Alg_WC_Related_Categories::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_related_categories' );
