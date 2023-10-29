<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.enweby.com/
 * @since      1.0.0
 *
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/public
 * @author     Enweby <support@enweby.com>
 */
class Enwbvs_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Admin settings
     *
     * @var array
     */
    private  $enweby_plugin_settings ;
    /**
     * Common Functions
     *
     * @var array
     */
    private  $enwbvs_common_functions ;
    /**
     * Premium Functions
     *
     * @var array
     */
    private  $enwbvs_premium_functions ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->enwbvs_get_common();
        $this->enweby_get_plugin_settings();
        $this->public_hooks();
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * An instance of this class should be passed to the run() function
         * defined in Enwbvs_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Enwbvs_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $enwbvs_default_stylesheet = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'general_section_general', 'enwbvs-default-stylesheet' );
        $enwbvs_default_stylesheet_settings = ( isset( $enwbvs_default_stylesheet ) && '' != $enwbvs_default_stylesheet ? $enwbvs_default_stylesheet : '1' );
        // phpcs:ignore
        
        if ( 1 === (int) $enwbvs_default_stylesheet_settings ) {
            $suffix = $this->get_debug_suffix();
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'css/enwbvs-public' . $suffix . '.css',
                array(),
                $this->version,
                'all'
            );
        }
        
        wp_add_inline_style( $this->plugin_name, $this->enwbvs_get_custom_style() );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * An instance of this class should be passed to the run() function
         * defined in Enwbvs_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Enwbvs_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $suffix = $this->get_debug_suffix();
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/enwbvs-public' . $suffix . '.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, str_replace( '-', '_', $this->plugin_name ), array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
        wp_add_inline_script( $this->plugin_name, $this->enwbvs_get_custom_script() );
        $enwbvs_config_var = array(
            'enwbvs_disable_outofstock'   => apply_filters( 'enwbvs_disable_outofstock_config', $this->enwbvs_disable_outofstock_settings() ),
            'enwbvs_clickable_outofstock' => apply_filters( 'enwbvs_clickable_outofstock_config', $this->enwbvs_clickable_outofstock_settings() ),
        );
        wp_localize_script( $this->plugin_name, 'enwbvs_config_var', $enwbvs_config_var );
    }
    
    /**
     * Initialize public hooks.
     */
    public function public_hooks()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'enwbvs_gallery_scripts' ), 20 );
        add_filter(
            'woocommerce_dropdown_variation_attribute_options_html',
            array( $this, 'swatches_display' ),
            150,
            2
        );
        add_filter(
            'woocommerce_ajax_variation_threshold',
            array( $this, 'change_ajax_variation_threshold' ),
            10,
            2
        );
        add_filter( 'body_class', array( $this, 'enwbvs_wc_custom_css_body_class' ) );
        add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'add_class_to_attr_dropdown' ), 20 );
        add_action( 'init', array( $this, 'enwbvs_loop_variation_with_add_to_cart' ), 10 );
        add_action( 'wp_ajax_enwbvs_create_variation_link', array( $this, 'enwbvs_create_variation_link' ) );
        add_action( 'wp_ajax_nopriv_enwbvs_create_variation_link', array( $this, 'enwbvs_create_variation_link' ) );
    }
    
    /**
     * Getting admin settings.
     *
     * @since    1.0.0
     */
    public function enweby_get_plugin_settings()
    {
        require_once plugin_dir_path( __FILE__ ) . 'class-plugin-settings-functions.php';
        $this->enweby_plugin_settings = new \Enwbvs\Enweby\PluginSettingsFunctions\Plugin_Settings_Functions();
    }
    
    /**
     * Getting common settings.
     *
     * @since    1.0.0
     */
    public function enwbvs_get_common()
    {
        require_once WP_PLUGIN_DIR . '/enweby-variation-swatches-for-woocommerce/includes/class-enwbvs-common.php';
        $this->enwbvs_common_functions = new Enwbvs_Common();
    }
    
    /**
     * Getting enwbvs_disable_outofstock_config value.
     *
     */
    public function enwbvs_disable_outofstock_settings()
    {
        $enwbvs_disable_outofstock = 0;
        return $enwbvs_disable_outofstock;
    }
    
    /**
     * Getting enwbvs_disable_outofstock_config value.
     *
     */
    public function enwbvs_clickable_outofstock_settings()
    {
        $enwbvs_clickable_outofstock = 1;
        return $enwbvs_clickable_outofstock;
    }
    
    /**
     *  Get debug suffix.
     *
     * @return string.
     */
    public function get_debug_suffix()
    {
        $enwbvs_enable_debug = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'debug_mode_enable_debug', 'enwbvs-enable-debug' );
        $enwbvs_enable_debug_settings = ( isset( $enwbvs_enable_debug ) && '' != $enwbvs_enable_debug ? $enwbvs_enable_debug : '0' );
        // phpcs:ignore
        $suffix = ( isset( $enwbvs_enable_debug_settings ) && '1' === $enwbvs_enable_debug_settings ? '' : '.min' );
        return $suffix;
    }
    
    /**
     * Inculding Gallery script.
     */
    public function enwbvs_gallery_scripts()
    {
        $enwbvs_show_swatches_on_archive_shop = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-show-swatches-on-archive-shop' );
        $enwbvs_show_swatches_on_archive_shop_settings = ( isset( $enwbvs_show_swatches_on_archive_shop ) && '' != $enwbvs_show_swatches_on_archive_shop ? $enwbvs_show_swatches_on_archive_shop : '1' );
        // phpcs:ignore
        if ( 1 === (int) $enwbvs_show_swatches_on_archive_shop_settings ) {
            if ( is_archive() || is_shop() || is_product_category() || is_product_tag() ) {
                wp_enqueue_script( 'wc-single-product' );
            }
        }
    }
    
    /**
     * Replace add to cart button in the loop.
     */
    public function enwbvs_loop_variation_with_add_to_cart()
    {
        $enwbvs_show_swatches_on_archive_shop = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-show-swatches-on-archive-shop' );
        $enwbvs_show_swatches_on_archive_shop_settings = ( isset( $enwbvs_show_swatches_on_archive_shop ) && '' != $enwbvs_show_swatches_on_archive_shop ? $enwbvs_show_swatches_on_archive_shop : '1' );
        // phpcs:ignore
        
        if ( 1 === (int) $enwbvs_show_swatches_on_archive_shop_settings ) {
            add_action( 'woocommerce_after_shop_loop_item', array( $this, 'enwbvs_template_loop_add_to_cart' ), 200 );
            add_filter(
                'woocommerce_loop_add_to_cart_link',
                array( $this, 'enwbvs_loop_add_to_cart_link' ),
                10,
                3
            );
        }
    
    }
    
    /**
     * Changing ajax variation threshold.
     *
     * @param  int $threshold_value    ajax Threshold value.
     * @param  int $product            product id.
     * @return int                     threshold value.
     */
    public function change_ajax_variation_threshold( $threshold_value, $product )
    {
        $enwbvs_ajax_variation_threshold_limit = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'advanced_section_advanced', 'enwbvs-ajax-variation-threshold-limit' );
        $enwbvs_ajax_variation_threshold_limit_settings = ( isset( $enwbvs_ajax_variation_threshold_limit ) && '' != $enwbvs_ajax_variation_threshold_limit ? $enwbvs_ajax_variation_threshold_limit : '30' );
        // phpcs:ignore
        $threshold = $enwbvs_ajax_variation_threshold_limit_settings;
        if ( $threshold && is_numeric( $threshold ) ) {
            $threshold_value = $threshold;
        }
        return $threshold_value;
    }
    
    /**
     *  Adding class to variation dropdown.
     *
     * @param array $args argument.
     * @return string.
     */
    public function add_class_to_attr_dropdown( $args )
    {
        global  $product ;
        $args['class'] = 'enwbvs-prod-variation-ddn variation-ddn-' . $product->get_id();
        return $args;
    }
    
    /**
     *  Add class to gallery image.
     */
    public function enwbvs_template_loop_product_images()
    {
        global  $product ;
        
        if ( $product instanceof WC_Product_Variable ) {
            $available_variations = $product->get_available_variations();
            
            if ( !empty($available_variations) && false != $available_variations ) {
                // phpcs:ignore
                woocommerce_show_product_images();
                add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'enwbvs_add_class_to_loop_gallery' ), 10 );
            }
        
        }
    
    }
    
    /**
     * Adding custom class to loop images.
     *
     * @param  array $classes custom class.
     * @return array.
     */
    public function enwbvs_add_class_to_loop_gallery( $classes )
    {
        $custom_class = array( 'enwbvs-shop-gallery' );
        $classes = array_merge( $classes, $custom_class );
        return $classes;
    }
    
    /**
     * Setting up body class.
     *
     * @param  array $classes classes.
     * @return array.
     */
    public function enwbvs_wc_custom_css_body_class( $classes )
    {
        if ( is_product() ) {
            $classes[] = 'enwbvs-single-product';
        }
        if ( is_archive() || is_shop() || is_product_category() || is_product_tag() || $this->enwbvs_if_custom_shop_page() ) {
            $classes[] = 'enwbvs-archive-cat-tag';
        }
        return $classes;
    }
    
    /**
     * Use single add to cart button for variable products.
     */
    public function enwbvs_template_loop_add_to_cart()
    {
        global  $product ;
        if ( $product instanceof WC_Product_Variable ) {
            
            if ( !is_product() ) {
                $available_variations = $product->get_available_variations();
                
                if ( empty($available_variations) && false != $available_variations ) {
                    // phpcs:ignore
                    ?>
				<a href="<?php 
                    echo  esc_url( get_permalink( $product->get_id() ) ) ;
                    ?>" data-quantity="1" class="button product_type_<?php 
                    echo  esc_html( $product->get_type() ) ;
                    ?>" data-product_id="<?php 
                    esc_attr( $product->get_id() );
                    ?>" rel="nofollow"><?php 
                    echo  esc_html__( 'Read More', 'enweby-variation-swatches-for-woocommerce' ) ;
                    ?></a>
					<?php 
                } else {
                    woocommerce_template_single_add_to_cart();
                }
            
            }
        
        }
    }
    
    /**
     * Removing Select Option button from variable product.
     *
     * @param  string $html    html.
     * @param  int    $product    product id.
     * @param  array  $args     aurgument array.
     * @return string          mixed.
     */
    public function enwbvs_loop_add_to_cart_link( $html, $product, $args )
    {
        global  $product ;
        
        if ( $product instanceof WC_Product_Variable && !is_product() ) {
            // Removing the Show option button.
            remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_template_loop_add_to_cart' ) );
        } else {
            return $html;
        }
    
    }
    
    /**
     * Getting custom script generated.
     *
     * @return string
     */
    public function enwbvs_get_custom_script()
    {
        $js = 'jQuery(function($) {';
        $enwbvs_swatches_display_type_on_filter_widget = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-swatches-display-type-on-filter-widget' );
        $enwbvs_swatches_display_type_on_filter_widget_settings = ( isset( $enwbvs_swatches_display_type_on_filter_widget ) && '' != $enwbvs_swatches_display_type_on_filter_widget ? $enwbvs_swatches_display_type_on_filter_widget : '1' );
        // phpcs:ignore
        // Managing layerd navigation.
        switch ( $enwbvs_swatches_display_type_on_filter_widget_settings ) {
            case '1':
                break;
            case '2':
                $js .= "\$('.enwbvs-swatch-widget-layered-nav-list').parent().css('float','left');";
                break;
        }
        $js .= "\$('.enwbvs-swatch-widget-layered-nav-list').css('visibility','visible');";
        $js .= "\$(document).on('click','.enwbvs-swatch-widget-layered-nav-list', function(){window.location.href =\$(this).data('href');});";
        //if ( enwbvs_fs()->can_use_premium_code__premium_only() ) {
        $enwbvs_clear_on_reselect = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'advanced_section_advanced', 'enwbvs-clear-on-reselect' );
        /** New code **/
        
        if ( is_array( $enwbvs_clear_on_reselect ) && 1 === (int) $enwbvs_clear_on_reselect[0] ) {
            $enwbvs_clear_on_reselect_settings = (int) $enwbvs_clear_on_reselect[0];
        } else {
            $enwbvs_clear_on_reselect_settings = 1;
        }
        
        /***/
        /*if ( is_array( $enwbvs_clear_on_reselect ) && 1 === (int) $enwbvs_clear_on_reselect[0] ) {*/
        /* old code*/
        
        if ( 1 === (int) $enwbvs_clear_on_reselect_settings ) {
            $js .= "\$(document).on('click','.enwbvs-selected-elm', function(){";
            $js .= "var cur_pid = \$(this).parent().attr('data-rel-pid');";
            $js .= "\$(this).closest('.enwbvs-'+cur_pid).find('select[name=\"attribute_'+\$(this).parent().data('rel-id')+'\"]').prop('selectedIndex',0).trigger('change');";
            $js .= "\$(this).removeClass('enwbvs-selected-elm');";
            $js .= "\$(this).closest('.variations').find('th.label label[for='+\$(this).closest('.enwebyvs-attribute').attr('data-rel-id')+ '] .label-extended').remove();";
            $js .= "\$('.cpid-'+cur_pid+' .attr-option-disabled').each(function(i,item){";
            $js .= '});';
            $js .= "\$(this).closest('.variations').find('.reset_variations').trigger('click');";
            $js .= "\$(this).closest('.product').children('span.price').show();";
            $js .= "\$(this).closest('.product').children('.enwbvs-cat-variation-price-wrapper').remove();";
            /*
            				$js .= "$(this).closest('.product').children('a').find('span.price').show();";
            				$js .= "$(this).closest('.product').children('a').find('.enwbvs-cat-variation-price-wrapper').remove();";
            				$js .= "enwbvs_reset_product_image($(this).closest('.product').find('a.woocommerce-loop-product__link img'));";*/
            $js .= "\$(this).closest('.product').children('a, div').find('span.price').show();";
            $js .= "\$(this).closest('.product').children('a, div').find('.enwbvs-cat-variation-price-wrapper').remove();";
            $js .= "enwbvs_reset_product_image(\$(this).closest('.product').find('.attachment-woocommerce_thumbnail'));";
            $js .= '});';
        }
        
        //}
        $enwbvs_default_selected_on_archive = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-default-selected-on-archive' );
        $enwbvs_default_selected_on_archive_settings = ( isset( $enwbvs_default_selected_on_archive ) && '' != $enwbvs_default_selected_on_archive ? $enwbvs_default_selected_on_archive : '0' );
        // phpcs:ignore
        if ( is_product() ) {
            // Automatic default attr selection.
            $js .= "\$('.enwebyvs-option-wrapaper ul li[selected=selected]').each(function (index, liItem) {setTimeout( () => \$(this).trigger('click'), 500);});";
        }
        if ( enwbvs_fs()->is_free_plan() ) {
            $js .= "\$('.enwebyvs-option-wrapaper ul li[selected=selected]').each(function (index, liItem) {setTimeout( () => \$(this).closest('.enwbvs_fields').find('select[name=\"attribute_'+\$(this).parent().data('rel-id')+'\"]').prop('selectedIndex',0).trigger('change'), 100);});";
        }
        $js .= '});';
        // jQuery load codes ends here.
        $enwbvs_generate_variation_url = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'product_page_section_product_page_settings', 'enwbvs-generate-variation-url' );
        $enwbvs_generate_variation_url_settings = ( isset( $enwbvs_generate_variation_url ) && '' != $enwbvs_generate_variation_url ? $enwbvs_generate_variation_url : '0' );
        // phpcs:ignore
        
        if ( is_product() && 1 === (int) $enwbvs_generate_variation_url_settings ) {
            $nonce = wp_create_nonce( 'enwbvs_create_variation_link' );
            $js .= 'function set_variation_url(variation_id) {';
            $js .= ' jQuery.ajax({';
            $js .= " type: 'POST',";
            $js .= ' url: enweby_variation_swatches_for_woocommerce.ajax_url,';
            $js .= " dataType : 'json',";
            $js .= " data: {action: 'enwbvs_create_variation_link', variation_id : variation_id, 'nonce' : '" . $nonce . "' },";
            $js .= ' success: function(response) {';
            $js .= '}';
            $js .= '});';
            $js .= '}';
        } else {
            $js .= 'function set_variation_url(variation_id) {void(0);}';
        }
        
        
        if ( is_archive() || is_shop() || is_product_category() || is_product_tag() || $this->enwbvs_if_custom_shop_page() ) {
            $js .= 'function enwbvs_change_product_image( cur_product, variation ) {';
            $js .= "cur_product.wc_set_variation_attr('src', variation.image.src);";
            $js .= "cur_product.wc_set_variation_attr('height', variation.image.src_h);";
            $js .= "cur_product.wc_set_variation_attr('width', variation.image.src_w);";
            $js .= "cur_product.wc_set_variation_attr('srcset', variation.image.srcset);";
            $js .= "cur_product.wc_set_variation_attr('sizes', variation.image.sizes);";
            $js .= "cur_product.wc_set_variation_attr('title', variation.image.title);";
            $js .= "cur_product.wc_set_variation_attr('data-caption', variation.image.caption);";
            $js .= "cur_product.wc_set_variation_attr('alt', variation.image.alt);";
            $js .= "cur_product.wc_set_variation_attr('data-src', variation.image.full_src);";
            $js .= "cur_product.wc_set_variation_attr('data-large_image', variation.image.full_src);";
            $js .= "cur_product.wc_set_variation_attr('data-large_image_width', variation.image.full_src_w);";
            $js .= "cur_product.wc_set_variation_attr('data-large_image_height', variation.image.full_src_h);";
            $js .= '}';
        } else {
            $js .= 'function enwbvs_change_product_image( cur_product, variation ) {void(0);}';
        }
        
        
        if ( is_archive() || is_shop() || is_product_category() || is_product_tag() || $this->enwbvs_if_custom_shop_page() ) {
            $js .= 'function enwbvs_reset_product_image( cur_product ) {';
            $js .= "cur_product.wc_reset_variation_attr('src');";
            $js .= "cur_product.wc_reset_variation_attr('width');";
            $js .= "cur_product.wc_reset_variation_attr('height');";
            $js .= "cur_product.wc_reset_variation_attr('srcset');";
            $js .= "cur_product.wc_reset_variation_attr('sizes');";
            $js .= "cur_product.wc_reset_variation_attr('title');";
            $js .= "cur_product.wc_reset_variation_attr('data-caption');";
            $js .= "cur_product.wc_reset_variation_attr('alt');";
            $js .= "cur_product.wc_reset_variation_attr('data-src');";
            $js .= "cur_product.wc_reset_variation_attr('data-large_image');";
            $js .= "cur_product.wc_reset_variation_attr('data-large_image_width');";
            $js .= "cur_product.wc_reset_variation_attr('data-large_image_height');";
            $js .= '}';
        } else {
            $js .= 'function enwbvs_reset_product_image( cur_product, variation ) {void(0);}';
        }
        
        return $js;
    }
    
    /**
     * Check if current page is Custom Shop Page.
     */
    public function enwbvs_if_custom_shop_page()
    {
        global  $post ;
        
        if ( $post ) {
            
            if ( '1' == $post->enwbvs_is_custom_shop_page_option ) {
                return true;
            } else {
                return false;
            }
        
        } else {
            return false;
        }
    
    }
    
    /**
     * Create variation link.
     */
    public function enwbvs_create_variation_link()
    {
        $response = array();
        echo  wp_json_encode( $response ) ;
        die;
    }
    
    /**
     * Getting custom style generated.
     *
     * @return string.
     */
    public function enwbvs_get_custom_style()
    {
        $css = '';
        $enwbvs_disabled_attibute_style = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'advanced_section_advanced', 'enwbvs-disabled-attibute-style' );
        $enwbvs_swatch_tick_show = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_swatch_indicator', 'enwbvs-swatch-tick-show' );
        $enwbvs_swatch_tick_show_settings = ( isset( $enwbvs_swatch_tick_show ) && '' != $enwbvs_swatch_tick_show ? $enwbvs_swatch_tick_show : '1' );
        // phpcs:ignore
        
        if ( 1 === (int) $enwbvs_swatch_tick_show_settings ) {
            $enwbvs_swatch_tick_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_swatch_indicator', 'enwbvs-swatch-tick-color' );
            $enwbvs_swatch_tick_color_css = ( isset( $enwbvs_swatch_tick_color ) && '' != $enwbvs_swatch_tick_color ? $enwbvs_swatch_tick_color : '#6be388' );
            // phpcs:ignore
            $css = ".enwebyvs-attribute-child.enwbvs-selected-elm .enwebyvs-variable-item-span::before{background-image: url(\"data:image/svg+xml,%3Csvg class='ticked-class' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' width='20px' height='20px'%3E%3Cpath fill='%23" . str_replace( '#', '', $enwbvs_swatch_tick_color_css ) . "' d='M424.3,180c-1-1.2-1.5-2.8-1.5-4.3c-14.8-26.1-15.7-58-30.5-84.1c-41.7,23.4-70.2,65.1-97.4,103.1c-16.4,22.9-31.1,46.4-44.6,71.1c-13.6,24.8-26.8,49.9-42,73.8c-2.2,3.4-7.9,5-10.3,0.7c-7.2-13.3-15.3-26.2-24.6-38.2c-8-10.3-17.1-19.5-25.3-29.6c-12.7-15.7-26.3-34.5-43.9-45.4c-6.4,21-13.9,41.8-17.2,63.6c24.6,15.9,43.4,38.9,61.5,61.6c21.2,26.6,43.1,52,66.9,76.3c15.4-20.1,26-43.5,38.8-65.3c15.1-25.7,32.7-49.4,51.4-72.6c18.7-23.2,40.3-43.7,62-63.9c10.2-9.5,22.2-17.3,33.1-26c8.2-6.6,16.2-13.4,23.7-20.7C424.4,180.2,424.4,180.1,424.3,180z'/%3E%3Cpath d='M436.2,170.3h-2.8c-16.3-27.7-16.2-62.6-34-89.9c-1.9-3-5.4-3.1-8.3-1.6c-45.6,23.1-76.2,67.7-105.2,108.1c-16.6,23.2-31.6,47-45.4,72c-12.3,22.3-24.1,44.8-37.4,66.6c-6-10.4-12.6-20.4-19.9-29.9c-8.2-10.8-17.8-20.3-26.3-30.8c-15.2-18.7-31.4-40.9-53.7-51.5c-3.7-1.8-7.4,0.5-8.5,4.2c-6.8,23.7-15.8,47-19.5,71.4c0,0.1,0,0.2,0,0.4c-2,2.7-2.2,7.1,1.6,9.4c26.5,15.6,46,40.8,64.9,64.6c22,27.7,45.2,54.1,70.2,79.1c2.3,2.3,6.4,1.8,8.4-0.5c17.2-20.6,28.7-45,41.8-68.2c14.7-25.9,32-50.3,51.1-73.2c19.2-22.9,40-43.7,61.9-64c10.4-9.7,22.6-17.7,33.8-26.6c9-7.2,17.7-14.7,25.9-22.8c2.3-0.5,4.2-2.1,4.6-4.7c0.6-0.6,1.2-1.3,1.8-1.9C445,176.1,441.2,170.3,436.2,170.3z M400.7,201c-10.9,8.7-22.8,16.5-33.1,26c-21.8,20.2-43.4,40.7-62,63.9c-18.7,23.2-36.3,46.8-51.4,72.6c-12.8,21.8-23.4,45.2-38.8,65.3c-23.8-24.2-45.8-49.7-66.9-76.3c-18.1-22.7-37-45.8-61.5-61.6c3.3-21.8,10.8-42.6,17.2-63.6c17.7,10.9,31.2,29.7,43.9,45.4c8.2,10.1,17.3,19.3,25.3,29.6c9.3,12,17.4,24.9,24.6,38.2c2.4,4.4,8.1,2.7,10.3-0.7c15.3-23.9,28.4-49,42-73.8c13.5-24.7,28.2-48.2,44.6-71.1c27.2-38,55.8-79.8,97.4-103.1c14.8,26.1,15.7,58,30.5,84.1c0,1.5,0.5,3.1,1.5,4.3c0,0.1,0.1,0.2,0.1,0.2C416.9,187.6,408.9,194.4,400.7,201z'/%3E%3C/svg%3E\");}";
        }
        
        $enwbvs_swatch_cross_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_swatch_indicator', 'enwbvs-swatch-cross-color' );
        $enwbvs_swatch_cross_color_css = ( isset( $enwbvs_swatch_cross_color ) && '' != $enwbvs_swatch_cross_color ? $enwbvs_swatch_cross_color : '#ff0000' );
        // phpcs:ignore
        $enwbvs_product_swatch_width = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'product_page_section_product_page_swatch_size', 'enwbvs-product-swatch-width' );
        $enwbvs_product_swatch_width_css = ( isset( $enwbvs_product_swatch_width ) && '' != $enwbvs_product_swatch_width ? $enwbvs_product_swatch_width : '35' );
        // phpcs:ignore
        $enwbvs_product_swatch_height = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'product_page_section_product_page_swatch_size', 'enwbvs-product-swatch-height' );
        $enwbvs_product_swatch_height_css = ( isset( $enwbvs_product_swatch_height ) && '' != $enwbvs_product_swatch_height ? $enwbvs_product_swatch_height : '35' );
        // phpcs:ignore
        $enwbvs_product_swatch_font_size = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'product_page_section_product_page_swatch_size', 'enwbvs-product-swatch-font-size' );
        $enwbvs_product_swatch_font_size_css = ( isset( $enwbvs_product_swatch_font_size ) && '' != $enwbvs_product_swatch_font_size ? $enwbvs_product_swatch_font_size : '15' );
        // phpcs:ignore
        $enwbvs_arhive_swatch_width = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive_swatch_size', 'enwbvs-arhive-swatch-width' );
        $enwbvs_arhive_swatch_width_css = ( isset( $enwbvs_arhive_swatch_width ) && '' != $enwbvs_arhive_swatch_width ? $enwbvs_arhive_swatch_width : '35' );
        // phpcs:ignore
        $enwbvs_arhive_swatch_height = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive_swatch_size', 'enwbvs-arhive-swatch-height' );
        $enwbvs_arhive_swatch_height_css = ( isset( $enwbvs_arhive_swatch_height ) && '' != $enwbvs_arhive_swatch_height ? $enwbvs_arhive_swatch_height : '35' );
        // phpcs:ignore
        $enwbvs_archive_swatch_font_size = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive_swatch_size', 'enwbvs-archive-swatch-font-size' );
        $enwbvs_archive_swatch_font_size_css = ( isset( $enwbvs_archive_swatch_font_size ) && '' != $enwbvs_archive_swatch_font_size ? $enwbvs_archive_swatch_font_size : '15' );
        // phpcs:ignore
        // Product Page Settings.
        $enwbvs_show_attribute_variation_name = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'product_page_section_product_page_settings', 'enwbvs-show-attribute-variation-name' );
        $enwbvs_show_attribute_variation_name_settings = ( isset( $enwbvs_show_attribute_variation_name ) && '' != $enwbvs_show_attribute_variation_name ? $enwbvs_show_attribute_variation_name : '1' );
        // phpcs:ignore
        $enwbvs_product_page_variation_stock_info = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'product_page_section_product_page_settings', 'enwbvs-product-page-variation-stock-info' );
        $enwbvs_product_page_variation_stock_info_settings = ( isset( $enwbvs_product_page_variation_stock_info ) && '' != $enwbvs_product_page_variation_stock_info ? $enwbvs_product_page_variation_stock_info : '0' );
        // phpcs:ignore
        // Archive Page Settings.
        $enwbvs_archive_page_variation_stock_info = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-archive-page-variation-stock-info' );
        $enwbvs_archive_page_variation_stock_info_settings = ( isset( $enwbvs_archive_page_variation_stock_info ) && '' != $enwbvs_archive_page_variation_stock_info ? $enwbvs_archive_page_variation_stock_info : '0' );
        // phpcs:ignore
        $enwbvs_swatches_display_type_on_filter_widget = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-swatches-display-type-on-filter-widget' );
        $enwbvs_swatches_display_type_on_filter_widget_settings = ( isset( $enwbvs_swatches_display_type_on_filter_widget ) && '' != $enwbvs_swatches_display_type_on_filter_widget ? $enwbvs_swatches_display_type_on_filter_widget : '0' );
        // phpcs:ignore
        // Managing layerd navigation.
        switch ( $enwbvs_swatches_display_type_on_filter_widget_settings ) {
            case '1':
                $css .= '.enwbvs-swatch-widget-layered-nav-list{float:left;margin-right:10px;}';
                break;
            case '2':
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item a, .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item span {display:none;}';
                break;
        }
        
        if ( 1 === (int) $enwbvs_disabled_attibute_style ) {
            $css .= ".enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before{ content: ' '; display: block; opacity:1; background-repeat: no-repeat; background-position: 50%; height: 2px; top:50%; margin-top:-1px; background:" . $enwbvs_swatch_cross_color_css . ";transform:rotate(45deg);position:absolute;z-index:9999;}\n\t\t.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after{ content: ' '; display: block; opacity:1; background-repeat: no-repeat; background-position: 50%; height: 2px; top:50%; margin-top:-1px; background:" . $enwbvs_swatch_cross_color_css . ';transform:rotate(-45deg);position:absolute;z-index:9999;}
		.attr-option-disabled .enwebyvs-variable-item-wrapper {cursor:default;opacity:0.6;}
		.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-wrapper {opacity:0.6;}
		';
        } elseif ( 2 === (int) $enwbvs_disabled_attibute_style ) {
            $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before{ display:none;}
		.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after{ display:none;}
		.attr-option-disabled .enwebyvs-variable-item-wrapper{cursor:default;opacity:0.4;}
		.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-wrapper {opacity:0.4;}
		.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-span::before{ display:none;}
		.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-span::after{ display:none;}
		';
        } elseif ( 3 === (int) $enwbvs_disabled_attibute_style ) {
            $css .= '.enwebyvs-attribute-child.attr-option-disabled { display:none;}
			.enwebyvs-attribute .out-of-stock-swatch-item {display:none;}';
        } else {
            $css .= ".enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before{ content: ' '; display: block; opacity:1; background-repeat: no-repeat; background-position: 50%; height: 2px; top:50%; margin-top:-1px; background: " . $enwbvs_swatch_cross_color_css . ";transform:rotate(45deg);position:absolute;z-index:9999;}\n\t\t.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after{ content: ' '; display: block; opacity:1; background-repeat: no-repeat; background-position: 50%; height: 2px; top:50%; margin-top:-1px; background: " . $enwbvs_swatch_cross_color_css . ';
		transform:rotate(-45deg);position:absolute;z-index:9999;}
		.attr-option-disabled .enwebyvs-variable-item-wrapper {cursor:default;opacity:0.6;}
		.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-wrapper {opacity:0.6;}';
        }
        
        $enwbvs_enable_toolip_on_swatches = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'general_section_general', 'enwbvs-enable-toolip-on-swatches' );
        
        if ( isset( $enwbvs_enable_toolip_on_swatches ) && 1 === (int) $enwbvs_enable_toolip_on_swatches ) {
            $css .= '.enwebyvs-attribute .tooltiptext{display:block;}';
        } else {
            $css .= '.enwebyvs-attribute .tooltiptext{display:none;}';
        }
        
        $css .= '.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-span::before{background:' . $enwbvs_swatch_cross_color_css . ';width:100%;height:2px;}';
        $css .= '.enwebyvs-attribute .out-of-stock-swatch-item .enwebyvs-variable-item-span::after{background:' . $enwbvs_swatch_cross_color_css . ';}';
        $enwbvs_tooltip_text_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_tooltip_styling', 'enwbvs-tooltip-text-color' );
        $enwbvs_tooltip_text_color_css = ( isset( $enwbvs_tooltip_text_color ) && '' != $enwbvs_tooltip_text_color ? $enwbvs_tooltip_text_color : '#ffffff' );
        // phpcs:ignore
        $enwbvs_tooltip_background_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_tooltip_styling', 'enwbvs-tooltip-background-color' );
        $enwbvs_tooltip_background_color_css = ( isset( $enwbvs_tooltip_background_color ) && '' != $enwbvs_tooltip_background_color ? $enwbvs_tooltip_background_color : '#000000' );
        // phpcs:ignore
        $css .= '.enwbvs-tooltip .tooltiptext {background-color:' . $enwbvs_tooltip_background_color_css . ';color:' . $enwbvs_tooltip_text_color_css . ';}';
        $css .= '.enwbvs-tooltip .tooltiptext::after {border-color:' . $enwbvs_tooltip_background_color_css . ' transparent transparent transparent;}';
        $enwbvs_swatch_radio_circle_bg_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_radio_styling', 'enwbvs-swatch-radio-circle-bg-color' );
        $enwbvs_swatch_radio_circle_bg_color_css = ( isset( $enwbvs_swatch_radio_circle_bg_color ) && '' != $enwbvs_swatch_radio_circle_bg_color ? $enwbvs_swatch_radio_circle_bg_color : '#dddddd' );
        // phpcs:ignore
        $enwbvs_swatch_radio_text_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_radio_styling', 'enwbvs-swatch-radio-text-color' );
        $enwbvs_swatch_radio_text_color_css = ( isset( $enwbvs_swatch_radio_text_color ) && '' != $enwbvs_swatch_radio_text_color ? $enwbvs_swatch_radio_text_color : '#555555' );
        // phpcs:ignore
        $enwbvs_swatch_radio_text_color_hover = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_radio_styling', 'enwbvs-swatch-radio-text-color-hover' );
        $enwbvs_swatch_radio_text_color_hover_css = ( isset( $enwbvs_swatch_radio_text_color_hover ) && '' != $enwbvs_swatch_radio_text_color_hover ? $enwbvs_swatch_radio_text_color_hover : '#000000' );
        // phpcs:ignore
        $enwbvs_swatch_radio_text_color_selected = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_radio_styling', 'enwbvs-swatch-radio-text-color-selected' );
        $enwbvs_swatch_radio_text_color_selected_css = ( isset( $enwbvs_swatch_radio_text_color_selected ) && '' != $enwbvs_swatch_radio_text_color_selected ? $enwbvs_swatch_radio_text_color_selected : '#000000' );
        // phpcs:ignore
        $css .= '.enwebyvs-attribute.swatch-type-radio .checkmark{background-color:' . $enwbvs_swatch_radio_circle_bg_color_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
		   .enwebyvs-attribute.swatch-type-radio .enwebyvs-variable-item-span{color:' . $enwbvs_swatch_radio_text_color_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
           .enwebyvs-attribute.swatch-type-radio .enwebyvs-variable-item-span:hover{color:' . $enwbvs_swatch_radio_text_color_hover_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
           .enwebyvs-attribute.swatch-type-radio .enwbvs-selected-elm .enwebyvs-variable-item-span{color:' . $enwbvs_swatch_radio_text_color_selected_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
           .enwebyvs-attribute.swatch-type-radio .enwebyvs-variable-item-span:hover .checkmark:after{background:' . $enwbvs_swatch_radio_text_color_hover_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
		   .enwebyvs-attribute.swatch-type-radio .enwbvs-selected-elm  .enwebyvs-variable-item-span .checkmark:after{background:' . $enwbvs_swatch_radio_text_color_selected_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
		   .enwebyvs-attribute.swatch-type-radio .attr-option-disabled:hover .enwebyvs-variable-item-span:hover{color:' . $enwbvs_swatch_radio_circle_bg_color_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
		   .enwebyvs-attribute.swatch-type-radio .attr-option-disabled:hover .checkmark:after{background-color:' . $enwbvs_swatch_radio_circle_bg_color_css . ';-webkit-transition: all 0.3s 0s ease;
-moz-transition: all 0.3s 0s ease;-o-transition: all 0.3s 0s ease;transition: all 0.3s 0s ease;}
		   ';
        $css .= '.enwbvs-single-product .enwebyvs-attribute.swatch-type-radio .out-of-stock-swatch-item .enwebyvs-variable-item-span::before, .enwbvs-single-product .enwebyvs-attribute.swatch-type-radio .out-of-stock-swatch-item .enwebyvs-variable-item-span::after {width:' . $enwbvs_product_swatch_width_css . 'px;}';
        $css .= '.enwbvs-archive-cat-tag .enwebyvs-attribute.swatch-type-radio .out-of-stock-swatch-item .enwebyvs-variable-item-span::before, .enwbvs-archive-cat-tag .enwebyvs-attribute.swatch-type-radio .out-of-stock-swatch-item .enwebyvs-variable-item-span::after {width:' . $enwbvs_arhive_swatch_width_css . 'px;}';
        $enwbvs_swatch_image_color_shape_style = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_image_color_styling', 'enwbvs-swatch-image-color-shape-style' );
        $enwbvs_swatch_image_color_shape_style_css = ( isset( $enwbvs_swatch_image_color_shape_style ) && '' != $enwbvs_swatch_image_color_shape_style ? $enwbvs_swatch_image_color_shape_style : 'square' );
        // phpcs:ignore
        $enwbvs_swatch_image_color_border_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_image_color_styling', 'enwbvs-swatch-image-color-border-color' );
        $enwbvs_swatch_image_color_border_color_css = ( isset( $enwbvs_swatch_image_color_border_color ) && '' != $enwbvs_swatch_image_color_border_color ? $enwbvs_swatch_image_color_border_color : '#dddddd' );
        // phpcs:ignore
        $enwbvs_swatch_image_color_border_color_hover = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_image_color_styling', 'enwbvs-swatch-image-color-border-color-hover' );
        $enwbvs_swatch_image_color_border_color_hover_css = ( isset( $enwbvs_swatch_image_color_border_color_hover ) && '' != $enwbvs_swatch_image_color_border_color_hover ? $enwbvs_swatch_image_color_border_color_hover : '#666666' );
        // phpcs:ignore
        $enwbvs_swatch_image_color_border_color_selected = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_image_color_styling', 'enwbvs-swatch-image-color-border-color-selected' );
        $enwbvs_swatch_image_color_border_color_selected_css = ( isset( $enwbvs_swatch_image_color_border_color_selected ) && '' != $enwbvs_swatch_image_color_border_color_selected ? $enwbvs_swatch_image_color_border_color_selected : '#666666' );
        // phpcs:ignore
        $enwbvs_swatch_label_button_shape_style = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-button-shape-style' );
        $enwbvs_swatch_label_button_shape_style_css = ( isset( $enwbvs_swatch_label_button_shape_style ) && '' != $enwbvs_swatch_label_button_shape_style ? $enwbvs_swatch_label_button_shape_style : 'rounded' );
        // phpcs:ignore
        $enwbvs_swatch_label_text_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-text-color' );
        $enwbvs_swatch_label_text_color_css = ( isset( $enwbvs_swatch_label_text_color ) && '' != $enwbvs_swatch_label_text_color ? $enwbvs_swatch_label_text_color : '#555555' );
        // phpcs:ignore
        $enwbvs_swatch_label_text_color_hover = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-text-color-hover' );
        $enwbvs_swatch_label_text_color_hover_css = ( isset( $enwbvs_swatch_label_text_color_hover ) && '' != $enwbvs_swatch_label_text_color_hover ? $enwbvs_swatch_label_text_color_hover : '#000000' );
        // phpcs:ignore
        $enwbvs_swatch_label_text_color_selected = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-text-color-selected' );
        $enwbvs_swatch_label_text_color_selected_css = ( isset( $enwbvs_swatch_label_text_color_selected ) && '' != $enwbvs_swatch_label_text_color_selected ? $enwbvs_swatch_label_text_color_selected : '#000000' );
        // phpcs:ignore
        $enwbvs_swatch_label_background_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-background-color' );
        $enwbvs_swatch_label_background_color_css = ( isset( $enwbvs_swatch_label_background_color ) && '' != $enwbvs_swatch_label_background_color ? $enwbvs_swatch_label_background_color : '#ffffff' );
        // phpcs:ignore
        $enwbvs_swatch_label_background_color_hover = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-background-color-hover' );
        $enwbvs_swatch_label_background_color_hover_css = ( isset( $enwbvs_swatch_label_background_color_hover ) && '' != $enwbvs_swatch_label_background_color_hover ? $enwbvs_swatch_label_background_color_hover : '#ffffff' );
        // phpcs:ignore
        $enwbvs_swatch_label_background_color_selected = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-background-color-selected' );
        $enwbvs_swatch_label_background_color_selected_css = ( isset( $enwbvs_swatch_label_background_color_selected ) && '' != $enwbvs_swatch_label_background_color_selected ? $enwbvs_swatch_label_background_color_selected : '#ffffff' );
        // phpcs:ignore
        $enwbvs_swatch_label_border_color = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-border-color' );
        $enwbvs_swatch_label_border_color_css = ( isset( $enwbvs_swatch_label_border_color ) && '' != $enwbvs_swatch_label_border_color ? $enwbvs_swatch_label_border_color : '#dddddd' );
        // phpcs:ignore
        $enwbvs_swatch_label_border_color_hover = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-border-color-hover' );
        $enwbvs_swatch_label_border_color_hover_css = ( isset( $enwbvs_swatch_label_border_color_hover ) && '' != $enwbvs_swatch_label_border_color_hover ? $enwbvs_swatch_label_border_color_hover : '#666666' );
        // phpcs:ignore
        $enwbvs_swatch_label_border_color_selection = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'swatch_type_specific_styling_section_swatch_type_label_styling', 'enwbvs-swatch-label-border-color-selection' );
        $enwbvs_swatch_label_border_color_selection_css = ( isset( $enwbvs_swatch_label_border_color_selection ) && '' != $enwbvs_swatch_label_border_color_selection ? $enwbvs_swatch_label_border_color_selection : '#666666' );
        // phpcs:ignore
        $enwbvs_swatch_inner_padding = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_swatch_indicator', 'enwbvs-swatch-inner-padding' );
        $enwbvs_swatch_inner_padding_css = ( isset( $enwbvs_swatch_inner_padding ) && '' != $enwbvs_swatch_inner_padding ? $enwbvs_swatch_inner_padding : '2' );
        // phpcs:ignore
        $enwbvs_selected_swatch_border_width = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'styling_section_swatch_indicator', 'enwbvs-selected-swatch-border-width' );
        $enwbvs_selected_swatch_border_width_css = ( isset( $enwbvs_selected_swatch_border_width ) && '' != $enwbvs_selected_swatch_border_width ? $enwbvs_selected_swatch_border_width : '2' );
        // phpcs:ignore
        // Labelorbutton rounded corner radius.
        $border_radius_rounded = '5px';
        switch ( $enwbvs_swatch_label_button_shape_style_css ) {
            case 'square':
                $css .= '.enwbvs-single-product ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;height:' . ($enwbvs_product_swatch_height_css - 0) . 'px;line-height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;font-size:' . $enwbvs_product_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;height:' . ($enwbvs_product_swatch_height_css - 0) . 'px;line-height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;font-size:' . $enwbvs_archive_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper span {padding:2px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper {-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                break;
            case 'circle':
                $css .= '.enwbvs-single-product ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;height:' . ($enwbvs_product_swatch_height_css - 0) . 'px;line-height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;font-size:' . $enwbvs_product_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-label .enwebyvs-attribute-child  .enwebyvs-variable-item-wrapper{width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;height:' . ($enwbvs_product_swatch_height_css - 0) . 'px;line-height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;font-size:' . $enwbvs_archive_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper span {padding:2px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper {padding:2px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                break;
            case 'rounded':
                $css .= '.enwbvs-single-product ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper{min-width:' . ($enwbvs_product_swatch_width_css + 5) . 'px;height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;line-height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;font-size:' . $enwbvs_product_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:0 5px;-webkit-border-radius: ' . $border_radius_rounded . ';-moz-border-radius: ' . $border_radius_rounded . ';border-radius: ' . $border_radius_rounded . ';}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {min-width:' . ($enwbvs_product_swatch_width_css + 5) . 'px;height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;line-height:' . ($enwbvs_product_swatch_height_css - 5) . 'px;font-size:' . $enwbvs_archive_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:0 5px;-webkit-border-radius: ' . $border_radius_rounded . ';-moz-border-radius: ' . $border_radius_rounded . ';border-radius: ' . $border_radius_rounded . ';}';
                $css .= '.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before, .swatch-type-label .enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px !important;}';
                $css .= '.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after, .swatch-type-label .enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after {width:' . ($enwbvs_product_swatch_width_css - 0) . 'px !important;}';
                $css .= '.enwebyvs-attribute.swatch-type-swatch_dropdown_to_label .out-of-stock-swatch-item .enwebyvs-variable-item-span::before, .enwebyvs-attribute.swatch-type-label .out-of-stock-swatch-item .enwebyvs-variable-item-span::before{width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;}';
                $css .= '.enwebyvs-attribute.swatch-type-swatch_dropdown_to_label .out-of-stock-swatch-item .enwebyvs-variable-item-span::after,.enwebyvs-attribute.swatch-type-label .out-of-stock-swatch-item .enwebyvs-variable-item-span::after{width:' . ($enwbvs_product_swatch_width_css - 0) . 'px;}';
                $css .= 'ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, ul.swatch-type-label .enwebyvs-variable-item-span .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span {min-width:' . $enwbvs_product_swatch_width_css . 'px;}';
                $css .= 'ul.swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text, ul.swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text {padding:0 5px;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper span {min-width:30px;width:auto;padding:0 5px;-webkit-border-radius: ' . $border_radius_rounded . ';-moz-border-radius: ' . $border_radius_rounded . ';border-radius: ' . $border_radius_rounded . ';}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper {padding:2px;-webkit-border-radius: ' . $border_radius_rounded . ';-moz-border-radius: ' . $border_radius_rounded . ';border-radius: ' . $border_radius_rounded . ';}';
                break;
        }
        $css .= '.enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text, .enwebyvs-option-wrapaper .swatch-type-label .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text{color:' . $enwbvs_swatch_label_text_color_css . ';}
		  .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text, .enwebyvs-option-wrapaper  .swatch-type-label .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text{color:' . $enwbvs_swatch_label_text_color_hover_css . ';}
		  .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .attr-option-disabled:hover .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text, .enwebyvs-option-wrapaper  .swatch-type-label .attr-option-disabled:hover .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text{color:' . $enwbvs_swatch_label_text_color_css . ';}	
		  .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text, .enwebyvs-option-wrapaper  .swatch-type-label .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text{color:' . $enwbvs_swatch_label_text_color_css . ';}
		  .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .enwbvs-selected-elm .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text, .enwebyvs-option-wrapaper  .swatch-type-label .enwbvs-selected-elm .enwebyvs-variable-item-wrapper .enwebyvs-variable-item-span-text{color:' . $enwbvs_swatch_label_text_color_selected_css . '!important;}		   
		   ';
        // Setting border css for label.
        $css .= '.enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-label .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper{background:' . $enwbvs_swatch_label_background_color_css . ';box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . ';-moz-box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . ';-webkit-box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . ';}
		    .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-label .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper {background:' . $enwbvs_swatch_label_background_color_hover_css . ';box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_label_border_color_hover_css . ';-moz-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_label_border_color_hover_css . ';-webkit-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_label_border_color_hover_css . ';}
			.enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .attr-option-disabled:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-label .attr-option-disabled:hover .enwebyvs-variable-item-wrapper{background:' . $enwbvs_swatch_label_background_color_css . ';box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . '!important;-moz-box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . '!important;-webkit-box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . '!important;}
		   .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-label .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper{background:' . $enwbvs_swatch_label_background_color_css . ';box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . '!important;-moz-box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . '!important;-webkit-box-shadow:0 0 0 1px ' . $enwbvs_swatch_label_border_color_css . '!important;}	
		   .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_label .enwbvs-selected-elm .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-label .enwbvs-selected-elm .enwebyvs-variable-item-wrapper {background:' . $enwbvs_swatch_label_background_color_selected_css . ' !important;box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_label_border_color_selection_css . ';-moz-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_label_border_color_selection_css . '!important;-webkit-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_label_border_color_selection_css . '!important;}		   
		   ';
        // for view more on cat-tag page
        $css .= '.enwbvs-archive-cat-tag .enwebyvs-option-wrapaper .view-more{float:right;align-items:center;height:' . $enwbvs_product_swatch_height_css . 'px;}';
        switch ( $enwbvs_swatch_image_color_shape_style_css ) {
            case 'square':
                $css .= '.enwbvs-single-product ul.swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . $enwbvs_product_swatch_width_css . 'px;height:' . $enwbvs_product_swatch_height_css . 'px;font-size:' . $enwbvs_product_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . $enwbvs_arhive_swatch_width_css . 'px;height:' . $enwbvs_arhive_swatch_height_css . 'px;font-size:' . $enwbvs_archive_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before {width:100%;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after {width:100%;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-color-filter-wrapper span {padding:2px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-color-filter-wrapper {-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-image-filter-wrapper span {padding:2px;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-image-filter-wrapper {-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}';
                break;
            case 'circle':
                $css .= '.enwbvs-single-product ul.swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-single-product ul.swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . $enwbvs_product_swatch_width_css . 'px;height:' . $enwbvs_product_swatch_height_css . 'px;font-size:' . $enwbvs_product_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwbvs-archive-cat-tag ul.swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper {width:' . $enwbvs_arhive_swatch_width_css . 'px;height:' . $enwbvs_arhive_swatch_height_css . 'px;font-size:' . $enwbvs_archive_swatch_font_size_css . 'px;display:flex;justify-content:center;padding:' . $enwbvs_swatch_inner_padding_css . 'px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-span img, .enwbvs-archive-cat-tag ul.swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-span img {width:auto;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwbvs-single-product ul.swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-span img, .enwbvs-single-product ul.swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-span img {width:auto;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwbvs-archive-cat-tag ul.swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-span {-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwbvs-single-product ul.swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-span {-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::before {width:100%;}';
                $css .= '.enwebyvs-attribute-child.attr-option-disabled .enwebyvs-variable-item-span::after {width:100%;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-color-filter-wrapper span {padding:2px;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-color-filter-wrapper {-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-image-filter-wrapper span {padding:2px;}';
                $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-image-filter-wrapper img {-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                $css .= '.woocommerce-widget-layered-nav-list li.woocommerce-widget-layered-nav-list__item .enwbvs-swatch-image-filter-wrapper {-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;}';
                break;
        }
        // setting border and other css for image and color.
        $css .= '.enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-image .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-color .enwebyvs-attribute-child .enwebyvs-variable-item-wrapper{box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';-moz-box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';-webkit-box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';}
		   .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_image .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-image .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-color .enwebyvs-attribute-child:hover .enwebyvs-variable-item-wrapper{box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_image_color_border_color_hover_css . ';-moz-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_image_color_border_color_hover_css . ';-webkit-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_image_color_border_color_hover_css . ';}
		   .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_image .attr-option-disabled:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-image .attr-option-disabled:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-color .attr-option-disabled:hover .enwebyvs-variable-item-wrapper{box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';-moz-box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';-webkit-box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';}	
           .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_image .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-image .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-color .click-disabled-outofstock:hover .enwebyvs-variable-item-wrapper{box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';-moz-box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';-webkit-box-shadow:0 0 0 1px ' . $enwbvs_swatch_image_color_border_color_css . ';}
		   .enwebyvs-option-wrapaper .swatch-type-swatch_dropdown_to_image .enwbvs-selected-elm .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-image .enwbvs-selected-elm .enwebyvs-variable-item-wrapper, .enwebyvs-option-wrapaper .swatch-type-color .enwbvs-selected-elm .enwebyvs-variable-item-wrapper {box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_image_color_border_color_selected_css . ';-moz-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_image_color_border_color_selected_css . '!important;-webkit-box-shadow:0 0 0 ' . $enwbvs_selected_swatch_border_width_css . 'px ' . $enwbvs_swatch_image_color_border_color_selected_css . '!important;}		   
		   ';
        $css .= '.woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-label-filter-wrapper, .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-color-filter-wrapper, .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item .enwbvs-swatch-image-filter-wrapper {padding:' . $enwbvs_swatch_inner_padding_css . 'px;}';
        if ( 0 === (int) $enwbvs_product_page_variation_stock_info_settings ) {
            $css .= '.enwbvs-single-product .enwebyvs-option-wrapaper .enwbvs-stock-left-alert {display:none !important;}';
        }
        if ( 0 === (int) $enwbvs_archive_page_variation_stock_info_settings ) {
            $css .= '.enwbvs-archive-cat-tag .enwebyvs-option-wrapaper .enwbvs-stock-left-alert {display:none !important;}';
        }
        if ( 0 === (int) $enwbvs_show_attribute_variation_name_settings ) {
            $css .= '.enwbvs-single-product table.variations .label-extended {display:none !important;}';
        }
        $enwbvs_show_attribute_name_on_archive = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-show-attribute-name-on-archive' );
        $enwbvs_show_attribute_name_on_archive_css = ( isset( $enwbvs_show_attribute_name_on_archive ) && '' != $enwbvs_show_attribute_name_on_archive ? $enwbvs_show_attribute_name_on_archive : '1' );
        // phpcs:ignore
        if ( 1 !== (int) $enwbvs_show_attribute_name_on_archive_css ) {
            $css .= '.enwbvs-archive-cat-tag table.variations th.label{display:none !important;}';
        }
        $enwbvs_show_attribute_variation_name_on_archive_css = 0;
        if ( 1 !== (int) $enwbvs_show_attribute_variation_name_on_archive_css ) {
            $css .= '.enwbvs-archive-cat-tag table.variations .label label .label-extended{display:none !important;}';
        }
        $enwbvs_code_editor = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'custom_css_section_css', 'enwbvs_code_editor' );
        $enwbvs_code_editor_css = ( isset( $enwbvs_code_editor ) && '' != $enwbvs_code_editor ? $enwbvs_code_editor : '' );
        // phpcs:ignore
        $css .= $enwbvs_code_editor_css;
        return $css;
    }
    
    /**
     * Getting attribute fields.
     *
     * @param  array  $attribute attribute.
     * @param  object $product product object.
     * @return mixed.
     */
    public function get_attribute_fields( $attribute, $product )
    {
        
        if ( taxonomy_exists( $attribute ) ) {
            $attribute_taxonomies = wc_get_attribute_taxonomies();
            // phpcs:disable
            foreach ( $attribute_taxonomies as $tax ) {
                
                if ( 'pa_' . $tax->attribute_name == $attribute ) {
                    // phpcs:ignore
                    return $tax->attribute_type;
                    break;
                }
            
            }
            // phpcs:enable
        } else {
            $product_id = $product->get_id();
            $attribute = sanitize_title( $attribute );
            $local_attr_settings = get_post_meta( $product_id, 'enwbvs_custom_attribute_settings', true );
            
            if ( is_array( $local_attr_settings ) && isset( $local_attr_settings[$attribute] ) ) {
                $settings = $local_attr_settings[$attribute];
                $type = ( isset( $settings['type'] ) ? $settings['type'] : '' );
                return $type;
            }
            
            return '';
        }
    
    }
    
    /**
     * Swatch dispaly html.
     *
     * @param  string $html html.
     * @param  array  $args arugement.
     * @return string.
     */
    public function swatches_display( $html, $args )
    {
        
        if ( apply_filters( 'enwbvs_enable_swatches_display', true ) ) {
            $attribute = $args['attribute'];
            $product = $args['product'];
            $product_id = $product->get_id();
            $type = $this->get_attribute_fields( $attribute, $product );
            $enwbvs_default_dropdown_to_buttons = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'general_section_general', 'enwbvs-default-dropdown-to-buttons' );
            $enwbvs_default_dropdown_to_images = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'general_section_general', 'enwbvs-default-dropdown-to-images' );
            $apply_auto_convert = false;
            // no use of this variable at this time, but for future purpose it is there.
            /*//"" == $enwbvs_default_dropdown_to_buttons to make sure default option enable setting work*/
            
            if ( 'select' == $type && ('' == $enwbvs_default_dropdown_to_buttons || 1 === (int) $enwbvs_default_dropdown_to_buttons) ) {
                // phpcs:ignore
                $type = 'swatch_dropdown_to_label';
                $apply_auto_convert = true;
            } elseif ( 'select' == $type && 1 === (int) $enwbvs_default_dropdown_to_images ) {
                // phpcs:ignore
                $type = 'swatch_dropdown_to_image';
                $apply_auto_convert = true;
            } else {
                $type = $type;
                $apply_auto_convert = false;
            }
            
            $swatch_types = array(
                'color',
                'image',
                'label',
                'radio'
            );
            $attr_type_html = '';
            
            if ( in_array( $type, $swatch_types, true ) ) {
                $attr_type_html .= $this->swatch_display_options_html(
                    $html,
                    $args,
                    $type,
                    $apply_auto_convert
                );
            } elseif ( 'swatch_dropdown_to_image' == $type || 'swatch_dropdown_to_label' == $type ) {
                // phpcs:ignore
                $attr_type_html .= $this->swatch_dropdown_auto_convert(
                    $html,
                    $args,
                    $type,
                    $apply_auto_convert
                );
            } else {
                return $html;
            }
            
            $html = $attr_type_html;
            $html = $this->wrapp_variation_in_class( $html, $product_id );
        }
        
        return $html;
    }
    
    /**
     * Wrapping variation in custom html element.
     *
     * @param  string $html    html.
     * @param  int    $product_id product id.
     * @return sting.
     */
    /*public function wrapp_variation_in_class( $html, $product_id ) {
    		$html = '<div class="enwbvs_fields enwbvs-' . $product_id . '"> ' . $html . ' </div>';
    		return $html;
    	}*/
    /**
     * Wrapping variation in custom html element.
     *
     * @param  string $html    html.
     * @param  int    $product_id product id.
     * @return sting.
     */
    public function wrapp_variation_in_class( $html, $product_id )
    {
        $html = '<div class="enwbvs_fields enwbvs-' . $product_id . '"> ' . $html . ' </div>';
        return $html;
    }
    
    /**
     * Auto convert to swatch from dropdown.
     *
     * @param  sting $html               html.
     * @param  array $args               args.
     * @param  sting $type               type.
     * @param  sting $apply_auto_convert  auto convert val.
     * @return string.
     */
    public function swatch_dropdown_auto_convert(
        $html,
        $args,
        $type,
        $apply_auto_convert
    )
    {
        $options = $args['options'];
        $product = $args['product'];
        $attribute = $args['attribute'];
        $name = ( $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute ) );
        $id = ( $args['id'] ? $args['id'] : sanitize_title( $attribute ) );
        $class = $args['class'];
        $selector_class = 'enwebyvs-attribute';
        $show_option_none = (bool) $args['show_option_none'];
        
        if ( empty($options) && !empty($product) && !empty($attribute) ) {
            $attributes = $product->get_variation_attributes();
            $options = $attributes[$attribute];
        }
        
        $html .= '<div class="enwebyvs-option-wrapaper cpid-' . $product->get_id() . '" >';
        $html .= '<ul data-rel-id="' . esc_attr( $id ) . '" data-rel-pid="' . esc_attr( $product->get_id() ) . '" class="' . esc_attr( $class ) . ' ' . esc_attr( $selector_class ) . ' swatch-type-' . $type . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . (( $show_option_none ? 'yes' : 'no' )) . '">';
        $enwbvs_product_page_attribute_display_limit = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-product-page-attribute-display-limit' );
        $enwbvs_product_page_attribute_display_limit_settings = ( isset( $enwbvs_product_page_attribute_display_limit ) && '' != $enwbvs_product_page_attribute_display_limit ? $enwbvs_product_page_attribute_display_limit : '0' );
        // phpcs:ignore
        $enwbvs_product_page_attribute_display_limit_settings = ( 0 === (int) $enwbvs_product_page_attribute_display_limit_settings ? '10000' : $enwbvs_product_page_attribute_display_limit_settings );
        $ctr_term = 1;
        $ctr_option = 1;
        if ( !empty($options) ) {
            
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms( $product->get_id(), $attribute, array(
                    'fields' => 'all',
                ) );
                foreach ( $terms as $term ) {
                    
                    if ( in_array( $term->slug, $options, true ) ) {
                        $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $term->slug );
                        $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
                        $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
                        if ( 'swatch_dropdown_to_label' == $type ) {
                            // phpcs:ignore
                            $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $term->name . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                                'woocommerce_variation_option_name',
                                $term->name,
                                $term,
                                $attribute,
                                $product
                            ) ) . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                        }
                        if ( 'swatch_dropdown_to_image' == $type ) {
                            // phpcs:ignore
                            if ( enwbvs_fs()->is_free_plan() ) {
                                $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $term->name . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                                    'woocommerce_variation_option_name',
                                    $term->name,
                                    $term,
                                    $attribute,
                                    $product
                                ) ) . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            }
                        }
                        
                        if ( $ctr_term >= $enwbvs_product_page_attribute_display_limit_settings && !is_product() && 0 !== (int) (count( $terms ) - $enwbvs_product_page_attribute_display_limit_settings) ) {
                            $html .= '<li class="view-more ' . esc_attr( $selector_class ) . '-child"><div class="enwebyvs-variable-read-more"><span class="enwebyvs-variable-item-span"><a href="' . get_permalink( $product->get_id() ) . '"> + ' . (count( $terms ) - $enwbvs_product_page_attribute_display_limit_settings) . ' ' . esc_html__( 'More', 'enweby-variation-swatches-for-woocommerce' ) . '</a></span></div></li>';
                            break;
                        }
                        
                        $ctr_term++;
                    }
                
                }
            } else {
                foreach ( $options as $option ) {
                    $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $option );
                    $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
                    $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
                    // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                    $selected = ( sanitize_title( $args['selected'] ) == $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false ) );
                    // phpcs:ignore
                    $custom_option_attr_settings = get_post_meta( $product->get_id(), 'enwbvs_custom_attribute_settings', true );
                    $term_settings = ( isset( $custom_option_attr_settings[sanitize_title( $attribute )] ) ? $custom_option_attr_settings[sanitize_title( $attribute )] : '' );
                    $value_settings = ( $term_settings && isset( $term_settings[$option] ) ? $term_settings[$option] : '' );
                    $term_val = ( empty($value_settings['term_value']) ? $option : $value_settings['term_value'] );
                    switch ( $type ) {
                        case 'swatch_dropdown_to_label':
                            $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term_val ) . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . $term_val . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            break;
                        case 'swatch_dropdown_to_image':
                            if ( enwbvs_fs()->is_free_plan() ) {
                                $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term_val ) . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . $term_val . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            }
                            break;
                    }
                    
                    if ( $ctr_option >= $enwbvs_product_page_attribute_display_limit_settings && !is_product() && 0 !== (int) (count( $options ) - $enwbvs_product_page_attribute_display_limit_settings) ) {
                        $html .= '<li class="view-more ' . esc_attr( $selector_class ) . '-child"><div class="enwebyvs-variable-read-more"><span class="enwebyvs-variable-item-span"><a href="' . get_permalink( $product->get_id() ) . '"> + ' . (count( $options ) - $enwbvs_product_page_attribute_display_limit_settings) . ' ' . esc_html__( 'More', 'enweby-variation-swatches-for-woocommerce' ) . '</a></span></div></li>';
                        break;
                    }
                    
                    $ctr_option++;
                }
            }
        
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Swatch display html.
     *
     * @param  str   $html               html.
     * @param  array $args               argumentss.
     * @param  str   $type               type.
     * @param  str   $apply_auto_convert auto-convert value.
     * @return string.
     */
    public function swatch_display_options_html(
        $html,
        $args,
        $type,
        $apply_auto_convert
    )
    {
        $options = $args['options'];
        $product = $args['product'];
        $attribute = $args['attribute'];
        $name = ( $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute ) );
        $id = ( $args['id'] ? $args['id'] : sanitize_title( $attribute ) );
        $class = $args['class'];
        $selector_class = 'enwebyvs-attribute';
        $show_option_none = (bool) $args['show_option_none'];
        
        if ( empty($options) && !empty($product) && !empty($attribute) ) {
            $attributes = $product->get_variation_attributes();
            $options = $attributes[$attribute];
        }
        
        $html .= '<div class="enwebyvs-option-wrapaper cpid-' . $product->get_id() . '" >';
        $html .= '<ul data-rel-id="' . esc_attr( $id ) . '" data-rel-pid="' . esc_attr( $product->get_id() ) . '" class="' . esc_attr( $class ) . ' ' . esc_attr( $selector_class ) . ' swatch-type-' . $type . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . (( $show_option_none ? 'yes' : 'no' )) . '">';
        $enwbvs_product_page_attribute_display_limit = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'shop_archive_section_shop_archive', 'enwbvs-product-page-attribute-display-limit' );
        $enwbvs_product_page_attribute_display_limit_settings = ( isset( $enwbvs_product_page_attribute_display_limit ) && '' != $enwbvs_product_page_attribute_display_limit ? $enwbvs_product_page_attribute_display_limit : '0' );
        // phpcs:ignore
        $enwbvs_product_page_attribute_display_limit_settings = ( 0 === (int) $enwbvs_product_page_attribute_display_limit_settings ? '10000' : $enwbvs_product_page_attribute_display_limit_settings );
        $ctr_term = 1;
        $ctr_option = 1;
        if ( !empty($options) ) {
            
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms( $product->get_id(), $attribute, array(
                    'fields' => 'all',
                ) );
                foreach ( $terms as $term ) {
                    
                    if ( in_array( $term->slug, $options, true ) ) {
                        $term_name = esc_html( apply_filters(
                            'woocommerce_variation_option_name',
                            $term->name,
                            $term,
                            $attribute,
                            $product
                        ) );
                        $selected = selected( sanitize_title( $args['selected'] ), $term->slug, false );
                        $attr_class = preg_replace( '/[^A-Za-z0-9\\-\\_]/', '', $term->slug );
                        $tt_html = '';
                        $data_val = $term_name;
                        $html .= $this->config_swatch_item_type(
                            $id,
                            $product,
                            $name,
                            $attribute,
                            $term,
                            $attr_class,
                            $selector_class,
                            $selected,
                            $data_val,
                            $tt_html,
                            $type
                        );
                        
                        if ( $ctr_term >= $enwbvs_product_page_attribute_display_limit_settings && !is_product() && 0 !== (int) (count( $terms ) - $enwbvs_product_page_attribute_display_limit_settings) ) {
                            $html .= '<li class="view-more ' . esc_attr( $selector_class ) . '-child"><div class="enwebyvs-variable-read-more"><span class="enwebyvs-variable-item-span"><a href="' . get_permalink( $product->get_id() ) . '"> + ' . (count( $terms ) - $enwbvs_product_page_attribute_display_limit_settings) . ' ' . esc_html__( 'More', 'enweby-variation-swatches-for-woocommerce' ) . '</a></span></div></li>';
                            break;
                        }
                        
                        $ctr_term++;
                    }
                
                }
            } else {
                foreach ( $options as $option ) {
                    // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                    $selected = ( sanitize_title( $args['selected'] ) == $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false ) );
                    // phpcs:ignore
                    $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $option );
                    $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
                    $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
                    $custom_option_attr_settings = get_post_meta( $product->get_id(), 'enwbvs_custom_attribute_settings', true );
                    $term_settings = ( isset( $custom_option_attr_settings[sanitize_title( $attribute )] ) ? $custom_option_attr_settings[sanitize_title( $attribute )] : '' );
                    $value_settings = ( $term_settings && isset( $term_settings[$option] ) ? $term_settings[$option] : '' );
                    $term_val = ( empty($value_settings['term_value']) ? $option : $value_settings['term_value'] );
                    switch ( $type ) {
                        case 'label':
                            $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term_val ) . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . $term_val . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            break;
                        case 'color':
                            $term_color2 = ( empty($value_settings['term_color2']) ? '' : $value_settings['term_color2'] );
                            
                            if ( '' != $term_color2 ) {
                                $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $option ) . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper" ><span class="enwebyvs-variable-item-span" style="background: -webkit-linear-gradient(top left, ' . $term_val . ' 50%, ' . $term_color2 . ' 50%); background: -o-linear-gradient(top left, ' . $term_val . ' 50%, ' . $term_color2 . ' 50%); background: -moz-linear-gradient(top left, ' . $term_val . ' 50%, ' . $term_color2 . ' 50%); background: linear-gradient(top left, ' . $term_val . ' 50%, ' . $term_color2 . ' 50%);"><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                                    'woocommerce_variation_option_name',
                                    $option,
                                    null,
                                    $attribute,
                                    $product
                                ) ) . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            } else {
                                $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $option ) . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper" ><span class="enwebyvs-variable-item-span" style="background:' . $term_val . '"><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                                    'woocommerce_variation_option_name',
                                    $option,
                                    null,
                                    $attribute,
                                    $product
                                ) ) . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            }
                            
                            break;
                        case 'image':
                            $term_image_array = wp_get_attachment_image_src( $term_val, 'thumbnail' );
                            $term_image = ( $term_image_array ? $term_image_array[0] : plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' );
                            $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-attr-option-term-name="' . esc_attr( $option ) . '" data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text"><img alt="' . esc_html( apply_filters(
                                'woocommerce_variation_option_name',
                                $option,
                                null,
                                $attribute,
                                $product
                            ) ) . '" src="' . $term_image . '" /></span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            break;
                        case 'radio':
                            $checked = $selected;
                            $html .= '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-attr-option-term-name="' . esc_attr( $term_val ) . '" data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-value="' . esc_attr( $option ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $option . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                                'woocommerce_variation_option_name',
                                $option,
                                null,
                                $attribute,
                                $product
                            ) ) . '</span>
					<input type="radio" class="enwbvsfw-radio"   name="attribute_' . esc_attr( $id ) . '"  value="' . esc_attr( $option ) . '"  data-attribute_name="attribute_' . esc_attr( $id ) . '" data-value="' . esc_attr( $option ) . '" ' . $checked . '>' . esc_html( apply_filters(
                                'woocommerce_variation_option_name',
                                $option,
                                null,
                                $attribute,
                                $product
                            ) ) . '<span class="checkmark"></span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
                            break;
                    }
                    
                    if ( $ctr_option >= $enwbvs_product_page_attribute_display_limit_settings && !is_product() && 0 !== (int) (count( $options ) - $enwbvs_product_page_attribute_display_limit_settings) ) {
                        $html .= '<li class="view-more ' . esc_attr( $selector_class ) . '-child"><div class="enwebyvs-variable-read-more"><span class="enwebyvs-variable-item-span"><a href="' . get_permalink( $product->get_id() ) . '"> + ' . (count( $options ) - $enwbvs_product_page_attribute_display_limit_settings) . ' ' . esc_html__( 'More', 'enweby-variation-swatches-for-woocommerce' ) . '</a></span></div></li>';
                        break;
                    }
                    
                    $ctr_option++;
                }
            }
        
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Setting swatch item html.
     *
     * @param  int   $id             [description].
     * @param  int   $product        [description].
     * @param  str   $name           [description].
     * @param  array $attribute      [description].
     * @param  str   $term           [description].
     * @param  str   $attr_class     [description].
     * @param  str   $selector_class [description].
     * @param  str   $selected       [description].
     * @param  str   $data_val       [description].
     * @param  str   $tt_html        [description].
     * @param  str   $type           [description].
     * @return str                   [description].
     */
    public function config_swatch_item_type(
        $id,
        $product,
        $name,
        $attribute,
        $term,
        $attr_class,
        $selector_class,
        $selected,
        $data_val,
        $tt_html,
        $type
    )
    {
        switch ( $type ) {
            case 'color':
                $html = $this->html_color_swatch_display(
                    $id,
                    $product,
                    $name,
                    $attribute,
                    $term,
                    $attr_class,
                    $selector_class,
                    $selected,
                    $data_val,
                    $tt_html
                );
                break;
            case 'image':
                $html = $this->html_image_swatch_display(
                    $id,
                    $product,
                    $name,
                    $attribute,
                    $term,
                    $attr_class,
                    $selector_class,
                    $selected,
                    $data_val,
                    $tt_html
                );
                break;
            case 'label':
                $html = $this->html_label_swatch_display(
                    $id,
                    $product,
                    $name,
                    $attribute,
                    $term,
                    $attr_class,
                    $selector_class,
                    $selected,
                    $data_val,
                    $tt_html
                );
                break;
            case 'radio':
                $html = $this->html_radio_swatch_display(
                    $id,
                    $product,
                    $name,
                    $attribute,
                    $term,
                    $attr_class,
                    $selector_class,
                    $selected,
                    $data_val,
                    $tt_html
                );
                break;
        }
        return $html;
    }
    
    /**
     * Variation tooltip.
     *
     * @param  Obj $term term.
     * @return str
     */
    public function variation_tooltip( $term )
    {
        $term_tt_text = get_term_meta( $term->term_id, 'term_tt_text_' . $term->taxonomy, true );
        $term_tt_img = get_term_meta( $term->term_id, 'term_tt_img_' . $term->taxonomy, true );
        $tooltip = ( $term_tt_text && '' != $term_tt_text ? $term_tt_text : '' );
        // phpcs:ignore
        $tooltip .= ( $term_tt_img && '' != $term_tt_img ? '<span class="tooltip-image">' . wp_get_attachment_image( $term_tt_img, 'thumbnail' ) . '</span>' : '' );
        // phpcs:ignore
        return $tooltip;
    }
    
    /**
     * Color swatch display html generation.
     *
     * @param  int   $id             [description].
     * @param  int   $product        [description].
     * @param  str   $name           [description].
     * @param  array $attribute      [description].
     * @param  obj   $term           [description].
     * @param  str   $attr_class     [description].
     * @param  str   $selector_class [description].
     * @param  str   $selected       [description].
     * @param  str   $data_val       [description].
     * @param  str   $tt_html        [description].
     * @return str                   [description].
     */
    public function html_color_swatch_display(
        $id,
        $product,
        $name,
        $attribute,
        $term,
        $attr_class,
        $selector_class,
        $selected,
        $data_val,
        $tt_html
    )
    {
        $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $term->slug );
        $bg_color = get_term_meta( $term->term_id, 'product_' . $attribute, true );
        $bg_product_term_color2 = get_term_meta( $term->term_id, 'product_term_color2_' . $attribute, true );
        $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
        $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
        $term_name = esc_html( apply_filters(
            'woocommerce_variation_option_name',
            $term->name,
            $term,
            $attribute,
            $product
        ) );
        // Getting tooltip if set.
        $variation_tooltip = $this->variation_tooltip( $term );
        $tooltip = ( $variation_tooltip && '' != $variation_tooltip ? $variation_tooltip : $term_name );
        // phpcs:ignore
        
        if ( '' != $bg_product_term_color2 ) {
            $html = '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . '"  data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $tooltip . '</span></div><div class="enwebyvs-variable-item-wrapper" ><span class="enwebyvs-variable-item-span" style="background: -webkit-linear-gradient(top left, ' . $bg_color . ' 50%, ' . $bg_product_term_color2 . ' 50%); background: -o-linear-gradient(top left, ' . $bg_color . ' 50%, ' . $bg_product_term_color2 . ' 50%); background: -moz-linear-gradient(top left, ' . $bg_color . ' 50%, ' . $bg_product_term_color2 . ' 50%); background: linear-gradient(top left, ' . $bg_color . ' 50%, ' . $bg_product_term_color2 . ' 50%);" ><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                'woocommerce_variation_option_name',
                $term->name,
                $term,
                $attribute,
                $product
            ) ) . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
        } else {
            $html = '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . '" data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $tooltip . '</span></div><div class="enwebyvs-variable-item-wrapper" ><span class="enwebyvs-variable-item-span" style="background:' . $bg_color . '" ><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
                'woocommerce_variation_option_name',
                $term->name,
                $term,
                $attribute,
                $product
            ) ) . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
        }
        
        return $html;
    }
    
    /**
     * Generate image swatch html.
     *
     * @param  int   $id             [description].
     * @param  int   $product        [description].
     * @param  str   $name           [description].
     * @param  array $attribute      [description].
     * @param  obj   $term           [description].
     * @param  str   $attr_class     [description].
     * @param  str   $selector_class [description].
     * @param  str   $selected       [description].
     * @param  str   $data_val       [description].
     * @param  str   $tt_html        [description].
     * @return str                   [description].
     */
    public function html_image_swatch_display(
        $id,
        $product,
        $name,
        $attribute,
        $term,
        $attr_class,
        $selector_class,
        $selected,
        $data_val,
        $tt_html
    )
    {
        $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $term->slug );
        $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
        $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
        $image_id = get_term_meta( $term->term_id, 'product_' . $attribute, true );
        $image_array = wp_get_attachment_image_src( $image_id, 'thumbnail' );
        $image = ( $image_array ? $image_array[0] : plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' );
        $term_name = esc_html( apply_filters(
            'woocommerce_variation_option_name',
            $term->name,
            $term,
            $attribute,
            $product
        ) );
        // Getting tooltip if set.
        $variation_tooltip = $this->variation_tooltip( $term );
        $tooltip = ( $variation_tooltip && '' != $variation_tooltip ? $variation_tooltip : $term_name );
        // phpcs:ignore
        $html = '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . $selected . '"><div class="enwbvs-tooltip"><span class="tooltiptext">' . $tooltip . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text"><img alt="' . esc_html( apply_filters(
            'woocommerce_variation_option_name',
            $term->name,
            $term,
            $attribute,
            $product
        ) ) . '" src="' . $image . '" /></span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
        return $html;
    }
    
    /**
     * Generating label swatch html.
     *
     * @param  int   $id             [description].
     * @param  int   $product        [description].
     * @param  str   $name           [description].
     * @param  array $attribute      [description].
     * @param  obj   $term           [description].
     * @param  str   $attr_class     [description].
     * @param  str   $selector_class [description].
     * @param  str   $selected       [description].
     * @param  str   $data_val       [description].
     * @param  str   $tt_html        [description].
     * @return str                   [description].
     */
    public function html_label_swatch_display(
        $id,
        $product,
        $name,
        $attribute,
        $term,
        $attr_class,
        $selector_class,
        $selected,
        $data_val,
        $tt_html
    )
    {
        $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $term->slug );
        $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
        $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
        $term_name = esc_html( apply_filters(
            'woocommerce_variation_option_name',
            $term->name,
            $term,
            $attribute,
            $product
        ) );
        // Getting tooltip if set.
        $variation_tooltip = $this->variation_tooltip( $term );
        $tooltip = ( $variation_tooltip && '' != $variation_tooltip ? $variation_tooltip : $term_name );
        // phpcs:ignore
        // Getting label.
        $value = get_term_meta( $term->term_id, 'product_' . $term->taxonomy, true );
        $value = ( $value && '' != $value ? $value : $term_name );
        // phpcs:ignore
        $html = '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $tooltip . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . $value . '</span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
        return $html;
    }
    
    /**
     * Generating radio swatch html.
     *
     * @param  int   $id             [description].
     * @param  int   $product        [description].
     * @param  str   $name           [description].
     * @param  array $attribute      [description].
     * @param  obj   $term           [description].
     * @param  str   $attr_class     [description].
     * @param  str   $selector_class [description].
     * @param  str   $selected       [description].
     * @param  str   $data_val       [description].
     * @param  str   $tt_html        [description].
     * @return str                   [description].
     */
    public function html_radio_swatch_display(
        $id,
        $product,
        $name,
        $attribute,
        $term,
        $attr_class,
        $selector_class,
        $selected,
        $data_val,
        $tt_html
    )
    {
        $enwbvs_variation_data = $this->enwbvs_get_variations_data( $product, $name, $term->slug );
        $class_out_of_stock_state = $enwbvs_variation_data['stock_status'];
        $stock_left_alert = $enwbvs_variation_data['stock_left_text'];
        $checked = $selected;
        $html = '<li class="' . esc_attr( $selector_class ) . '-child ' . $class_out_of_stock_state . ' " data-item-variation="' . $enwbvs_variation_data['item_variation_data'] . '" data-attr-option-term-name="' . esc_attr( $term->name ) . '" data-attr-option-value="' . esc_attr( $term->slug ) . '" ' . $selected . '><div class="enwbvs-tooltip"><span class="tooltiptext">' . $term->name . '</span></div><div class="enwebyvs-variable-item-wrapper"><span class="enwebyvs-variable-item-span"><span class="enwebyvs-variable-item-span-text">' . esc_html( apply_filters(
            'woocommerce_variation_option_name',
            $term->name,
            $term,
            $attribute,
            $product
        ) ) . '</span><input type="radio" class="enwbvsfw-radio"   name="attribute_' . esc_attr( $id ) . '"  value="' . esc_attr( $term->name ) . '"  data-attribute_name="attribute_' . esc_attr( $id ) . '" data-value="' . esc_attr( $term->name ) . '" ' . $checked . '>' . esc_html( apply_filters(
            'woocommerce_variation_option_name',
            $term->name,
            null,
            $attribute,
            $product
        ) ) . '<span class="checkmark"></span><span class="enwbvs-stock-left-alert">' . $stock_left_alert . '</span></span></div></li>';
        return $html;
    }
    
    /**
     * Function that will get variations data.
     *
     * @param  id  $product   produt id.
     * @param  str $name      product name.
     * @param  str $term_slug slug.
     * @return array.
     */
    public function enwbvs_get_variations_data( $product, $name, $term_slug )
    {
        
        if ( enwbvs_fs()->is_free_plan() ) {
            $enwbvs_disable_outofstock = 0;
            /*default*/
            $enwbvs_clickable_outofstock = 1;
            /*default*/
        }
        
        $enwbvs_show_stock_left_label_var = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'advanced_section_advanced', 'enwbvs-show-stock-left-label' );
        $enwbvs_show_stock_left_label = ( isset( $enwbvs_show_stock_left_label_var ) && '' != $enwbvs_show_stock_left_label_var ? $enwbvs_show_stock_left_label_var : '1' );
        // phpcs:ignore
        $enwbvs_minimum_qty_to_show_stock_left = $this->enweby_plugin_settings->wpsf_get_setting( ENWEBY_VARIATION_SWATCHES_FWAS, 'advanced_section_advanced', 'enwbvs-minimum-qty-to-show-stock-left' );
        $variation_data = array();
        $stock = '';
        $stock_qty = '';
        $variation_image_id = '';
        $item_variation_data = array();
        $disabled_attribute = 'attr-option-disabled attr-option-disabled-real';
        $number_of_attributes = '1';
        foreach ( $product->get_available_variations() as $variation ) {
            
            if ( $variation['attributes'][$name] == $term_slug ) {
                // phpcs:ignore
                $stock = $variation['is_in_stock'];
                $variation_data = wc_get_product( $variation['variation_id'] );
                $stock_qty = $variation_data->get_stock_quantity();
                $variation_image_id = ( '' != $variation['image_id'] ? $variation['image_id'] : '' );
                // phpcs:ignore
                $disabled_attribute = '';
                $stock_left_text_item = '';
                $item_variation_data[] = $variation['variation_id'] . '_' . $stock_left_text_item;
                $number_of_attributes = count( $variation['attributes'] );
            }
        
        }
        //print_r($item_variation_data);
        // enable this code only if by on page requires stock status and disable below code, needs to be tested.
        
        if ( 1 === (int) $enwbvs_disable_outofstock && 1 == $number_of_attributes ) {
            
            if ( 1 === (int) $enwbvs_clickable_outofstock ) {
                $stock_status = ( 1 === (int) $stock ? '' : 'out-of-stock-swatch-item' );
            } else {
                $stock_status = ( 1 === (int) $stock ? '' : 'out-of-stock-swatch-item click-disabled-outofstock' );
            }
        
        } else {
            $stock_status = '';
        }
        
        // phpcs:ignore
        // $stock_status = ''; //Forcing each swatch option enabled on page load. This is an altrnate of above code.
        $stock_left_text = '';
        $variation_data = array(
            'stock_status'        => $stock_status . " " . $disabled_attribute,
            'variation_image_id'  => $variation_image_id,
            'stock_left_text'     => $stock_left_text,
            'item_variation_data' => implode( ',', $item_variation_data ),
        );
        return $variation_data;
    }
    
    /**
     * Render swatch value in layered nav.
     *
     * @param string     $term_html Term HTML.
     * @param object     $term      Term.
     * @param string     $link      Link.
     * @param string|int $count     Count.
     *
     * @return string
     */
    public function enwbvs_layered_nav_term_html(
        $term_html,
        $term,
        $link,
        $count
    )
    {
        $html = $term_html;
        return $html;
    }

}