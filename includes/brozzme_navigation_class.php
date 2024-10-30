<?php


if (!defined('ABSPATH')) {
exit;
}

if (!class_exists('brozzme_navigation_class')) {

    class  brozzme_navigation_class{


        public function __construct()
        {

            $this->settings_options = get_option('b_prod_nav_general_settings');

            if($this->settings_options['enable_navigation']=='true'){
                $wc_action = (empty($this->settings_options['navigation_position'])) ? 'woocommerce_before_single_product' : $this->settings_options['navigation_position'];
                add_action($wc_action, array($this, 'wc_bpn_nav'), 10);

            }

            add_shortcode( 'wc_bpn_navigation', array($this, 'navigation_shortcode') );
        }

        /**
         *
         */
        public function wc_bpn_nav(){

            do_shortcode('[wc_bpn_navigation ]');
        }


        /**
         * @param $atts
         * @return string
         */
        public function navigation_shortcode($atts){
            global $post;
            // verify that we are on a product page
            if(!is_singular() || is_front_page() || is_home() || is_archive() || is_tax() || is_404()
                    || $post->post_type != 'product'){
                return;
            }

            $atts = shortcode_atts(
                array(
                    'title' => '',
                    'echo'  => 'true',
                    'position' => (empty($this->settings_options['navigation_float']))? 'right' : $this->settings_options['navigation_float'],
                    'same_tax' => (empty($this->settings_options['adjacent_in_same_tax']))? 'true' : $this->settings_options['adjacent_in_same_tax'],
                    'tax_name' => (empty($this->settings_options['adjacent_tax']))? 'product_cat' : $this->settings_options['adjacent_tax'],
                    'nav_text' => (empty($this->settings_options['navigation_text']))? 'false' : $this->settings_options['navigation_text'],
                    'navigation_container_css' => (empty($this->settings_options['navigation_container_css']))? '' : $this->settings_options['navigation_container_css'],
                ), $atts, 'wc_bpn_navigation'
            );
            $adjacent_tax = $atts['tax_name'];

            $excluded_term_ids = $this->_get_exclude_term_ids($post->ID, $adjacent_tax);
            // Get the previous and next product links
            if($atts['same_tax'] == 'true'){
                $previous_adjacent_post = get_adjacent_post(true, $excluded_term_ids, true, $adjacent_tax);
                $next_adjacent_post = get_adjacent_post(true, $excluded_term_ids, false, $adjacent_tax);

            }
            else{
                $previous_adjacent_post = get_adjacent_post(false, $excluded_term_ids, true);
                $next_adjacent_post = get_adjacent_post(false, $excluded_term_ids, false);
            }

            if(is_a($previous_adjacent_post, 'WP_Post')){
                $previous_link = get_permalink($previous_adjacent_post->ID);
            }
            if(is_a($next_adjacent_post, 'WP_Post')){
                $next_link = get_permalink($next_adjacent_post->ID);
            }

            if($previous_adjacent_post == ''){
                $previous_link = $this->_get_adjacent_fallback($next_adjacent_post->ID, $adjacent_tax);
            }
            if($next_adjacent_post ==''){
                $next_link = $this->_get_adjacent_fallback($previous_adjacent_post->ID, $adjacent_tax);
            }

            $previous_css_class = '';
            $next_css_class = '';
                if($atts['nav_text']=='false'){

                    $previous_css_class = 'bpn-arrow-';
                    $next_css_class = 'bpn-arrow-';
                }
            
            $previous_text = apply_filters('wc_bpnav_previous_text', $this->settings_options['navigation_previous_text']);
            $next_text = apply_filters('wc_bpnav_next_text', $this->settings_options['navigation_next_text']);

            if($atts['nav_text'] == 'fa_font_only' or $atts['nav_text'] == 'icons_only'){
                $previous_text = '';
                $next_text = '';
            }
            else{
                if($previous_text == ''){
                    $previous_text = apply_filters('wc_bpnav_previous_text', __('Previous','brozzme-product-navigation'));
                }
                if($next_text == ''){
                    $next_text = apply_filters('wc_bpnav_next_text',__('Next','brozzme-product-navigation'));
                }
            }
            $fa_class = '';
               if($atts['nav_text']== 'fa_font' or $atts['nav_text']=='fa_font_only'){
                   $fa_class = '-'.$this->settings_options['fa_navigation_symbol'];
               }

            $additional_class = $atts['navigation_container_css'];

            $content = '<div class="bpn-nav-container '.$additional_class.'" style="float:'.$atts['position'].';">';

            if($atts['title']!= ''){
                $content .= '<h4 >'.$atts['title'].'</h4>';
            }

            $content .= '<div class="'.$previous_css_class.'previous"><a href="'.$previous_link.'" class="fa-bpn'.$fa_class.'">'.$previous_text.'</a></div>
                            <div class="bpn-separator"></div>
                            <div class="'.$next_css_class.'next"><a href="'.$next_link.'" class="fa-bpn'.$fa_class.'">'.$next_text.'</a> </div>
                        </div>
                        <div class="clear"></div>';
            
            if($atts['echo'] == 'true'){
                echo $content;
            }
            else{
                return $content;
            }

        }

        /**
         *
         */
        public function print_additional_styles(){
            $option = get_option('b_prod_nav_general_settings');


            if($option['fontawesome_size']!=''){

                $fa_font_size = $option['fontawesome_size'];

                $array_unit = array('px', 'em', 'rem', '%', 'vh');
                foreach($array_unit as $unit){
                    $fa_font_stack = strpos($option['fontawesome_size'], $unit);
                    if($fa_font_stack !== false ){
                        $fa_font_size = explode($unit, $option['fontawesome_size']);
                        $fa_font_size = $fa_font_size[0];
                    }
                }

                ?><style>
                    .bpn-nav-container h4{
                        line-height: <?php echo $fa_font_size;?>rem;
                    }
                    .fa-bpn-default:after, .fa-bpn-default:before, .fa-bpn-cc:after, .fa-bpn-cc:before,
                    .fa-bpn-arrow:before, .fa-bpn-arrow:after, .fa-bpn-carretsquare:before, .fa-bpn-carretsquare:after,
                    .fa-bpn-step:before, .fa-bpn-step:after{
                        font-size: <?php echo $fa_font_size;?>rem;
                    }
                </style><?php
            }

        }

        /**
         * @param $post__not_in
         * @param $adjacent_tax
         * @return mixed
         */
        public function _get_adjacent_fallback($post__not_in, $adjacent_tax){
            global $post;

            $exclude_term_ids = array(
                'taxonomy' => $adjacent_tax,
                'field'    => 'ids',
                'terms'    => $this->_get_exclude_term_ids($post->ID, $adjacent_tax),
                'operator' => 'NOT IN'
            );

            $args = array(
                'post_type'=>'product',
                'posts_per_page'=> -1,
                'post_status'=>'publish',
                'post__not_in'=> array($post->ID, $post__not_in),
                'tax_query' =>array($exclude_term_ids),
                'orderby' => 'rand'
            );
            $post_fallback = new WP_Query($args);

            return get_permalink($post_fallback->posts[0]->ID);
        }

        /**
         * @since 1.4.0
         * @param $post_id
         * @param $taxonomy
         * @return mixed
         */
        public function _get_exclude_term_ids($post_id, $taxonomy){
            $post_terms = wp_get_object_terms($post_id, $taxonomy);

            foreach ($post_terms as $post_term){
                $included[] = $post_term->term_id;
            }

            $excluded = $this->_get_all_terms_ids($included, $taxonomy);

            return $excluded;

        }

        /**
         * @since 1.4.0
         * @param $excluded
         * @param $taxonomy
         * @return mixed
         */
        public function _get_all_terms_ids($excluded, $taxonomy){
            $term_ids = get_terms( $taxonomy, array(
                'hide_empty' => false,
                'exclude' => $excluded,
                'fields' => 'ids'
            ) );

            return $term_ids;
        }
    }
}