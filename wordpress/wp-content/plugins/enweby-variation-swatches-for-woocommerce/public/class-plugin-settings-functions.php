<?php
/**
 * Settings Functions
 *
 * @author  Enweby
 * @link       https://www.enweby.com/
 * @since      1.0.0
 * @package    Enweby_Variation_Swatches_For_Woocommerce
 * @subpackage Enweby_Variation_Swatches_For_Woocommerce/public
 */

namespace Enwbvs\Enweby\PluginSettingsFunctions;

/**
 * Settings class
 */
class Plugin_Settings_Functions {
		/**
		 * Get a setting from an option group.
		 *
		 * @param string $option_group option group.
		 * @param string $section_id May also be prefixed with tab ID.
		 * @param string $field_id field id.
		 *
		 * @return mixed
		 */
	public function wpsf_get_setting( $option_group, $section_id, $field_id ) {
		$options = get_option( $option_group . '_settings' );
		if ( isset( $options[ $section_id . '_' . $field_id ] ) ) {
			return $options[ $section_id . '_' . $field_id ];
		}

		return false;
	}

		/**
		 * Delete all the saved settings from a settings file/option group
		 *
		 * @param string $option_group option group.
		 */
	public function wpsf_delete_settings( $option_group ) {
		delete_option( $option_group . '_settings' );
	}
}
