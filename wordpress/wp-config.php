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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpstore' );

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
define( 'AUTH_KEY',         'A2qDqn|o4hb1<%naM[/DA5HhF?A-eK>(6BzDYC`%y(~h3Ks+QNf=4s/+RbtH,CBR' );
define( 'SECURE_AUTH_KEY',  'mkD,@=JYYl7w;|$LJxr34Wzrk#!pglbee>n4]=GODU+o~<GMNq&A (.Xr1=0][}-' );
define( 'LOGGED_IN_KEY',    'L]<[R`&?-Z@pXYkg@r&dW<mNDGX}8m>I:bn$#a:HTm|IJ@vJ?T[V8EdO%0W$]3>=' );
define( 'NONCE_KEY',        'SvzteL*j*1<xvHOi&*R%H37=:G7G.QIL}5bKEE.!+G7|ud$;D5USFI}+,1fNha%r' );
define( 'AUTH_SALT',        'sQTBQ<u8fH)h[6e%{?n!!s/A#dXL)MrKEde,w>8okkPe}%n`qk3c}[&x}3#>i~h;' );
define( 'SECURE_AUTH_SALT', '0D0Vbe=kM5A%_DWaOE.cHNUg]H%^LrSPjV2>V#iz/jpj+{k|/&3z-pDDf>j{ECmW' );
define( 'LOGGED_IN_SALT',   'aXBm/B,Gjv8P/Sd[Z<=22.|9mbh!wBbXhF-UU&H^s>q]mNk^UX3QZQn:2FsD&N>f' );
define( 'NONCE_SALT',       'Z)!h(}p<;;eLTkedNO5U>vG@+||e[/IAgt@Z:[)y8~UB8L@;=[ZaNlCsoK?F,!Vo' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
