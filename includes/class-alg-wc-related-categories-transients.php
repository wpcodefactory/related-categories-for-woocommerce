<?php
/**
 * Related Categories for WooCommerce - Transients Class
 *
 * @version 1.8.0
 * @since   1.8.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Transients' ) ) :

class Alg_WC_Related_Categories_Transients {

	/**
	 * Constructor.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 */
	function __construct() {
		add_action( 'alg_wc_related_categories_after_save_settings', array( $this, 'maybe_delete_all_after_save_settings' ) );
	}

	/**
	 * get_name.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 */
	function get_name( $single_or_loop, $relate_options, $product_or_product_category_id ) {
		return 'transient_alg_wc_related_categories_' . $single_or_loop . '_' . $product_or_product_category_id . '_' . md5( serialize( $relate_options ) );
	}

	/**
	 * maybe_get.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/get_transient/
	 */
	function maybe_get( $single_or_loop, $relate_options, $product_or_product_category_id ) {
		if ( alg_wc_related_categories()->core->options['advanced']['do_use_transients'] ) {
			return get_transient( $this->get_name( $single_or_loop, $relate_options, $product_or_product_category_id ) );
		}
		return false;
	}

	/**
	 * maybe_set.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/set_transient/
	 */
	function maybe_set( $single_or_loop, $relate_options, $product_or_product_category_id, $related_categories ) {
		if ( alg_wc_related_categories()->core->options['advanced']['do_use_transients'] ) {
			set_transient( $this->get_name( $single_or_loop, $relate_options, $product_or_product_category_id ),
				$related_categories, alg_wc_related_categories()->core->options['advanced']['transient_expiration'] );
		}
	}

	/**
	 * maybe_delete_all_after_save_settings.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @todo    [maybe] (dev) remove `add_message()`?
	 */
	function maybe_delete_all_after_save_settings() {
		global $current_section;
		if ( ! in_array( $current_section, array( '', 'loop' ) ) || alg_wc_related_categories()->core->options['advanced']['do_use_transients'] ) {
			$this->delete_all();
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Transients deleted.', 'related-categories-for-woocommerce' ) );
			}
		}
	}

	/**
	 * delete_all.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/delete_transient/
	 * @see     https://wordpress.stackexchange.com/questions/73163/using-a-wildcard-with-delete-transient
	 *
	 * @todo    [next] [!] (dev) fix: when using memcache (or similar), transients are not stored in the database
	 */
	function delete_all() {
		global $wpdb;
		$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE ('transient_alg_wc_related_categories_%')" );
	}

}

endif;

return new Alg_WC_Related_Categories_Transients();
