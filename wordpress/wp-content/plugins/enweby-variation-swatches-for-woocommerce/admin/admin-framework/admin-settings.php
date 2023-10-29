<?php

/**
 * Settings Framework
 *
 * @link       https://www.enweby.com/
 * @since      1.0.0
 *
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/admin
 */
add_filter( 'wpsf_register_settings_' . ENWEBY_VARIATION_SWATCHES_FWAS . '', 'enwbvs_variation_swatches_wpsf_tabbed_settings' );
/**
 * Premium features Content
 *
 */
function enwbvs_premium_features_html()
{
    $html = '<div class="premium-key-features">
	<div class="upgrade-notice-box">
		<h3><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span> Premium Version <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></h3>
		<p>Premium version comes with below mentioned premium features.</p>
		<p>Upgrade today to get all premium features and premium support. Click below button to upgrade.<p class="moneyback-guarentee"><span class="dashicons dashicons-awards"></span>&nbsp;30 days money back guarentee.</p>
		<p class="button-class"><span class="button-left"><a class="upgrade-buttton" href="https://checkout.freemius.com/mode/dialog/plugin/11585/plan/19754/">Upgrade Now</a></span> <span class="button-right"><a target="_blank" href="https://www.enweby.com/product/variation-swatches-for-woocommerce/">View All Features</a></span></p>
	</div>

	<div class="feature-left">
	<ul>
	<li>
	<h4>Automatically convert dropdowns to images</h4>
	<p> This feature automatically convert variation dropdowns to variation images.
		   </p></li>
	<li>
	<h4>Clear selection on reselecting variation</h4>
	<p>		     Clear selection on clicking the selected swatch.
		   </p></li>
	<li>
	<h4>Customize Design &amp; Style of Swatches</h4>
	<p>		The plugin allows you to change design and styles of swatches. You can change background color, border color, text color, and styling on hover etc with just simply using admin options. Plugin allows you to choose different style for image/color swath type, label/button swatch type, and radio button swatch type.
			</p></li>
	<li>
	<h4>Set AJAX Variation Threshold</h4>
	<p>		Variation swatches for wooCommerce plugin allows you to set the Ajax variation threshold value. So, based on this threshold value, the product availability check can be done through Ajax method or JavaScript.
			</p></li>
	<li>
	<h4>Make out of stock variation clickable</h4>
	<p>		Premium version allows admin to decide whether he wants to enable out of stock items to be clickable or not.
			</p></li>
	<li>
	<h4>Make out of stock item disabled/unavailable</h4>
	<p>		Premium version of product variation swatches also allows admin to decide whether he wants to make out of stock item to behave as disabled in wooCommerce store. This is very helpful to prevent your customer to get frustrated because of selecting out of stock items and redoing the process again.
			</p></li>
	</ul></div>
	<div class="feature-right">
	<ul>
		<li>
	<h4>Dual Color type swatch</h4>
	<p>			Premium version also allows now admin to use dual color swatch too, which very handy when your product is having dual color tone in your products.
				</p></li>
	<li>
	<h4>Variation Stock Info on Product Page</h4>
	<p>			Premium version also allows admin to enable/disable displaying stock left label or out of stock label on product page only while keeping this feature on/off on archive page.
				</p></li>
	<li>
	<h4>Variation Stock Info Shop/Archive Page</h4>
	<p>			Premium version also allows admin to enable/disable displaying stock left label or out of stock label on shop/archive page only while keeping this feature on/off on product page.
				</p></li>
	<li>
	<h4>Display Stock Left alert min Qty</h4>
	<p>			Premium version allows admin to set qty so that stock left alert can be triggered based on this qty.
				</p></li>
	<li>
	<h4>Selected Variation Label on Shop/Archive Page</h4>
	<p>			Using premium version you can display selected variation label near the term title on shop/archive page also.
				</p></li>
	<li>
	<h4>Default Variation Selection on Shop/Archive Page</h4>
	<p>			Premium version supports default selection set in the product setting for shop/archive page also.
				</p></li>
	<li>
	<h4>Display Limit for Showing Variation Swatches on Shop/Archive Page</h4>
	<p>			Premium version allows store owner to limit the display of swatches on shop/archive page.
				</p></li>
	</ul></div>
	</div>';
    return $html;
}

