<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WCFA_Widget
 *
 */
abstract class WCFA_Widget extends WP_Widget {

    /**
     * CSS class.
     *
     * @var string
     */
    public $widget_cssclass;
    public $widget_width;
    public $widget_height;

    /**
     * Widget description.
     *
     * @var string
     */
    public $widget_description;

    /**
     * Widget ID.
     *
     * @var string
     */
    public $widget_id;

    /**
     * Widget name.
     *
     * @var string
     */
    public $widget_name;

    /**
     * Settings.
     *
     * @var array
     */
    public $settings;

    /**
     * Constructor.
     */
    public function __construct() {
        $widget_ops = array(
            'classname'   => $this->widget_cssclass,
            'description' => $this->widget_description,
            'customize_selective_refresh' => true,
        );

        $control_ops = array(
            'width' => $this->widget_width,
            'height' => $this->widget_height,
        );

        parent::__construct( $this->widget_id, $this->widget_name, $widget_ops, $control_ops );

        add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
    }

    /**
     * Get cached widget.
     *
     * @param  array $args Arguments.
     * @return bool true if the widget is cached otherwise false
     */
    public function get_cached_widget( $args ) {
        $cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        if ( isset( $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] ) ) {
            echo $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ]; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
            return true;
        }

        return false;
    }

    /**
     * Cache the widget.
     *
     * @param  array  $args Arguments.
     * @param  string $content Content.
     * @return string the content that was cached
     */
    public function cache_widget( $args, $content ) {
        $cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] = $content;

        wp_cache_set( $this->get_widget_id_for_cache( $this->widget_id ), $cache, 'widget' );

        return $content;
    }

    /**
     * Flush the cache.
     */
    public function flush_widget_cache() {
        foreach ( array( 'https', 'http' ) as $scheme ) {
            wp_cache_delete( $this->get_widget_id_for_cache( $this->widget_id, $scheme ), 'widget' );
        }
    }

    /**
     * Output the html at the start of a widget.
     *
     * @param array $args Arguments.
     * @param array $instance Instance.
     */
    public function widget_start( $args, $instance ) {
        echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

        if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        }
    }

    /**
     * Output the html at the end of a widget.
     *
     * @param  array $args Arguments.
     */
    public function widget_end( $args ) {
        echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Updates a particular instance of a widget.
     *
     * @see    WP_Widget->update
     * @param  array $new_instance New instance.
     * @param  array $old_instance Old instance.
     * @return array
     */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        if ( empty( $this->settings ) ) {
            return $instance;
        }

        // Loop settings and get values to save.
        foreach ( $this->settings as $key => $setting ) {
            if ( ! isset( $setting['type'] ) ) {
                continue;
            }

            // Format the value based on settings type.
            switch ( $setting['type'] ) {
                case 'number':
                    $instance[ $key ] = absint( $new_instance[ $key ] );

                    if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
                        $instance[ $key ] = max( $instance[ $key ], $setting['min'] );
                    }

                    if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
                        $instance[ $key ] = min( $instance[ $key ], $setting['max'] );
                    }
                    break;
                case 'textarea':
                    $instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
                    break;
                case 'checkbox':
                    $instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
                    break;
                case 'img_text':
                    $instance[ $key ] = !empty( $new_instance[ $key ] ) ? $new_instance[ $key ] : array();
                    break;
                default:
                    $instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
                    break;
            }

            /**
             * Sanitize the value of a setting.
             */
            $instance[ $key ] = apply_filters( 'woocommerce_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
        }

        $this->flush_widget_cache();

        return $instance;
    }

    /**
     * Outputs the settings update form.
     *
     * @see   WP_Widget->form
     *
     * @param array $instance Instance.
     */
    public function form( $instance ) {

        if ( empty( $this->settings ) ) {
            return;
        }

        foreach ( $this->settings as $key => $setting ) {

            $class = isset( $setting['class'] ) ? $setting['class'] : '';
            $value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

            switch ( $setting['type'] ) {

                case 'text':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; ?></label><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
                        <input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                    break;

                case 'hidden':
                    ?>
                    <input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>" />
                    <?php
                    break;

                case 'number':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                    break;

                case 'select':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>">
                            <?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
                                <option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <?php
                    break;

                case 'textarea':
                    ?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <textarea class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
                        <?php if ( isset( $setting['desc'] ) ) : ?>
                            <small><?php echo esc_html( $setting['desc'] ); ?></small>
                        <?php endif; ?>
                    </p>
                    <?php
                    break;

                case 'checkbox':
                    ?>
                    <p>
                        <input class="checkbox <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                    </p>
                    <?php
                    break;

                case 'img_text':
                    wp_enqueue_style( 'wp-color-picker' );
                    wp_enqueue_media();
                    $attribute = isset( $instance[ 'attribute' ] ) ? $instance[ 'attribute' ] : $this->settings['attribute']['std'];
                    $args      = array(
                        'taxonomy' => 'pa_' . $attribute,
                        'hide_empty' => 0,
                    );
                    $all_terms = get_terms( $args );
                    if ( $all_terms ) {
                        ?>
                        <div class="devvn_woo_filter">
                            <table>
                                <thead>
                                <tr>
                                    <th class="wcfa_attr_title"><?php _e('Attribute Value', 'devvn-woofilter'); ?></th>
                                    <th><?php _e('Image', 'devvn-woofilter'); ?></th>
                                    <th><?php _e('Color', 'devvn-woofilter'); ?></th>
                                    <th><?php _e('Text 1', 'devvn-woofilter'); ?></th>
                                    <th><?php _e('Text 2', 'devvn-woofilter'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ( $all_terms as $term ) :
                                    $imgid = isset($value[$term->term_id]['img']) ? intval($value[$term->term_id]['img']) : '';
                                    $color = isset($value[$term->term_id]['color']) ? esc_attr($value[$term->term_id]['color']) : '';
                                    $text1 = isset($value[$term->term_id]['text1']) ? esc_attr($value[$term->term_id]['text1']) : '';
                                    $text2 = isset($value[$term->term_id]['text2']) ? esc_attr($value[$term->term_id]['text2']) : '';
                                    ?>
                                    <tr>
                                        <td class="wcfa_attr_title"><?php echo $term->name;?></td>
                                        <td>
                                            <div class="wcfa-upload-image <?php if($imgid):?>wcfa-has-image<?php endif;?>">
                                                <div class="wcfa-view-has-value">
                                                    <input type="hidden" class="clone_delete" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>[<?php echo $term->term_id; ?>][img]" value="<?php echo $imgid;?>"/>
                                                    <img src="<?php echo wp_get_attachment_image_url($imgid,'full')?>" class="wcfa-image_view"/>
                                                    <a href="#" class="wcfa-delete-image">x</a>
                                                </div>
                                                <div class="wcfa-hidden-has-value"><input type="button" class="wcfa-upload button" value="<?php _e( 'Select images', 'devvn-woofilter' )?>" /></div>
                                            </div>
                                        </td>
                                        <td><input type="text" name="<?php echo esc_attr($this->get_field_name($key)); ?>[<?php echo $term->term_id; ?>][color]" value="<?php echo $color;?>" class="wcfa-color" ></td>
                                        <td><input class="widefat" type="text" name="<?php echo esc_attr($this->get_field_name($key)); ?>[<?php echo $term->term_id; ?>][text1]" value="<?php echo $text1; ?>"></td>
                                        <td><input class="widefat" type="text" name="<?php echo esc_attr($this->get_field_name($key)); ?>[<?php echo $term->term_id; ?>][text2]" value="<?php echo $text2; ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" value="<?php echo esc_attr(maybe_serialize($value));?>" class="value_default">
                        <input type="hidden" value="<?php echo esc_attr($this->get_field_name($key)); ?>" class="key_default">
                        <?php
                    }
                    break;

                // Default: run an action.
                default:
                    do_action( 'woocommerce_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
                    break;
            }
        }
    }

    /**
     * Get current page URL with various filtering props supported by WC.
     *
     * @return string
     * @since  3.3.0
     */
    protected function get_current_page_url() {
        if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
            $link = home_url();
        } elseif ( is_shop() ) {
            $link = get_permalink( wc_get_page_id( 'shop' ) );
        } elseif ( is_product_category() ) {
            $link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
        } elseif ( is_product_tag() ) {
            $link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
        } else {
            $queried_object = get_queried_object();
            $link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
        }

        // Min/Max.
        if ( isset( $_GET['min_price'] ) ) {
            $link = add_query_arg( 'min_price', wc_clean( wp_unslash( $_GET['min_price'] ) ), $link );
        }

        if ( isset( $_GET['max_price'] ) ) {
            $link = add_query_arg( 'max_price', wc_clean( wp_unslash( $_GET['max_price'] ) ), $link );
        }

        // Order by.
        if ( isset( $_GET['orderby'] ) ) {
            $link = add_query_arg( 'orderby', wc_clean( wp_unslash( $_GET['orderby'] ) ), $link );
        }

        /**
         * Search Arg.
         * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
         */
        if ( get_search_query() ) {
            $link = add_query_arg( 's', rawurlencode( htmlspecialchars_decode( get_search_query() ) ), $link );
        }

        // Post Type Arg.
        if ( isset( $_GET['post_type'] ) ) {
            $link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link );
        }

        // Min Rating Arg.
        if ( isset( $_GET['rating_filter'] ) ) {
            $link = add_query_arg( 'rating_filter', wc_clean( wp_unslash( $_GET['rating_filter'] ) ), $link );
        }

        // All current filters.
        if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
                if ( ! empty( $data['terms'] ) ) {
                    $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                }
                if ( 'or' === $data['query_type'] ) {
                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                }
            }
        }

        return $link;
    }

    /**
     * Get widget id plus scheme/protocol to prevent serving mixed content from (persistently) cached widgets.
     *
     * @param  string $widget_id Id of the cached widget.
     * @param  string $scheme    Scheme for the widget id.
     * @return string            Widget id including scheme/protocol.
     */
    protected function get_widget_id_for_cache( $widget_id, $scheme = '' ) {
        if ( $scheme ) {
            $widget_id_for_cache = $widget_id . '-' . $scheme;
        } else {
            $widget_id_for_cache = $widget_id . '-' . ( is_ssl() ? 'https' : 'http' );
        }

        return apply_filters( 'woocommerce_cached_widget_id', $widget_id_for_cache );
    }
}
