<?php
/**
 * Related Categories for WooCommerce - Main Class
 *
 * @version 1.9.9
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Related_Categories' ) ) :

final class Alg_WC_Related_Categories {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_RELATED_CATEGORIES_VERSION;

	/**
	 * @var   Alg_WC_Related_Categories The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Core.
	 *
	 * @since 1.9.8
	 */
	public $core;

	/**
	 * $plugin_path.
	 *
	 * @since 1.9.8
	 *
	 * @var
	 */
	public $plugin_path = null;

	/**
	 * Main Alg_WC_Related_Categories Instance
	 *
	 * Ensures only one instance of Alg_WC_Related_Categories is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_Related_Categories - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initializer.
	 *
	 * @version 1.9.9
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function init() {

		// Adds cross-selling library.
		$this->add_cross_selling_library();

		// Move WC Settings tab to WPFactory menu.
		add_action( 'init', array( $this, 'move_wc_settings_tab_to_wpfactory_menu' ) );

		// Check for active WooCommerce plugin.
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation.
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce.
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'related-categories-for-woocommerce-pro.php' === basename( ALG_WC_RELATED_CATEGORIES_FILE ) ) {
			require_once( 'pro/class-alg-wc-related-categories-pro.php' );
		}

		// Include required files.
		$this->includes();

		// Admin.
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 1.9.8
	 * @since   1.9.8
	 *
	 * @return void
	 */
	function add_cross_selling_library(){
		if ( ! is_admin() ) {
			return;
		}
		require_once $this->plugin_path() . '/vendor/autoload.php';
		// Cross-selling library.
		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path'   => $this->plugin_path() ) );
		$cross_selling->init();
	}

	/**
	 * move_wc_settings_tab_to_wpfactory_submenu.
	 *
	 * @version 1.9.9
	 * @since   1.9.8
	 *
	 * @return void
	 */
	function move_wc_settings_tab_to_wpfactory_menu() {
		if ( ! is_admin() ) {
			return;
		}
		require_once $this->plugin_path() . '/vendor/autoload.php';
		// WC Settings tab as WPFactory submenu item.
		$wpf_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();
		$wpf_admin_menu->move_wc_settings_tab_to_wpfactory_menu( array(
			'wc_settings_tab_id' => 'alg_wc_related_categories',
			'menu_title'         => __( 'Related Categories', 'related-categories-for-woocommerce' ),
			'page_title'         => __( 'Related Categories for WooCommerce', 'related-categories-for-woocommerce' ),
			'plugin_icon' => array(
				'get_url_method'    => 'wporg_plugins_api',
				'wporg_plugin_slug' => 'related-categories-for-woocommerce',
				'style'             => 'margin-left:-4px',
			)
		) );
	}

	/**
	 * localize.
	 *
	 * @version 1.9.0
	 * @since   1.7.0
	 */
	function localize() {
		load_plugin_textdomain( 'related-categories-for-woocommerce', false, dirname( plugin_basename( ALG_WC_RELATED_CATEGORIES_FILE ) ) . '/langs/' );
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 1.9.5
	 * @since   1.9.5
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', ALG_WC_RELATED_CATEGORIES_FILE, true );
		}
	}

	/**
	 * includes.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once( 'class-alg-wc-related-categories-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_RELATED_CATEGORIES_FILE ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_related_categories_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * action_links.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_related_categories' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'related-categories-for-woocommerce.php' === basename( ALG_WC_RELATED_CATEGORIES_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/related-categories-for-woocommerce/">' .
				__( 'Go Pro', 'related-categories-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * add_woocommerce_settings_tab.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'settings/class-alg-wc-related-categories-settings.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function version_updated() {
		update_option( 'alg_wc_related_categories_version', $this->version );
	}

	/**
	 * plugin_url.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_RELATED_CATEGORIES_FILE ) );
	}

	/**
	 * plugin_path.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		if(is_null($this->plugin_path)){
			$this->plugin_path = untrailingslashit( plugin_dir_path( ALG_WC_RELATED_CATEGORIES_FILE ) );
		}
		return $this->plugin_path;
	}

}

endif;
