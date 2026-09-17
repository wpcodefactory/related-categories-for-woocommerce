<?php
/**
 * Related Categories for WooCommerce - Frontend Class
 *
 * @version 2.1.0
 * @since   1.7.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Related_Categories
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Related_Categories_Frontend' ) ) :

	/**
	 * Alg_WC_Related_Categories_Frontend class.
	 *
	 * @version 2.1.0
	 * @since   1.7.0
	 */
	class Alg_WC_Related_Categories_Frontend {

		/**
		 * Image size.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var array
		 */
		public $image_size;

		/**
		 * Placeholder image.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var array
		 */
		public $placeholder_image;

		/**
		 * Constructor.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 *
		 * @todo (dev) Move all shortcodes to a separate `class-alg-wc-related-categories-shortcodes.php` file?
		 */
		public function __construct() {
			if ( ! is_admin() ) {
				add_action( 'init', array( $this, 'init' ) );
			}
		}

		/**
		 * Get core.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		public function get_core() {
			return alg_wc_related_categories()->core;
		}

		/**
		 * Get option with args.
		 *
		 * @version 2.0.0
		 * @since   1.7.0
		 *
		 * @param string $type   Option type.
		 * @param string $option Option name.
		 * @param array  $args   Arguments.
		 *
		 * @return mixed Option value.
		 *
		 * @todo (dev) Maybe remove this (i.e., as we have `override_options()` and `restore_options()` now)?
		 */
		public function get_option_with_args( $type, $option, $args = array() ) {
			$value = ( $args[ $option ] ?? $this->get_core()->options[ $type ][ $option ] );

			if ( 'template_header' === $option ) { // for proper translation loading.
				$value = str_replace(
					'%title%',
					__( 'Related categories', 'related-categories-for-woocommerce' ),
					$value
				);
			}

			return $value;
		}

		/**
		 * Init.
		 *
		 * @version 2.1.0
		 * @since   1.7.0
		 *
		 * @todo (feature) Multiple positions/hooks (for both single and loop).
		 * @todo (feature) Hide related *products*: only if there are related *categories* available for the product.
		 */
		public function init() {
			// Output.
			foreach ( array( 'single', 'loop' ) as $type ) {
				if ( 'yes' === $this->get_core()->options[ $type ]['enabled'] ) {
					$position = $this->get_core()->options[ $type ]['position'];
					if ( 'disable' !== $position ) {
						add_action(
							$position,
							array( $this, 'output_related_categories_' . $type ),
							$this->get_core()->options[ $type ]['position_priority']
						);
					}
					add_shortcode(
						'alg_wc_related_categories_' . $type,
						array( $this, 'output_related_categories_' . $type . '_shortcode' )
					);
				}
			}

			// Visibility.
			add_filter( 'alg_wc_related_categories_loop', array( $this, 'check_visibility_loop' ), 10, 2 );

			// Hide related *products*.
			if ( 'yes' === $this->get_core()->options['single']['hide_related_products'] ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				add_filter( 'woocommerce_related_products', '__return_empty_array', PHP_INT_MAX );
			}

			// Translation (WPML/Polylang) shortcode.
			add_shortcode(
				'alg_wc_related_categories_translate',
				array( $this, 'language_shortcode' )
			);
		}

		/**
		 * Check visibility loop.
		 *
		 * @version 2.1.0
		 * @since   1.8.0
		 *
		 * @param array $related_categories  Related categories.
		 * @param int   $product_category_id Product category ID.
		 *
		 * @return array
		 *
		 * @todo (dev) Maybe move this to the `core`?
		 */
		public function check_visibility_loop( $related_categories, $product_category_id ) {
			if ( 'all' !== $this->get_core()->options['loop']['visibility'] ) {
				switch ( $this->get_core()->options['loop']['visibility'] ) {
					case 'with_children_only':
						$children = get_term_children( $product_category_id, 'product_cat' );
						return ( ! empty( $children ) ? $related_categories : array() );
					case 'no_children_only':
						$children = get_term_children( $product_category_id, 'product_cat' );
						return ( empty( $children ) ? $related_categories : array() );
				}
			}
			return $related_categories;
		}

		/**
		 * Output related categories loop.
		 *
		 * @version 1.7.0
		 * @since   1.3.0
		 *
		 * @param int|false $product_category_id Product category ID or false.
		 * @param array     $args                Additional arguments.
		 *
		 * @todo (dev) Output inside standard subcategories (see `woocommerce_maybe_show_product_subcategories()`).
		 */
		public function output_related_categories_loop( $product_category_id = false, $args = array() ) {
			if ( ! $product_category_id && is_product_category() ) {
				$product_category_id = get_queried_object_id();
			}
			$this->output_related_categories(
				$this->get_core()->get_related_categories_loop( $product_category_id ),
				'loop',
				$args
			);
		}

		/**
		 * Output related categories single.
		 *
		 * @version 1.8.0
		 * @since   1.3.0
		 *
		 * @param WC_Product|false $product Product object or false.
		 * @param array            $args    Additional arguments.
		 */
		public function output_related_categories_single( $product = false, $args = array() ) {
			if ( ! $product ) {
				if ( is_product() ) {
					$product = wc_get_product( get_the_ID() );
				}
			} elseif ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}
			if (
				$product &&
				is_object( $product ) &&
				is_a( $product, 'WC_Product' )
			) {
				$this->output_related_categories(
					$this->get_core()->get_related_categories_single( $product ),
					'single',
					$args
				);
			}
		}

		/**
		 * Get allowed HTML.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @todo (v2.1.0) Recheck if all allowed HTML tags and attributes are included.
		 */
		public function get_allowed_html() {
			$allowed_html = wp_kses_allowed_html( 'post' );

			$allowed_html['img']['srcset'] = true;
			$allowed_html['img']['sizes']  = true;

			return $allowed_html;
		}

		/**
		 * Output related categories.
		 *
		 * @version 2.1.0
		 * @since   1.0.0
		 *
		 * @param array  $related_categories Array of related categories.
		 * @param string $type               Type of related categories.
		 * @param array  $args               Additional arguments.
		 *
		 * @todo (dev) `orderby`: `count`: Does not include children (even though `$term->count` shows it with children).
		 * @todo (dev) Add placeholders, e.g., `%columns%`, `%limit%`?
		 * @todo (dev) Add more `orderby` options?
		 * @todo (dev) Rethink `$limit` default `4`?
		 */
		public function output_related_categories( $related_categories, $type, $args = array() ) {
			if ( $related_categories ) {
				$output_func = 'output_related_categories_' . $this->get_option_with_args( $type, 'template_type', $args );
				$output      = $this->$output_func( $related_categories, $type, $args );
				if ( ! empty( $output ) ) {
					remove_shortcode( 'alg_wc_related_categories_' . $type );
					$template_header = do_shortcode( $this->get_option_with_args( $type, 'template_header', $args ) );
					$template_footer = do_shortcode( $this->get_option_with_args( $type, 'template_footer', $args ) );
					add_shortcode(
						'alg_wc_related_categories_' . $type,
						array( $this, 'output_related_categories_' . $type . '_shortcode' )
					);
					echo wp_kses(
						$template_header . $output . $template_footer,
						$this->get_allowed_html()
					);
				}
			}
		}

		/**
		 * Prepare terms.
		 *
		 * @version 1.8.0
		 * @since   1.8.0
		 *
		 * @param array  $terms Array of terms to prepare.
		 * @param string $type  Type of related categories.
		 * @param array  $args  Additional arguments.
		 *
		 * @todo (dev) Via filter?
		 * @todo (feature) `relate_current_always_first`: `single`.
		 */
		public function prepare_terms( $terms, $type, $args = array() ) {
			if ( 'loop' === $type ) {
				if (
					'yes' === $this->get_option_with_args( $type, 'relate_current', $args ) &&
					'yes' === $this->get_option_with_args( $type, 'relate_current_always_first', $args )
				) {
					$current_term = array();
					$all_terms    = array();
					foreach ( $terms as $term ) {
						if ( is_product_category( $term->term_id ) ) {
							$current_term[] = $term;
						} else {
							$all_terms[] = $term;
						}
					}
					if ( empty( $current_term ) ) {
						$queried_object = get_queried_object();
						if ( $queried_object && is_a( $queried_object, 'WP_Term' ) ) {
							$current_term = array( $queried_object );
							array_pop( $all_terms );
						}
					}
					$terms = array_merge( $current_term, $all_terms );
				}
			}
			return $terms;
		}

		/**
		 * Output related categories custom.
		 *
		 * @version 1.8.1
		 * @since   1.7.0
		 *
		 * @param array  $related_categories Array of related category IDs.
		 * @param string $type               Type of related categories.
		 * @param array  $args               Additional arguments.
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_terms/
		 * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
		 * @see https://developer.wordpress.org/reference/classes/wp_term/
		 *
		 * @todo (dev) Check `WC_Shortcodes::product_categories()` for "... workaround WP bug with parents/pad counts...".
		 * @todo (dev) `template_custom`: `do_shortcode()`?
		 * @todo (dev) Cache: Call `wc_placeholder_img_src()` only once?
		 * @todo (dev) `pad_counts`: No effect?
		 * @todo (dev) Use `filter_var( ..., FILTER_VALIDATE_BOOLEAN )` everywhere?
		 * @todo (dev) Merge "Maybe random orderby" from here with same in `output_related_categories_default()`?
		 * @todo (feature) Add more placeholders, e.g., `$term->parent`?
		 */
		public function output_related_categories_custom( $related_categories, $type, $args = array() ) {
			// Maybe random orderby.
			$orderby = $this->get_option_with_args( $type, 'orderby', $args );
			if ( 'random' === $orderby ) {
				$orderby = 'include';
				shuffle( $related_categories );
			}
			// Get terms.
			$hide_empty = (
				filter_var(
					$this->get_option_with_args( $type, 'hide_empty', $args ),
					FILTER_VALIDATE_BOOLEAN
				) ?
				1 :
				0
			);
			$terms      = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'number'     => $this->get_option_with_args( $type, 'limit', $args ),
					'hide_empty' => $hide_empty,
					'orderby'    => $orderby,
					'order'      => $this->get_option_with_args( $type, 'order', $args ),
					'include'    => $related_categories,
					'pad_counts' => true,
				)
			);
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$terms                            = $this->prepare_terms( $terms, $type, $args );
				$this->placeholder_image[ $type ] = $this->get_option_with_args( $type, 'placeholder_image', $args );
				if ( ! empty( $this->placeholder_image[ $type ] ) ) {
					add_filter( 'woocommerce_placeholder_img_src', array( $this, 'placeholder_img_src_' . $type ) );
				}
				$output  = array();
				$counter = 0;
				foreach ( $terms as $term ) {
					if ( $hide_empty && 0 === $term->count ) {
						continue;
					}
					$column_nr          = ( $counter % $this->get_option_with_args( $type, 'columns', $args ) ) + 1;
					$thumbnail_id       = get_term_meta( $term->term_id, 'thumbnail_id', true );
					$category_image_src = (
						$thumbnail_id ?
						wp_get_attachment_image_src(
							$thumbnail_id,
							$this->get_option_with_args( $type, 'image_size', $args )
						) :
						false
					);
					$category_link      = get_term_link( $term );
					$placeholders       = array(
						'%category_id%'          => $term->term_id,
						'%category_name%'        => $term->name,
						'%category_slug%'        => $term->slug,
						'%category_description%' => $term->description,
						'%category_count%'       => $term->count,
						'%category_link%'        => ( ! is_wp_error( $category_link ) ? $category_link : '' ),
						'%category_image_id%'    => $thumbnail_id,
						'%category_image_link%'  => (
							$category_image_src ?
							$category_image_src[0] :
							wc_placeholder_img_src( $this->get_option_with_args( $type, 'image_size', $args ) )
						),
						'%column_nr%'            => $column_nr,
						'%is_active%'            => (
							'loop' === $type && is_product_category( $term->term_id ) ?
							'active' :
							''
						),
					);
					$output[]           = str_replace(
						array_keys( $placeholders ),
						$placeholders,
						$this->get_option_with_args( $type, 'template_custom', $args )
					);
					++$counter;
				}
				if ( ! empty( $this->placeholder_image[ $type ] ) ) {
					remove_filter( 'woocommerce_placeholder_img_src', array( $this, 'placeholder_img_src_' . $type ) );
					$this->placeholder_image[ $type ] = false;
				}
				if ( ! empty( $output ) ) {
					return implode( $this->get_option_with_args( $type, 'template_custom_glue', $args ), $output );
				}
			}
			return false;
		}

		/**
		 * Output related categories default.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 *
		 * @param array  $related_categories Array of related category IDs.
		 * @param string $type               The type of output.
		 * @param array  $args               Additional arguments.
		 *
		 * @return string|false The output HTML or false if no output.
		 *
		 * @see https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-shortcodes.php#L152
		 * @see https://github.com/woocommerce/woocommerce/blob/master/templates/content-product-cat.php
		 * @see https://developer.wordpress.org/reference/functions/get_terms/
		 * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
		 *
		 * @todo (dev) `$related_categories = array_slice( $related_categories, 0, $limit );` (after `shuffle()`)?
		 * @todo (dev) Use `parent` shortcode attribute?
		 */
		public function output_related_categories_default( $related_categories, $type, $args = array() ) {
			// Maybe random orderby.
			$orderby = $this->get_option_with_args( $type, 'orderby', $args );
			if ( 'random' === $orderby ) {
				$orderby = 'include';
				shuffle( $related_categories );
			}

			// Image options: Before the output.
			if ( filter_var( $this->get_option_with_args( $type, 'remove_image', $args ), FILTER_VALIDATE_BOOLEAN ) ) {
				// Remove image.
				$thumbnail_action_removed = remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
			} else {
				// Properties.
				$this->image_size[ $type ]        = $this->get_option_with_args( $type, 'image_size', $args );
				$this->placeholder_image[ $type ] = $this->get_option_with_args( $type, 'placeholder_image', $args );
				// Image size.
				add_filter( 'subcategory_archive_thumbnail_size', array( $this, 'image_size_' . $type ) );
				// Image placeholder.
				if ( ! empty( $this->placeholder_image[ $type ] ) ) {
					add_filter( 'woocommerce_placeholder_img_src', array( $this, 'placeholder_img_src_' . $type ) );
				}
			}

			// Shortcode output.
			$shortcode_output = do_shortcode(
				'[product_categories' .
				' limit="' . $this->get_option_with_args( $type, 'limit', $args ) . '"' .
				' columns="' . $this->get_option_with_args( $type, 'columns', $args ) . '"' .
				' hide_empty="' . (
					filter_var(
						$this->get_option_with_args( $type, 'hide_empty', $args ),
						FILTER_VALIDATE_BOOLEAN
					) ?
					1 :
					0
				) . '"' .
				' orderby="' . $orderby . '"' .
				' order="' . $this->get_option_with_args( $type, 'order', $args ) . '"' .
				' ids="' . implode( ',', $related_categories ) . '"' .
				']'
			);

			// Image options: After the output.
			if ( filter_var( $this->get_option_with_args( $type, 'remove_image', $args ), FILTER_VALIDATE_BOOLEAN ) ) {
				// Remove image.
				if ( $thumbnail_action_removed ) {
					add_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
				}
			} else {
				// Image size.
				remove_filter( 'subcategory_archive_thumbnail_size', array( $this, 'image_size_' . $type ) );
				// Image placeholder.
				if ( ! empty( $this->placeholder_image[ $type ] ) ) {
					remove_filter( 'woocommerce_placeholder_img_src', array( $this, 'placeholder_img_src_' . $type ) );
				}
				// Properties (clean up).
				$this->image_size[ $type ]        = false;
				$this->placeholder_image[ $type ] = false;
			}

			return (
				'<div class="woocommerce columns-' . $this->get_option_with_args( $type, 'columns', $args ) . '"></div>' !== $shortcode_output ?
				$shortcode_output :
				false
			);
		}

		/**
		 * Image size loop.
		 *
		 * @version 1.7.0
		 * @since   1.5.0
		 *
		 * @param array|string $image_size Name of the image size to get, or an array of dimensions.
		 *
		 * @todo (dev) Check if `image_size` exists in `get_intermediate_image_sizes()` (same in `image_size_single()`).
		 */
		public function image_size_loop( $image_size ) {
			return (
				! empty( $this->image_size['loop'] ) ?
				$this->image_size['loop'] :
				$image_size
			);
		}

		/**
		 * Image size single.
		 *
		 * @version 1.7.0
		 * @since   1.5.0
		 *
		 * @param array|string $image_size Name of the image size to get, or an array of dimensions.
		 */
		public function image_size_single( $image_size ) {
			return (
				! empty( $this->image_size['single'] ) ?
				$this->image_size['single'] :
				$image_size
			);
		}

		/**
		 * Placeholder image source.
		 *
		 * @version 1.7.0
		 * @since   1.5.0
		 *
		 * @param string $src               The source URL of the placeholder image.
		 * @param string $placeholder_image The placeholder image URL.
		 * @param string $size              The name of the image size.
		 */
		public function placeholder_img_src( $src, $placeholder_image, $size ) {
			if ( ! empty( $placeholder_image ) ) {
				if ( is_numeric( $placeholder_image ) ) {
					$image = wp_get_attachment_image_src( $placeholder_image, $size );
					if ( ! empty( $image[0] ) ) {
						return $image[0];
					}
				} else {
					return $placeholder_image;
				}
			}
			return $src;
		}

		/**
		 * Placeholder image source loop.
		 *
		 * @version 1.7.0
		 * @since   1.5.0
		 *
		 * @param string $src The source URL of the placeholder image.
		 *
		 * @todo (dev) Cache: `$this->placeholder_img_src['loop']` (also `$this->placeholder_img_src['single']`)?
		 */
		public function placeholder_img_src_loop( $src ) {
			return $this->placeholder_img_src(
				$src,
				$this->placeholder_image['loop'],
				$this->image_size['loop']
			);
		}

		/**
		 * Placeholder image source single.
		 *
		 * @version 1.7.0
		 * @since   1.5.0
		 *
		 * @param string $src The source URL of the placeholder image.
		 */
		public function placeholder_img_src_single( $src ) {
			return $this->placeholder_img_src(
				$src,
				$this->placeholder_image['single'],
				$this->image_size['single']
			);
		}

		/**
		 * Output related categories single shortcode.
		 *
		 * @version 2.1.0
		 * @since   1.3.2
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string
		 *
		 * @todo (feature) Customizable `relate_options`? (same for loop).
		 * @todo (dev) Add `[alg_wc_related_categories type="single"]` (and `[alg_wc_related_categories type="loop"]`) shortcodes?
		 */
		public function output_related_categories_single_shortcode( $atts, $content = '' ) {
			/**
			 * Available atts (same for `output_related_categories_loop_shortcode()`):
			 *
			 * 'template_type'
			 * 'template_header'
			 * 'template_footer'
			 * 'template_custom'
			 * 'template_custom_glue'
			 * 'limit'
			 * 'columns'
			 * 'hide_empty'
			 * 'orderby'
			 * 'order'
			 * 'placeholder_image'
			 * 'image_size'
			 * 'remove_image'
			 */

			$product_id = ( $atts['product_id'] ?? false );
			if ( '' !== $content ) {
				$atts['template_custom'] = $content;
			}

			ob_start();
			$this->output_related_categories_single(
				$product_id,
				( empty( $atts ) ? array() : $atts )
			);
			$output = ob_get_clean();

			return wp_kses(
				$output,
				$this->get_allowed_html()
			);
		}

		/**
		 * Output related categories loop shortcode.
		 *
		 * @version 2.1.0
		 * @since   1.3.2
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string
		 */
		public function output_related_categories_loop_shortcode( $atts, $content = '' ) {
			$product_category_id = ( $atts['product_category_id'] ?? false );
			if ( '' !== $content ) {
				$atts['template_custom'] = $content;
			}

			ob_start();
			$this->output_related_categories_loop(
				$product_category_id,
				( empty( $atts ) ? array() : $atts )
			);
			$output = ob_get_clean();

			return wp_kses(
				$output,
				$this->get_allowed_html()
			);
		}

		/**
		 * Language shortcode.
		 *
		 * @version 2.1.0
		 * @since   1.0.0
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string
		 */
		public function language_shortcode( $atts, $content = '' ) {
			// E.g.: `[alg_wc_related_categories_translate lang="DE" lang_text="Verwandte Kategorien" not_lang_text="Related categories"]`.
			if (
				isset( $atts['lang_text'] ) &&
				isset( $atts['not_lang_text'] ) &&
				! empty( $atts['lang'] )
			) {
				return (
					(
						! defined( 'ICL_LANGUAGE_CODE' ) ||
						! in_array(
							strtolower( ICL_LANGUAGE_CODE ),
							array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
							true
						)
					) ?
					wp_kses(
						$atts['not_lang_text'],
						$this->get_allowed_html()
					) :
					wp_kses(
						$atts['lang_text'],
						$this->get_allowed_html()
					)
				);
			}

			// E.g.: `[alg_wc_related_categories_translate lang="DE"]Verwandte Kategorien[/alg_wc_related_categories_translate][alg_wc_related_categories_translate not_lang="DE"]Related categories[/alg_wc_related_categories_translate]`.
			return (
				(
					(
						! empty( $atts['lang'] ) &&
						(
							! defined( 'ICL_LANGUAGE_CODE' ) ||
							! in_array(
								strtolower( ICL_LANGUAGE_CODE ),
								array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
								true
							)
						)
					) ||
					(
						! empty( $atts['not_lang'] ) &&
						(
							defined( 'ICL_LANGUAGE_CODE' ) &&
							in_array(
								strtolower( ICL_LANGUAGE_CODE ),
								array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ),
								true
							)
						)
					)
				) ?
				'' :
				wp_kses(
					$content,
					$this->get_allowed_html()
				)
			);
		}
	}

endif;

return new Alg_WC_Related_Categories_Frontend();
