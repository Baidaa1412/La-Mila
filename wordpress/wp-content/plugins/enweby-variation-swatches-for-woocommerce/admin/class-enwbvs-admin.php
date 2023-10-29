<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.enweby.com/
 * @since      1.0.0
 *
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/admin
 * @author     Enweby <support@enweby.com>
 */
class Enwbvs_Admin
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
     * Setting admin settings
     *
     * @var array.
     */
    private  $enweby_admin_settings ;
    /**
     * Common Functions.
     *
     * @var array.
     */
    private  $enwbvs_common_functions ;
    /**
     * Setting toxonomy property.
     *
     * @var array|string.
     */
    private  $attr_taxonomies ;
    /**
     * Setting property.
     *
     * @var array|string.
     */
    private  $product_attr_type ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name       The name of this plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->add_setting_framework();
        $this->enwbvs_get_common();
        $this->admin_hooks();
        $this->load_notices_files();
    }
    
    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/enwbvs-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Load core files.
     */
    public function load_notices_files()
    {
        if ( is_admin() ) {
            require_once plugin_dir_path( __DIR__ ) . 'admin/lib/enwb-notices/class-enwb-vs-notices.php';
        }
        include_once plugin_dir_path( __FILE__ ) . 'class-enwb-vs-admin-notices.php';
    }
    
    /**
     * Action links for admin.
     *
     * @param  array $links Array of action links.
     * @return array
     */
    public function plugin_action_links( $links )
    {
        $settings_link = esc_url( add_query_arg( array(
            'page' => 'enweby-variation-swatches-for-woocommerce-settings',
        ), admin_url( 'admin.php' ) ) );
        $new_links['settings'] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', $settings_link, esc_attr__( 'Settings', 'enweby-variation-swatches-for-woocommerce' ) );
        return array_merge( $links, $new_links );
    }
    
    /**
     * Plugin row meta.
     *
     * @param  array  $links array of row meta.
     * @param  string $file  plugin base name.
     * @return array
     */
    public function plugin_row_meta( $links, $file )
    {
        // phpcs:ignore
        
        if ( $file == ENWEBY_VARIATION_SWATCHES_FOR_WOOCOMMERCE_BASE_NAME ) {
            $report_url = add_query_arg( array(
                'utm_source'   => 'wp-admin-plugins',
                'utm_medium'   => 'row-meta-link',
                'utm_campaign' => 'enweby-variation-swatches-for-woocommerce',
            ), 'https://www.enweby.com/product/variation-swatches-for-woocommerce#support/' );
            $documentation_url = add_query_arg( array(
                'utm_source'   => 'wp-admin-plugins',
                'utm_medium'   => 'row-meta-link',
                'utm_campaign' => 'enweby-variation-swatches-for-woocommerce',
            ), 'https://www.enweby.com/product/variation-swatches-for-woocommerce#documentation/' );
            $row_meta['documentation'] = sprintf( '<a target="_blank" href="%1$s" title="%2$s">%2$s</a>', esc_url( $documentation_url ), esc_html__( 'Documentation', 'enweby-variation-swatches-for-woocommerce' ) );
            // phpcs:ignore
            $row_meta['issues'] = sprintf(
                '%2$s <a target="_blank" href="%1$s">%3$s</a>',
                esc_url( $report_url ),
                esc_html__( '', 'enweby-variation-swatches-for-woocommerce' ),
                '<span style="color: #45b450;font-weight:bold;">' . esc_html__( 'Get Free Support', 'enweby-variation-swatches-for-woocommerce' ) . '</span>'
            );
            return array_merge( $links, $row_meta );
        }
        
        return (array) $links;
    }
    
    /**
     * Register the JavaScript for the admin area.
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
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/enwbvs-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        $enwbvs_var = array(
            'placeholder_image' => esc_url( plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' ),
            'upload_image'      => esc_url( plugin_dir_url( __FILE__ ) . 'images/upload.svg' ),
            'remove_image'      => esc_url( plugin_dir_url( __FILE__ ) . 'images/remove.svg' ),
            'admin_url'         => admin_url(),
            'admin_path'        => plugins_url( '/', __FILE__ ),
            'ajaxurl'           => admin_url( 'admin-ajax.php' ),
        );
        wp_localize_script( $this->plugin_name, 'enwbvs_var', $enwbvs_var );
    }
    
    /**
     * Enqueue scripts and styles.
     */
    public function admin_enqueue_scripts()
    {
        // Load Color Picker if required.
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_media();
    }
    
    /**
     *  Initialize admin hooks.
     */
    public function admin_hooks()
    {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enwbvs_snippet_premium' ), 20 );
        add_filter( 'product_attributes_type_selector', array( $this, 'add_attribute_types' ) );
        // Add an optional settings validation filter (recommended).
        add_filter( ENWEBY_VARIATION_SWATCHES_FWAS . '_settings_validate', array( &$this, 'enwbvs_validate_settings' ) );
        add_action( 'woocommerce_init', array( $this, 'add_custom_fields_to_attribute' ) );
        add_action(
            'created_term',
            array( $this, 'save_term_meta' ),
            10,
            3
        );
        add_action(
            'edit_term',
            array( $this, 'save_term_meta' ),
            10,
            3
        );
        add_action(
            'woocommerce_product_option_terms',
            array( $this, 'enwbvs_product_option_terms' ),
            20,
            2
        );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'new_tabs_for_swatches_settings' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'output_custom_swatches_settings' ) );
        add_action(
            'woocommerce_process_product_meta',
            array( $this, 'save_custom_fields' ),
            10,
            2
        );
        //For page attributte meta box
        add_action( 'page_attributes_misc_attributes', array( $this, 'enwbvs_is_custom_shop_page_attributes' ) );
        add_action( 'save_post', array( $this, 'enwbvs_is_custom_shop_page_metabox_save_post' ) );
        // Plugin Row Meta.
        add_action( 'plugin_action_links_' . ENWEBY_VARIATION_SWATCHES_FOR_WOOCOMMERCE_BASE_NAME, array( $this, 'plugin_action_links' ) );
        add_action(
            'plugin_row_meta',
            array( $this, 'plugin_row_meta' ),
            10,
            2
        );
    }
    
    /**
     * Adding style and script for premimum version
     *
     * @since    1.0.0
     */
    public function enwbvs_snippet_premium()
    {
    }
    
    /**
     * Adding Custom Shop Option as Page Attibute.
     */
    function enwbvs_is_custom_shop_page_attributes( $post )
    {
        wp_nonce_field( 'enwbvs_is_custom_shop_page_attributes_nonce', 'enwbvs_is_custom_shop_page_attributes_nonce' );
        ?>
		<p class="post-attributes-label-wrapper">
			<label class="enwbvs-is-custom-shop-page-label" for="enwbvs_is_custom_shop_page_option" style="font-weight: 600;"><?php 
        _e( 'It is Custom Shop/Product list page', 'enweby-variation-swatches-for-woocommerce' );
        ?></label>
			<input id="enwbvs_is_custom_shop_page_option" name="enwbvs_is_custom_shop_page_option" type="checkbox" value="1" style="margin-left: 5px;"<?php 
        if ( isset( $post->enwbvs_is_custom_shop_page_option ) && $post->enwbvs_is_custom_shop_page_option ) {
            echo  ' checked="checked"' ;
        }
        ?>>
		</p>
		<?php 
    }
    
    /**
     * Saving Custom Shop Option Page Attibute.
     */
    function enwbvs_is_custom_shop_page_metabox_save_post( $post_id )
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( !isset( $_POST['enwbvs_is_custom_shop_page_attributes_nonce'] ) || !wp_verify_nonce( $_POST['enwbvs_is_custom_shop_page_attributes_nonce'], 'enwbvs_is_custom_shop_page_attributes_nonce' ) ) {
            return;
        }
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $enwbvs_is_custom_shop_page_option = (int) (isset( $_POST['enwbvs_is_custom_shop_page_option'] ) && $_POST['enwbvs_is_custom_shop_page_option']);
        update_post_meta( $post_id, 'enwbvs_is_custom_shop_page_option', $enwbvs_is_custom_shop_page_option );
    }
    
    /**
     * Validate settings.
     *
     * @param  array $input input value.
     * @return mixed.
     */
    public function enwbvs_validate_settings( $input )
    {
        // Do your settings validation here.
        // Same as $sanitize_callback from http://codex.wordpress.org/Function_Reference/register_setting.
        foreach ( $input as $key => $value ) {
            $field_type_array = $this->get_field_type( $key );
            $field_type = $field_type_array['field_type'];
            $input[$key] = $this->process_validate_settings( $field_type, $value );
        }
        return $input;
    }
    
    /**
     * Getting field type.
     *
     * @param str $input_key input_key.
     * @return mixed.
     */
    public function get_field_type( $input_key )
    {
        $fields_array = array();
        $field_type = array();
        // default type.
        $admin_settings_array = (array) $this->enweby_admin_settings;
        foreach ( $admin_settings_array as $item ) {
            if ( isset( $item['sections'] ) ) {
                foreach ( $item['sections'] as $field_part ) {
                    foreach ( $field_part['fields'] as $field ) {
                        // phpcs:disable
                        if ( $input_key == $field_part['tab_id'] . '_' . $field_part['section_id'] . '_' . $field['id'] ) {
                            $field_type = array(
                                'field_type' => $field['type'],
                                'field'      => $field_part['tab_id'] . '_' . $field_part['section_id'] . '_' . $field['id'],
                            );
                        }
                        // phpcs:enable
                    }
                }
            }
        }
        return $field_type;
    }
    
    /**
     * Process validate settings.
     *
     * @param  string $field_type [description].
     * @param  string $value      [description].
     * @return string             [description].
     */
    public function process_validate_settings( $field_type, $value )
    {
        switch ( $field_type ) {
            case 'text':
                $value = $this->sanitize_text( $value );
                break;
            case 'color':
                $value = $this->sanitize_color( $value );
                break;
            case 'number':
                $value = $this->sanitize_number( $value );
                break;
            case 'toggle':
                $value = $this->sanitize_number( $value );
                break;
            case 'checkboxes':
                $value = $this->sanitize_checkboxes( $value );
                break;
            case 'radio':
                $value = $this->sanitize_text( $value );
                break;
            case 'code_editor':
                $value = $this->sanitize_editor( $value );
                break;
            case 'htmltext':
                $value = $this->sanitize_editor( $value );
                break;
            case 'textarea':
                $value = $this->sanitize_textarea( $value );
                break;
        }
        return $value;
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_text( $value )
    {
        return ( !empty($value) ? sanitize_text_field( $value ) : '' );
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_number( $value )
    {
        return ( is_numeric( $value ) ? $value : 0 );
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_editor( $value )
    {
        return wp_kses_post( $value );
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_textarea( $value )
    {
        return sanitize_textarea_field( $value );
    }
    
    // phpcs:disable
    /* public function sanitize_checkbox( $value ) {
    		return ( '1' === $value ) ? 1 : 0;
    	}*/
    // phpcs:enable
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_checkboxes( $value )
    {
        
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $item ) {
                $value[$key] = $this->sanitize_text( $item );
            }
        } else {
            $value = $this->sanitize_text( $value );
        }
        
        return $value;
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_select( $value )
    {
        return $this->sanitize_text( $value );
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_radio( $value )
    {
        return $this->sanitize_text( $value );
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_color( $value )
    {
        return sanitize_hex_color( $value );
    }
    
    /**
     * Sanitizing value.
     *
     * @param  string $value field value.
     * @return string.
     */
    public function sanitize_url( $value )
    {
        return esc_url_raw( $value );
    }
    
    /**
     * Getting common functions.
     */
    public function enwbvs_get_common()
    {
        require_once WP_PLUGIN_DIR . '/enweby-variation-swatches-for-woocommerce/includes/class-enwbvs-common.php';
        $this->enwbvs_common_functions = new Enwbvs_Common();
    }
    
    /**
     * Including admin settings framework.
     *
     * @since    1.0.0
     */
    public function add_setting_framework()
    {
        require_once plugin_dir_path( __FILE__ ) . 'admin-framework/framework/class-wp-settings-framework.php';
        $this->enweby_admin_settings = new \Enwbvs\Enweby\SettingsFramework\WordPressSettingsFramework( plugin_dir_path( __FILE__ ) . 'admin-framework/admin-settings.php', ENWEBY_VARIATION_SWATCHES_FWAS );
    }
    
    /**
     * Admin Settings.
     */
    public function add_settings_page()
    {
        $this->enweby_admin_settings->add_settings_page( array(
            'page_title' => __( 'Enweby Variation Swatches for WoocCommerce', 'enweby-variation-swatches-for-woocommerce' ),
            'menu_title' => __( 'Variation Swatches', 'enweby-variation-swatches-for-woocommerce' ),
            'capability' => 'manage_options',
        ) );
    }
    
    /**
     * Adding new attribute.
     *
     * @param array $types type array.
     */
    public function add_attribute_types( $types )
    {
        return $this->enwbvs_common_functions->add_attribute_types( $types );
    }
    
    /**
     * Add custom fields to atttributes.
     */
    public function add_custom_fields_to_attribute()
    {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $this->attr_taxonomies = $attribute_taxonomies;
        foreach ( $attribute_taxonomies as $tax ) {
            $this->product_attr_type = $tax->attribute_type;
            add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
            add_action(
                'pa_' . $tax->attribute_name . '_edit_form_fields',
                array( $this, 'edit_attribute_fields' ),
                10,
                2
            );
            add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array( $this, 'add_attribute_column' ) );
            add_filter(
                'manage_pa_' . $tax->attribute_name . '_custom_column',
                array( $this, 'add_attribute_column_content' ),
                10,
                3
            );
        }
    }
    
    /**
     * Add attribute fields.
     *
     * @param str $taxonomy attribute type.
     */
    public function add_attribute_fields( $taxonomy )
    {
        $attribute_type = $this->get_attribute_type( $taxonomy );
        $this->product_attribute_fields(
            $taxonomy,
            $attribute_type,
            'new',
            'add'
        );
    }
    
    /**
     * Edit_attribute_fields description.
     *
     * @param  obj $term     term.
     * @param  str $taxonomy Taxonomy.
     * @return mixed.
     */
    public function edit_attribute_fields( $term, $taxonomy )
    {
        $attribute_type = $this->get_attribute_type( $taxonomy );
        $term_fields = array();
        $term_type_field = get_term_meta( $term->term_id, 'product_' . $taxonomy, true );
        $term_fields = array(
            'term_type_field' => ( $term_type_field ? $term_type_field : '' ),
        );
        //extra fields since version 1.0.4
        $extra_term_fields = array();
        $extra_term_fields['product_term_color2'] = get_term_meta( $term->term_id, 'product_term_color2_' . $taxonomy, true );
        // tooltip text and image.
        $tool_tip_array = array();
        $term_tt_text_field = get_term_meta( $term->term_id, 'term_tt_text_' . $taxonomy, true );
        $tool_tip_array['term_tt_text_field'] = ( $term_tt_text_field ? $term_tt_text_field : '' );
        $term_tt_img_field = get_term_meta( $term->term_id, 'term_tt_img_' . $taxonomy, true );
        $tool_tip_array['term_tt_img_field'] = ( $term_tt_img_field ? $term_tt_img_field : '' );
        $term_fields = array_merge( $term_fields, $extra_term_fields, $tool_tip_array );
        $this->product_attribute_fields(
            $taxonomy,
            $attribute_type,
            $term_fields,
            'edit'
        );
    }
    
    /**
     * Geting attribute type.
     *
     * @param  str $taxonomy toxonomy.
     * @return mixed.
     */
    public function get_attribute_type( $taxonomy )
    {
        foreach ( $this->attr_taxonomies as $tax ) {
            // phpcs:disable
            
            if ( 'pa_' . $tax->attribute_name == $taxonomy ) {
                return $tax->attribute_type;
                break;
            }
            
            // phpcs:enable
        }
    }
    
    /**
     * Setting product attribute fields.
     *
     * @param  str $taxonomy [description].
     * @param  str $type     [description].
     * @param  str $value    [description].
     * @param  str $form     [description].
     */
    public function product_attribute_fields(
        $taxonomy,
        $type,
        $value,
        $form
    )
    {
        switch ( $type ) {
            case 'color':
                $this->add_color_field( $value, $taxonomy );
                // tooltip text and image.
                $this->add_tooltip_image_field( $value, $taxonomy );
                $this->add_tooltip_text( $value, $taxonomy );
                break;
            case 'image':
                $this->add_image_field( $value, $taxonomy );
                // tooltip text and image.
                $this->add_tooltip_image_field( $value, $taxonomy );
                $this->add_tooltip_text( $value, $taxonomy );
                break;
            case 'label':
                $this->add_label_field( $value, $taxonomy );
                // tooltip text and image.
                $this->add_tooltip_image_field( $value, $taxonomy );
                $this->add_tooltip_text( $value, $taxonomy );
                break;
            default:
                break;
        }
    }
    
    /**
     * Adding color_field.
     *
     * @param array|str $value    [description].
     * @param str       $taxonomy [description].
     */
    private function add_color_field( $value, $taxonomy )
    {
        $term_type_field = ( is_array( $value ) && $value['term_type_field'] ? $value['term_type_field'] : '' );
        $label = __( 'Term Color', 'enweby-variation-swatches-for-woocommerce' );
        $product_term_color2 = ( is_array( $value ) && $value['product_term_color2'] ? $value['product_term_color2'] : '' );
        $color_type = ( '' != $product_term_color2 ? 'dual' : 'single' );
        $color_type_extra = ( '' != $product_term_color2 ? 'Dual' : 'Single' );
        $label_color_type = __( 'Color Type', 'enweby-variation-swatches-for-woocommerce' );
        
        if ( 'new' == $value ) {
            ?>
			<div class="enwbvs-types gbl-attr-color gbl-attr-terms gbl-attr-terms-new">
				<label><?php 
            echo  esc_html( $label ) ;
            ?></label>
				<div class="enwbvs_settings_fields_form enwbvsfw-col-div">
					<span class="enwbvs-admin-colorpickpreview color_preview"></span>
					<input type="text" name= "<?php 
            echo  'product_' . esc_attr( $taxonomy ) ;
            ?>" class="enwbvs-admin-colorpick"/>
					<?php 
            ?>	
				</div>
			</div>

			<?php 
        } else {
            ?>
			<?php 
            ?>
			<tr class="gbl-attr-terms gbl-attr-terms-edit" > 
				<th><?php 
            echo  esc_html( $label ) ;
            ?></th>
				<td>
					<div class="enwbvs_settings_fields_form enwbvsfw-col-div">
						<span class="enwbvs-admin-colorpickpreview color_preview" style="background:<?php 
            echo  esc_attr( $term_type_field ) ;
            ?>;"></span>						
						<input type="text"  name= "<?php 
            echo  'product_' . esc_attr( $taxonomy ) ;
            ?>" class="enwbvs-admin-colorpick term-color1" data-alpha="true" value="<?php 
            echo  esc_attr( $term_type_field ) ;
            ?>"/>
						
						<?php 
            ?>
					</div>         
				</td>
			</tr>
			<?php 
        }
    
    }
    
    /**
     * Add image field.
     *
     * @param array|str $value  [description].
     * @param str       $taxonomy [description].
     */
    private function add_image_field( $value, $taxonomy )
    {
        $image = ( is_array( $value ) && $value['term_type_field'] ? wp_get_attachment_image_src( $value['term_type_field'] ) : '' );
        $image = ( $image ? $image[0] : plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' );
        $label = __( 'Image', 'enweby-variation-swatches-for-woocommerce' );
        
        if ( 'new' == $value ) {
            // phpcs:ignore
            ?>
			<div class="enwbvs-types gbl-attr-img gbl-attr-terms gbl-attr-terms-new">
				<div class='enwbvs-upload-image'>
					<label><?php 
            echo  esc_html( $label ) ;
            ?></label>
					<div class="enwbvs-term-image-thumbnail">
						<img class="i_index_media_img" src="<?php 
            echo  esc_url( $image ) ;
            ?>" width="50px" height="50px" alt="term-image"/>  					</div>
					<div style="line-height:60px;">
						<input type="hidden" class="i_index_media" name="product_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="">
		   
						<button type="button" class="enwbvs-upload-image-button button " onclick="enwbvs_upload_icon_image(this,event)">
							<img class="enwbvs-upload-button" src="<?php 
            echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/upload.svg' ) ;
            ?>" alt="upload-button">
						</button>

						<button type="button" style="display:none" class="enwbvs_remove_image_button button " onclick="enwbvs_remove_icon_image(this,event)">
							<img class="enwbvs-remove-button" src="<?php 
            echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/remove.svg' ) ;
            ?>" alt="remove-button">
						</button>
					</div>
				</div>
			</div>
			<?php 
        } else {
            ?>
			<tr class="form-field gbl-attr-img gbl-attr-terms gbl-attr-terms-edit">
				<th><?php 
            echo  esc_html( $label ) ;
            ?></th>
				<td>
					<div class = 'enwbvs-upload-image'>
						<div class="enwbvs-term-image-thumbnail">
							<img  class="i_index_media_img" src="<?php 
            echo  esc_url( $image ) ;
            ?>" width="50px" height="50px" alt="term-image"/>  						</div>
						<div style="line-height:60px;">
							<input type="hidden" class="i_index_media"  name= "product_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="<?php 
            echo  esc_attr( $value['term_type_field'] ) ;
            ?>">
			   
							<button type="button" class="enwbvs-upload-image-button  button" onclick="enwbvs_upload_icon_image(this,event)">
								<img class="enwbvs-upload-button" src="<?php 
            echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/upload.svg' ) ;
            ?>" alt="upload-button">                             
							</button>
							<?php 
            
            if ( '' != $value['term_type_field'] ) {
                // phpcs:ignore
                ?>
							<button type="button" style="<?php 
                echo  ( is_array( $value ) && $value['term_type_field'] ? '' : 'display:none' ) ;
                ?> "  class="enwbvs_remove_image_button button " onclick="enwbvs_remove_icon_image(this,event)">
								<img class="enwbvs-remove-button" src="<?php 
                echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/remove.svg' ) ;
                ?>" alt="remove-button">
							</button>
							<?php 
            }
            
            ?>
						</div>
					</div>
				</td>
			</tr> 
			<?php 
        }
    
    }
    
    /**
     * Adding label field.
     *
     * @param array|str $value    [description].
     * @param str       $taxonomy       [description].
     */
    public function add_label_field( $value, $taxonomy )
    {
        $label = __( 'Term Label', 'enweby-variation-swatches-for-woocommerce' );
        
        if ( 'new' == $value ) {
            // phpcs:ignore
            ?>
			<div class="enwbvs-types gbl-attr-label gbl-attr-terms gbl-attr-terms-new">
				<label><?php 
            echo  esc_html( $label ) ;
            ?></label> 
				<input type="text" class="i_label" name="product_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="" />
			</div>
			<?php 
        } else {
            ?>
			<tr class="form-field gbl-attr-label gbl-attr-terms gbl-attr-terms-edit" > 
				<th><?php 
            echo  esc_html( $label ) ;
            ?></th>
				<td>
					<input type="text" class="i_label" name="product_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="<?php 
            echo  esc_attr( $value['term_type_field'] ) ;
            ?>" />
				</td>
			</tr> 
			<?php 
        }
    
    }
    
    /**
     * Adding tooltip image field.
     *
     * @param array|str $value    [description].
     * @param str       $taxonomy       [description].
     */
    private function add_tooltip_image_field( $value, $taxonomy )
    {
        $image = ( is_array( $value ) && $value['term_tt_img_field'] ? wp_get_attachment_image_src( $value['term_tt_img_field'] ) : '' );
        $image = ( $image ? $image[0] : plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' );
        $label = __( 'Tooltip Image', 'enweby-variation-swatches-for-woocommerce' );
        $pro_feature_class = 'pro-feature-row';
        $pro_feature_locked_class = 'locked-icon';
        $pro_feature_text = '( Pro Feature )';
        
        if ( 'new' == $value ) {
            // phpcs:ignore
            ?>
			<div class="enwbvs-types gbl-attr-img gbl-attr-terms gbl-attr-terms-new <?php 
            echo  esc_attr( $pro_feature_class ) ;
            ?>">
				<div class='enwbvs-upload-image'>
					<label><?php 
            echo  esc_html( $label ) . ' <span class="hint">' . esc_html( $pro_feature_text ) . '</span>' ;
            ?></label>
					<div class="enwbvs-term-image-thumbnail <?php 
            echo  esc_attr( $pro_feature_locked_class ) ;
            ?>">
						<img class="i_index_media_img" src="<?php 
            echo  esc_url( $image ) ;
            ?>" width="50px" height="50px" alt="term-tooltip-image"/>  					</div>
					<div style="line-height:60px;">
						<input type="hidden" class="i_index_media" name="term_tt_img_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="">
						<?php 
            ?>
						<?php 
            
            if ( enwbvs_fs()->is_free_plan() ) {
                ?>
						<button type="button">
							<img class="enwbvs-upload-button" src="<?php 
                echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/upload.svg' ) ;
                ?>" alt="upload-button">
						</button>
						
						<?php 
            }
            
            ?>
						<button type="button" style="display:none" class="enwbvs_remove_image_button button " onclick="enwbvs_remove_icon_image(this,event)">
							<img class="enwbvs-remove-button" src="<?php 
            echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/remove.svg' ) ;
            ?>" alt="remove-button">
						</button>
					</div>
				</div>
			</div>
			<?php 
        } else {
            ?>
			<tr class="form-field gbl-attr-img gbl-attr-terms gbl-attr-terms-edit pro-feature-row">
				<th><?php 
            echo  esc_attr( $label ) . ' <span class="hint">' . esc_html( $pro_feature_text ) . '</span>' ;
            ?></th>
				<td>
					<div class = 'enwbvs-upload-image'>
						<div class="enwbvs-term-image-thumbnail">
							<img  class="i_index_media_img" src="<?php 
            echo  esc_url( $image ) ;
            ?>" width="50px" height="50px" alt="tooltip-term-image"/>  						</div>
						<div style="line-height:60px;">
							<input type="hidden" class="i_index_media"  name= "term_tt_img_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="<?php 
            echo  esc_attr( $value['term_tt_img_field'] ) ;
            ?>">
							<?php 
            ?>
							<?php 
            
            if ( enwbvs_fs()->is_free_plan() ) {
                ?>
							<button type="button" class="button">
								<img class="enwbvs-upload-button" src="<?php 
                echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/upload.svg' ) ;
                ?>" alt="upload-button">                             
							</button>
							<?php 
            }
            
            ?>
							<?php 
            
            if ( '' != $value['term_tt_img_field'] ) {
                // phpcs:ignore
                ?>
							<button type="button" style="<?php 
                echo  ( is_array( $value ) && $value['term_tt_img_field'] ? '' : 'display:none' ) ;
                ?> "  class="enwbvs_remove_image_button button " onclick="enwbvs_remove_icon_image(this,event)">
								<img class="enwbvs-remove-button" src="<?php 
                echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/remove.svg' ) ;
                ?>" alt="remove-button">
							</button>
							<?php 
            }
            
            ?>
						</div>
					</div>
				</td>
			</tr> 
			<?php 
        }
    
    }
    
    /**
     * Adding tooltip text.
     *
     * @param array|str $value    [description].
     * @param str       $taxonomy     [description].
     */
    public function add_tooltip_text( $value, $taxonomy )
    {
        $label = __( 'Tooltip Text', 'enweby-variation-swatches-for-woocommerce' );
        
        if ( 'new' == $value ) {
            // phpcs:ignore
            ?>
			<div class="enwbvs-types gbl-attr-label gbl-attr-terms gbl-attr-terms-new">
				<label><?php 
            echo  esc_html( $label ) ;
            ?></label> 
				<input type="text" class="i_label" name="term_tt_text_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="" />
			</div>
			<?php 
        } else {
            ?>
			<tr class="form-field gbl-attr-label gbl-attr-terms gbl-attr-terms-edit" > 
				<th><?php 
            echo  esc_html( $label ) ;
            ?></th>
				<td>
					<input type="text" class="i_label" name="term_tt_text_<?php 
            echo  esc_attr( $taxonomy ) ;
            ?>" value="<?php 
            echo  esc_attr( $value['term_tt_text_field'] ) ;
            ?>" />
				</td>
			</tr> 
			<?php 
        }
    
    }
    
    /**
     * Save term meta description.
     *
     * @param  int $term_id  [description].
     * @param  int $tt_id    [description].
     * @param  str $taxonomy [description].
     */
    public function save_term_meta( $term_id, $tt_id, $taxonomy )
    {
        
        if ( isset( $_POST['product_' . $taxonomy] ) ) {
            // phpcs:ignore
            update_term_meta( $term_id, 'product_' . $taxonomy, sanitize_text_field( wp_unslash( $_POST['product_' . $taxonomy] ) ) );
            // phpcs:ignore
        }
        
        //color2 update.
        if ( isset( $_POST['product_term_color2_' . $taxonomy] ) ) {
            // phpcs:ignore
            
            if ( isset( $_POST['term_color_type_' . $taxonomy] ) && 'dual' == $_POST['term_color_type_' . $taxonomy] ) {
                update_term_meta( $term_id, 'product_term_color2_' . $taxonomy, sanitize_text_field( wp_unslash( $_POST['product_term_color2_' . $taxonomy] ) ) );
                // phpcs:ignore
            } else {
                update_term_meta( $term_id, 'product_term_color2_' . $taxonomy, '' );
                // phpcs:ignore
                //delete_post_meta( $term_id, 'term_color_type_' . $taxonomy );
            }
        
        }
        // tooltip text.
        
        if ( isset( $_POST['term_tt_text_' . $taxonomy] ) ) {
            // phpcs:ignore
            update_term_meta( $term_id, 'term_tt_text_' . $taxonomy, sanitize_text_field( wp_unslash( $_POST['term_tt_text_' . $taxonomy] ) ) );
            // phpcs:ignore
        }
        
        // tooltip image.
        
        if ( isset( $_POST['term_tt_img_' . $taxonomy] ) ) {
            // phpcs:ignore
            update_term_meta( $term_id, 'term_tt_img_' . $taxonomy, sanitize_text_field( wp_unslash( $_POST['term_tt_img_' . $taxonomy] ) ) );
            // phpcs:ignore
        }
    
    }
    
    /**
     * Add attribute column.
     *
     * @param array|str $columns [description].
     */
    public function add_attribute_column( $columns )
    {
        $new_columns = array();
        
        if ( isset( $columns['cb'] ) ) {
            $new_columns['cb'] = $columns['cb'];
            unset( $columns['cb'] );
        }
        
        // phpcs:ignore
        $new_columns['thumb'] = __( '', 'woocommerce' );
        $columns = array_merge( $new_columns, $columns );
        return $columns;
    }
    
    /**
     * Adding_attribute_column_content.
     *
     * @param array|str $columns [description].
     * @param array|str $column  [description].
     * @param int       $term_id [description].
     */
    public function add_attribute_column_content( $columns, $column, $term_id )
    {
        
        if ( isset( $_REQUEST['taxonomy'] ) ) {
            // phpcs:ignore
            $taxonomy = sanitize_text_field( wp_unslash( $_REQUEST['taxonomy'] ) );
            // phpcs:ignore
            $attr_type = $this->get_attribute_type( sanitize_text_field( wp_unslash( $_REQUEST['taxonomy'] ) ) );
            // phpcs:ignore
            $value = get_term_meta( $term_id, 'product_' . $taxonomy, true );
            switch ( $attr_type ) {
                case 'color':
                    $product_term_color2 = get_term_meta( $term_id, 'product_term_color2_' . $taxonomy, true );
                    
                    if ( '' == $product_term_color2 ) {
                        printf( '<span class="enwbvs-term-color-preview" style="background-color:%s;"></span>', esc_attr( $value ) );
                    } else {
                        printf( '<span class="dual-color-wrapper"><span class="enwbvs-term-color-preview dualcolor color1" style="background-color:%s;"></span><span class="enwbvs-term-color-preview dualcolor color2" style="background-color:%s;"></span></span>', esc_attr( $value ), esc_attr( $product_term_color2 ) );
                    }
                    
                    break;
                case 'image':
                    $image = ( $value ? wp_get_attachment_image_src( $value ) : '' );
                    $image = ( $image ? $image[0] : plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' );
                    printf( '<img class="swatch-preview swatch-image" src="%s" width="44px" height="44px" alt="preview-image">', esc_url( $image ) );
                    break;
                case 'label':
                    printf( '<div class="swatch-preview swatch-label">%s</div>', esc_html( $value ) );
                    break;
            }
        }
    
    }
    
    /**
     * Adding product option term.
     *
     * @param  obj $attribute_taxonomy [description].
     * @param  int $i                  [description].
     */
    public function enwbvs_product_option_terms( $attribute_taxonomy, $i )
    {
        
        if ( 'select' != $attribute_taxonomy->attribute_type ) {
            // phpcs:ignore
            global  $post, $thepostid, $product_object ;
            $taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
            $product_id = $thepostid;
            
            if ( is_null( $thepostid ) && isset( $_POST['post_id'] ) ) {
                // phpcs:ignore
                $product_id = absint( $_POST['post_id'] );
                // phpcs:ignore
            }
            
            ?>
			<select multiple="multiple" data-placeholder="<?php 
            esc_attr_e( 'Select terms', 'woocommerce' );
            ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php 
            echo  esc_attr( $i ) ;
            ?>][]">
			<?php 
            $args = array(
                'orderby'    => 'name',
                'hide_empty' => 0,
            );
            $all_terms = get_terms( $taxonomy, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
            
            if ( $all_terms ) {
                $options = array();
                foreach ( $all_terms as $key ) {
                    $options[] = $key->term_id;
                }
                foreach ( $all_terms as $term ) {
                    $options = ( !empty($options) ? $options : array() );
                    // phpcs:ignore
                    echo  '<option value="' . esc_attr( $term->term_id ) . '" ' . wc_selected( has_term( absint( $term->term_id ), $taxonomy, $product_id ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>' ;
                }
            }
            
            ?>
			</select>
		   
			<button class="button plus select_all_attributes"><?php 
            esc_html_e( 'Select all', 'woocommerce' );
            ?></button>
			<button class="button minus select_no_attributes"><?php 
            esc_html_e( 'Select none', 'woocommerce' );
            ?></button>

			<?php 
            $taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
            $attr_type = $attribute_taxonomy->attribute_type;
            
            if ( 'label' == $attribute_taxonomy->attribute_type || 'image' == $attribute_taxonomy->attribute_type || 'color' == $attribute_taxonomy->attribute_type ) {
                // phpcs:ignore
                // phpcs:disable
                ?>
				<button class="button fr plus enwbvs_add_new_attribute"  data-attr_taxonomy="<?php 
                echo  esc_attr( $taxonomy ) ;
                ?>"  data-attr_type="<?php 
                echo  esc_attr( $attr_type ) ;
                ?>"  data-dialog_title="<?php 
                printf( esc_html__( 'Add new %s', 'enweby-variation-swatches-for-woocommerce' ), esc_attr( $attribute_taxonomy->attribute_label ) );
                ?>">  <?php 
                esc_html_e( 'Add new', 'enweby-variation-swatches-for-woocommerce' );
                ?>  </button>
				<?php 
                // phpcs:enable
                ?>
				<?php 
            } else {
                ?>
					<button class="button fr plus add_new_attribute"><?php 
                esc_html_e( 'Add new', 'woocommerce' );
                ?></button>
				<?php 
            }
        
        }
    
    }
    
    /**
     * Adding new tab admin edit section for custom option
     *
     * @param  array $tabs [description].
     * @return array.
     */
    public function new_tabs_for_swatches_settings( $tabs )
    {
        $tabs['enwbvs_swatches_settings'] = array(
            'label'    => __( 'Swatches Settings', 'enweby-variation-swatches-for-woocommerce' ),
            'target'   => 'enwbvs-product-attribute-settings',
            'class'    => array( 'variations_tab', 'show_if_variable' ),
            'priority' => 65,
        );
        return $tabs;
    }
    
    /**
     * Output html to select option type.
     */
    public function output_custom_swatches_settings()
    {
        global 
            $post,
            $thepostid,
            $product_object,
            $wc_product_attributes
        ;
        $saved_settings = get_post_meta( $thepostid, 'enwbvs_custom_attribute_settings', true );
        $type_options = array(
            'select' => __( 'Select', 'enweby-variation-swatches-for-woocommerce' ),
            'color'  => __( 'Color', 'enweby-variation-swatches-for-woocommerce' ),
            'label'  => __( 'Button/Label', 'enweby-variation-swatches-for-woocommerce' ),
            'image'  => __( 'Image', 'enweby-variation-swatches-for-woocommerce' ),
            'radio'  => __( 'Radio', 'enweby-variation-swatches-for-woocommerce' ),
        );
        $design_types = array(
            'swatch_design_default' => 'Default Design',
            'swatch_design_1'       => 'Design 1',
            'swatch_design_2'       => 'Design 2',
            'swatch_design_3'       => 'Design 3',
        );
        ?>
		<div id="enwbvs-product-attribute-settings" class="panel wc-metaboxes-wrapper hidden">
			<div id="custom_variations_inner">
				<h2><?php 
        esc_html_e( 'Custom Attribute Settings', 'enweby-variation-swatches-for-woocommerce' );
        ?></h2>
			<?php 
        $attributes = $product_object->get_attributes();
        $i = -1;
        $has_custom_attribute = false;
        foreach ( $attributes as $attribute ) {
            $attribute_name = sanitize_title( $attribute->get_name() );
            $type = '';
            $i++;
            // phpcs:ignore
            
            if ( $attribute->is_taxonomy() == false ) {
                $has_custom_attribute = true;
                ?>
					<div data-taxonomy="<?php 
                echo  esc_attr( $attribute->get_taxonomy() ) ;
                ?>" class="woocommerce_attribute wc-metabox closed" rel="<?php 
                echo  esc_attr( $attribute->get_position() ) ;
                ?>">
			   
						<h3>
							<div class="handlediv-enwbvs" title="<?php 
                esc_attr_e( 'Click to toggle', 'woocommerce' );
                ?>"><span class='toggle-span arrow-down'></span></div>
							<strong class="attribute_name"><?php 
                echo  esc_html( wc_attribute_label( $attribute_name ) ) ;
                ?></strong>
						</h3>
						<div class="enwbvs_custom_attribute wc-metabox-content  <?php 
                echo  'enwbvs-' . esc_attr( $attribute_name ) ;
                ?> hidden">
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td colspan="2">

											<p class="form-row form-row-full ">
												<label for="custom_attribute_type"><?php 
                esc_html_e( 'Swatch Type', 'enweby-variation-swatches-for-woocommerce' );
                ?></label>
												<span class="woocommerce-help-tip" data-tip=" Determines how this custom attribute's values are displayed">
												</span>
													<?php 
                // phpcs:ignore
                ?>
												   <!--  <?php 
                // echo wc_help_tip(" Determines how this custom attributes are displayed"); // WPCS: XSS ok.
                ?> -->

												<select   name="<?php 
                echo  'enwbvs_attribute_type_' . esc_attr( $attribute_name ) ;
                ?>" class="select short enwbvs-attr-select" value = '' onchange="enwbvs_change_term_type(this,event)">
												<?php 
                $type = $this->get_custom_fields_settings( $thepostid, $attribute_name, 'type' );
                foreach ( $type_options as $key => $value ) {
                    $default = ( isset( $type ) && $type == $key ? 'selected' : '' );
                    // phpcs:ignore
                    // phpcs:ignore
                    ?>
														<option value="<?php 
                    echo  esc_attr( $key ) ;
                    ?>" <?php 
                    echo  esc_attr( $default ) ;
                    ?> > <?php 
                    echo  esc_html( $value ) ;
                    ?> </option>
														<?php 
                }
                ?>
												</select>
											</p>
										</td>

									</tr>
									<?php 
                // phpcs:disable
                ?>
									<!-- <tr>
										<td colspan="2">

											<p class="form-row form-row-full ">
												<label for="custom_attribute_type"><?php 
                // esc_html_e('Swatch Design Type','enweby-variation-swatches-for-woocommerce');
                ?> </label>
												<span class="woocommerce-help-tip" data-tip=" Determines how this custom attribute types are displayed">

												</span>
												<select   name="<?php 
                // echo esc_attr('enwbvs_attribute_design_type_'. $attribute_name);
                ?>" class="select short enwbvs-attr-select" value = ''>
													<?php 
                // $design_type = $this->get_custom_fields_settings($thepostid,$attribute_name,'design_type');
                // foreach ($design_types as $key => $value) {
                // $default = (isset($design_type) &&  $design_type == $key) ? 'selected' : '';
                ?>
														<option value="<?php 
                // echo esc_attr($key);
                ?>" <?php 
                // echo $default
                ?> > <?php 
                // echo esc_html($value);
                ?> </option>
													<?php 
                // }
                ?>
												</select>

											</p>
										</td>

									</tr> -->
									<?php 
                // phpcs:enable
                ?>
									<tr>
										<th></th>

									</tr>

									<tr>
										<td>
											<?php 
                $this->custom_attribute_settings_field( $attribute, $thepostid );
                ?>
										</td>
									</tr>

								</tbody>
							</table>
						</div>
					</div>
					<?php 
            }
        
        }
        
        if ( !$has_custom_attribute ) {
            ?>
					<div class="inline notice woocommerce-message">

						<p>
					<?php 
            esc_html_e( 'No custom attributes added yet.', 'woocommerce-product-variation-swatches' );
            esc_html_e( ' You can add custom attributes from the', 'woocommerce-product-variation-swatches' );
            ?>
						<a onclick="enwbvsTriggerAttributeTab(this)" href="#woocommerce-product-data"><?php 
            esc_html_e( ' Attributes', 'woocommerce-product-variation-swatches' );
            ?> </a> <?php 
            esc_html_e( 'tab', 'woocommerce-product-variation-swatches' );
            ?></p>
					</div>
					<?php 
        }
        
        ?>

			</div>
		</div> 
			<?php 
    }
    
    /**
     * Output html of custom field.
     *
     * @param  obj $attribute [description].
     * @param  int $post_id   [description].
     */
    public function custom_attribute_settings_field( $attribute, $post_id )
    {
        $attribute_name = sanitize_title( $attribute->get_name() );
        $type = $this->get_custom_fields_settings( $post_id, $attribute_name, 'type' );
        $this->output_field_label( $type, $attribute, $post_id );
        $this->output_field_image( $type, $attribute, $post_id );
        $this->output_field_color( $type, $attribute, $post_id );
    }
    
    /**
     * Generating Output field for label.
     *
     * @param  str $type      [description].
     * @param  obj $attribute [description].
     * @param  int $post_id   [description].
     */
    public function output_field_label( $type, $attribute, $post_id )
    {
        $attribute_name = sanitize_title( $attribute->get_name() );
        $display_status = ( 'label' == $type ? 'display: table' : 'display: none' );
        // phpcs:ignore
        ?>
		<table class="enwbvs-custom-table enwbvs-custom-table-label" style="<?php 
        echo  esc_html( $display_status ) ;
        ?>">
		<?php 
        $i = 0;
        foreach ( $attribute->get_options() as $term ) {
            // phpcs:ignore
            $css = ( 0 == $i ? 'display:table-row-group' : '' );
            // phpcs:ignore
            $open = ( 0 == $i ? 'open' : '' );
            ?>
				<tr class="enwbvs-term-name">
					<td colspan="2">
						<h3 class="enwbvs-local-head <?php 
            echo  esc_attr( $open ) ;
            ?>" data-type="<?php 
            echo  esc_attr( $type ) ;
            ?>" data-term_name="<?php 
            echo  esc_attr( $term ) ;
            ?>" onclick="enwbvs_open_body(this,event)"><?php 
            echo  esc_html( $term ) ;
            ?></h3>
						<table class="enwbvs-local-body-table">
							<tbody class="enwbvs-local-body enwbvs-local-body-<?php 
            echo  esc_attr( $term ) ;
            ?>" style="<?php 
            echo  esc_attr( $css ) ;
            ?>">
								<tr> 
									<td width="30%"><?php 
            esc_html_e( 'Term Name', 'enweby-variation-swatches-for-woocommerce' );
            ?></td>
									<td width="70%"><?php 
            echo  esc_html( $term ) ;
            ?></td>
								</tr>
								<tr class="form-field"> 
									<td><?php 
            esc_html_e( 'Label Text', 'enweby-variation-swatches-for-woocommerce' );
            ?></td>
									<td>
									<?php 
            $term_field = ( 'label' == $type ? $this->get_custom_fields_settings(
                $post_id,
                $attribute_name,
                $term,
                'term_value'
            ) : '' );
            // phpcs:ignore
            $term_field = ( $term_field ? $term_field : '' );
            ?>
										<input type="text" class="i_label" name="<?php 
            echo  esc_attr( sanitize_title( 'label_' . $attribute_name . '_term_' . $term ) ) ;
            ?>" style="width:275px;" value="<?php 
            echo  esc_attr( $term_field ) ;
            ?>">
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>

				<?php 
            $i++;
        }
        ?>
		</table>
		<?php 
    }
    
    /**
     * Output field for image.
     *
     * @param  str $type      [description].
     * @param  obj $attribute [description].
     * @param  int $post_id   [description].
     */
    public function output_field_image( $type, $attribute, $post_id )
    {
        $attribute_name = sanitize_title( $attribute->get_name() );
        $display_status = ( 'image' == $type ? 'display:table' : 'display: none' );
        // phpcs:ignore
        ?>
		<table class="enwbvs-custom-table enwbvs-custom-table-image" style="<?php 
        echo  esc_attr( $display_status ) ;
        ?>">
		<?php 
        $i = 0;
        foreach ( $attribute->get_options() as $term ) {
            // phpcs:ignore
            $css = ( 0 == $i ? 'display:table-row-group' : '' );
            // phpcs:ignore
            $open = ( 0 == $i ? 'open' : '' );
            ?>
				<tr class="enwbvs-term-name">
					<td colspan="2">
						<h3 class="enwbvs-local-head <?php 
            echo  esc_attr( $open ) ;
            ?>" data-term_name="<?php 
            echo  esc_attr( $term ) ;
            ?>" onclick="enwbvs_open_body(this,event)"><?php 
            echo  esc_html( $term ) ;
            ?></h3>
						<table class="enwbvs-local-body-table">
							<tbody class="enwbvs-local-body enwbvs-local-body-<?php 
            echo  esc_attr( $term ) ;
            ?>" style="<?php 
            echo  esc_attr( $css ) ;
            ?>">
								<tr> 
									<td width="30%">Term Name</td>
									<td width="70%"><?php 
            echo  esc_html( $term ) ;
            ?></td>
								</tr>
								<tr class="form-field"> <td><?php 
            esc_html_e( 'Term Image', 'enweby-variation-swatches-for-woocommerce' );
            ?></td>
									<td>
									<?php 
            $term_field = $this->get_custom_fields_settings(
                $post_id,
                $attribute_name,
                $term,
                'term_value'
            );
            $term_field = ( $term_field ? $term_field : '' );
            $image = ( 'image' == $type ? $this->get_custom_fields_settings(
                $post_id,
                $attribute_name,
                $term,
                'term_value'
            ) : '' );
            // phpcs:ignore
            $image = ( $image ? wp_get_attachment_image_src( $image ) : '' );
            $remove_img = ( $image ? 'display:inline' : 'display:none' );
            $image = ( $image ? $image[0] : plugin_dir_url( __FILE__ ) . 'images/placeholder.svg' );
            ?>

										<div class = 'enwbvs-upload-image'>

											<div class="enwbvs-term-image-thumbnail" style="float:left;margin-right:10px;">
												<img  class="i_index_media_img" src="<?php 
            echo  esc_url( $image ) ;
            ?>" width="60px" height="60px" alt="term-image"/>
											</div>

											<div style="line-height:30px;">
												<input type="hidden" class="i_index_media"  name= "<?php 
            echo  esc_attr( sanitize_title( 'image_' . $attribute_name . '_term_' . $term ) ) ;
            ?>" value="<?php 
            echo  esc_attr( $term_field ) ;
            ?>">

												<button type="button" class="enwbvs-upload-image-button button " onclick="enwbvs_upload_icon_image(this,event)">
													<img class="enwbvs-upload-button" src="<?php 
            echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/upload.svg' ) ;
            ?>" alt="upload-button">
												</button>
												<button type="button" style="<?php 
            echo  esc_attr( $remove_img ) ;
            ?>" class="enwbvs_remove_image_button button " onclick="enwbvs_remove_icon_image(this,event)">
													<img class="enwbvs-remove-button" src="<?php 
            echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/remove.svg' ) ;
            ?>" alt="remove-button">
												</button> 

											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<?php 
            $i++;
        }
        ?>
		</table>
		<?php 
    }
    
    /**
     * Output field for color.
     *
     * @param  str $type      [description].
     * @param  obj $attribute [description].
     * @param  int $post_id   [description].
     */
    public function output_field_color( $type, $attribute, $post_id )
    {
        $attribute_name = sanitize_title( $attribute->get_name() );
        $display_status = ( 'color' == $type ? 'display: table' : 'display: none' );
        // phpcs:ignore
        ?>
		<table class="enwbvs-custom-table enwbvs-custom-table-color" style="<?php 
        echo  esc_attr( $display_status ) ;
        ?>">
		<?php 
        $i = 0;
        foreach ( $attribute->get_options() as $term ) {
            // phpcs:ignore
            $css = ( $i == 0 ? 'display:table-row-group' : '' );
            // phpcs:ignore
            $open = ( $i == 0 ? 'open' : '' );
            ?>
				<tr class="enwbvs-term-name">
					<td colspan="2">
						<h3 class="enwbvs-local-head <?php 
            echo  esc_attr( $open ) ;
            ?>" data-term_name="<?php 
            echo  esc_attr( $term ) ;
            ?>" onclick="enwbvs_open_body(this,event)"><?php 
            echo  esc_html( $term ) ;
            ?></h3>
						<table class="enwbvs-local-body-table">
							<tbody class="enwbvs-local-body enwbvs-local-body-<?php 
            echo  esc_attr( $term ) ;
            ?>" style="<?php 
            echo  esc_attr( $css ) ;
            ?>">
								<tr>
									<td width="30%"><?php 
            esc_html_e( 'Term Name', 'enweby-variation-swatches-for-woocommerce' );
            ?></td>
									<td width="70%"><?php 
            echo  esc_html( $term ) ;
            ?></td>
								</tr>
							<?php 
            $color_type = $this->get_custom_fields_settings(
                $post_id,
                $attribute_name,
                $term,
                'color_type'
            );
            ?>
								<tr>
									<td><?php 
            esc_html_e( 'Term Color', 'enweby-variation-swatches-for-woocommerce' );
            ?></td>
									<td class = "enwbvs-custom-attr-color-td">
								<?php 
            $term_field = ( 'color' == $type ? $this->get_custom_fields_settings(
                $post_id,
                $attribute_name,
                $term,
                'term_value'
            ) : '' );
            // phpcs:ignore
            $term_field = ( $term_field ? $term_field : '' );
            ?>

										<div class="enwbvs_settings_fields_form enwbvsfw-col-div" style="margin-bottom: 5px">
											<span class="enwbvs-admin-colorpickpreview color_preview" style="background-color: <?php 
            echo  esc_attr( $term_field ) ;
            ?> ;"></span>
											<input type="text" name="<?php 
            echo  esc_attr( sanitize_title( 'color_' . $attribute_name . '_term_' . $term ) ) ;
            ?>" class="enwbvs-admin-colorpick" value="<?php 
            echo  esc_attr( $term_field ) ;
            ?>" style="width:250px;"/>
											
										</div>
								<?php 
            ?>			
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<?php 
            $i++;
        }
        ?>
		</table>
		<?php 
    }
    
    /**
     * Geting custom_fields_settings.
     *
     * @param  int     $post_id   [description].
     * @param  boolean $attribute [description].
     * @param  boolean $term      [description].
     * @param  boolean $term_key  [description].
     * @return str|array          [description].
     */
    public function get_custom_fields_settings(
        $post_id,
        $attribute = false,
        $term = false,
        $term_key = false
    )
    {
        $saved_settings = get_post_meta( $post_id, 'enwbvs_custom_attribute_settings', true );
        
        if ( is_array( $saved_settings ) ) {
            
            if ( $attribute ) {
                
                if ( isset( $saved_settings[$attribute] ) ) {
                    $attr_settings = $saved_settings[$attribute];
                    if ( is_array( $attr_settings ) && $term ) {
                        
                        if ( 'type' == $term || 'tooltip_type' == $term || 'radio-type' == $term || 'design_type' == $term ) {
                            // phpcs:ignore
                            $term_types = ( isset( $attr_settings[$term] ) ? $attr_settings[$term] : false );
                            return $term_types;
                        } else {
                            $term_settings = ( isset( $attr_settings[$term] ) ? $attr_settings[$term] : '' );
                            
                            if ( is_array( $term_settings ) && $term_key ) {
                                $settings_value = ( isset( $term_settings[$term_key] ) ? $term_settings[$term_key] : '' );
                                return $settings_value;
                            } else {
                                return false;
                            }
                            
                            return $term_settings;
                        }
                    
                    }
                    return $attr_settings;
                }
                
                return false;
            }
            
            return $saved_settings;
        } else {
            return false;
        }
    
    }
    
    /**
     *  Saving custom fields.
     *
     * @param  int $post_id [description].
     * @param  obj $post    [description].
     */
    public function save_custom_fields( $post_id, $post )
    {
        $product = wc_get_product( $post_id );
        $local_attr_settings = array();
        foreach ( $product->get_attributes() as $attribute ) {
            // phpcs:ignore
            
            if ( $attribute->is_taxonomy() == false ) {
                $attr_settings = array();
                $attr_name = sanitize_title( $attribute->get_name() );
                $type_key = 'enwbvs_attribute_type_' . $attr_name;
                $attr_settings['type'] = ( isset( $_POST[$type_key] ) ? sanitize_text_field( wp_unslash( $_POST[$type_key] ) ) : '' );
                // phpcs:ignore
                $tt_key = sanitize_title( 'enwbvs_tooltip_type_' . $attr_name );
                // phpcs:ignore
                $attr_settings['tooltip_type'] = ( isset( $_POST[$tt_key] ) ? sanitize_text_field( wp_unslash( $_POST[$tt_key] ) ) : '' );
                // phpcs:ignore
                $design_type_key = sanitize_title( 'enwbvs_attribute_design_type_' . $attr_name );
                $attr_settings['design_type'] = ( isset( $_POST[$design_type_key] ) ? sanitize_text_field( wp_unslash( $_POST[$design_type_key] ) ) : '' );
                // phpcs:ignore
                
                if ( 'radio' == $attr_settings['type'] ) {
                    // phpcs:ignore
                    $radio_style_key = sanitize_title( $attr_name . '_radio_button_style' );
                    $attr_settings['radio-type'] = ( isset( $_POST[$radio_style_key] ) ? sanitize_text_field( wp_unslash( $_POST[$radio_style_key] ) ) : '' );
                    // phpcs:ignore
                } else {
                    $term_settings = array();
                    foreach ( $attribute->get_options() as $term ) {
                        $term_settings['name'] = $term;
                        
                        if ( 'color' == $attr_settings['type'] ) {
                            // phpcs:ignore
                            $color_type_key = sanitize_title( $attr_name . '_color_type_' . $term );
                            $term_settings['color_type'] = ( isset( $_POST[$color_type_key] ) ? sanitize_text_field( wp_unslash( $_POST[$color_type_key] ) ) : '' );
                            // phpcs:ignore
                        }
                        
                        $term_key = sanitize_title( $attr_settings['type'] . '_' . $attr_name . '_term_' . $term );
                        $term_settings['term_value'] = ( isset( $_POST[$term_key] ) ? sanitize_text_field( wp_unslash( $_POST[$term_key] ) ) : '' );
                        // phpcs:ignore
                        //For dual color value
                        
                        if ( isset( $term_settings['color_type'] ) && 'dual' == $term_settings['color_type'] ) {
                            $term_key2 = sanitize_title( $attr_settings['type'] . '_' . $attr_name . '_term_color2_' . $term );
                        } else {
                            $term_key2 = '';
                        }
                        
                        $term_settings['term_color2'] = ( isset( $_POST[$term_key2] ) ? sanitize_text_field( wp_unslash( $_POST[$term_key2] ) ) : '' );
                        // phpcs:ignore
                        $attr_settings[$term] = $term_settings;
                    }
                }
                
                $local_attr_settings[$attr_name] = $attr_settings;
            }
        
        }
        update_post_meta( $post_id, 'enwbvs_custom_attribute_settings', $local_attr_settings );
    }

}