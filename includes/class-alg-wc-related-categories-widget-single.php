<?php
/**
 * Related Categories for WooCommerce - Widget Class - Single
 *
 * @version 2.1.0
 * @since   2.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Related_Categories
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Related_Categories_Widget_Single' ) ) :

	/**
	 * Alg_WC_Related_Categories_Widget_Single class.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	class Alg_WC_Related_Categories_Widget_Single extends Alg_WC_Related_Categories_Widget {

		/**
		 * Constructor.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		public function __construct() {
			$this->type = 'single';
			$this->desc = __( 'Single Product Page', 'related-categories-for-woocommerce' );
			parent::__construct();
		}
	}

endif;
