<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @link https://github.com/timber/starter-theme
 */

namespace App;

use Timber\Timber;

$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
    require_once __DIR__ . '/src/Kingdom.php';
    Timber::init();
}

require get_template_directory() . "/includes/enqueue.php";
require get_template_directory() . "/includes/extends.php";

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Check for ACF
 */
if ( class_exists('acf_pro') || class_exists('acf') )
{
    // Define path and URL to the ACF plugin.
    define( 'KING_ACF_PATH', get_stylesheet_directory() . '/includes/acf/' );
    define( 'KING_ACF_URL', get_stylesheet_directory_uri() . '/includes/acf/' );

    // Include the ACF plugin.
    include_once( KING_ACF_PATH . 'acf.php' );

} else {
    add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>ACF not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#acf' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);
}

new Kingdom();
