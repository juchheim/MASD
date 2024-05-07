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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'U82L/W><7<r!sD4K{REytHSns?nf|o$[+g(XpuT8w408JESpK<+:8/]%2L12l]o&' );
define( 'SECURE_AUTH_KEY',   'v23*ho)2:jzZMRYBR)Q!pf(YUA:p$}>P|lO$PeMJ>-w7C+o^H$u4@}LgiIZRjU}u' );
define( 'LOGGED_IN_KEY',     'yV{)FZ6:[[K>SYYf4m:OvC*lWj1n#m&SaDd@nqh`V)8Wc?u||,Nv2OrOelwA_iJn' );
define( 'NONCE_KEY',         '_BnHpa%Lky0qGBnC)%#Y<bu.@3KL/hpb]CdZwB/9_X|Na*dP,_pR!FL<]%xRW#{I' );
define( 'AUTH_SALT',         'n`}s~|L+`=bO*;T9bwYdGP1_3O. UqvJ(22fXD,-W=oy0Y/+cD=Io]`x[N10^4d}' );
define( 'SECURE_AUTH_SALT',  'W6<yI4*w(y{=A1sJ;S|xb0:Tzh!#<sk1.Lb`FXW@f{o!Bb-*^Bhu_TX0NX3T?p)p' );
define( 'LOGGED_IN_SALT',    'D#2:6jZ/)`!+wGp&KdROu^|Gb6&]cLH[utH/IrqnA`L%?0cLe~-V*}=LKLJnER5]' );
define( 'NONCE_SALT',        '2I^FQe(JPAn&)eDd|*0{Yq_F#iZy9$n`7JS/RQI!5zOi<tL4T$|4NN]~cB#rM#ps' );
define( 'WP_CACHE_KEY_SALT', 'QMsf9n.FpM!M$W/K[ukHr1ZkcB!^bkaa46Z0l<el(]:N`e48tpyPR8HPajdvlxO<' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
$base = '/';
define( 'DOMAIN_CURRENT_SITE', 'masd.local' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );

/* define('WP_DEBUG_DISPLAY', false); */
/*
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
*/
