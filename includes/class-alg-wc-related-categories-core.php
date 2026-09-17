<?php
/**
 * Related Categories for WooCommerce - Core Class
 *
 * @version 2.1.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Related_Categories
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Related_Categories_Core' ) ) :

	/**
	 * Alg_WC_Related_Categories_Core class.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	class Alg_WC_Related_Categories_Core {

		/**
		 * Frontend class handler.
		 *
		 * @version 1.9.8
		 * @since   1.9.8
		 *
		 * @var Alg_WC_Related_Categories_Frontend
		 */
		public $frontend;

		/**
		 * Transient class handler.
		 *
		 * @version 1.9.8
		 * @since   1.9.8
		 *
		 * @var Alg_WC_Related_Categories_Transients
		 */
		public $transients;

		/**
		 * Options.
		 *
		 * @version 1.9.8
		 * @since   1.9.8
		 *
		 * @var array
		 */
		public $options;

		/**
		 * Old options.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var array
		 */
		public $old_options;

		/**
		 * Constructor.
		 *
		 * @version 2.1.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Recheck all variable names, e.g., `$related_categories` vs `$related_product_category_ids`, etc.
		 */
		public function __construct() {
			$this->init_options();

			$this->frontend = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-related-categories-frontend.php';

			$this->transients = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-related-categories-transients.php';

			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-related-categories-widget.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-related-categories-widget-single.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-related-categories-widget-loop.php';
			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			do_action( 'alg_wc_related_categories_core_loaded', $this );
		}

		/**
		 * Init options.
		 *
		 * @version 2.0.0
		 * @since   1.3.0
		 */
		public function init_options() {
			$default_options = array(
				'single' => array(
					'enabled'                              => 'yes',
					'position'                             => 'woocommerce_after_single_product_summary',
					'position_priority'                    => 21,
					'widget'                               => 'no',
					'widget_override_relate_options'       => 'no',
					'relate_siblings'                      => 'yes',
					'relate_siblings_include_grandparents' => 'yes',
					'relate_siblings_include_grandchildren' => 'yes',
					'relate_siblings_include_top_level'    => 'no',
					'relate_parents'                       => 'yes',
					'relate_parents_include_grandparents'  => 'yes',
					'relate_children'                      => 'yes',
					'relate_children_include_grandchildren' => 'yes',
					'relate_current'                       => 'no',
					'relate_per_category'                  => 'no',
					'relate_per_tag'                       => 'no',
					'relate_per_taxonomy'                  => '',
					'relate_per_product'                   => 'no',
					'relate_per_product_override'          => 'yes',
					'limit'                                => 4,
					'columns'                              => 4,
					'hide_empty'                           => 'yes',
					'orderby'                              => 'name',
					'order'                                => 'ASC',
					'template_header'                      => '<section class="related categories">' . PHP_EOL . '<h2>%title%</h2>',
					'template_footer'                      => '</section>',
					'template_type'                        => 'default',
					'template_custom'                      => '',
					'template_custom_glue'                 => '',
					'image_size'                           => 'woocommerce_thumbnail',
					'placeholder_image'                    => '',
					'remove_image'                         => 'no',
					'hide_related_products'                => 'no',
				),
				'loop'   => array(
					'enabled'                              => 'no',
					'position'                             => 'woocommerce_after_shop_loop',
					'position_priority'                    => 9,
					'widget'                               => 'no',
					'widget_override_relate_options'       => 'no',
					'relate_siblings'                      => 'yes',
					'relate_siblings_include_grandparents' => 'yes',
					'relate_siblings_include_grandchildren' => 'yes',
					'relate_siblings_include_top_level'    => 'no',
					'relate_parents'                       => 'yes',
					'relate_parents_include_grandparents'  => 'yes',
					'relate_children'                      => 'yes',
					'relate_children_include_grandchildren' => 'yes',
					'relate_current'                       => 'no',
					'relate_current_always_first'          => 'no',
					'relate_per_category'                  => 'no',
					'relate_per_tag'                       => 'no',
					'relate_per_taxonomy'                  => '',
					'limit'                                => 4,
					'columns'                              => 4,
					'hide_empty'                           => 'yes',
					'orderby'                              => 'name',
					'order'                                => 'ASC',
					'template_header'                      => '<section class="related categories">' . PHP_EOL . '<h2>%title%</h2>',
					'template_footer'                      => '</section>',
					'template_type'                        => 'default',
					'template_custom'                      => '',
					'template_custom_glue'                 => '',
					'image_size'                           => 'woocommerce_thumbnail',
					'placeholder_image'                    => '',
					'remove_image'                         => 'no',
					'visibility'                           => 'all',
				),
			);
			$single          = get_option( 'alg_wc_related_categories_options_single', array() );
			$loop            = get_option( 'alg_wc_related_categories_options_loop', array() );
			$this->options   = array(
				'single' => array_merge( $default_options['single'], $single ),
				'loop'   => array_merge( $default_options['loop'], $loop ),
			);
			// Advanced.
			$this->options['advanced']['multi_language']       = get_option( 'alg_wc_related_categories_multi_language', 'no' );
			$this->options['advanced']['do_use_transients']    = ( 'yes' === get_option( 'alg_wc_related_categories_use_transients', 'no' ) );
			$this->options['advanced']['transient_expiration'] = get_option( 'alg_wc_related_categories_transient_expiration', DAY_IN_SECONDS );
		}

		/**
		 * Register Alg_WC_Related_Categories_Widget widget.
		 *
		 * @version 2.1.0
		 * @since   1.7.0
		 */
		public function register_widget() {
			if (
				'yes' === $this->options['single']['enabled'] &&
				'yes' === $this->options['single']['widget']
			) {
				register_widget( 'Alg_WC_Related_Categories_Widget_Single' );
			}

			if (
				'yes' === $this->options['loop']['enabled'] &&
				'yes' === $this->options['loop']['widget']
			) {
				register_widget( 'Alg_WC_Related_Categories_Widget_Loop' );
			}
		}

		/**
		 * Override options.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @param string $type        Type of options to override.
		 * @param array  $new_options New options to override the existing ones.
		 */
		public function override_options( $type, $new_options ) {
			$this->old_options[ $type ] = $this->options[ $type ];
			$this->options[ $type ]     = array_replace( $this->options[ $type ], $new_options );
		}

		/**
		 * Restore options.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @param string $type Type of options to restore.
		 */
		public function restore_options( $type ) {
			if ( isset( $this->old_options[ $type ] ) ) {
				$this->options[ $type ] = $this->old_options[ $type ];
				unset( $this->old_options[ $type ] );
			}
		}

		/**
		 * Get relate options.
		 *
		 * @version 1.4.0
		 * @since   1.3.0
		 *
		 * @param string $type Type of options to get.
		 */
		public function get_relate_options( $type ) {
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
				$relate_options['parents_include_grandparents'] = ( 'yes' === $this->options[ $type ]['relate_parents_include_grandparents'] );
			}
			if ( $relate_options['children'] ) {
				$relate_options['children_include_grandchildren'] = ( 'yes' === $this->options[ $type ]['relate_children_include_grandchildren'] );
			}
			return $relate_options;
		}

		/**
		 * Get related categories single.
		 *
		 * @version 2.1.0
		 * @since   1.0.0
		 *
		 * @param WC_Product $product The product object.
		 *
		 * @todo (feature) Relate "through product".
		 */
		public function get_related_categories_single( $product ) {
			$relate_options = $this->get_relate_options( 'single' );
			// Transient.
			$transient = $this->transients->maybe_get( 'single', $relate_options, $product->get_id() );
			if ( false !== $transient ) {
				return $transient;
			}
			// Filters.
			$related_categories = apply_filters( 'alg_wc_related_categories_before_single', false );
			if ( false === $related_categories ) {
				$related_categories = array();
			} elseif ( apply_filters( 'alg_wc_related_categories_before_single_override', true, $related_categories, $product ) ) {
				$related_categories = array_unique( $related_categories );
				$this->transients->maybe_set(
					'single',
					$relate_options,
					$product->get_id(),
					$related_categories
				);
				return $related_categories;
			}
			// Children, Siblings, Parents, Current, Per category.
			if ( in_array( true, $relate_options, true ) ) {
				$product_category_ids = apply_filters(
					'alg_wc_related_categories_single_product_category_ids',
					$product->get_category_ids(),
					$product
				);
				if ( ! empty( $product_category_ids ) ) {
					foreach ( $product_category_ids as $product_category_id ) {
						$related_categories = $this->get_related_categories(
							$related_categories,
							$product_category_id,
							$relate_options
						);
					}
					if ( $relate_options['current'] ) {
						$related_categories = array_merge(
							$related_categories,
							$product_category_ids
						);
					}
				}
			}
			$related_categories = apply_filters(
				'alg_wc_related_categories_single',
				$related_categories,
				$product,
				$relate_options
			);
			$related_categories = array_unique( $related_categories );
			$this->transients->maybe_set(
				'single',
				$relate_options,
				$product->get_id(),
				$related_categories
			);
			return $related_categories;
		}

		/**
		 * Get related categories loop.
		 *
		 * @version 2.1.0
		 * @since   1.3.0
		 *
		 * @param int $product_category_id The product category ID.
		 *
		 * @return array The related categories.
		 */
		public function get_related_categories_loop( $product_category_id ) {
			$relate_options = $this->get_relate_options( 'loop' );
			// Transient.
			$transient = $this->transients->maybe_get( 'loop', $relate_options, $product_category_id );
			if ( false !== $transient ) {
				return $transient;
			}
			$related_categories = array();
			// Children, Siblings, Parents, Current, Per category.
			if ( in_array( true, $relate_options, true ) ) {
				$related_categories = $this->get_related_categories(
					$related_categories,
					$product_category_id,
					$relate_options
				);
				if ( $product_category_id && $relate_options['current'] ) {
					$related_categories[] = $product_category_id;
				}
			}
			$related_categories = apply_filters(
				'alg_wc_related_categories_loop',
				$related_categories,
				$product_category_id,
				$relate_options
			);
			$related_categories = array_unique( $related_categories );
			$this->transients->maybe_set(
				'loop',
				$relate_options,
				$product_category_id,
				$related_categories
			);
			return $related_categories;
		}

		/**
		 * Get category children.
		 *
		 * @version 2.1.0
		 * @since   1.4.0
		 *
		 * @param int  $product_category_id      The product category ID.
		 * @param bool $do_include_grandchildren Whether to include grandchildren categories.
		 *
		 * @return array The child categories.
		 */
		public function get_category_children( $product_category_id, $do_include_grandchildren ) {
			if ( $do_include_grandchildren ) {
				if ( 0 === intval( $product_category_id ) ) {
					$children = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => false,
						)
					); // i.e., all categories.
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
				$children = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $product_category_id,
						'hide_empty' => false,
					)
				);
				if ( ! empty( $children ) && ! is_wp_error( $children ) ) {
					return wp_list_pluck( $children, 'term_id' );
				}
			}
			return array();
		}

		/**
		 * Get category parents.
		 *
		 * @version 2.1.0
		 * @since   1.4.0
		 *
		 * @param int  $product_category_id     The product category ID.
		 * @param bool $do_include_grandparents Whether to include grandparent categories.
		 * @param bool $do_include_top_level    Whether to include the top-level category.
		 *
		 * @return array The parent categories.
		 */
		public function get_category_parents( $product_category_id, $do_include_grandparents, $do_include_top_level ) {
			if ( $do_include_grandparents ) {
				$parents = get_ancestors( $product_category_id, 'product_cat', 'taxonomy' );
				if ( $do_include_top_level ) {
					$parents[] = 0;
				}
				return $parents;
			} else {
				$category_term = get_term( $product_category_id, 'product_cat' );
				return (
					(
						! empty( $category_term ) &&
						! is_wp_error( $category_term ) &&
						(
							0 !== intval( $category_term->parent ) ||
							$do_include_top_level
						)
					) ?
					array( $category_term->parent ) :
					array()
				);
			}
		}

		/**
		 * Get related categories.
		 *
		 * @version 2.1.0
		 * @since   1.3.0
		 *
		 * @param array $related_categories  The related categories.
		 * @param int   $product_category_id The product category ID.
		 * @param array $relate_options      The relate options.
		 *
		 * @return array The related categories.
		 */
		public function get_related_categories( $related_categories, $product_category_id, $relate_options ) {
			if ( $product_category_id ) {
				if ( $relate_options['siblings'] ) {
					$parent_categories = $this->get_category_parents(
						$product_category_id,
						$relate_options['siblings_include_grandparents'],
						$relate_options['siblings_include_top_level']
					);
					foreach ( $parent_categories as $parent_category ) {
						$children_categories  = $this->get_category_children(
							$parent_category,
							$relate_options['siblings_include_grandchildren']
						);
						$children_categories  = array_map(
							'intval',
							(array) $children_categories
						);
						$current_category_key = array_search(
							(int) $product_category_id,
							$children_categories,
							true
						);
						if ( false !== $current_category_key ) {
							unset( $children_categories[ $current_category_key ] );
						}
						$related_categories = array_merge( $related_categories, $children_categories );
					}
				}
				if ( $relate_options['parents'] ) {
					$related_categories = array_merge(
						$related_categories,
						$this->get_category_parents(
							$product_category_id,
							$relate_options['parents_include_grandparents'],
							false
						)
					);
				}
				if ( $relate_options['children'] ) {
					$related_categories = array_merge(
						$related_categories,
						$this->get_category_children(
							$product_category_id,
							$relate_options['children_include_grandchildren']
						)
					);
				}
			}
			return apply_filters(
				'alg_wc_related_categories',
				$related_categories,
				$product_category_id,
				$relate_options
			);
		}
	}

endif;

return new Alg_WC_Related_Categories_Core();
