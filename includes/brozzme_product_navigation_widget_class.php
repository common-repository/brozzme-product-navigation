<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/06/16
 * Time: 17:57
 */

class brozzme_product_navigation_widget_class extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'bpn_widget',
            'description' => __('Add navigation to product page.', 'brozzme-product-navigation'),
        );
        parent::__construct( 'Brozzme Product navigation', __('Product navigation', 'brozzme-product-navigation'), $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        // outputs the content of the widget
        //echo $args['before_widget'];
        do_shortcode('[wc_bpn_navigation]');
        //echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'brozzme-product-navigation' );

        ?>
        <p><?php _e('You must set this widget within the <a href="admin.php?page=wc-settings&tab=settings_tab_brozzme_product_navigation">Woocommerce settings / Navigation</a> panel.', 'brozzme-product-navigation');?><br/>
          </p>
    <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}