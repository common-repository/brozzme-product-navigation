<?php

/**
 * Created by PhpStorm.
 * User: benoti
 * Date: 02/09/2016
 * Time: 14:54
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Brozzme_Product_Navigation' ) ) :

    /**
     * Settings class
     *
     * @since 1.0.0
     */
    class WC_Settings_Brozzme_Product_Navigation extends WC_Settings_Page {

        // the magic will go here
        /**
         * Setup settings class
         *
         * @since  1.0
         */
        public function __construct() {

            $this->plugin_txt_domain = 'brozzme-product-navigation';

            $this->id    = 'bpn_tab';
            $this->label = __( 'Navigation', $this->plugin_txt_domain );
            


            add_filter( 'woocommerce_settings_tabs_array',        array( $this, 'add_settings_page' ), 50 );
            add_action( 'woocommerce_settings_' . $this->id,      array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

            // only add this if you need to add sections for your settings tab
            add_action( 'woocommerce_sections_' . $this->id,      array( $this, 'output_sections' ) );
        }

        public function add_settings_page($settings_tabs){

            $settings_tabs[$this->id] = __( 'Navigation', $this->plugin_txt_domain );
            return $settings_tabs;
        }
        public function get_sections() {

            $sections = array(
                ''         => __( 'Settings', $this->plugin_txt_domain ),
                'help' => __( 'Shortcode Help', $this->plugin_txt_domain )
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
        }

        /**
         * Get settings array
         *
         * @since 1.0.0
         * @param string $current_section Optional. Defaults to empty string.
         * @return array Array of settings
         */
        public function get_settings( $current_section = '' ) {

            if ( 'help' == $current_section ) {

                /**
                 * Filter Plugin Section 2 Settings
                 *
                 * @since 1.0.0
                 * @param array $settings Array of the plugin settings
                 */
                $settings = apply_filters( 'settings_tab_brozzme_product_navigation_help', array(

                    array(
                        'name' => __( 'Shortcode', $this->plugin_txt_domain ),
                        'type' => 'title',
                        'desc' => '',
                        'id'   => 'bpn_group1_options',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'bpn_group1_options'
                    ),

                ) );

            } else {

                /**
                 * Filter Plugin Section 1 Settings
                 *
                 * @since 1.0.0
                 * @param array $settings Array of the plugin settings
                 */
                $settings = apply_filters( 'settings_tab_brozzme_product_navigation', array(

                    'section_title' => array(
                        'name'     => __( 'Product navigation', $this->plugin_txt_domain ),
                        'type'     => 'title',
                        'desc'     => __('Product navigation configuration.', $this->plugin_txt_domain),
                        'id'       => 'wc_settings_tab_brozzme_product_navigation_section_title'
                    ),
                    'enable_navigation' => array(
                        'name' => __( 'Enable navigation', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options'=> array('true' 	=> __('Yes',$this->plugin_txt_domain),
                            'false'  	=> __('No',$this->plugin_txt_domain)
                        ),
                        'default'=> 'true',
                        'class'    => 'wc-enhanced-select',
                        'desc' => 'Disable to allow only shortcode (and widget if appropriate)',
                        'id'   => 'b_prod_nav_general_settings[enable_navigation]'
                    ),
                    'adjacent_in_same_tax' => array(
                        'name' => __( 'Same taxonomy terms', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options'=> array('true' 	=> __('Yes',$this->plugin_txt_domain),
                            'false'  	=> __('No',$this->plugin_txt_domain),
                        ),
                        'default'=> 'product_cat',
                        'class'    => 'wc-enhanced-select',
                        'desc' => 'Wether to display post link of the same taxonomy term',
                        'id'   => 'b_prod_nav_general_settings[adjacent_in_same_tax]'
                    ),
                    'adjacent_tax' => array(
                        'name' => __( 'Taxonomy to follow', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options' => $this->_retrieve_product_taxonomies(),
                        'default'=> 'product_cat',
                        'class'    => 'wc-enhanced-select',
                        'desc' => '',
                        'id'   => 'b_prod_nav_general_settings[adjacent_tax]'
                    ),
                    'navigation_position' => array(
                        'name' => __( 'Navigation position', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options'=> array('woocommerce_before_single_product' 	=> 'before_product',
                            'woocommerce_before_single_product_summary'  	=> 'before_summary',
                            'woocommerce_single_product_summary'	=> 'product_summary' ,
                            'woocommerce_after_single_product' 	=> 'after_product',
                            'woocommerce_after_single_product_summary'	=> 'after_summary'),
                        'default'=> 'true',
                        'class'    => 'wc-enhanced-select',
                        'desc' => '',
                        'id'   => 'b_prod_nav_general_settings[navigation_position]'
                    ),
                    'navigation_float' => array(
                        'name' => __( 'Navigation float', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options'=> array('left' 	=> __('Left',$this->plugin_txt_domain),
                            'right'  	=> __('Right',$this->plugin_txt_domain)),
                        'default'=> 'true',
                        'class'    => 'wc-enhanced-select',
                        'desc' => '',
                        'id'   => 'b_prod_nav_general_settings[navigation_float]'
                    ),
                    'navigation_text' => array(
                        'name' => __( 'Navigation text', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options'=> array('true'=>__('Text only', $this->plugin_txt_domain ),
                            'false'=>__('Icons', $this->plugin_txt_domain ),
                            'fa_font'=> __('Fontawesome symbol', $this->plugin_txt_domain),
                            'fa_font_only'=>__('No text with Fontawesome symbol',$this->plugin_txt_domain),
                            'icons_only'=>__('No text with icons',$this->plugin_txt_domain)),
                        'default'=> 'true',
                        'class'    => 'wc-enhanced-select',
                        'desc' => __( 'To use only text or with next and previous arrow', $this->plugin_txt_domain ),
                        'id'   => 'b_prod_nav_general_settings[navigation_text]'
                    ),
                    'navigation_previous_text' => array(

                        'name' => __( 'Change previous text', $this->plugin_txt_domain ),
                        'type' => 'text',
                        'desc' => __( 'Change the previous link text if applicable.', $this->plugin_txt_domain ),
                        'id'   => 'b_prod_nav_general_settings[navigation_previous_text]'

                    ),
                    'navigation_next_text' => array(

                        'name' => __( 'Change next text', $this->plugin_txt_domain ),
                        'type' => 'text',
                        'desc' => __( 'Change the next link text if applicable.', $this->plugin_txt_domain ),
                        'id'   => 'b_prod_nav_general_settings[navigation_next_text]'

                    ),
                    'fa_navigation_symbol' => array(
                        'name' => __( 'Fontawesome Navigation symbol', $this->plugin_txt_domain ),
                        'type' => 'select',
                        'options'=> array(
                            'default'=>__('Angle left and right', $this->plugin_txt_domain ),
                            'cc'=>__('Chevron circle left and right', $this->plugin_txt_domain ),
                            'arrow'=> __('Arrow', $this->plugin_txt_domain),
                            'carretsquare'=>__('Carret square',$this->plugin_txt_domain),
                            'step'=>__('Step backward and forward',$this->plugin_txt_domain),
                        ),
                        'default'=> 'default',
                        'class'    => 'wc-enhanced-select',
                        'desc' => __( 'To use only text or with next and previous arrow.', $this->plugin_txt_domain ),
                        'id'   => 'b_prod_nav_general_settings[fa_navigation_symbol]'
                    ),
                    'fontawesome_size' => array(

                        'name' => __( 'Fontawesome size', $this->plugin_txt_domain ),
                        'type' => 'text',
                        'desc' => __( 'Default unit is rem.', $this->plugin_txt_domain ),
                        'id'   => 'b_prod_nav_general_settings[fontawesome_size]'

                    ),
                    'navigation_container_css' => array(

                        'name' => __( 'Additional Navigation container Css', $this->plugin_txt_domain ),
                        'type' => 'text',
                        'desc' => __( 'Use it for styling and container position. Only one class, without dot.', $this->plugin_txt_domain ),
                        'id'   => 'b_prod_nav_general_settings[navigation_container_css]'

                    ),
                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'wc_settings_tab_brozzme_product_navigation_section_end'
                    )

                ) );

            }

            /**
             * Filter bpn_tab Settings
             *
             * @since 1.0.0
             * @param array $settings Array of the plugin settings
             */
            return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

        }
        /**
         * Output the settings
         *
         * @since 1.0
         */
        public function output() {

            global $current_section;

            $settings = $this->get_settings( $current_section );

            if($current_section == 'help'){
                $GLOBALS['hide_save_button'] = true;
                ?>
                <h3>Shortcode</h3>

                <p><?php _e('You may want to change button navigation behaviours. It is possible, when you add a shortcode into your content.', $this->plugin_txt_domain);?><br/>
                    <?php _e('Don\'t forget to disable automatic button if you don\'t to display many navigation buttons.', $this->plugin_txt_domain);?><br/>
                    <?php _e('The plugin options settings of the Settings section will stand for your default values if the shortcode miss one.', $this->plugin_txt_domain);?><br/>
                <?php _e('The shortcode as it appears automatically:', $this->plugin_txt_domain);?> <code>[wc_bpn_navigation]</code>
                <div style="clear: both"></div>
                <b><?php _e('You can add parameters:', $this->plugin_txt_domain);?></b>
                     <ul style="padding-left: 15px;">
                    <li>echo :
                        <li><?php _e('depending on theme and shortcode area paste, if navigation displays before content set echo to false,', $this->plugin_txt_domain);?></li>
                        <li><code>[wc_bpn_navigation echo="false"]</code></li>
                        <li><?php _e('Possible values : true / false', $this->plugin_txt_domain);?></li>
                    </li>
                    <li>position
                        <li><code>[wc_bpn_navigation position="left"]</code></li>
                        <li><?php _e('Possible values:', $this->plugin_txt_domain);?> left / right</li>
                        <li><?php _e('This value will override the settings value, only for this shortcode.', $this->plugin_txt_domain);?></li>
                    </li>
                    <li>same_tax
                        <li><code>[wc_bpn_navigation same_tax="true"]</code></li>
                        <li><?php _e('Possible values:', $this->plugin_txt_domain);?> true / false</li></li>
                    <li>adjacent_tax
                        <li><code>[wc_bpn_navigation adjacent_tax="product_cat"]</code></li>
                        <li><?php _e('Possible values:', $this->plugin_txt_domain);?> product_cat for category, product_tag for tags, any registered product category.</li>
                    </li>
                    <li>nav_text
                        <li><code>[wc_bpn_navigation nav_text="fa_font_only"]</code></li>
                        <li><?php _e('Possible values:', $this->plugin_txt_domain);?> true / false / fa_font / fa_font_only / icons_only</li>
                    </li>
                    <li>navigation_container_css
                        <li><code>[wc_bpn_navigation navigation_container_css="wc-bpn"]</code></li>
                        <li><?php _e('Possible values:', $this->plugin_txt_domain);?> <?php _e('any CSS class without the prefix dot', $this->plugin_txt_domain);?></li>
                    </li>
                    <li>title
                        <li><code>[wc_bpn_navigation title="My block Title"]</code></li>
                        <li><?php _e('if you want to display a title for your block, you can customize it with css pointing to previous "navigation container css" and h4 definitions.', $this->plugin_txt_domain);?></li>
                    </li>
                    </ul>

                <code>[wc_bpn_navigation echo="false" position="right" same_tax="true" adjacent_tax="product_cat" nav_text="fa_font_only" navigation_container_css="wc-bpnav" title="My product in this cool category"]
                </code>
                </p>
                <?php

            }
            else{
                WC_Admin_Settings::output_fields( $settings );
            }

        }
        /**
         * Save settings
         *
         * @since 1.0
         */
        public function save() {

            global $current_section;

            $settings = $this->get_settings( $current_section );
            WC_Admin_Settings::save_fields( $settings );
        }

        public function _retrieve_product_taxonomies(){

            $post_type = apply_filters('wc_bpn_tax', array('product') );

            $taxonomies = get_object_taxonomies( $post_type, 'objects');

            $registered_terms = array();

            foreach( $taxonomies as $taxonomy ) {
                $registered_terms[$taxonomy->name] = $taxonomy->labels->singular_name;
            }

            return $registered_terms;
        }
    }

endif;

return new WC_Settings_Brozzme_Product_Navigation();
