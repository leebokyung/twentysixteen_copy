<?php
/**
 * sir community back compat functionality
 *
 * Prevents sir community from running on WordPress versions prior to 4.4,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.4.
 *
 * @package WordPress
 */

/**
 * Prevent switching to sir community on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since sir community 1.0
 */
function sircomm_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );

	unset( $_GET['activated'] );

	add_action( 'admin_notices', 'sircomm_upgrade_notice' );
}
add_action( 'after_switch_theme', 'sircomm_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * sir community on WordPress versions prior to 4.4.
 *
 * @since sir community 1.0
 *
 * @global string $wp_version WordPress version.
 */
function sircomm_upgrade_notice() {
	$message = sprintf( __( 'sir community requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'gnucommerce-2016-summer-ipha' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 4.4.
 *
 * @since sir community 1.0
 *
 * @global string $wp_version WordPress version.
 */
function sircomm_customize() {
	wp_die( sprintf( __( 'sir community requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'gnucommerce-2016-summer-ipha' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'sircomm_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 4.4.
 *
 * @since sir community 1.0
 *
 * @global string $wp_version WordPress version.
 */
function sircomm_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'sir community requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'gnucommerce-2016-summer-ipha' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'sircomm_preview' );