/**
 * Tabless example.
 *
 * @param array $wpsf_settings Settings.
 */
function enwbvs_variation_swatches_wpsf_tabless_settings( $wpsf_settings )
{
    // General Settings section.
    $wpsf_settings[] = array(
        'section_id'    => 'general',
        'section_title' => 'General Settings',
        'section_order' => 1,
        'fields'        => array(
        array(
        'id'       => 'enwbvs-shape-style',
        'title'    => 'Swatch Shape Style',
        'subtitle' => 'Global Swatch shape style',
        'type'     => 'select',
        'choices'  => array(
        'rounded' => 'Rounded',
        'square'  => 'Square',
        'circle'  => 'Circle',
    ),
        'default'  => 'rounded',
    ),
        array(
        'id'      => 'enwbvs-default-dropdown-to-buttons',
        'title'   => 'Convert default dropdown to buttons',
        'type'    => 'checkboxes',
        'choices' => array(
        '1' => 'Convert default dropdown to buttons',
    ),
        'default' => array( '1' ),
    ),
        array(
        'id'      => 'enwbvs-default-stylesheet',
        'title'   => 'Enable plugin\'s default stylesheet.',
        'type'    => 'checkboxes',
        'choices' => array(
        '1' => 'Enable plugin\'s default stylesheet.',
    ),
        'default' => array( '1' ),
    ),
        array(
        'id'      => 'enwbvs-default-dropdown-to-images',
        'title'   => 'Convert dropdown to images.',
        'type'    => 'checkboxes',
        'choices' => array(
        '1' => 'Convert default dropdown to images type if variation has an image.',
    ),
        'default' => array( '1' ),
    )
    ),
    );
    return $wpsf_settings;
}

/**
 * Tabbed example.
 *
 * @param array $wpsf_settings settings.
 */
