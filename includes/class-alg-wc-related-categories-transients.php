<?php
/**
 * Related Categories for WooCommerce - Transients Class
 *
 * @version 2.1.0
 * @since   1.8.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Related_Categories
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Related_Categories_Transients' ) ) :

	/**
	 * Alg_WC_Related_Categories_Transients class.
	 *
	 * @version 2.1.0
	 * @since   1.8.0
	 */
	class Alg_WC_Related_Categories_Transients {

		/**
		 * Constructor.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 */
		public function __construct() {
			add_action(
				'alg_wc_related_categories_after_save_settings',
				array( $this, 'maybe_delete_all_after_save_settings' )
			);
		}

		/**
		 * Get name.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @param string $single_or_loop                 Single or loop context.
		 * @param array  $relate_options                 Relate options.
		 * @param int    $product_or_product_category_id Product or product category ID.
		 */
		public function get_name( $single_or_loop, $relate_options, $product_or_product_category_id ) {
			return (
				'transient_alg_wc_related_categories_' .
				$single_or_loop .
				'_' .
				$product_or_product_category_id .
				'_' .
				md5( serialize( $relate_options ) ) // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			);
		}

		/**
		 * Maybe get.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @param string $single_or_loop                 Single or loop context.
		 * @param array  $relate_options                 Relate options.
		 * @param int    $product_or_product_category_id Product or product category ID.
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_transient/
		 */
		public function maybe_get( $single_or_loop, $relate_options, $product_or_product_category_id ) {
			if ( alg_wc_related_categories()->core->options['advanced']['do_use_transients'] ) {
				return get_transient(
					$this->get_name( $single_or_loop, $relate_options, $product_or_product_category_id )
				);
			}
			return false;
		}

		/**
		 * Maybe set.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @param string $single_or_loop                 Single or loop context.
		 * @param array  $relate_options                 Relate options.
		 * @param int    $product_or_product_category_id Product or product category ID.
		 * @param array  $related_categories             Related categories.
		 *
		 * @see https://developer.wordpress.org/reference/functions/set_transient/
		 */
		public function maybe_set( $single_or_loop, $relate_options, $product_or_product_category_id, $related_categories ) {
			if ( alg_wc_related_categories()->core->options['advanced']['do_use_transients'] ) {
				set_transient(
					$this->get_name( $single_or_loop, $relate_options, $product_or_product_category_id ),
					$related_categories,
					alg_wc_related_categories()->core->options['advanced']['transient_expiration']
				);
			}
		}

		/**
		 * Maybe delete all after save settings.
		 *
		 * @version 2.1.0
		 * @since   1.8.0
		 *
		 * @todo (dev) Remove `add_message()`?
		 */
		public function maybe_delete_all_after_save_settings() {
			global $current_section;
			if (
				! in_array( $current_section, array( '', 'loop' ), true ) ||
				alg_wc_related_categories()->core->options['advanced']['do_use_transients']
			) {
				$this->delete_all();
				if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					WC_Admin_Settings::add_message( __( 'Transients deleted.', 'related-categories-for-woocommerce' ) );
				}
			}
		}

		/**
		 * Delete all.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @see https://developer.wordpress.org/reference/functions/delete_transient/
		 * @see https://wordpress.stackexchange.com/questions/73163/using-a-wildcard-with-delete-transient
		 *
		 * @todo (dev) Fix: When using memcache (or similar), transients are not stored in the database!
		 */
		public function delete_all() {
			global $wpdb;
			$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE ('transient_alg_wc_related_categories_%')" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}
	}

endif;

return new Alg_WC_Related_Categories_Transients();
