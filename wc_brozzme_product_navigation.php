<?php
/**
 * Plugin Name: Brozzme Product Navigation
 * Plugin URI: https://brozzme.com/woocommerce-product-navigation/
 * Description: Add navigation buttons to surf between product page.
 * Version: 1.4.1
 * Author: Benoti
 * Author URI: https://brozzme.com/
 * text-domain: brozzme-product-navigation
 * License: GPLv3 or later License
 * URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 2.3
 * WC tested up to: 3.0
 */


if ( !defined( 'ABSPATH' ) ) exit ( 'restricted access' );

class brozzme_product_navigation {

    public function __construct() {

        // Define plugin constants
        $this->basename			 = plugin_basename( __FILE__ );
        $this->directory_path    = plugin_dir_path( __FILE__ );
        $this->directory_url	 = plugins_url( dirname( $this->basename ) );

        // Run our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array($this, 'deactivate') );

        $this->_init();
    }

    public function _init() {
        $this->admin_page();

        // Load translations
        load_plugin_textdomain( 'brozzme-product-navigation', false, dirname( $this->basename ) . '/languages' );

        add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_action_links' ) );

        add_action( 'init', array( $this, 'load_front_ressources' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_ressources' ) );

        add_action( 'wp_head', array('brozzme_navigation_class', 'print_additional_styles'));

        // WIDGETS
        // Accept shortcode in widgets for translating
        if ( !is_admin() ) {
            add_filter('widget_text', 'do_shortcode', 11);
        }

        add_action( 'widgets_init', array($this, 'register_bpns_widget') );
    }

    public function register_bpns_widget(){
        include_once $this->directory_path . 'includes/brozzme_product_navigation_widget_class.php';
        register_widget( 'brozzme_product_navigation_widget_class' );
    }
    public function activate() {
        
        if ( false === get_option('b_prod_nav_general_settings') ) {
            $arg = array(
                'enable_navigation'             => 'true',
                'navigation_position'           =>'woocommerce_before_single_product_summary',
                'navigation_float'              =>'right',
                'navigation_text'               => 'fa_font',
                'navigation_previous_text'      => __('Previous', 'brozzme-product-navigation'),
                'navigation_next_text'          => __('Next', 'brozzme-product-navigation'),
                'fa_navigation_symbol'          => 'cc',
                'fontawesome_size'              => '3',
                'navigation_container_css'      => '',
                'adjacent_in_same_tax'          =>'true',
                'adjacent_tax'                  =>'product_cat'

            );
            add_option( 'b_prod_nav_general_settings', $arg );
        }
        
    }

    public function deactivate(){

    }

    public function admin_page() {


        if(!class_exists('WC_Settings_Page')){

            include_once WP_PLUGIN_DIR.'/woocommerce/includes/admin/settings/class-wc-settings-page.php';
            include_once $this->directory_path . 'includes/wc_bpn_settings.php';

        }

    }

    public function register_ressources(){
        if ( !is_admin() ) {
            $options = get_option('b_prod_nav_general_settings');

            wp_enqueue_style('b-prod-nav', plugins_url('/css/style.css', __FILE__), array(), false, false);

            if(apply_filters('load_fontawesome', true) == true){
                if($options['navigation_text'] != 'true' ){
                    wp_enqueue_style('font-awesome', plugins_url('/css/font-awesome.min.css', __FILE__), array(),'4.6.3', false);
                }
            }
        }
    }

    public function load_front_ressources(){
        include_once $this->directory_path . 'includes/brozzme_navigation_class.php';
        new brozzme_navigation_class();

    }

    public function add_action_links ( $links ) {
        $mylinks = array(
            '<a href="' . admin_url('admin.php?page=wc-settings&tab=settings_tab_brozzme_product_navigation' ) . '">' . __( 'Settings', 'brozzme-product-navigation' ) . '</a>',
        );
        return array_merge( $links, $mylinks );
    }

}

new brozzme_product_navigation();