function enwbvs_variation_swatches_wpsf_tabbed_settings( $wpsf_settings )
{
    // Tabs.
    $wpsf_settings['tabs'] = array(
        array(
        'id'    => 'general',
        'title' => esc_html__( 'General', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'advanced',
        'title' => esc_html__( 'Advanced', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'styling',
        'title' => esc_html__( 'Styling', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'swatch_type_specific_styling',
        'title' => esc_html__( 'Swatch Type Specific Styling', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'product_page',
        'title' => esc_html__( 'Product Page', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'shop_archive',
        'title' => esc_html__( 'Shop/Archive', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'custom_css',
        'title' => esc_html__( 'Custom CSS (Optional)', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'debug_mode',
        'title' => esc_html__( 'Debug', 'enweby-variation-swatches-for-woocommerce' ),
    ),
        array(
        'id'    => 'premium_features',
        'title' => esc_html__( 'Premium Features', 'enweby-variation-swatches-for-woocommerce' ),
    )
    );
    // Settings.
    $wpsf_settings['sections'] = array(
        array(
        'tab_id'        => 'general',
        'section_id'    => 'section_general',
        'section_title' => 'General Settings',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'      => 'enwbvs-enable-toolip-on-swatches',
        'title'   => 'Enable tooltip on swatches.',
        'type'    => 'toggle',
        'default' => '0',
    ),
        array(
        'id'       => 'enwbvs-default-stylesheet',
        'title'    => 'Enable plugin\'s default stylesheet.',
        'subtitle' => 'Recommended.',
        'type'     => 'toggle',
        'default'  => '1',
    ),
        array(
        'id'       => 'enwbvs-default-dropdown-to-buttons',
        'title'    => 'Convert default dropdown to buttons',
        'subtitle' => 'Enable to convert default dropdown to buttons automatically.',
        'type'     => 'toggle',
        'default'  => '1',
    ),
        array(
        'id'       => 'enwbvs-default-dropdown-to-images',
        'title'    => 'Convert dropdown to images.',
        'subtitle' => 'Convert default dropdown to images type if variation has an image.',
        'type'     => 'toggle',
        'class'    => 'pro-feature',
        'default'  => '0',
    )
    ),
    ),
        array(
        'tab_id'        => 'advanced',
        'section_id'    => 'section_advanced',
        'section_title' => 'Advanced Settings',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'      => 'enwbvs-clear-on-reselect',
        'title'   => 'Clear on re-selecting',
        'type'    => 'checkboxes',
        'choices' => array(
        '1' => 'Clear on selected attribute on selecting again',
    ),
        'default' => array( '1' ),
    ),
        array(
        'id'      => 'enwbvs-disabled-attibute-style',
        'title'   => 'Unavailable/Out of stock attribute style',
        'type'    => 'radio',
        'choices' => array(
        '1' => 'Blur with cross',
        '2' => 'Blur without cross',
        '3' => 'Hide',
    ),
        'default' => '1',
    ),
        array(
        'id'      => 'enwbvs-clickable-outofstock',
        'title'   => 'Make Out of Stock variation clickable',
        'class'   => 'pro-feature',
        'type'    => 'toggle',
        'default' => '1',
    ),
        array(
        'id'      => 'enwbvs-disable-outofstock',
        'title'   => 'Make Out of Stock variations as Unavailable/Disabled',
        'class'   => 'pro-feature',
        'type'    => 'toggle',
        'default' => '0',
    ),
        array(
        'id'      => 'enwbvs-show-stock-left-label',
        'title'   => 'Show Stock Left alert label on variation',
        'class'   => 'pro-feature',
        'type'    => 'toggle',
        'default' => '0',
    ),
        array(
        'id'      => 'enwbvs-minimum-qty-to-show-stock-left',
        'title'   => 'Minimum Qty to show Stock Left alert label',
        'desc'    => 'Minimum Qty to show Stock Left alert label. Default is 5',
        'class'   => 'pro-feature',
        'type'    => 'number',
        'default' => '5',
    ),
        array(
        'id'      => 'enwbvs-ajax-variation-threshold-limit',
        'title'   => 'Ajax variation threshold limit',
        'desc'    => 'Recommended value is 30. By default, if the number of product variations is less than 30, then product availability is checked through JavaScript, if greater than 30, ajax method is used. This field controls this behavior',
        'type'    => 'number',
        'default' => '30',
    )
    ),
    ),
        array(
        'tab_id'        => 'styling',
        'section_id'    => 'section_swatch_indicator',
        'section_title' => 'Swatch Indicator Styling',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'      => 'enwbvs-swatch-tick-color',
        'title'   => 'Selected Swatch Tick Color',
        'desc'    => 'Selected swatch tick color. Default color is #6be388',
        'type'    => 'color',
        'default' => '#6be388',
    ),
        array(
        'id'       => 'enwbvs-swatch-tick-show',
        'title'    => 'Show tick mark for selected swatch',
        'subtitle' => 'Show/hide Tick Mark for selected swatch.',
        'type'     => 'toggle',
        'default'  => '1',
    ),
        array(
        'id'      => 'enwbvs-swatch-cross-color',
        'title'   => 'Disabled Swatch Cross Color',
        'desc'    => 'Disabled swatch cross color. Default color is #ff0000',
        'type'    => 'color',
        'default' => '#ff0000',
    ),
        array(
        'id'      => 'enwbvs-swatch-inner-padding',
        'title'   => 'Swatch Inner Padding',
        'desc'    => 'Swatch Inner Padding in px. Recommended is 1 to 5. Default is 2',
        'type'    => 'number',
        'default' => '2',
    ),
        array(
        'id'      => 'enwbvs-selected-swatch-border-width',
        'title'   => 'Selected Swatch Border Width',
        'desc'    => 'Border width of selected swatch in px. Default width is 2',
        'type'    => 'number',
        'default' => '2',
    )
    ),
    ),
        array(
        'tab_id'        => 'styling',
        'section_id'    => 'section_tooltip_styling',
        'section_title' => 'Tooltip Style Settings',
        'section_order' => 10,
        'fields'        => array( array(
        'id'      => 'enwbvs-tooltip-text-color',
        'title'   => 'Tooltip Text color',
        'desc'    => 'Selected Tool tip text color. Default color is #ffffff',
        'type'    => 'color',
        'default' => '#ffffff',
    ), array(
        'id'      => 'enwbvs-tooltip-background-color',
        'title'   => 'Tooltip background color',
        'desc'    => 'Selected Tool tip text color. Default color is #000000',
        'type'    => 'color',
        'default' => '#000000',
    ) ),
    ),
        array(
        'tab_id'        => 'swatch_type_specific_styling',
        'section_id'    => 'section_swatch_type_image_color_styling',
        'section_title' => 'Image/Color Swatch Type Styling',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'       => 'enwbvs-swatch-image-color-shape-style',
        'title'    => 'Image/Color Swatch Shape Style',
        'subtitle' => 'Select swatch shape style for Image or Color swatch type',
        'type'     => 'radio',
        'choices'  => array(
        'square' => 'Square',
        'circle' => 'Circle',
    ),
        'default'  => 'square',
    ),
        array(
        'id'      => 'enwbvs-swatch-image-color-border-color',
        'title'   => 'Image/Color Swatch border color',
        'desc'    => 'Applicable only Swatch type of Image/Color. Default color is #dddddd',
        'type'    => 'color',
        'default' => '#dddddd',
    ),
        array(
        'id'      => 'enwbvs-swatch-image-color-border-color-hover',
        'title'   => 'Image/Color Swatch border color on Hover',
        'desc'    => 'Applicable only Swatch type of Image/Color. Default color is #666666',
        'type'    => 'color',
        'default' => '#666666',
    ),
        array(
        'id'      => 'enwbvs-swatch-image-color-border-color-selected',
        'title'   => 'Image/Color Swatch border color when selected',
        'desc'    => 'Applicable only on Swatch type of Image/Color. Default color is #666666',
        'type'    => 'color',
        'default' => '#666666',
    )
    ),
    ),
        array(
        'tab_id'        => 'swatch_type_specific_styling',
        'section_id'    => 'section_swatch_type_label_styling',
        'section_title' => 'Label/Button Swatch Type Styling',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'       => 'enwbvs-swatch-label-button-shape-style',
        'title'    => 'Label/Button Swatch Shape Style',
        'subtitle' => 'Select swatch shape style for Label or Button swatch type',
        'type'     => 'radio',
        'choices'  => array(
        'square'  => 'Square',
        'circle'  => 'Circle',
        'rounded' => 'Rounded',
    ),
        'default'  => 'rounded',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-text-color',
        'title'   => 'Label/Button Swatch Text color',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #555555',
        'type'    => 'color',
        'default' => '#555555',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-text-color-hover',
        'title'   => 'Label/Button Swatch Text color on Hover',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #666666',
        'type'    => 'color',
        'default' => '#666666',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-text-color-selected',
        'title'   => 'Label/Button Swatch Text color when selected',
        'desc'    => 'Applicable only on Swatch type of Label/Button. Default color is #666666',
        'type'    => 'color',
        'default' => '#666666',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-background-color',
        'title'   => 'Label/Button Swatch Background color',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #ffffff',
        'type'    => 'color',
        'default' => '#ffffff',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-background-color-hover',
        'title'   => 'Label/Button Swatch Background color on Hover',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #ffffff',
        'type'    => 'color',
        'default' => '#ffffff',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-background-color-selected',
        'title'   => 'Label/Button Swatch Background color when selected',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #ffffff',
        'type'    => 'color',
        'default' => '#ffffff',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-border-color',
        'title'   => 'Label/Button Swatch Border color',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #dddddd',
        'type'    => 'color',
        'default' => '#dddddd',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-border-color-hover',
        'title'   => 'Label/Button Swatch Border color on Hover',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #666666',
        'type'    => 'color',
        'default' => '#666666',
    ),
        array(
        'id'      => 'enwbvs-swatch-label-border-color-selection',
        'title'   => 'Label/Button Swatch Border color when selected',
        'desc'    => 'Applicable only Swatch type of Label/Button. Default color is #666666',
        'type'    => 'color',
        'default' => '#666666',
    )
    ),
    ),
        array(
        'tab_id'        => 'swatch_type_specific_styling',
        'section_id'    => 'section_swatch_type_radio_styling',
        'section_title' => 'Radio Swatch Type Styling',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'      => 'enwbvs-swatch-radio-circle-bg-color',
        'title'   => 'Radio Swatch Circle Bacakground color',
        'desc'    => 'Applicable only Swatch type Radio. Default color is #eeeeee',
        'type'    => 'color',
        'default' => '#eeeeee',
    ),
        array(
        'id'      => 'enwbvs-swatch-radio-text-color',
        'title'   => 'Radio Swatch Text color',
        'desc'    => 'Applicable only Swatch type Radio. Default color is #555555',
        'type'    => 'color',
        'default' => '#555555',
    ),
        array(
        'id'      => 'enwbvs-swatch-radio-text-color-hover',
        'title'   => 'Radio Swatch Text color on Hover',
        'desc'    => 'Applicable only Swatch type Radio. Default color is #000000',
        'type'    => 'color',
        'default' => '#000000',
    ),
        array(
        'id'      => 'enwbvs-swatch-radio-text-color-selected',
        'title'   => 'Radio Swatch Text color when selected',
        'desc'    => 'Applicable only on Swatch type Radio. Default color is #000000',
        'type'    => 'color',
        'default' => '#000000',
    )
    ),
    ),
        array(
        'tab_id'        => 'product_page',
        'section_id'    => 'section_product_page_settings',
        'section_title' => 'Product Page Settings',
        'section_order' => 10,
        'fields'        => array( array(
        'id'       => 'enwbvs-show-attribute-variation-name',
        'title'    => 'Show selected Attribute Variation Name',
        'subtitle' => 'Show selected attribute variation name beside the term title',
        'type'     => 'toggle',
        'default'  => '1',
    ), array(
        'id'       => 'enwbvs-generate-variation-url',
        'title'    => 'Generate Variation Url',
        'subtitle' => 'Generate shareable variation url based on selected attributes',
        'class'    => 'pro-feature',
        'type'     => 'toggle',
        'default'  => '0',
    ), array(
        'id'      => 'enwbvs-product-page-variation-stock-info',
        'title'   => 'Show variation stock info',
        'class'   => 'pro-feature',
        'type'    => 'toggle',
        'default' => '0',
    ) ),
    ),
        array(
        'tab_id'        => 'product_page',
        'section_id'    => 'section_product_page_swatch_size',
        'section_title' => 'Product Page Swatch Size',
        'section_order' => 10,
        'fields'        => array( array(
        'id'       => 'enwbvs-product-swatch-width',
        'title'    => 'Product Page Swatch Width',
        'subtitle' => 'Applicable only on Square and Circle shape type',
        'desc'     => 'Swatch width in px. Default is 35',
        'type'     => 'number',
        'default'  => '35',
    ), array(
        'id'      => 'enwbvs-product-swatch-height',
        'title'   => 'Product Page Swatch Height',
        'desc'    => 'Swatch height in px. Default is 35',
        'type'    => 'number',
        'default' => '35',
    ), array(
        'id'      => 'enwbvs-product-swatch-font-size',
        'title'   => 'Product Page Swatch Font Size',
        'desc'    => 'Swatch font size in px. Default is 15',
        'type'    => 'number',
        'default' => '15',
    ) ),
    ),
        array(
        'tab_id'        => 'shop_archive',
        'section_id'    => 'section_shop_archive',
        'section_title' => 'Archive/Shop Page Settings',
        'section_order' => 10,
        'fields'        => array(
        array(
        'id'      => 'enwbvs-show-swatches-on-archive-shop',
        'title'   => 'Show Swathes on Archive/Shop Page',
        'type'    => 'toggle',
        'default' => '1',
    ),
        array(
        'id'       => 'enwbvs-show-attribute-name-on-archive',
        'title'    => 'Show attribute Name on shop/archive',
        'subtitle' => 'Show attribute Name on shop/Archive',
        'type'     => 'toggle',
        'default'  => '1',
    ),
        array(
        'id'       => 'enwbvs-show-attribute-variation-name-on-archive',
        'title'    => 'Show selected Variation label on shop/archive',
        'subtitle' => 'Show selected variation label beside the term title',
        'class'    => 'pro-feature',
        'type'     => 'toggle',
        'default'  => '1',
    ),
        array(
        'id'       => 'enwbvs-archive-page-variation-stock-info',
        'title'    => 'Show Variation Stock Info on Shop/Archive',
        'subtitle' => 'Show archive/shop variation product stock info',
        'class'    => 'pro-feature',
        'type'     => 'toggle',
        'default'  => '0',
    ),
        array(
        'id'       => 'enwbvs-default-selected-on-archive',
        'title'    => 'Show default selected attributes',
        'subtitle' => 'Show default selected attributes swathes on archive/shop page',
        'class'    => 'pro-feature',
        'type'     => 'toggle',
        'default'  => '0',
    ),
        array(
        'id'      => 'enwbvs-show-on-filter-widget',
        'title'   => 'Show variation swatches on filter widget layered nav',
        'class'   => 'pro-feature',
        'type'    => 'checkboxes',
        'choices' => array(
        '1' => 'Show variation swatches on filter widget',
    ),
        'default' => array( '0' ),
    ),
        array(
        'id'      => 'enwbvs-swatches-display-type-on-filter-widget',
        'title'   => 'Swatch Display Type on filter widget layered nav',
        'class'   => 'pro-feature',
        'type'    => 'radio',
        'choices' => array(
        '1' => 'Vertical',
        '2' => 'Horizontal',
    ),
        'default' => '1',
    ),
        array(
        'id'      => 'enwbvs-swatches-alignment-archive',
        'title'   => 'Swatches alignment on shop/archive page',
        'class'   => 'pro-feature',
        'type'    => 'radio',
        'choices' => array(
        'flex-start' => 'Left',
        'flex-end'   => 'Right',
        'center'     => 'Center',
    ),
        'default' => 'flex-start',
    ),
        array(
        'id'      => 'enwbvs-product-page-attribute-display-limit',
        'title'   => 'Attribute Display Limit on Shop/Archive Page',
        'desc'    => '0 value means all attribute will dispalyed without any limit. Default is 0',
        'class'   => 'pro-feature',
        'type'    => 'number',
        'default' => '0',
    )
    ),
    ),
        array(
        'tab_id'        => 'shop_archive',
        'section_id'    => 'section_shop_archive_swatch_size',
        'section_title' => 'Achive/shop Swatch Size',
        'section_order' => 10,
        'fields'        => array( array(
        'id'       => 'enwbvs-arhive-swatch-width',
        'title'    => 'Swatch Width',
        'subtitle' => 'Applicable only on Square and Circle shape type',
        'desc'     => 'Swatch width in px.  Default is 35',
        'type'     => 'number',
        'default'  => '35',
    ), array(
        'id'      => 'enwbvs-arhive-swatch-height',
        'title'   => 'Swatch Height',
        'desc'    => 'Swatch height in px. Default is 35',
        'type'    => 'number',
        'default' => '35',
    ), array(
        'id'      => 'enwbvs-archive-swatch-font-size',
        'title'   => 'Swatch Font Size',
        'desc'    => 'Swatch font size in px. Default is 15',
        'type'    => 'number',
        'default' => '15',
    ) ),
    ),
        array(
        'tab_id'        => 'custom_css',
        'section_id'    => 'section_css',
        'section_title' => 'Custom CSS (Optional)',
        'section_order' => 10,
        'fields'        => array( array(
        'id'          => 'enwbvs_code_editor',
        'title'       => 'Custom Css (Optional)',
        'desc'        => 'Write your custom css code above without any <style>,<style> tags',
        'placeholder' => '',
        'type'        => 'textarea',
        'mimetype'    => 'css',
        'default'     => '',
    ) ),
    ),
        array(
        'tab_id'        => 'debug_mode',
        'section_id'    => 'enable_debug',
        'section_title' => 'Enable Debug Mode',
        'section_order' => 10,
        'fields'        => array( array(
        'id'       => 'enwbvs-enable-debug',
        'title'    => 'Enable Debug Mode',
        'subtitle' => 'Enable debug mode for troubleshooting css and jquery codes',
        'type'     => 'toggle',
        'default'  => '0',
    ) ),
    ),
        array(
        'tab_id'        => 'premium_features',
        'section_id'    => 'show_premium',
        'section_title' => 'Premium Version for Premium Features',
        'section_order' => 10,
        'fields'        => array( array(
        'id'       => 'enwbvs-premium-features',
        'title'    => enwbvs_premium_features_html(),
        'subtitle' => '',
        'type'     => 'custom',
        'default'  => '',
    ) ),
    )
    );
    return $wpsf_settings;
}
