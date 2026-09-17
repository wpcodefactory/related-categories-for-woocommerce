<?php
/**
 * Related Categories for WooCommerce - Widget Class - Loop
 *
 * @version 2.1.0
 * @since   2.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Related_Categories
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Related_Categories_Widget_Loop' ) ) :

	/**
	 * Alg_WC_Related_Categories_Widget_Loop class.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	class Alg_WC_Related_Categories_Widget_Loop extends Alg_WC_Related_Categories_Widget {

		/**
		 * Constructor.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		public function __construct() {
			$this->type = 'loop';
			$this->desc = __( 'Archives', 'related-categories-for-woocommerce' );
			parent::__construct();
		}
	}

endif;
