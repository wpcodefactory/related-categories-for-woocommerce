<?php
/**
 * Related Categories for WooCommerce - Widget Class
 *
 * @version 1.8.0
 * @since   1.7.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Widget' ) ) :

class Alg_WC_Related_Categories_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$widget_ops = array(
			'classname'   => "alg_wc_related_categories_widget_{$this->type}",
			'description' => __( 'A list of related categories.', 'related-categories-for-woocommerce' ),
		);
		parent::__construct( $widget_ops['classname'], __( 'Related Categories', 'related-categories-for-woocommerce' ) . ': ' . $this->desc, $widget_ops );
	}

	/**
	 * get_related_categories.
	 *
	 * @version 1.8.0
	 * @since   1.7.0
	 *
	 * @todo    [next] [!] (dev) `! $do_override_relate_options`: remove all hidden options from `$instance`
	 * @todo    [next] [!] (dev) override_options + visibility: better solution
	 */
	function get_related_categories( $instance ) {
		$func = "output_related_categories_{$this->type}";
		if ( isset( $instance['title'] ) ) {
			unset( $instance['title'] );
		}
		$instance['template_type'] = 'custom';
		ob_start();
		$do_override_relate_options = ( 'yes' === alg_wc_related_categories()->core->options[ $this->type ]['widget_override_relate_options'] );
		if ( $do_override_relate_options ) {
			if ( isset( $instance['relate_per_taxonomy'] ) ) {
				$instance['relate_per_taxonomy'] = ( 'yes' === $instance['relate_per_taxonomy'] ?
					alg_wc_related_categories()->core->options[ $this->type ]['relate_per_taxonomy'] : '' );
			}
			alg_wc_related_categories()->core->override_options( $this->type, $instance );
		} else {
			if ( isset( $instance['visibility'] ) ) {
				alg_wc_related_categories()->core->override_options( $this->type, array( 'visibility' => $instance['visibility'] ) );
			}
			if ( isset( $instance['relate_current'] ) ) {
				unset( $instance['relate_current'] );
			}
			if ( isset( $instance['relate_current_always_first'] ) ) {
				unset( $instance['relate_current_always_first'] );
			}
		}
		alg_wc_related_categories()->core->frontend->$func( false, $instance );
		alg_wc_related_categories()->core->restore_options( $this->type );
		return ob_get_clean();
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @param   array $args
	 * @param   array $instance
	 */
	function widget( $args, $instance ) {
		if ( $related_categories = $this->get_related_categories( $instance ) ) {
			$html = '';
			$html .= $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				$html .= $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			$html .= $related_categories;
			$html .= $args['after_widget'];
			echo $html;
		}
	}

	/**
	 * get_widget_option_fields.
	 *
	 * @version 1.8.0
	 * @since   1.7.0
	 *
	 * @todo    [next] (dev) Relate Options: `relate_per_taxonomy`: rethink
	 * @todo    [next] (dev) Relate Options: `show_if_checked`
	 * @todo    [next] (dev) automatically get options list from `Alg_WC_Related_Categories_Settings_General`
	 * @todo    [next] (dev) get default values from `get_option()`? (and then maybe remove `widget_override_relate_options` option, i.e. always `yes`)
	 * @todo    [next] (dev) `placeholder_image` and `image_size`?
	 * @todo    [next] (dev) `textarea`, `glue`: sanitize?
	 * @todo    [next] (desc) `orderby`: shorter desc?
	 * @todo    [next] (desc) `order`: shorter desc?
	 * @todo    [later] (dev) Relate Options: `relate_per_category`, `relate_per_tag`, etc.: apply `alg_wc_related_categories_settings` filter?
	 * @todo    [maybe] (desc) Relate Options: `relate_per_category`, `relate_per_tag`, etc.: add tip: '... options must be enabled in "WooCommerce > Settings > Related Categories"...'
	 * @todo    [maybe] (dev) customizable `template_type`? (then also `remove_image`)?
	 * @todo    [maybe] (dev) Template(s) & Glue: better default values?
	 */
	function get_widget_option_fields() {
		$settings = array(
			// General Options
			'title' => array(
				'title'    => __( 'Title', 'related-categories-for-woocommerce' ),
				'default'  => '',
			),
			'limit' => array(
				'title'    => __( 'Limit', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'The number of categories to display.', 'related-categories-for-woocommerce' ),
				'default'  => 4,
				'type'     => 'number',
				'custom_atts' => 'min="1"',
			),
			'columns' => array(
				'title'    => __( 'Columns', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'The number of columns to display.', 'related-categories-for-woocommerce' ) . ' ' .
					sprintf( __( 'You can use this in the "%s" option, e.g.: %s.', 'related-categories-for-woocommerce' ),
						__( 'Template', 'related-categories-for-woocommerce' ),
						'<code>' . esc_html( 'class="column-%column_nr%"' ) . '</code>' ),
				'default'  => 4,
				'type'     => 'number',
				'custom_atts' => 'min="1"',
			),
			'hide_empty' => array(
				'title'    => __( 'Hide empty', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Hide/show empty categories.', 'related-categories-for-woocommerce' ),
				'default'  => 'yes',
				'type'     => 'select',
				'options'  => array(
					'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
					'no'  => __( 'No', 'related-categories-for-woocommerce' ),
				),
			),
			'orderby' => array(
				'title'    => __( 'Order by', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'If you want to order by the ids you specified then you can use "None".', 'related-categories-for-woocommerce' ) . ' ' .
					sprintf( __( '"Count" option will sort categories as when sorted by "Count" column in %s.', 'related-categories-for-woocommerce' ),
						'<a href="' . admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product&orderby=count&order=desc' ) . '">' .
							__( 'Products > Categories', 'related-categories-for-woocommerce' ) .
						'</a>' ),
				'default'  => 'name',
				'type'     => 'select',
				'options'  => array(
					'name'       => __( 'Name', 'related-categories-for-woocommerce' ),
					'id'         => __( 'ID', 'related-categories-for-woocommerce' ),
					'slug'       => __( 'Slug', 'related-categories-for-woocommerce' ),
					'menu_order' => __( 'Menu order', 'related-categories-for-woocommerce' ),
					'include'    => __( 'None', 'related-categories-for-woocommerce' ),
					'random'     => __( 'Random', 'related-categories-for-woocommerce' ),
					'count'      => __( 'Count', 'related-categories-for-woocommerce' ),
				),
			),
			'order' => array(
				'title'    => __( 'Order', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'States whether the category ordering is ascending or descending, using the method set in "Order by".', 'related-categories-for-woocommerce' ),
				'default'  => 'ASC',
				'type'     => 'select',
				'options'  => array(
					'ASC'  => __( 'Ascending', 'related-categories-for-woocommerce' ),
					'DESC' => __( 'Descending', 'related-categories-for-woocommerce' ),
				),
			),
		);
		if ( 'loop' === $this->type ) {
			$settings = array_merge( $settings, array(
				'visibility' => array(
					'title'    => __( 'Visibility', 'related-categories-for-woocommerce' ),
					'default'  => 'all',
					'type'     => 'select',
					'options'  => array(
						'all'                =>  __( 'Show for all categories', 'related-categories-for-woocommerce' ),
						'with_children_only' =>  __( 'Show for categories with children only', 'related-categories-for-woocommerce' ),
						'no_children_only'   =>  __( 'Show for categories with no children only', 'related-categories-for-woocommerce' ),
					),
				),
			) );
		}
		if ( 'yes' === alg_wc_related_categories()->core->options[ $this->type ]['widget_override_relate_options'] ) {
			$settings = array_merge( $settings, array(
				// Relate Options
				'relate_section_title' => array(
					'title'    => __( 'Relate Options', 'related-categories-for-woocommerce' ),
					'type'     => 'title',
				),
				'relate_siblings' => array(
					'title'    => __( 'Siblings', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to display all categories with the same parents (i.e. siblings).', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_siblings_include_grandparents' => array(
					'title'    => __( 'Siblings', 'related-categories-for-woocommerce' ) . ': ' . __( 'Include grandparents', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to include category grandparents\' children as well.', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_siblings_include_top_level' => array(
					'title'    => __( 'Siblings', 'related-categories-for-woocommerce' ) . ': ' . __( 'Include top-level', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to include top-level (i.e. zero) category in (grand)parents.', 'related-categories-for-woocommerce' ),
					'default'  => 'no',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_siblings_include_grandchildren' => array(
					'title'    => __( 'Siblings', 'related-categories-for-woocommerce' ) . ': ' . __( 'Include grandchildren', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to include (grand)parents\' grandchildren as well.', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_parents' => array(
					'title'    => __( 'Parents', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to display all the parent categories.', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_parents_include_grandparents' => array(
					'title'    => __( 'Parents', 'related-categories-for-woocommerce' ) . ': ' . __( 'Include grandparents', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to include category grandparents as well. If disabled will always show single parent category only.', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_children' => array(
					'title'    => __( 'Children', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to display all the child categories.', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_children_include_grandchildren' => array(
					'title'    => __( 'Children', 'related-categories-for-woocommerce' ) . ': ' . __( 'Include grandchildren', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable this if you would like to include category grandchildren as well.', 'related-categories-for-woocommerce' ),
					'default'  => 'yes',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
				'relate_current' => array(
					'title'    => __( 'Current', 'related-categories-for-woocommerce' ),
					'desc'     => ( 'single' === $this->type ?
						__( 'Enable this if you would like to display all the current categories.', 'related-categories-for-woocommerce' ) :
						__( 'Enable this if you would like to display the current category.', 'related-categories-for-woocommerce' ) ),
					'default'  => 'no',
					'type'     => 'select',
					'options'  => array(
						'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
						'no'  => __( 'No', 'related-categories-for-woocommerce' ),
					),
				),
			) );
			if ( 'loop' === $this->type ) {
				$settings = array_merge( $settings, array(
					'relate_current_always_first' => array(
						'title'    => __( 'Current', 'related-categories-for-woocommerce' ) . ': ' . __( 'Always show first', 'related-categories-for-woocommerce' ),
						'default'  => 'no',
						'type'     => 'select',
						'options'  => array(
							'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
							'no'  => __( 'No', 'related-categories-for-woocommerce' ),
						),
					),
				) );
			}
			if ( 'yes' === alg_wc_related_categories()->core->options[ $this->type ]['relate_per_category'] ) {
				$settings = array_merge( $settings, array(
					'relate_per_category' => array(
						'title'    => __( 'Per category', 'related-categories-for-woocommerce' ),
						'default'  => 'no',
						'type'     => 'select',
						'options'  => array(
							'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
							'no'  => __( 'No', 'related-categories-for-woocommerce' ),
						),
					),
				) );
			}
			if ( 'yes' === alg_wc_related_categories()->core->options[ $this->type ]['relate_per_tag'] ) {
				$settings = array_merge( $settings, array(
					'relate_per_tag' => array(
						'title'    => __( 'Per tag', 'related-categories-for-woocommerce' ),
						'default'  => 'no',
						'type'     => 'select',
						'options'  => array(
							'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
							'no'  => __( 'No', 'related-categories-for-woocommerce' ),
						),
					),
				) );
			}
			if ( '' !== alg_wc_related_categories()->core->options[ $this->type ]['relate_per_taxonomy'] ) {
				$settings = array_merge( $settings, array(
					'relate_per_taxonomy' => array(
						'title'    => __( 'Per custom taxonomy', 'related-categories-for-woocommerce' ),
						'default'  => 'no',
						'type'     => 'select',
						'options'  => array(
							'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
							'no'  => __( 'No', 'related-categories-for-woocommerce' ),
						),
					),
				) );
			}
			if ( 'single' === $this->type && 'yes' === alg_wc_related_categories()->core->options[ $this->type ]['relate_per_product'] ) {
				$settings = array_merge( $settings, array(
					'relate_per_product' => array(
						'title'    => __( 'Per product', 'related-categories-for-woocommerce' ),
						'default'  => 'no',
						'type'     => 'select',
						'options'  => array(
							'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
							'no'  => __( 'No', 'related-categories-for-woocommerce' ),
						),
					),
					'relate_per_product_override' => array(
						'title'    => __( 'Per product', 'related-categories-for-woocommerce' ) . ': ' . __( 'Override', 'related-categories-for-woocommerce' ),
						'desc'     => __( 'Choose if you want categories to be overridden by "Per product" settings or merged with them.', 'related-categories-for-woocommerce' ),
						'default'  => 'yes',
						'type'     => 'select',
						'options'  => array(
							'yes' => __( 'Yes', 'related-categories-for-woocommerce' ),
							'no'  => __( 'No', 'related-categories-for-woocommerce' ),
						),
					),
				) );
			}
		}
		$placeholders = array(
			'%category_name%',
			'%category_description%',
			'%category_count%',
			'%category_link%',
			'%category_image_link%',
			'%column_nr%',
			'%category_id%',
			'%category_slug%',
			'%category_image_id%',
		);
		if ( 'loop' === $this->type ) {
			$placeholders[] = '%is_active%';
		}
		$settings = array_merge( $settings, array(
			// Template Options
			'template_section_title' => array(
				'title'    => __( 'Template Options', 'related-categories-for-woocommerce' ),
				'type'     => 'title',
			),
			'template_header' => array(
				'title'    => __( 'Header', 'related-categories-for-woocommerce' ),
				'default'  => '<ul>',
				'type'     => 'textarea',
			),
			'template_footer' => array(
				'title'    => __( 'Footer', 'related-categories-for-woocommerce' ),
				'default'  => '</ul>',
				'type'     => 'textarea',
			),
			'template_custom' => array(
				'title'    => __( 'Template', 'related-categories-for-woocommerce' ),
				'desc'     => '<details><summary>' . __( 'Available placeholders', 'related-categories-for-woocommerce' ) . '</summary><p>' .
					'<ul><li><code>' . implode( '</code></li><li><code>', $placeholders ) . '</code></li></ul>' . '</p></details>',
				'default'  => '<li><a href="%category_link%" title="%category_name%">%category_name%</a></li>',
				'type'     => 'textarea',
			),
			'template_custom_glue' => array(
				'title'    => __( 'Glue', 'related-categories-for-woocommerce' ),
				'default'  => '',
				'type'     => 'text',
			),
		) );
		return $settings;
	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @version 1.8.0
	 * @since   1.7.0
	 *
	 * @param   array $instance The widget options
	 */
	function form( $instance ) {
		$html = '';
		foreach ( $this->get_widget_option_fields() as $id => $widget_option_field ) {
			if ( isset( $widget_option_field['type'] ) && 'title' === $widget_option_field['type'] ) {
				$html .= '<hr><p><strong>' . $widget_option_field['title'] . '</strong></p>';
				continue;
			}
			$value = ( isset( $instance[ $id ] ) ? $instance[ $id ] : $widget_option_field['default'] );
			$label = sprintf( '<label for="%s">%s</label>', $this->get_field_id( $id ), $widget_option_field['title'] );
			if ( ! isset( $widget_option_field['type'] ) ) {
				$widget_option_field['type'] = 'text';
			}
			if ( ! isset( $widget_option_field['class'] ) ) {
				$widget_option_field['class'] = 'widefat';
			}
			$desc        = ( isset( $widget_option_field['desc'] ) ? '<br><em>' . $widget_option_field['desc'] . '</em>' : '' );
			$custom_atts = ( isset( $widget_option_field['custom_atts'] ) ? ' ' . $widget_option_field['custom_atts'] : '' );
			switch ( $widget_option_field['type'] ) {
				case 'select':
					$options = '';
					foreach ( $widget_option_field['options'] as $option_id => $option_title ) {
						$options .= sprintf( '<option value="%s"%s>%s</option>', $option_id, selected( $option_id, $value, false ), $option_title );
					}
					$field = sprintf( '<select class="' . $widget_option_field['class'] . '" id="%s" name="%s"' . $custom_atts . '>%s</select>',
						$this->get_field_id( $id ), $this->get_field_name( $id ), $options );
					break;
				case 'textarea':
					$field = sprintf( '<textarea class="' . $widget_option_field['class'] . '" id="%s" name="%s"' . $custom_atts . '>%s</textarea>',
						$this->get_field_id( $id ), $this->get_field_name( $id ), esc_attr( $value ) );
					break;
				default: // e.g. 'text'
					$field = sprintf( '<input class="' . $widget_option_field['class'] . '" id="%s" name="%s" type="' . $widget_option_field['type'] . '" value="%s"' . $custom_atts . '>',
						$this->get_field_id( $id ), $this->get_field_name( $id ), esc_attr( $value ) );
			}
			$html .= '<p>' . $label . $field . $desc . '</p>';
		}
		echo $html;
	}

	/**
	 * Processing widget options on save.
	 *
	 * @version 1.8.0
	 * @since   1.7.0
	 *
	 * @param   array $new_instance The new options
	 * @param   array $old_instance The previous options
	 */
	function update( $new_instance, $old_instance ) {
		foreach ( $this->get_widget_option_fields() as $id => $widget_option_field ) {
			if ( ( ! isset( $widget_option_field['type'] ) || 'title' !== $widget_option_field['type'] ) && ! isset( $new_instance[ $id ] ) ) {
				$new_instance[ $id ] = $widget_option_field['default'];
			}
		}
		return $new_instance;
	}

}

endif;

if ( ! class_exists( 'Alg_WC_Related_Categories_Widget_Single' ) ) :

class Alg_WC_Related_Categories_Widget_Single extends Alg_WC_Related_Categories_Widget {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$this->type = 'single';
		$this->desc = __( 'Single Product Page', 'related-categories-for-woocommerce' );
		parent::__construct();
	}

}

endif;

if ( ! class_exists( 'Alg_WC_Related_Categories_Widget_Loop' ) ) :

class Alg_WC_Related_Categories_Widget_Loop extends Alg_WC_Related_Categories_Widget {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$this->type = 'loop';
		$this->desc = __( 'Archives', 'related-categories-for-woocommerce' );
		parent::__construct();
	}

}

endif;

if ( ! function_exists( 'register_alg_wc_related_categories_widget' ) ) {
	/**
	 * register Alg_WC_Related_Categories_Widget widget.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function register_alg_wc_related_categories_widget() {
		if ( 'yes' === alg_wc_related_categories()->core->options['single']['enabled'] && 'yes' === alg_wc_related_categories()->core->options['single']['widget'] ) {
			register_widget( 'Alg_WC_Related_Categories_Widget_Single' );
		}
		if ( 'yes' === alg_wc_related_categories()->core->options['loop']['enabled']   && 'yes' === alg_wc_related_categories()->core->options['loop']['widget'] ) {
			register_widget( 'Alg_WC_Related_Categories_Widget_Loop' );
		}
	}
}
add_action( 'widgets_init', 'register_alg_wc_related_categories_widget' );
