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
	$css      = $path . '/assets/css/hero-carousel.css';
	$js       = $path . '/assets/js/hero-carousel.js';
	$css_ver  = file_exists( $css ) ? filemtime( $css ) : false;
	$js_ver   = file_exists( $js ) ? filemtime( $js ) : false;

	wp_register_style(
		'twb-hero-carousel',
		$dir . '/assets/css/hero-carousel.css',
		array( 'flickity' ),
		$css_ver
	);

	wp_register_script(
		'twb-hero-carousel',
		$dir . '/assets/js/hero-carousel.js',
		array( 'flickity', 'flickity-fade' ),
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_child_register_assets' );

/**
 * Load custom WPBakery elements added by the child theme.
 */
require_once get_stylesheet_directory() . '/inc/hero-carousel.php';