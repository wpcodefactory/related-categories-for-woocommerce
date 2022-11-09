<?php
/**
 * Related Categories for WooCommerce - General Section Settings
 *
 * @version 1.9.2
 * @since   1.7.0
 *
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Related_Categories_Settings_General' ) ) :

class Alg_WC_Related_Categories_Settings_General extends Alg_WC_Related_Categories_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @todo    [maybe] use `{$this->option_prefix}` instead of `alg_wc_related_categories_options_{$this->type}`
	 */
	function __construct( $single_or_loop ) {
		$this->id    = ( 'single' === $single_or_loop ? ''                                                                : 'loop' );
		$this->desc  = ( 'single' === $single_or_loop ? __( 'Single', 'related-categories-for-woocommerce' )              : __( 'Archives', 'related-categories-for-woocommerce' ) );
		$this->title = ( 'single' === $single_or_loop ? __( 'Single Product Page', 'related-categories-for-woocommerce' ) : __( 'Archives', 'related-categories-for-woocommerce' ) );
		$this->type  = $single_or_loop;
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @todo    [maybe] move all to this function (i.e. instead of separate `get_general_settings()`, `get_relate_settings()`, etc.)?
	 */
	function get_settings() {
		return array_merge(
			$this->get_general_settings(),
			$this->get_relate_settings(),
			$this->get_position_settings(),
			$this->get_template_settings(),
			$this->get_image_settings(),
			$this->get_advanced_settings()
		);
	}

	/**
	 * get_general_settings.
	 *
	 * @version 1.9.1
	 * @since   1.3.0
	 *
	 * @todo    [now] (desc) rename "Archives" to "Shop"?
	 * @todo    [next] Order by: Count: remove desc?
	 */
	function get_general_settings() {
		$default_enabled = ( 'single' === $this->type ? 'yes' : 'no' );
		return array(
			array(
				'title'    => sprintf( __( '%s Options', 'related-categories-for-woocommerce' ), $this->title ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_general_options',
			),
			array(
				'title'    => sprintf( __( 'Display on %s', 'related-categories-for-woocommerce' ), $this->title ),
				'desc'     => '<strong>' . __( 'Enable section', 'related-categories-for-woocommerce' ) . '</strong>',
				'id'       => "alg_wc_related_categories_options_{$this->type}[enabled]",
				'default'  => $default_enabled,
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Limit', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'The number of categories to display.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[limit]",
				'default'  => 4,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Columns', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'The number of columns to display.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[columns]",
				'default'  => 4,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'title'    => __( 'Hide empty', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Hide', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Hide/show empty categories.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[hide_empty]",
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Order by', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'If you want to order by the ids you specified then you can use "None".', 'related-categories-for-woocommerce' ),
				'desc'     => sprintf( __( '"Count" option will sort categories as when sorted by the "Count" column in %s.', 'related-categories-for-woocommerce' ),
					'<a href="' . admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product&orderby=count&order=desc' ) . '">' .
						__( 'Products > Categories', 'related-categories-for-woocommerce' ) .
					'</a>' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[orderby]",
				'default'  => 'name',
				'type'     => 'select',
				'class'    => 'chosen_select',
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
			array(
				'title'    => __( 'Order', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'States whether the category ordering is ascending or descending, using the method set in "Order by".', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[order]",
				'default'  => 'ASC',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'ASC'  => __( 'Ascending', 'related-categories-for-woocommerce' ),
					'DESC' => __( 'Descending', 'related-categories-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_general_options',
			),
		);
	}

	/**
	 * get_relate_settings.
	 *
	 * @version 1.8.0
	 * @since   1.3.0
	 *
	 * @todo    [maybe] (dev) `alg_wc_related_categories_settings`: better styling?
	 * @todo    [maybe] (desc) Per custom taxonomy: better desc?
	 * @todo    [maybe] (desc) "Relate Options": `__( 'These options will allow you to set related categories <strong>automatically</strong>.', 'related-categories-for-woocommerce' )`?
	 */
	function get_relate_settings() {
		$current_desc = ( 'single' === $this->type ?
			__( 'Enable this if you would like to display all the current categories.', 'related-categories-for-woocommerce' ) :
			__( 'Enable this if you would like to display the current category.', 'related-categories-for-woocommerce' )
		);
		$settings = array(
			array(
				'title'    => __( 'Relate Options', 'related-categories-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_relate_options',
			),
			array(
				'title'    => __( 'Siblings', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to display all categories with the same parents (i.e. siblings).', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_siblings]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
			),
			array(
				'desc'     => __( 'Include grandparents', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to include category grandparents\' children as well.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_siblings_include_grandparents]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => '',
				'show_if_checked' => 'yes',
			),
			array(
				'desc'     => __( 'Include top-level', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to include top-level (i.e. zero) category in (grand)parents.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_siblings_include_top_level]",
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'   => '',
				'show_if_checked' => 'yes',
			),
			array(
				'desc'     => __( 'Include grandchildren', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to include (grand)parents\' grandchildren as well.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_siblings_include_grandchildren]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'end',
				'show_if_checked' => 'yes',
			),
			array(
				'title'    => __( 'Parents', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to display all the parent categories.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_parents]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
			),
			array(
				'desc'     => __( 'Include grandparents', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to include category grandparents as well. If disabled will always show single parent category only.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_parents_include_grandparents]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'end',
				'show_if_checked' => 'yes',
			),
			array(
				'title'    => __( 'Children', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to display all the child categories.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_children]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
			),
			array(
				'desc'     => __( 'Include grandchildren', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you would like to include category grandchildren as well.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_children_include_grandchildren]",
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'end',
				'show_if_checked' => 'yes',
			),
			array(
				'title'    => __( 'Current', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => $current_desc,
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_current]",
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'   => ( 'loop' === $this->type ? 'start'  : null ),
				'show_if_checked' => ( 'loop' === $this->type ? 'option' : null ),
			),
		);
		if ( 'loop' === $this->type ) {
			$settings = array_merge( $settings, array(
				array(
					'desc'     => __( 'Always show first', 'related-categories-for-woocommerce' ),
					'desc_tip' => __( 'Always show the current category in first position.', 'related-categories-for-woocommerce' ) . ' ' .
						sprintf( __( 'Works for the "%s" template type only.', 'related-categories-for-woocommerce' ),
							'<strong>' . __( 'Custom', 'related-categories-for-woocommerce' ) . '</strong>' ),
					'id'       => "alg_wc_related_categories_options_{$this->type}[relate_current_always_first]",
					'default'  => 'no',
					'type'     => 'checkbox',
					'checkboxgroup'   => 'end',
					'show_if_checked' => 'yes',
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_relate_options',
			),
			array(
				'title'    => __( 'Relate Manually', 'related-categories-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_relate_manually_options',
				'desc'     => __( 'These options will allow you to set related categories <strong>manually</strong>.', 'related-categories-for-woocommerce' ) .
					apply_filters( 'alg_wc_related_categories_settings',
						' ' . 'You will need <a target="_blank" href="https://wpfactory.com/item/related-categories-for-woocommerce/">Related Categories for WooCommerce Pro</a> plugin version to enable options in this section.' ),
			),
			array(
				'title'    => __( 'Per category', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'This option will allow you to set related categories manually for each category.', 'related-categories-for-woocommerce' ) . ' ' .
					sprintf( __( 'It will add "Related categories" settings section to each category edit page (in %s).', 'related-categories-for-woocommerce' ),
						'<a href="' . admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ) . '">' .
							__( 'Products > Categories > Edit category', 'related-categories-for-woocommerce' ) . '</a>' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_per_category]",
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_related_categories_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per tag', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'This option will allow you to set related categories manually for each tag.', 'related-categories-for-woocommerce' ) . ' ' .
					sprintf( __( 'It will add "Related categories" settings section to each tag edit page (in %s).', 'related-categories-for-woocommerce' ),
						'<a href="' . admin_url( 'edit-tags.php?taxonomy=product_tag&post_type=product' ) . '">' .
							__( 'Products > Tags > Edit tag', 'related-categories-for-woocommerce' ) . '</a>' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_per_tag]",
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_related_categories_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per custom taxonomy', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'This option will allow you to set related categories manually for each custom taxonomy.', 'related-categories-for-woocommerce' ) . ' ' .
					__( 'It will add "Related categories" settings section to each custom taxonomy edit page.', 'related-categories-for-woocommerce' ) . '<br>' .
					sprintf( __( 'Set as comma separated taxonomy slug list, e.g.: %s.', 'related-categories-for-woocommerce' ), '<code>product_brand,product_color</code>' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[relate_per_taxonomy]",
				'default'  => '',
				'type'     => 'text',
				'custom_attributes' => apply_filters( 'alg_wc_related_categories_settings', array( 'readonly' => 'readonly' ) ),
			),
		) );
		if ( 'single' === $this->type ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Per product', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
					'desc_tip' => __( 'This option will allow you to set related categories manually for each product.', 'related-categories-for-woocommerce' ) . ' ' .
						sprintf( __( 'It will add "Related categories" meta box to each product edit page (in %s).', 'related-categories-for-woocommerce' ),
							'<a href="' . admin_url( 'edit.php?post_type=product' ) . '">' .
								__( 'Products > Edit product', 'related-categories-for-woocommerce' ) . '</a>' ),
					'id'       => "alg_wc_related_categories_options_{$this->type}[relate_per_product]",
					'default'  => 'no',
					'type'     => 'checkbox',
					'checkboxgroup'   => 'start',
					'show_if_checked' => 'option',
					'custom_attributes' => apply_filters( 'alg_wc_related_categories_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'     => __( 'Override', 'related-categories-for-woocommerce' ),
					'desc_tip' => __( 'Choose if you want categories to be overridden by "Per product" settings or merged with them.', 'related-categories-for-woocommerce' ),
					'id'       => "alg_wc_related_categories_options_{$this->type}[relate_per_product_override]",
					'default'  => 'yes',
					'type'     => 'checkbox',
					'checkboxgroup'   => 'end',
					'show_if_checked' => 'yes',
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_relate_manually_options',
			),
		) );
		return $settings;
	}

	/**
	 * get_position_settings.
	 *
	 * @version 1.9.2
	 * @since   1.3.0
	 *
	 * @todo    [next] widget: move to a separate subsection?
	 * @todo    [next] widget: remove option (i.e. always `yes`), and then move the desc to the "Enable section" option? (unless we move Widget options to a separate subsection)?
	 * @todo    [later] loop: add `$section_desc`
	 * @todo    [later] (feature) custom hooks (i.e. positions)
	 * @todo    [maybe] add more Positions (both single and loop)
	 * @todo    [maybe] shortcode: better desc?
	 */
	function get_position_settings() {
		$section_desc     = ( 'single' === $this->type ? __( 'To place related categories section before the standard related products section, select "After single product summary" for "Position" and "19" for "Position order". To place it after the related products section, set "21" for "Position order".', 'related-categories-for-woocommerce' ) . '<br>' : '' );
		$default_position = ( 'single' === $this->type ? 'woocommerce_after_single_product_summary' : 'woocommerce_after_shop_loop' );
		$default_priority = ( 'single' === $this->type ? 21 : 9 );
		$options          = ( 'single' === $this->type ?
			array(
				'woocommerce_before_single_product'         => __( 'Before single product', 'related-categories-for-woocommerce' ),
				'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'related-categories-for-woocommerce' ),
				'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'related-categories-for-woocommerce' ),
				'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'related-categories-for-woocommerce' ),
				'woocommerce_after_single_product'          => __( 'After single product', 'related-categories-for-woocommerce' ),
				'disable'                                   => __( 'Disable', 'related-categories-for-woocommerce' ),
			) :
			array(
				'woocommerce_before_main_content'           => __( 'Before main content', 'related-categories-for-woocommerce' ),
				'woocommerce_before_shop_loop'              => __( 'Before shop loop', 'related-categories-for-woocommerce' ),
				'woocommerce_after_shop_loop'               => __( 'After shop loop', 'related-categories-for-woocommerce' ),
				'woocommerce_after_main_content'            => __( 'After main content', 'related-categories-for-woocommerce' ),
				'disable'                                   => __( 'Disable', 'related-categories-for-woocommerce' ),
			)
		);
		return array(
			array(
				'title'    => __( 'Position Options', 'related-categories-for-woocommerce' ),
				'desc'     => $section_desc . sprintf( __( 'You can also use the %s shortcode to output the related categories.', 'related-categories-for-woocommerce' ),
					'<code>[alg_wc_related_categories_' . $this->type . ']</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_position_options',
			),
			array(
				'title'    => __( 'Position', 'related-categories-for-woocommerce' ),
				'desc_tip' => sprintf( __( '"Disable" position can be set if you are going to use %s shortcode or widget instead.', 'related-categories-for-woocommerce' ),
					'[alg_wc_related_categories_' . $this->type . ']' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[position]",
				'default'  => $default_position,
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => $options,
			),
			array(
				'title'    => __( 'Position order (i.e. priority)', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[position_priority]",
				'default'  => $default_priority,
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Widget', 'related-categories-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will add "%s" widget to "%s".', 'related-categories-for-woocommerce' ),
					__( 'Related Categories', 'related-categories-for-woocommerce' ) . ': ' . $this->title,
					'<a href="' . admin_url( 'widgets.php' ) . '">' . __( 'Appearance > Widgets', 'related-categories-for-woocommerce' ) . '</a>' ),
				'desc'     => __( 'Enable', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[widget]",
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
			),
			array(
				'desc'     => __( 'Override "Relate Options" in widget settings', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[widget_override_relate_options]",
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'   => 'end',
				'show_if_checked' => 'yes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_position_options',
			),
		);
	}

	/**
	 * get_template_settings.
	 *
	 * @version 1.9.0
	 * @since   1.3.0
	 *
	 * @todo    [next] template_custom: better default value
	 * @todo    [next] default/custom: better desc
	 * @todo    [maybe] template_custom: clean-up some placeholders, e.g. `%category_id%`, etc.?
	 */
	function get_template_settings() {
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
		return array(
			array(
				'title'    => __( 'Template Options', 'related-categories-for-woocommerce' ),
				'desc'     => sprintf( __( 'You can use HTML and/or shortcodes here, e.g. %s.', 'related-categories-for-woocommerce' ),
					'<code>[alg_wc_related_categories_translate]</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_template_options',
			),
			array(
				'title'    => __( 'Header', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[template_header]",
				'default'  => '<section class="related categories">' . PHP_EOL . '<h2>' . __( 'Related categories', 'related-categories-for-woocommerce' ) . '</h2>',
				'type'     => 'textarea',
				'css'      => 'width:100%;min-height:100px;',
			),
			array(
				'title'    => __( 'Footer', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[template_footer]",
				'default'  => '</section>',
				'type'     => 'textarea',
				'css'      => 'width:100%;min-height:100px;',
			),
			array(
				'title'    => __( 'Template type', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[template_type]",
				'default'  => 'default',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'default' => __( 'Default', 'related-categories-for-woocommerce' ),
					'custom'  => __( 'Custom', 'related-categories-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Template type: Custom', 'related-categories-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Ignored, unless "%s" option is set to "%s".', 'related-categories-for-woocommerce' ),
					__( 'Template type', 'related-categories-for-woocommerce' ), __( 'Custom', 'related-categories-for-woocommerce' ) ),
				'desc'     => sprintf( __( 'E.g.: %s', 'related-categories-for-woocommerce' ),
						'<code>' . esc_html( '<a href="%category_link%" title="%category_name%">%category_name%</a>' ) . '</code>' ) . '<br>' .
					sprintf( __( 'Available placeholders: %s.', 'related-categories-for-woocommerce' ), '<code>' . implode( '</code>, <code>', $placeholders ) . '</code>' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[template_custom]",
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;min-height:100px;',
			),
			array(
				'title'    => __( 'Template type: Custom: Glue', 'related-categories-for-woocommerce' ),
				'desc'     => sprintf( __( 'E.g.: %s', 'related-categories-for-woocommerce' ), '<code>&nbsp;|&nbsp;</code>' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[template_custom_glue]",
				'default'  => '',
				'type'     => 'text',
				'alg_wc_rc_sanitize' => 'textarea',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_template_options',
			),
		);
	}

	/**
	 * get_image_settings.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 *
	 * @todo    [next] Image size: add (and default to) `'' => __( 'Default', 'related-categories-for-woocommerce' )` option?
	 * @todo    [maybe] Remove image: better desc?
	 * @todo    [maybe] Placeholder image: better desc?
	 * @todo    [maybe] Image size: `text` instead of `select`?
	 */
	function get_image_settings() {
		return array(
			array(
				'title'    => __( 'Image Options', 'related-categories-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_image_options',
			),
			array(
				'title'    => __( 'Image size', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[image_size]",
				'default'  => 'woocommerce_thumbnail',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array_combine( get_intermediate_image_sizes(), get_intermediate_image_sizes() ),
			),
			array(
				'title'    => __( 'Placeholder image', 'related-categories-for-woocommerce' ),
				'desc_tip' => __( 'Enter attachment ID or URL to an image.', 'related-categories-for-woocommerce' ) . ' ' .
					__( 'Categories with no image will use this.', 'related-categories-for-woocommerce' ) . ' ' .
					__( 'Ignored if empty, i.e. default placeholder image will be used.', 'related-categories-for-woocommerce' ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[placeholder_image]",
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Remove image', 'related-categories-for-woocommerce' ),
				'desc'     => __( 'Remove', 'related-categories-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Ignored, unless "%s" option is set to "%s".', 'related-categories-for-woocommerce' ),
					__( 'Template Options', 'related-categories-for-woocommerce' ) . ' > ' . __( 'Template type', 'related-categories-for-woocommerce' ), __( 'Default', 'related-categories-for-woocommerce' ) ),
				'id'       => "alg_wc_related_categories_options_{$this->type}[remove_image]",
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_image_options',
			),
		);
	}

	/**
	 * get_advanced_settings.
	 *
	 * @version 1.8.0
	 * @since   1.4.0
	 */
	function get_advanced_settings() {
		$settings = array(
			array(
				'title'    => __( 'Advanced Options', 'related-categories-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_related_categories_advanced_options',
			),
		);
		if ( 'single' === $this->type ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Hide "Related products"', 'related-categories-for-woocommerce' ),
					'desc'     => __( 'Hide', 'related-categories-for-woocommerce' ),
					'desc_tip' => __( 'Hides standard WooCommerce "Related <strong>products</strong>" section on single product pages.', 'related-categories-for-woocommerce' ),
					'id'       => "alg_wc_related_categories_options_{$this->type}[hide_related_products]",
					'default'  => 'no',
					'type'     => 'checkbox',
				),
			) );
		} elseif ( 'loop' === $this->type ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Visibility', 'related-categories-for-woocommerce' ),
					'id'       => "alg_wc_related_categories_options_{$this->type}[visibility]",
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'options'  => array(
						'all'                =>  __( 'Show for all categories', 'related-categories-for-woocommerce' ),
						'with_children_only' =>  __( 'Show for categories with children only', 'related-categories-for-woocommerce' ),
						'no_children_only'   =>  __( 'Show for categories with no children only', 'related-categories-for-woocommerce' ),
					),
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_related_categories_advanced_options',
			),
		) );
		return $settings;
	}

}

endif;
