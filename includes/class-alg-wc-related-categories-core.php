<?php
/**
 * Related Categories for WooCommerce - Core Class
 *
 * @version 1.9.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Core' ) ) :

class Alg_WC_Related_Categories_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @todo    [next] recheck all variable names, e.g. `$related_categories` vs `$related_product_category_ids`, etc.
	 */
	function __construct() {
		$this->init_options();
		$this->frontend   = require_once( 'class-alg-wc-related-categories-frontend.php' );
		$this->transients = require_once( 'class-alg-wc-related-categories-transients.php' );
		$this->widget     = require_once( 'class-alg-wc-related-categories-widget.php' );
		do_action( 'alg_wc_related_categories_core_loaded', $this );
	}

	/**
	 * init_options.
	 *
	 * @version 1.8.0
	 * @since   1.3.0
	 */
	function init_options() {
		$default_options = array(
			'single' => array(
				'enabled'                                => 'yes',
				'position'                               => 'woocommerce_after_single_product_summary',
				'position_priority'                      => 21,
				'widget'                                 => 'no',
				'widget_override_relate_options'         => 'no',
				'relate_siblings'                        => 'yes',
				'relate_siblings_include_grandparents'   => 'yes',
				'relate_siblings_include_grandchildren'  => 'yes',
				'relate_siblings_include_top_level'      => 'no',
				'relate_parents'                         => 'yes',
				'relate_parents_include_grandparents'    => 'yes',
				'relate_children'                        => 'yes',
				'relate_children_include_grandchildren'  => 'yes',
				'relate_current'                         => 'no',
				'relate_per_category'                    => 'no',
				'relate_per_tag'                         => 'no',
				'relate_per_taxonomy'                    => '',
				'relate_per_product'                     => 'no',
				'relate_per_product_override'            => 'yes',
				'limit'                                  => 4,
				'columns'                                => 4,
				'hide_empty'                             => 'yes',
				'orderby'                                => 'name',
				'order'                                  => 'ASC',
				'template_header'                        => '<section class="related categories">' . PHP_EOL . '<h2>' . __( 'Related categories', 'related-categories-for-woocommerce' ) . '</h2>',
				'template_footer'                        => '</section>',
				'template_type'                          => 'default',
				'template_custom'                        => '',
				'template_custom_glue'                   => '',
				'image_size'                             => 'woocommerce_thumbnail',
				'placeholder_image'                      => '',
				'remove_image'                           => 'no',
				'hide_related_products'                  => 'no',
			),
			'loop'   => array(
				'enabled'                                => 'no',
				'position'                               => 'woocommerce_after_shop_loop',
				'position_priority'                      => 9,
				'widget'                                 => 'no',
				'widget_override_relate_options'         => 'no',
				'relate_siblings'                        => 'yes',
				'relate_siblings_include_grandparents'   => 'yes',
				'relate_siblings_include_grandchildren'  => 'yes',
				'relate_siblings_include_top_level'      => 'no',
				'relate_parents'                         => 'yes',
				'relate_parents_include_grandparents'    => 'yes',
				'relate_children'                        => 'yes',
				'relate_children_include_grandchildren'  => 'yes',
				'relate_current'                         => 'no',
				'relate_current_always_first'            => 'no',
				'relate_per_category'                    => 'no',
				'relate_per_tag'                         => 'no',
				'relate_per_taxonomy'                    => '',
				'limit'                                  => 4,
				'columns'                                => 4,
				'hide_empty'                             => 'yes',
				'orderby'                                => 'name',
				'order'                                  => 'ASC',
				'template_header'                        => '<section class="related categories">' . PHP_EOL . '<h2>' . __( 'Related categories', 'related-categories-for-woocommerce' ) . '</h2>',
				'template_footer'                        => '</section>',
				'template_type'                          => 'default',
				'template_custom'                        => '',
				'template_custom_glue'                   => '',
				'image_size'                             => 'woocommerce_thumbnail',
				'placeholder_image'                      => '',
				'remove_image'                           => 'no',
				'visibility'                             => 'all',
			),
		);
		$single = get_option( 'alg_wc_related_categories_options_single', array() );
		$loop   = get_option( 'alg_wc_related_categories_options_loop',   array() );
		$this->options = array(
			'single' => array_merge( $default_options['single'], $single ),
			'loop'   => array_merge( $default_options['loop'],   $loop ),
		);
		// Advanced
		$this->options['advanced']['multi_language']       = get_option( 'alg_wc_related_categories_multi_language', 'no' );
		$this->options['advanced']['do_use_transients']    = ( 'yes' === get_option( 'alg_wc_related_categories_use_transients', 'no' ) );
		$this->options['advanced']['transient_expiration'] = get_option( 'alg_wc_related_categories_transient_expiration', DAY_IN_SECONDS );
	}

	/**
	 * override_options.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 */
	function override_options( $type, $new_options ) {
		$this->old_options[ $type ] = $this->options[ $type ];
		$this->options[ $type ]     = array_replace( $this->options[ $type ], $new_options );
	}

	/**
	 * restore_options.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 */
	function restore_options( $type ) {
		if ( isset( $this->old_options[ $type ] ) ) {
			$this->options[ $type ] = $this->old_options[ $type ];
			unset( $this->old_options[ $type ] );
		}
	}

	/**
	 * get_relate_options.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function get_relate_options( $type ) {
		$relate_options = array(
			'siblings'     => ( 'yes' === $this->options[ $type ]['relate_siblings'] ),
			'parents'      => ( 'yes' === $this->options[ $type ]['relate_parents'] ),
			'children'     => ( 'yes' === $this->options[ $type ]['relate_children'] ),
			'current'      => ( 'yes' === $this->options[ $type ]['relate_current'] ),
			'per_category' => ( 'yes' === $this->options[ $type ]['relate_per_category'] ),
		);
		if ( $relate_options['siblings'] ) {
			$relate_options['siblings_include_grandparents']  = ( 'yes' === $this->options[ $type ]['relate_siblings_include_grandparents'] );
			$relate_options['siblings_include_grandchildren'] = ( 'yes' === $this->options[ $type ]['relate_siblings_include_grandchildren'] );
			$relate_options['siblings_include_top_level']     = ( 'yes' === $this->options[ $type ]['relate_siblings_include_top_level'] );
		}
		if ( $relate_options['parents'] ) {
			$relate_options['parents_include_grandparents']   = ( 'yes' === $this->options[ $type ]['relate_parents_include_grandparents'] );
		}
		if ( $relate_options['children'] ) {
			$relate_options['children_include_grandchildren'] = ( 'yes' === $this->options[ $type ]['relate_children_include_grandchildren'] );
		}
		return $relate_options;
	}

	/**
	 * get_related_categories_single.
	 *
	 * @version 1.9.1
	 * @since   1.0.0
	 *
	 * @todo    [later] (feature) relate "through product"
	 */
	function get_related_categories_single( $product ) {
		$relate_options = $this->get_relate_options( 'single' );
		// Transient
		if ( false !== ( $transient = $this->transients->maybe_get( 'single', $relate_options, $product->get_id() ) ) ) {
			return $transient;
		}
		// Filters
		if ( false === ( $related_categories = apply_filters( 'alg_wc_related_categories_before_single', false ) ) ) {
			$related_categories = array();
		} elseif ( apply_filters( 'alg_wc_related_categories_before_single_override', true, $related_categories, $product ) ) {
			$related_categories = array_unique( $related_categories );
			$this->transients->maybe_set( 'single', $relate_options, $product->get_id(), $related_categories );
			return $related_categories;
		}
		// Children, Siblings, Parents, Current, Per category
		if ( in_array( true, $relate_options ) ) {
			$product_category_ids = apply_filters( 'alg_wc_related_categories_single_product_category_ids', $product->get_category_ids(), $product );
			if ( ! empty( $product_category_ids ) ) {
				foreach ( $product_category_ids as $product_category_id ) {
					$related_categories = $this->get_related_categories( $related_categories, $product_category_id, $relate_options );
				}
				if ( $relate_options['current'] ) {
					$related_categories = array_merge( $related_categories, $product_category_ids );
				}
			}
		}
		$related_categories = apply_filters( 'alg_wc_related_categories_single', $related_categories, $product, $relate_options );
		$related_categories = array_unique( $related_categories );
		$this->transients->maybe_set( 'single', $relate_options, $product->get_id(), $related_categories );
		return $related_categories;
	}

	/**
	 * get_related_categories_loop.
	 *
	 * @version 1.8.0
	 * @since   1.3.0
	 */
	function get_related_categories_loop( $product_category_id ) {
		$relate_options = $this->get_relate_options( 'loop' );
		// Transient
		if ( false !== ( $transient = $this->transients->maybe_get( 'loop', $relate_options, $product_category_id ) ) ) {
			return $transient;
		}
		$related_categories = array();
		// Children, Siblings, Parents, Current, Per category
		if ( in_array( true, $relate_options ) ) {
			$related_categories = $this->get_related_categories( $related_categories, $product_category_id, $relate_options );
			if ( $product_category_id && $relate_options['current'] ) {
				$related_categories[] = $product_category_id;
			}
		}
		$related_categories = apply_filters( 'alg_wc_related_categories_loop', $related_categories, $product_category_id, $relate_options );
		$related_categories = array_unique( $related_categories );
		$this->transients->maybe_set( 'loop', $relate_options, $product_category_id, $related_categories );
		return $related_categories;
	}

	/**
	 * get_category_children.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 */
	function get_category_children( $product_category_id, $do_include_grandchildren ) {
		if ( $do_include_grandchildren ) {
			if ( 0 == $product_category_id ) {
				$children = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ); // i.e. all categories
				if ( ! empty( $children ) && ! is_wp_error( $children ) ) {
					return wp_list_pluck( $children, 'term_id' );
				}
			} else {
				$children = get_term_children( $product_category_id, 'product_cat' );
				if ( ! empty( $children ) && ! is_wp_error( $children ) ) {
					return $children;
				}
			}
		} else {
			$children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $product_category_id, 'hide_empty' => false ) );
			if ( ! empty( $children ) && ! is_wp_error( $children ) ) {
				return wp_list_pluck( $children, 'term_id' );
			}
		}
		return array();
	}

	/**
	 * get_category_parents.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 */
	function get_category_parents( $product_category_id, $do_include_grandparents, $do_include_top_level ) {
		if ( $do_include_grandparents ) {
			$parents = get_ancestors( $product_category_id, 'product_cat', 'taxonomy' );
			if ( $do_include_top_level ) {
				$parents[] = 0;
			}
			return $parents;
		} else {
			$category_term = get_term( $product_category_id, 'product_cat' );
			return ( ! empty( $category_term ) && ! is_wp_error( $category_term ) && ( 0 != $category_term->parent || $do_include_top_level ) ) ?
				array( $category_term->parent ) : array();
		}
	}

	/**
	 * get_related_categories.
	 *
	 * @version 1.7.0
	 * @since   1.3.0
	 */
	function get_related_categories( $related_categories, $product_category_id, $relate_options ) {
		if ( $product_category_id ) {
			if ( $relate_options['siblings'] ) {
				$parent_categories = $this->get_category_parents( $product_category_id, $relate_options['siblings_include_grandparents'], $relate_options['siblings_include_top_level'] );
				foreach ( $parent_categories as $parent_category ) {
					$children_categories = $this->get_category_children( $parent_category, $relate_options['siblings_include_grandchildren'] );
					if ( false !== ( $current_category_key = array_search( $product_category_id, $children_categories ) ) ) {
						unset( $children_categories[ $current_category_key ] );
					}
					$related_categories = array_merge( $related_categories, $children_categories );
				}
			}
			if ( $relate_options['parents'] ) {
				$related_categories = array_merge( $related_categories, $this->get_category_parents( $product_category_id, $relate_options['parents_include_grandparents'], false ) );
			}
			if ( $relate_options['children'] ) {
				$related_categories = array_merge( $related_categories, $this->get_category_children( $product_category_id, $relate_options['children_include_grandchildren'] ) );
			}
		}
		return apply_filters( 'alg_wc_related_categories', $related_categories, $product_category_id, $relate_options );
	}

}

endif;

return new Alg_WC_Related_Categories_Core();
