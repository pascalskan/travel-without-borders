<?php
/**
 * Right-size the images inside `ld_content_box` cards.
 *
 * The card elements ask the theme for the FULL size image and WordPress then
 * writes the matching `sizes` attribute, which for a 1600px original reads
 * `(max-width: 1600px) 100vw, 1600px`. That tells the browser the picture
 * fills the window, so it fetches the largest candidate in the srcset - for a
 * card whose image box is 275px wide.
 *
 * Measured on the homepage before this file existed: 30 image requests
 * totalling 5,162KB on desktop, 21 totalling 2,399KB on a phone. The six
 * "Wide choice of destinations" tiles and the twelve carousel cards were
 * pulling originals of 300-465KB each.
 *
 * Nothing here changes which file is *available* - it only corrects the hint,
 * so the browser can pick the 300w or 1024w version WordPress already
 * generated. No visual change is intended and none was measured.
 *
 * Scope: only while an `ld_content_box` shortcode is rendering. The obvious
 * hooks (`wp_calculate_image_sizes`, `wp_get_attachment_image_attributes`)
 * apply to every image on the site, including page banners that genuinely do
 * fill the viewport, so the flag below narrows them to these cards alone.
 *
 * `pre_do_shortcode_tag` fires BEFORE the shortcode runs and receives its
 * attributes, which is what makes the per-style widths below possible;
 * `do_shortcode_tag` fires after and clears the flag.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widths the card image actually occupies, measured on the rendered page at
 * 390, 768, 992, 1200, 1440 and 1920px:
 *
 *   s03  "Wide choice" tiles   360 / 595 / 617 / 750 / 750 / 750
 *   s04  carousel cards        360 / 353 / 225 / 275 / 275 / 275
 *
 * Expressed as viewport fractions below the desktop breakpoint and as a fixed
 * px above it, because above 1200 the row stops growing.
 */
function twb_card_image_sizes_for_style( $style ) {
	$map = array(
		's03' => '(max-width: 767px) 92vw, (max-width: 991px) 78vw, 750px',
		's04' => '(max-width: 767px) 92vw, (max-width: 991px) 46vw, 275px',
	);

	// Anything else falls back to the roomier of the two rather than the
	// tighter one: over-fetching is a slow card, under-fetching is a blurry one.
	return isset( $map[ $style ] ) ? $map[ $style ] : $map['s03'];
}

/**
 * Raise the flag as a content box starts rendering, carrying its style.
 *
 * Returns false unchanged - returning anything else would short-circuit the
 * shortcode and render nothing.
 */
function twb_card_image_sizes_open( $short_circuit, $tag, $attr ) {
	if ( 'ld_content_box' === $tag ) {
		$style = ( is_array( $attr ) && ! empty( $attr['style'] ) ) ? $attr['style'] : '';
		$GLOBALS['twb_card_image_sizes'] = twb_card_image_sizes_for_style( $style );
	}

	return $short_circuit;
}
add_filter( 'pre_do_shortcode_tag', 'twb_card_image_sizes_open', 10, 3 );

/**
 * Lower it again the moment the box is done, so no other image can inherit it.
 */
function twb_card_image_sizes_close( $output, $tag ) {
	if ( 'ld_content_box' === $tag ) {
		unset( $GLOBALS['twb_card_image_sizes'] );
	}

	return $output;
}
add_filter( 'do_shortcode_tag', 'twb_card_image_sizes_close', 10, 2 );

/**
 * Stop the card asking for the FULL file in the first place.
 *
 * Correcting `sizes` alone was not enough, and measurement showed why. Smush's
 * lazyload writes `src` first and `srcset` a moment later, so the browser
 * fetched the full-size original for `src` AND then a 1024w candidate once the
 * srcset arrived - two requests where there had been one. Desktop image weight
 * fell 5,162KB to 4,481KB, but request count rose 30 to 37, and phones did not
 * improve at all.
 *
 * Resolving the card to `large` instead means `src`, `srcset` and `sizes` all
 * describe the same sensibly sized picture. `large` is 1024w here; the 300w
 * candidate stays in the srcset for phones to choose.
 *
 * The static guard matters: this filter calls the function it is filtering, so
 * without it the first card would recurse until the stack gave out.
 */
function twb_card_image_downsize( $image, $attachment_id, $size ) {
	static $busy = false;

	if ( $busy || empty( $GLOBALS['twb_card_image_sizes'] ) || 'full' !== $size ) {
		return $image;
	}

	$busy  = true;
	$large = wp_get_attachment_image_src( $attachment_id, 'large' );
	$busy  = false;

	// Only take it if a real intermediate came back. When an original is smaller
	// than the `large` threshold WordPress hands back the original again, and
	// swapping it for itself would be pointless churn.
	if ( ! empty( $large[0] ) && ! empty( $large[1] ) && $large[1] < $image[1] ) {
		return $large;
	}

	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'twb_card_image_downsize', 10, 3 );

/**
 * Replace the `sizes` hint while the flag is up.
 *
 * Priority 20 so it runs after anything that sets `sizes` at the default.
 */
function twb_card_image_sizes_apply( $attr ) {
	if ( ! empty( $GLOBALS['twb_card_image_sizes'] ) && isset( $attr['sizes'] ) ) {
		$attr['sizes'] = $GLOBALS['twb_card_image_sizes'];
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twb_card_image_sizes_apply', 20 );
