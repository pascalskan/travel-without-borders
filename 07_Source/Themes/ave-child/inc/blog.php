<?php
/**
 * Blog: sharing, meta, comments and thumbnail resolution.
 *
 * Four separate problems are handled here, all of which live in the parent
 * theme or the ave-core plugin and therefore cannot be fixed by CSS alone:
 *
 *  1. Share icons — ave-core hard-codes a Twitter bird. Font Awesome 4.7 has
 *     no X glyph, so the icon is an inline SVG and the intent URL points at
 *     x.com. Instagram is deliberately absent: it has no web share-intent,
 *     so a link could only ever open a profile, not share the article.
 *
 *  2. Comments — removed from posts entirely, form and existing threads.
 *
 *  3. Blog index blur — `liquid-timeline-blog` is a 490x300 hard crop and is
 *     the only registered size at that aspect ratio. WordPress builds srcset
 *     only from sizes sharing the source's ratio, so the markup ships a
 *     single 490px file for a 473px slot: correct at 1x, upscaled 2x on every
 *     retina screen. Registering a matching 2x size gives srcset something to
 *     offer. Note this only helps where the original is large enough — several
 *     featured images are barely wider than the crop and need replacing.
 *
 *  4. Stylesheet — loaded only on blog views.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A 2x companion to the theme's 490x300 timeline crop.
 *
 * Same aspect ratio to the fourth decimal, which is what lets WordPress
 * include it in the generated srcset alongside the 490.
 */
function twb_blog_image_sizes() {
	add_image_size( 'twb-timeline-blog-2x', 980, 600, true );
}
add_action( 'after_setup_theme', 'twb_blog_image_sizes' );

/**
 * Let the 2x size actually be generated.
 *
 * The parent theme's liquid_media_prevent_resize_on_upload() throws away
 * every registered size except thumbnail/medium/large, because it builds its
 * own `liquid-*` crops lazily through aq_resize() instead of at upload time.
 * That blanket strip catches this size too, so it is added back at a later
 * priority. Only this one size is restored — the theme's disk-saving
 * behaviour for its own fifty-odd sizes is left exactly as it is.
 */
function twb_blog_allow_2x_size( $sizes ) {
	$registered = wp_get_registered_image_subsizes();
	if ( isset( $registered['twb-timeline-blog-2x'] ) ) {
		$sizes['twb-timeline-blog-2x'] = $registered['twb-timeline-blog-2x'];
	}
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'twb_blog_allow_2x_size', 20 );

/**
 * Blog stylesheet, on blog views only.
 */
function twb_blog_styles() {
	if ( ! ( is_home() || is_single() || is_archive() ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/blog.css';
	$ver  = file_exists( $path ) ? filemtime( $path ) : false;

	wp_enqueue_style(
		'twb-blog',
		get_stylesheet_directory_uri() . '/assets/css/blog.css',
		array( 'child-one-style' ),
		$ver
	);
}
add_action( 'wp_enqueue_scripts', 'twb_blog_styles' );

/**
 * Close comments on posts and hide any that already exist.
 *
 * `default.php` renders the comment template when comments are open *or* a
 * comment already exists, so both have to be answered to remove the section.
 */
function twb_blog_comments_closed( $open, $post_id ) {
	return ( 'post' === get_post_type( $post_id ) ) ? false : $open;
}
add_filter( 'comments_open', 'twb_blog_comments_closed', 20, 2 );

function twb_blog_hide_existing_comments( $count, $post_id ) {
	return ( 'post' === get_post_type( $post_id ) ) ? 0 : $count;
}
add_filter( 'get_comments_number', 'twb_blog_hide_existing_comments', 20, 2 );

/**
 * The X logo as inline SVG.
 *
 * Font Awesome 4.7 predates the rebrand and ships only `fa-twitter`, so the
 * mark is drawn here rather than pulling in a whole new icon font for one
 * glyph. `currentColor` keeps it inheriting the theme's icon colouring.
 */
function twb_x_logo_svg() {
	return '<svg class="twb-icon-x" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" '
		. 'width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false">'
		. '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68'
		. 'l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
}

/**
 * Share row for single posts.
 *
 * Replaces ave-core's `liquid_portfolio_share()`, which cannot be overridden
 * from a child theme: the plugin loads first and its own `function_exists`
 * guard means it always wins. The single-post template calls this instead.
 *
 * @param array $args Optional. `class`, `before`, `after`.
 */
function twb_blog_share( $args = array() ) {
	$defaults = array(
		'class'  => 'social-icon circle branded social-icon-sm twb-share',
		'before' => '<span class="share-links"><span class="text-uppercase ltr-sp-1">'
			. esc_html__( 'Share On', 'ave' ) . '</span>',
		'after'  => '</span>',
	);
	$args = wp_parse_args( $args, $defaults );

	$url             = rawurlencode( get_the_permalink() );
	$title           = rawurlencode( get_the_title() );
	$site            = rawurlencode( get_bloginfo( 'name' ) );
	$pinterest_image = wp_get_attachment_url( get_post_thumbnail_id() );

	$links = array(
		array(
			'label' => 'Facebook',
			'href'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'icon'  => '<i class="fa fa-facebook"></i>',
		),
		array(
			'label' => 'X',
			'href'  => 'https://x.com/intent/tweet?text=' . $title . '&url=' . $url,
			'icon'  => twb_x_logo_svg(),
		),
	);

	if ( ! empty( $pinterest_image ) ) {
		$links[] = array(
			'label' => 'Pinterest',
			'href'  => 'https://pinterest.com/pin/create/button/?url=' . $url
				. '&media=' . rawurlencode( $pinterest_image ) . '&description=' . $title,
			'icon'  => '<i class="fa fa-pinterest-p"></i>',
		);
	}

	$links[] = array(
		'label' => 'LinkedIn',
		'href'  => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url
			. '&title=' . $title . '&source=' . $site,
		'icon'  => '<i class="fa fa-linkedin"></i>',
	);

	echo wp_kses_post( $args['before'] );
	printf( '<ul class="%s">', esc_attr( $args['class'] ) );
	foreach ( $links as $l ) {
		printf(
			'<li><a rel="nofollow noopener" target="_blank" href="%s" aria-label="%s">%s</a></li>',
			esc_url( $l['href'] ),
			/* translators: %s: social network name. */
			esc_attr( sprintf( __( 'Share on %s', 'ave' ), $l['label'] ) ),
			$l['icon'] // phpcs:ignore WordPress.Security.EscapeOutput -- markup built above.
		);
	}
	echo '</ul>';
	echo wp_kses_post( $args['after'] );
}
