<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'choco' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '5lq,%`uQ`|L;z$*Ly7W^l^fJUIS2]]cwesA4m#>d:[f#6d-U6Jb[@B.K!XVS TuZ' );
define( 'SECURE_AUTH_KEY',  '&6vp,)(V73a7LycCboCy8#p8k(-rpD,?yQ)Bvk_g-y|A.=N8gY05ihWfIO)yPhe!' );
define( 'LOGGED_IN_KEY',    'NE5FXH_|agN@Xb{{(g`wfKn{L2QR1!eOeJM J,k/L-mm#QW~@o9/i<c06nIFy]>A' );
define( 'NONCE_KEY',        '*?gb?;a`#wRJ^<W1Mt/+fMan@1n~}e-XC{]Lq4M5d/C{n@i8-*WgO `hRm_o/+w~' );
define( 'AUTH_SALT',        '[7RCP0/#g[2KxcP*lo?6F9PE9UKsm^]{E?Ht4+5QP;VzN46xmc@>}~t8!-_8{wJ=' );
define( 'SECURE_AUTH_SALT', '~>O~RiaH~WeB`P<P2P(;AG%fo=DNZYQ5:z]wXV.n^v#H{]7,$yqWMI]!/ mdgBz0' );
define( 'LOGGED_IN_SALT',   ':bc{MmDL.d*68zX:nq]7%N%`/xg|;N0knMXbG[i7r5GaB<_X:HKHPTHRp-0>m40t' );
define( 'NONCE_SALT',       '1I@zmO4QlrMb]H:4J&!j8u-4mu(^lTuI4MDWq4.7[k!tEENT#t`M jAH1eQ$sP[/' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
