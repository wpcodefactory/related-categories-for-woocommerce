<?php
/**
 * Related Categories for WooCommerce - Frontend Class
 *
 * @version 1.8.1
 * @since   1.7.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Frontend' ) ) :

class Alg_WC_Related_Categories_Frontend {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @todo    [maybe] move all shortcodes to a separate `class-alg-wc-related-categories-shortcodes.php` file?
	 */
	function __construct() {
		if ( ! is_admin() ) {
			add_action( 'init', array( $this, 'init' ) );
		}
	}

	/**
	 * get_core.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function get_core() {
		return alg_wc_related_categories()->core;
	}

	/**
	 * get_option_with_args.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @todo    [next] [!] (dev) maybe remove this? (i.e. as we have `override_options()` and `restore_options()` now)?
	 */
	function get_option_with_args( $type, $option, $args = array() ) {
		return ( isset( $args[ $option ] ) ? $args[ $option ] : $this->get_core()->options[ $type ][ $option ] );
	}

	/**
	 * init.
	 *
	 * @version 1.8.0
	 * @since   1.7.0
	 *
	 * @todo    [maybe] (feature) multiple positions/hooks (for both single and loop)
	 * @todo    [maybe] (feature) Hide related *products*: only if there are related *categories* available for the product
	 */
	function init() {
		// Output
		foreach ( array( 'single', 'loop' ) as $type ) {
			if ( 'yes' === $this->get_core()->options[ $type ]['enabled'] ) {
				if ( 'disable' != ( $position = $this->get_core()->options[ $type ]['position'] ) ) {
					add_action( $position, array( $this, 'output_related_categories_' . $type ), $this->get_core()->options[ $type ]['position_priority'] );
				}
				add_shortcode( 'alg_wc_related_categories_' . $type, array( $this, 'output_related_categories_' . $type . '_shortcode' ) );
			}
		}
		// Visibility
		add_filter( 'alg_wc_related_categories_loop', array( $this, 'check_visibility_loop' ), 10, 3 );
		// Hide related *products*
		if ( 'yes' === $this->get_core()->options['single']['hide_related_products'] ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			add_filter( 'woocommerce_related_products', '__return_empty_array', PHP_INT_MAX );
		}
		// Translation (WPML/Polylang) shortcode
		add_shortcode( 'alg_wc_related_categories_translate', array( $this, 'language_shortcode' ) );
	}

	/**
	 * check_visibility_loop.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @todo    [next] (dev) maybe move this to the `core`?
	 */
	function check_visibility_loop( $related_categories, $product_category_id, $relate_options ) {
		if ( 'all' !== $this->get_core()->options['loop']['visibility'] ) {
			switch ( $this->get_core()->options['loop']['visibility'] ) {
				case 'with_children_only':
					$children = get_term_children( $product_category_id, 'product_cat' );
					return ( ! empty( $children ) ? $related_categories : array() );
				case 'no_children_only':
					$children = get_term_children( $product_category_id, 'product_cat' );
					return (   empty( $children ) ? $related_categories : array() );
			}
		}
		return $related_categories;
	}

	/**
	 * output_related_categories_loop.
	 *
	 * @version 1.7.0
	 * @since   1.3.0
	 *
	 * @todo    [next] output inside standard subcategories (see `woocommerce_maybe_show_product_subcategories()`)
	 */
	function output_related_categories_loop( $product_category_id = false, $args = array() ) {
		if ( ! $product_category_id && is_product_category() ) {
			$product_category_id = get_queried_object_id();
		}
		$this->output_related_categories( $this->get_core()->get_related_categories_loop( $product_category_id ), 'loop', $args );
	}

	/**
	 * output_related_categories_single.
	 *
	 * @version 1.8.0
	 * @since   1.3.0
	 */
	function output_related_categories_single( $product = false, $args = array() ) {
		if ( ! $product ) {
			if ( is_product() ) {
				$product = wc_get_product( get_the_ID() );
			}
		} elseif ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		if ( $product && is_object( $product ) && is_a( $product, 'WC_Product' ) ) {
			$this->output_related_categories( $this->get_core()->get_related_categories_single( $product ), 'single', $args );
		}
	}

	/**
	 * output_related_categories.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @todo    [next] `orderby`: `count`: does not include children (even though `$term->count` shows it with children)
	 * @todo    [next] add placeholders, e.g. `%columns%`, `%limit%`?
	 * @todo    [next] add more `orderby` options?
	 * @todo    [maybe] rethink `$limit` default `4`?
	 */
	function output_related_categories( $related_categories, $type, $args = array() ) {
		if ( $related_categories ) {
			$output_func = 'output_related_categories_' . $this->get_option_with_args( $type, 'template_type', $args );
			$output      = $this->$output_func( $related_categories, $type, $args );
			if ( ! empty( $output ) ) {
				remove_shortcode( 'alg_wc_related_categories_' . $type );
				$template_header = do_shortcode( $this->get_option_with_args( $type, 'template_header', $args ) );
				$template_footer = do_shortcode( $this->get_option_with_args( $type, 'template_footer', $args ) );
				add_shortcode( 'alg_wc_related_categories_' . $type, array( $this, 'output_related_categories_' . $type . '_shortcode' ) );
				echo $template_header . $output . $template_footer;
			}
		}
	}

	/**
	 * prepare_terms.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @todo    [next] (dev) via filter?
	 * @todo    [next] [!] (feature) `relate_current_always_first`: `single`
	 */
	function prepare_terms( $terms, $type, $args = array() ) {
		if ( 'loop' === $type ) {
			if ( 'yes' === $this->get_option_with_args( $type, 'relate_current', $args ) && 'yes' === $this->get_option_with_args( $type, 'relate_current_always_first', $args ) ) {
				$current_term = array();
				$all_terms    = array();
				foreach ( $terms as $term ) {
					if ( is_product_category( $term->term_id ) ) {
						$current_term[] = $term;
					} else {
						$all_terms[]    = $term;
					}
				}
				if ( empty( $current_term ) && ( $queried_object = get_queried_object() ) && is_a( $queried_object, 'WP_Term' ) ) {
					$current_term = array( $queried_object );
					array_pop( $all_terms );
				}
				$terms = array_merge( $current_term, $all_terms );
			}
		}
		return $terms;
	}

	/**
	 * output_related_categories_custom.
	 *
	 * @version 1.8.1
	 * @since   1.7.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/get_terms/
	 * @see     https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
	 * @see     https://developer.wordpress.org/reference/classes/wp_term/
	 *
	 * @todo    [next] (dev) check `WC_Shortcodes::product_categories()` for "... workaround WP bug with parents/pad counts..."
	 * @todo    [next] (dev) `template_custom`: `do_shortcode()`?
	 * @todo    [next] (dev) cache: call `wc_placeholder_img_src()` only once?
	 * @todo    [next] (dev) `pad_counts`: no effect?
	 * @todo    [maybe] (dev) use `filter_var( ..., FILTER_VALIDATE_BOOLEAN )` everywhere?
	 * @todo    [maybe] (dev) merge "Maybe random orderby" from here with same in `output_related_categories_default()`?
	 * @todo    [maybe] (feature) add more placeholders, e.g. `$term->parent`?
	 */
	function output_related_categories_custom( $related_categories, $type, $args = array() ) {
		// Maybe random orderby
		$orderby = $this->get_option_with_args( $type, 'orderby', $args );
		if ( 'random' === $orderby ) {
			$orderby = 'include';
			shuffle( $related_categories );
		}
		// Get terms
		$hide_empty = ( filter_var( $this->get_option_with_args( $type, 'hide_empty', $args ), FILTER_VALIDATE_BOOLEAN ) ? 1 : 0 );
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'number'     => $this->get_option_with_args( $type, 'limit', $args ),
			'hide_empty' => $hide_empty,
			'orderby'    => $orderby,
			'order'      => $this->get_option_with_args( $type, 'order', $args ),
			'include'    => $related_categories,
			'pad_counts' => true,
		) );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$terms = $this->prepare_terms( $terms, $type, $args );
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
				$category_image_src = ( $thumbnail_id ? wp_get_attachment_image_src( $thumbnail_id, $this->get_option_with_args( $type, 'image_size', $args ) ) : false );
				$category_link      = get_term_link( $term );
				$placeholders       = array(
					'%category_id%'          => $term->term_id,
					'%category_name%'        => $term->name,
					'%category_slug%'        => $term->slug,
					'%category_description%' => $term->description,
					'%category_count%'       => $term->count,
					'%category_link%'        => ( ! is_wp_error( $category_link ) ? $category_link : '' ),
					'%category_image_id%'    => $thumbnail_id,
					'%category_image_link%'  => ( $category_image_src ? $category_image_src[0] : wc_placeholder_img_src( $this->get_option_with_args( $type, 'image_size', $args ) ) ),
					'%column_nr%'            => $column_nr,
					'%is_active%'            => ( 'loop' === $type && is_product_category( $term->term_id ) ? 'active' : '' ),
				);
				$output[] = str_replace( array_keys( $placeholders ), $placeholders, $this->get_option_with_args( $type, 'template_custom', $args ) );
				$counter++;
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
	 * output_related_categories_default.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-shortcodes.php#L152
	 * @see     https://github.com/woocommerce/woocommerce/blob/master/templates/content-product-cat.php
	 * @see     https://developer.wordpress.org/reference/functions/get_terms/
	 * @see     https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
	 *
	 * @todo    [maybe] `$related_categories = array_slice( $related_categories, 0, $limit );` (after `shuffle()`)?
	 * @todo    [maybe] use `parent` shortcode attribute?
	 */
	function output_related_categories_default( $related_categories, $type, $args = array() ) {
		// Maybe random orderby
		$orderby = $this->get_option_with_args( $type, 'orderby', $args );
		if ( 'random' === $orderby ) {
			$orderby = 'include';
			shuffle( $related_categories );
		}
		// Image options: Before the output
		if ( filter_var( $this->get_option_with_args( $type, 'remove_image', $args ), FILTER_VALIDATE_BOOLEAN ) ) {
			// Remove image
			$thumbnail_action_removed = remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
		} else {
			// Properties
			$this->image_size[ $type ]        = $this->get_option_with_args( $type, 'image_size', $args );
			$this->placeholder_image[ $type ] = $this->get_option_with_args( $type, 'placeholder_image', $args );
			// Image size
			add_filter( 'subcategory_archive_thumbnail_size', array( $this, 'image_size_' . $type ) );
			// Image placeholder
			if ( ! empty( $this->placeholder_image[ $type ] ) ) {
				add_filter( 'woocommerce_placeholder_img_src', array( $this, 'placeholder_img_src_' . $type ) );
			}
		}
		// Shortcode output
		$shortcode_output = do_shortcode( '[product_categories' .
			' limit="'      . $this->get_option_with_args( $type, 'limit', $args ) . '"' .
			' columns="'    . $this->get_option_with_args( $type, 'columns', $args ) . '"' .
			' hide_empty="' . ( filter_var( $this->get_option_with_args( $type, 'hide_empty', $args ), FILTER_VALIDATE_BOOLEAN ) ? 1 : 0 ) . '"' .
			' orderby="'    . $orderby . '"' .
			' order="'      . $this->get_option_with_args( $type, 'order', $args ) . '"' .
			' ids="'        . implode( ',', $related_categories ) . '"' .
		']' );
		// Image options: After the output
		if ( filter_var( $this->get_option_with_args( $type, 'remove_image', $args ), FILTER_VALIDATE_BOOLEAN ) ) {
			// Remove image
			if ( $thumbnail_action_removed ) {
				add_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
			}
		} else {
			// Image size
			remove_filter( 'subcategory_archive_thumbnail_size', array( $this, 'image_size_' . $type ) );
			// Image placeholder
			if ( ! empty( $this->placeholder_image[ $type ] ) ) {
				remove_filter( 'woocommerce_placeholder_img_src', array( $this, 'placeholder_img_src_' . $type ) );
			}
			// Properties (clean up)
			$this->image_size[ $type ]        = false;
			$this->placeholder_image[ $type ] = false;
		}
		return ( '<div class="woocommerce columns-' . $this->get_option_with_args( $type, 'columns', $args ) . '"></div>' !== $shortcode_output ? $shortcode_output : false );
	}

	/**
	 * image_size_loop.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 *
	 * @todo    [next] check if `image_size` exists in `get_intermediate_image_sizes()` (same in `image_size_single()`)
	 */
	function image_size_loop( $image_size ) {
		return ( ! empty( $this->image_size['loop'] ) ? $this->image_size['loop'] : $image_size );
	}

	/**
	 * image_size_single.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 */
	function image_size_single( $image_size ) {
		return ( ! empty( $this->image_size['single'] ) ? $this->image_size['single'] : $image_size );
	}

	/**
	 * placeholder_img_src.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 */
	function placeholder_img_src( $src, $placeholder_image, $size ) {
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
	 * placeholder_img_src_loop.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 *
	 * @todo    [next] cache: `$this->placeholder_img_src['loop']` (also `$this->placeholder_img_src['single']`)?
	 */
	function placeholder_img_src_loop( $src ) {
		return $this->placeholder_img_src( $src, $this->placeholder_image['loop'], $this->image_size['loop'] );
	}

	/**
	 * placeholder_img_src_single.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 */
	function placeholder_img_src_single( $src ) {
		return $this->placeholder_img_src( $src, $this->placeholder_image['single'], $this->image_size['single'] );
	}

	/**
	 * output_related_categories_single_shortcode.
	 *
	 * @version 1.7.0
	 * @since   1.3.2
	 *
	 * @todo    [next] (feature) customizable `relate_options`? (same for loop)
	 * @todo    [next] (dev) add `[alg_wc_related_categories type="single"]` (and `[alg_wc_related_categories type="loop"]`) shortcodes?
	 */
	function output_related_categories_single_shortcode( $atts, $content = '' ) {

		/**
		 *
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
		 *
		 */

		$product_id = ( isset( $atts['product_id'] ) ? $atts['product_id'] : false );
		if ( '' !== $content ) {
			$atts['template_custom'] = $content;
		}
		ob_start();
		$this->output_related_categories_single( $product_id, ( empty( $atts ) ? array() : $atts ) );
		return ob_get_clean();
	}

	/**
	 * output_related_categories_loop_shortcode.
	 *
	 * @version 1.7.0
	 * @since   1.3.2
	 */
	function output_related_categories_loop_shortcode( $atts, $content = '' ) {
		$product_category_id = ( isset( $atts['product_category_id'] ) ? $atts['product_category_id'] : false );
		if ( '' !== $content ) {
			$atts['template_custom'] = $content;
		}
		ob_start();
		$this->output_related_categories_loop( $product_category_id, ( empty( $atts ) ? array() : $atts ) );
		return ob_get_clean();
	}

	/**
	 * language_shortcode.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_related_categories_translate lang="DE" lang_text="Verwandte Kategorien" not_lang_text="Related categories"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_related_categories_translate lang="DE"]Verwandte Kategorien[/alg_wc_related_categories_translate][alg_wc_related_categories_translate not_lang="DE"]Related categories[/alg_wc_related_categories_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

}

endif;

return new Alg_WC_Related_Categories_Frontend();
