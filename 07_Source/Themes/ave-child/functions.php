<?php

add_action( 'wp_enqueue_scripts', 'liquid_child_theme_style', 99 );

function liquid_parent_theme_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
function liquid_child_theme_style(){
    wp_enqueue_style( 'child-one-style', get_stylesheet_directory_uri() . '/style.css' );
}

/**
 * Register child-theme assets for the TWB Hero Carousel.
 *
 * Assets are only *enqueued* on demand from the element's render callback, so
 * they load solely on pages that actually use the hero. Flickity itself is
 * provided (and registered) by the parent Ave theme.
 */
function twb_child_register_assets() {
	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();

	// Version assets by file modification time so edits always bust the cache.
	$tokens   = $path . '/assets/css/twb-tokens.css';
	$css      = $path . '/assets/css/hero-carousel.css';
	$js       = $path . '/assets/js/hero-carousel.js';
	$tokens_ver = file_exists( $tokens ) ? filemtime( $tokens ) : false;
	$css_ver  = file_exists( $css ) ? filemtime( $css ) : false;
	$js_ver   = file_exists( $js ) ? filemtime( $js ) : false;

	// Shared design token layer. Registered here so component stylesheets can
	// declare it as a dependency and enqueue it on demand. Populated in M2.
	wp_register_style(
		'twb-tokens',
		$dir . '/assets/css/twb-tokens.css',
		array(),
		$tokens_ver
	);

	wp_register_style(
		'twb-hero-carousel',
		$dir . '/assets/css/hero-carousel.css',
		array( 'flickity' ),
		$css_ver
	);

	wp_register_script(
		'twb-hero-carousel',
		$dir . '/assets/js/hero-carousel.js',
		array( 'flickity' ),
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_child_register_assets' );

/**
 * Remove the flickity-fade plugin on the front end.
 *
 * Ave loads flickity-fade site-wide, but it patches Flickity's cell positioning
 * globally — which makes the hero's slide transition unreliable (slides desync /
 * stick mid-fade). None of Ave's own carousels use fade, so dequeuing it is safe
 * and lets the hero use Flickity's standard, reliable slide transition.
 */
function twb_remove_flickity_fade() {
	wp_dequeue_script( 'flickity-fade' );
	wp_deregister_script( 'flickity-fade' );
}
add_action( 'wp_enqueue_scripts', 'twb_remove_flickity_fade', 100 );

/**
 * Load custom child-theme components.
 *
 * A single, deterministic loader (inc/loader.php) requires every component in
 * explicit dependency order (see ADR-001 D9). New components are added in the
 * loader, not here — functions.php stays thin.
 */
require_once get_stylesheet_directory() . '/inc/loader.php';