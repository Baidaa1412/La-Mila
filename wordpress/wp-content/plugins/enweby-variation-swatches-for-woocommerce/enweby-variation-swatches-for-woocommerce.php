<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.enweby.com/
 * @since             1.0.0
 * @package           Enweby_Variation_Swatches_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Enweby Variation Swatches for Woocommerce
 * Plugin URI:        https://www.enweby.com/product/variation-swatches-for-woocommerce/
 * Description:       A lightweight plugin by Enweby used to display product variation swatches to enhance customer experience and engagement.
 * Version:           1.0.5
 * Author:            Enweby
 * Author URI:        https://www.enweby.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       enweby-variation-swatches-for-woocommerce
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'enwbvs_fs' ) ) {
    enwbvs_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'enwbvs_fs' ) ) {
        // ... Freemius integration snippet ...
        
        if ( !function_exists( 'enwbvs_fs' ) ) {
            /** Create a helper function for easy SDK access.***/
            function enwbvs_fs()
            {
                global  $enwbvs_fs ;
                
                if ( !isset( $enwbvs_fs ) ) {
                    // Include Freemius SDK.
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                    $enwbvs_fs = fs_dynamic_init( array(
                        'id'             => '11585',
                        'slug'           => 'enweby-variation-swatches-for-woocommerce',
                        'type'           => 'plugin',
                        'public_key'     => 'pk_6cf7b5ec9095f6ec91d7e2d8998b7',
                        'is_premium'     => false,
                        'premium_suffix' => 'Premium',
                        'has_addons'     => false,
                        'has_paid_plans' => true,
                        'menu'           => array(
                        'slug' => 'enweby-variation-swatches-for-woocommerce-settings',
                    ),
                        'is_live'        => true,
                    ) );
                }
                
                return $enwbvs_fs;
            }
            
            // Init Freemius.
            enwbvs_fs();
            // remove a permission from persmission list from optin screen
            enwbvs_fs()->add_filter( 'permission_list', 'enwbvs_remove_extensions_permission' );
            // Not like register_uninstall_hook(), you do NOT have to use a static function.
            enwbvs_fs()->add_action( 'after_uninstall', 'enwbvs_fs_uninstall_cleanup' );
            // Signal that SDK was initiated.
            do_action( 'enwbvs_fs_loaded' );
        }
    
    }
    /***.. Your plugin's main file logic ...*/
    if ( !function_exists( 'enwbvs_fs_uninstall_cleanup' ) ) {
        /**
         * Uninstall Cleanup
         */
        function enwbvs_fs_uninstall_cleanup()
        {
        }
    
    }
    /**
     * Removing extension info permission from optin screen
     *
     * @since    1.0.2
     */
    function enwbvs_remove_extensions_permission( $permissions )
    {
        foreach ( $permissions as $key => $val ) {
            if ( $val['id'] !== 'extensions' ) {
                continue;
            }
            unset( $permissions[$key] );
            break;
        }
        return $permissions;
    }
    
    if ( !function_exists( 'enwbvs_is_woocommerce_active' ) ) {
        /**
         * Checking if woocommerce is active.
         */
        function enwbvs_is_woocommerce_active()
        {
            $active_plugins = (array) get_option( 'active_plugins', array() );
            if ( is_multisite() ) {
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
            }
            return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || class_exists( 'WooCommerce' );
            // phpcs:ignore
        }
    
    }
    
    if ( enwbvs_is_woocommerce_active() ) {
        /**
         * Currently plugin version.
         * Start at version 1.0.0 and use SemVer - https://semver.org
         * This will be updaed for every new version.
         */
        define( 'ENWEBY_VARIATION_SWATCHES_FOR_WOOCOMMERCE_VERSION', '1.0.5' );
        /**
         * Plugin name.
         * used to get plugin dashed name.
         */
        define( 'ENWEBY_VARIATION_SWATCHES_FOR_WOOCOMMERCE_PLUGIN_NAME', 'enweby-variation-swatches-for-woocommerce' );
        /**
         * Plugin base name.
         * used to locate plugin resources primarily code files
         * Start at version 1.0.0
         */
        define( 'ENWEBY_VARIATION_SWATCHES_FOR_WOOCOMMERCE_BASE_NAME', plugin_basename( __FILE__ ) );
        /**
         * To add admin settings in the admmin and to fetch in the frontend later
         */
        define( 'ENWEBY_VARIATION_SWATCHES_FWAS', 'enweby_variation_swatches_for_woocommerce' );
        /**
         * The code that runs during plugin activation.
         * This action is documented in includes/class-enweby-variation-swatches-for-woocommerce-activator.php
         */
        function activate_enweby_variation_swatches_for_woocommerce()
        {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-enwbvs-activator.php';
            Enwbvs_Activator::activate();
        }
        
        /**
         * The code that runs during plugin deactivation.
         * This action is documented in includes/class-enweby-variation-swatches-for-woocommerce-deactivator.php
         */
        function deactivate_enweby_variation_swatches_for_woocommerce()
        {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-enwbvs-deactivator.php';
            Enwbvs_Deactivator::deactivate();
        }
        
        register_activation_hook( __FILE__, 'activate_enweby_variation_swatches_for_woocommerce' );
        register_deactivation_hook( __FILE__, 'deactivate_enweby_variation_swatches_for_woocommerce' );
        /**
         * The core plugin class that is used to define internationalization,
         * admin-specific hooks, and public-facing site hooks.
         */
        require plugin_dir_path( __FILE__ ) . 'includes/class-enwbvs.php';
        /**
         * Begins execution of the plugin.
         *
         * Since everything within the plugin is registered via hooks,
         * then kicking off the plugin from this point in the file does
         * not affect the page life cycle.
         *
         * @since    1.0.0
         */
        function run_enwbvs()
        {
            $plugin = new Enwbvs();
            $plugin->run();
        }
        
        run_enwbvs();
    }

}
