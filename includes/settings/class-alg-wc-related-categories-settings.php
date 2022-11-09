<?php
/**
 * Related Categories for WooCommerce - Settings
 *
 * @version 1.9.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Settings' ) ) :

class Alg_WC_Related_Categories_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_related_categories';
		$this->label = __( 'Related Categories', 'related-categories-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'alg_wc_rc_sanitize' ), PHP_INT_MAX, 3 );
		// Sections
		require_once( 'class-alg-wc-related-categories-settings-section.php' );
		require_once( 'class-alg-wc-related-categories-settings-general.php' );
		$this->sections['single']   = new Alg_WC_Related_Categories_Settings_General( 'single' );
		$this->sections['loop']     = new Alg_WC_Related_Categories_Settings_General( 'loop' );
		$this->sections['advanced'] = require_once( 'class-alg-wc-related-categories-settings-advanced.php' );
	}

	/**
	 * alg_wc_rc_sanitize.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 */
	function alg_wc_rc_sanitize( $value, $option, $raw_value ) {
		if ( ! empty( $option['alg_wc_rc_sanitize'] ) ) {
			switch ( $option['alg_wc_rc_sanitize'] ) {
				case 'textarea':
					return wp_kses_post( trim( $raw_value ) );
				default:
					$func = $option['alg_wc_rc_sanitize'];
					return ( function_exists( $func ) ? $func( $raw_value ) : $value );
			}
		}
		return $value;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'related-categories-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'related-categories-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'related-categories-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'related-categories-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'related-categories-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notices_settings_reset_success' ) );
			}
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'related-categories-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * save.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		do_action( 'alg_wc_related_categories_after_save_settings' );
	}

}

endif;

return new Alg_WC_Related_Categories_Settings();
