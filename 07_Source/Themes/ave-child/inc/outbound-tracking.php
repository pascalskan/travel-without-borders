<?php
/**
 * TWB Outbound Partner Link Tracking
 *
 * Loads the script that reports partner-link clicks into the site's existing
 * Google Tag Manager dataLayer. See assets/js/outbound-tracking.js for why
 * dataLayer is the correct integration point here rather than gtag().
 *
 * Loaded on every front-end page rather than on demand: partner links are
 * placed in page content by editors, so there is no reliable hook that says
 * "this page has one", and the script is under 2KB.
 *
 * The script does no consent checking of its own and does not need to. It
 * only ever pushes to an array; whether anything is transmitted is decided
 * entirely by whether the visitor's consent allowed GTM to load.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function twb_outbound_tracking_assets() {
	if ( is_admin() ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/js/outbound-tracking.js';
	$ver  = file_exists( $path ) ? filemtime( $path ) : false;

	wp_enqueue_script(
		'twb-outbound-tracking',
		get_stylesheet_directory_uri() . '/assets/js/outbound-tracking.js',
		array(),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_outbound_tracking_assets' );
