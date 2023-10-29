<?php
/**
 * Define the common functions
 *
 * Loads and defines common functions for this plugin.
 *
 * @link       https://www.enweby.com/
 * @since      1.0.0
 *
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/includes
 */

/**
 * Define the commoan functions.
 *
 * Loads and defines common functions for this plugin
 *
 * @since      1.0.0
 * @package    Enweby_Variation_Swatches_For_Woocommerce.
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/includes
 * @author     Enweby <support@enweby.com>
 */
class Enwbvs_Common {

	/**
	 * Add new attributes type.
	 *
	 * @param array $types [description].
	 */
	public function add_attribute_types( $types ) {
		$more_types = array(
			'color' => __( 'Color', 'enweby-variation-swatches-for-woocommerce' ),
			'image' => __( 'Image', 'enweby-variation-swatches-for-woocommerce' ),
			'label' => __( 'Button/Label', 'enweby-variation-swatches-for-woocommerce' ),
			'radio' => __( 'Radio', 'enweby-variation-swatches-for-woocommerce' ),
		);

		$types = array_merge( $types, $more_types );
		return $types;
	}

	/**
	 * Get attribute's properties.
	 *
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return object
	 */
	public function get_attribute( $taxonomy ) {
		global $wpdb;

		$attr = substr( $taxonomy, 3 );
		$attr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $attr ) ); // phpcs:ignore

		return $attr;
	}

	/**
	 * Parses the term value specifically for ux_color. Checks and
	 * returns parsed data for single and dual color value(s).
	 *
	 * @param string $value The term meta value.
	 *
	 * @return string[].
	 */
	public function parse_enwbvs_color_term_meta( $value ) {
		$data = array(
			'color'   => '',
			'color_2' => '',
			'class'   => '',
			'style'   => '',
		);

		$colors = explode( ',', $value );

		$data['color'] = $colors[0];

		if ( count( $colors ) > 1 ) {
			$data['color_2'] = $colors[1];
			$data['style']   = "--swatch-color: $colors[0]; --swatch-color-secondary: $colors[1];";
			$data['class']   = 'ux-swatch__color--dual-color';
		} else {
			$data['style'] = 'background-color: ' . $value;
			$data['class'] = 'enwbvs-swatch__filter-widget_single-color';
		}

		return $data;
	}

}
