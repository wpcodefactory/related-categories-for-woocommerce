<?php
/**
 * Plugin Name: Related Categories for WooCommerce
 * Plugin URI: https://wpfactory.com/item/related-categories-for-woocommerce/
 * Description: Add "Related categories" section to single product and/or shop pages in WooCommerce.
 * Version: 2.1.0
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * Requires at least: 4.7
 * Text Domain: related-categories-for-woocommerce
 * Domain Path: /langs
 * WC tested up to: 11.1
 * Requires Plugins: woocommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPFactory\WC_Related_Categories
 */

defined( 'ABSPATH' ) || exit;

if ( 'related-categories-for-woocommerce.php' === basename( __FILE__ ) ) {
	if ( ! function_exists( 'alg_wc_related_categories_is_pro_activated' ) ) {
		/**
		 * Check if Pro plugin version is activated.
		 *
		 * @version 2.1.0
		 * @since   1.9.0
		 */
		function alg_wc_related_categories_is_pro_activated() {
			$plugin = 'related-categories-for-woocommerce-pro/related-categories-for-woocommerce-pro.php';
			return (
				in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
				(
					is_multisite() &&
					array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
				)
			);
		}
	}

	if ( alg_wc_related_categories_is_pro_activated() ) {
		defined( 'ALG_WC_RELATED_CATEGORIES_FILE_FREE' ) || define( 'ALG_WC_RELATED_CATEGORIES_FILE_FREE', __FILE__ );
		return;
	}
}

/**
 * Plugin version.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
defined( 'ALG_WC_RELATED_CATEGORIES_VERSION' ) || define( 'ALG_WC_RELATED_CATEGORIES_VERSION', '2.1.0' );

/**
 * Plugin file.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
defined( 'ALG_WC_RELATED_CATEGORIES_FILE' ) || define( 'ALG_WC_RELATED_CATEGORIES_FILE', __FILE__ );

/**
 * Include main plugin class.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-related-categories.php';

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

/**
 * Initialize the plugin.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
add_action( 'plugins_loaded', 'alg_wc_related_categories' );
