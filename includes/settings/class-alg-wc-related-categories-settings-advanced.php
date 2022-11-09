<?php
/**
 * Related Categories for WooCommerce - Advanced Section Settings
 *
 * @version 1.9.0
 * @since   1.6.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Settings_Advanced' ) ) :

class Alg_WC_Related_Categories_Settings_Advanced extends Alg_WC_Related_Categories_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'related-categories-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.9.0
	 * @since   1.6.0
	 *
	 * @todo    [later] (desc) transients: better desc
	 * @todo    [later] transients: delete tool?
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Advanced Options', 'related-categories-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_advanced_options',
			),
			array(
				'title'    => __( 'Multi-language', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Use the default language product/term ID instead of the translated one.', 'related-categories-for-woocommerce' ) . ' ' .
					sprintf( __( 'Affects "%s", "%s", "%s" and "%s" settings.', 'related-categories-for-woocommerce' ),
						__( 'Per category', 'related-categories-for-woocommerce' ),
						__( 'Per tag', 'related-categories-for-woocommerce' ),
						__( 'Per custom taxonomy', 'related-categories-for-woocommerce' ),
						__( 'Per product', 'related-categories-for-woocommerce' )
					),
				'id'       => 'alg_wc_related_categories_multi_language',
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'       => __( 'Disabled', 'related-categories-for-woocommerce' ),
					'wpml'     => __( 'WPML', 'related-categories-for-woocommerce' ),
					'polylang' => __( 'Polylang', 'related-categories-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Transients', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Use transients to save the results.', 'related-categories-for-woocommerce' ),
				'id'       => 'alg_wc_related_categories_use_transients',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Transient expiration', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Time until expiration in seconds. Set to 0 for no expiration.', 'related-categories-for-woocommerce' ),
				'id'       => 'alg_wc_related_categories_transient_expiration',
				'default'  => DAY_IN_SECONDS,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_advanced_options',
			),
		);
	}

}

endif;

return new Alg_WC_Related_Categories_Settings_Advanced();
