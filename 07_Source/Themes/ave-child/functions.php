<?php

add_action( 'wp_enqueue_scripts', 'liquid_child_theme_style', 99 );

function liquid_parent_theme_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
function liquid_child_theme_style(){
	// Version the stylesheet by its modification time so edits are picked up
	// immediately. Without this the file is served with no version string and
	// browsers keep serving a cached copy long after the CSS has changed.
	$css_path = get_stylesheet_directory() . '/style.css';
	$css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : false;

	wp_enqueue_style( 'child-one-style', get_stylesheet_directory_uri() . '/style.css', array(), $css_ver );

	// Header strapline: shown on every page, so enqueued alongside the main
	// stylesheet rather than on demand. Kept in its own file because WP Rocket
	// was serving a stale minified copy of style.css - see the note in
	// assets/css/header-strapline.css.
	$strap_path = get_stylesheet_directory() . '/assets/css/header-strapline.css';
	$strap_ver  = file_exists( $strap_path ) ? filemtime( $strap_path ) : false;
	wp_enqueue_style(
		'twb-header-strapline',
		get_stylesheet_directory_uri() . '/assets/css/header-strapline.css',
		array( 'child-one-style' ),
		$strap_ver
	);

	// Hero band link colours. Site-wide because every inner page carries the
	// same dark hero row. Kept in its own file rather than style.css for the
	// same reason as the strapline above.
	$hero_path = get_stylesheet_directory() . '/assets/css/hero-colours.css';
	$hero_ver  = file_exists( $hero_path ) ? filemtime( $hero_path ) : false;
	wp_enqueue_style(
		'twb-hero-colours',
		get_stylesheet_directory_uri() . '/assets/css/hero-colours.css',
		array( 'twb-tokens', 'child-one-style' ),
		$hero_ver
	);

	// Content layout helpers (image text-wrap). Site-wide: the classes are
	// applied in page content by editors, so there is no reliable hook that
	// says "this page uses one".
	$cl_path = get_stylesheet_directory() . '/assets/css/content-layout.css';
	$cl_ver  = file_exists( $cl_path ) ? filemtime( $cl_path ) : false;
	wp_enqueue_style(
		'twb-content-layout',
		get_stylesheet_directory_uri() . '/assets/css/content-layout.css',
		array( 'child-one-style' ),
		$cl_ver
	);
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
 * Equalise the Special Interest listing cards across both of its rows.
 *
 * Loaded only on that page (ID 4476); the CSS in style.css handles everything
 * else, this just levels the two separate WPBakery rows against each other.
 */
function twb_child_special_interest_cards() {
	if ( ! is_page( 4476 ) ) {
		return;
	}

	$js  = get_stylesheet_directory() . '/assets/js/special-interest-cards.js';
	$ver = file_exists( $js ) ? filemtime( $js ) : false;

	wp_enqueue_script(
		'twb-special-interest-cards',
		get_stylesheet_directory_uri() . '/assets/js/special-interest-cards.js',
		array(),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_child_special_interest_cards' );

/**
 * Keep the "Marketing Email Consent" checkbox on the Contact page's forms
 * (both the Individual form, quform_1_8, and the Business form, quform_3_8)
 * in sync with each form's hidden status field (quform_{1,3}_9), so the
 * admin notification email always states plainly whether the client agreed
 * or not — Quform's merge tags render an unticked checkbox as an empty
 * string, which would otherwise say nothing at all.
 */
function twb_child_marketing_consent() {
	if ( ! is_page( 4014 ) ) {
		return;
	}

	$js  = get_stylesheet_directory() . '/assets/js/marketing-consent.js';
	$ver = file_exists( $js ) ? filemtime( $js ) : false;

	wp_enqueue_script(
		'twb-marketing-consent',
		get_stylesheet_directory_uri() . '/assets/js/marketing-consent.js',
		array(),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_child_marketing_consent' );

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
 * Tag every Destinations page (and its region hubs) with a body class.
 *
 * The destination pages share a common template but no common body class, so
 * destination-wide styling (header text colour, "What to do" list indentation)
 * had nothing stable to hook onto. Add `twb-destination` to any page that is the
 * Destinations page or a descendant of it.
 */
function twb_child_destination_body_class( $classes ) {
	if ( ! is_page() ) {
		return $classes;
	}

	$destinations = get_page_by_path( 'destinations' );
	if ( ! $destinations ) {
		return $classes;
	}

	$id = get_queried_object_id();
	if ( $id === (int) $destinations->ID || in_array( $destinations->ID, get_post_ancestors( $id ), true ) ) {
		$classes[] = 'twb-destination';
	}

	return $classes;
}
add_filter( 'body_class', 'twb_child_destination_body_class' );

/**
 * Load custom child-theme components.
 *
 * A single, deterministic loader (inc/loader.php) requires every component in
 * explicit dependency order (see ADR-001 D9). New components are added in the
 * loader, not here — functions.php stays thin.
 */
require_once get_stylesheet_directory() . '/inc/loader.php';