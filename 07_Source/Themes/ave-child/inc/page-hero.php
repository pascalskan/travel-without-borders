<?php
/**
 * TWB Page Hero
 *
 * A lightweight, reusable inner-page hero: a brand-green text panel (eyebrow +
 * H1 + intro) beside a cover image. This mirrors the site's destination-landing
 * pattern (a coloured panel *beside* the image rather than text over it — see
 * the Design System Audit) and is deliberately NOT the homepage hero carousel.
 *
 * Reusable on any inner page (Testimonials, About, Tailor-made…). Owns its own
 * stylesheet, enqueued only when the element renders; no JavaScript.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register this component's stylesheet (enqueued on render).
 */
function twb_page_hero_register_assets() {
	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();
	$css  = $path . '/assets/css/page-hero.css';
	$ver  = file_exists( $css ) ? filemtime( $css ) : false;

	wp_register_style( 'twb-page-hero', $dir . '/assets/css/page-hero.css', array( 'twb-tokens' ), $ver );
}
add_action( 'wp_enqueue_scripts', 'twb_page_hero_register_assets' );

/**
 * Register the element with WPBakery.
 */
function twb_page_hero_vc_map() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => __( 'TWB Page Hero', 'ave' ),
			'base'        => 'twb_page_hero',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-application-icon-large',
			'description' => __( 'Inner-page hero: heading and intro in a brand panel beside an image.', 'ave' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Eyebrow', 'ave' ),
					'param_name'  => 'eyebrow',
					'description' => __( 'Optional small label above the heading.', 'ave' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Heading', 'ave' ),
					'param_name'  => 'heading',
					'admin_label' => true,
					'description' => __( 'The page heading (rendered as the H1).', 'ave' ),
				),
				array(
					'type'       => 'textarea',
					'heading'    => __( 'Introduction', 'ave' ),
					'param_name' => 'intro',
				),
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'Image', 'ave' ),
					'param_name'  => 'image',
					'description' => __( 'Shown beside the panel (below it on mobile).', 'ave' ),
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'twb_page_hero_vc_map' );

/**
 * Render callback for [twb_page_hero].
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner content (unused).
 * @return string
 */
function twb_page_hero_render( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'eyebrow' => '',
			'heading' => '',
			'intro'   => '',
			'image'   => '',
		),
		$atts,
		'twb_page_hero'
	);

	$eyebrow  = trim( $atts['eyebrow'] );
	$heading  = trim( $atts['heading'] );
	$intro    = trim( $atts['intro'] );
	$image_id = absint( $atts['image'] );

	if ( '' === $heading && ! $image_id ) {
		return '';
	}

	wp_enqueue_style( 'twb-page-hero' );

	// Hero image is the LCP element: load it eagerly. Ave force-lazyloads every
	// wp_get_attachment_image, so bypass that filter for this one image (same
	// approach as the hero carousel's first slide).
	$img_html = '';
	if ( $image_id ) {
		$attr = array(
			'class'         => 'twb-page-hero__img',
			'alt'           => $heading,
			'decoding'      => 'async',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
		);
		$lazy_cb       = 'liquid_filter_gallery_img_atts';
		$lazy_priority = has_filter( 'wp_get_attachment_image_attributes', $lazy_cb );
		if ( false !== $lazy_priority ) {
			remove_filter( 'wp_get_attachment_image_attributes', $lazy_cb, $lazy_priority );
		}
		$img_html = wp_get_attachment_image( $image_id, 'large', false, $attr );
		if ( false !== $lazy_priority ) {
			add_filter( 'wp_get_attachment_image_attributes', $lazy_cb, $lazy_priority, 2 );
		}
	}

	ob_start();
	?>
	<section class="twb-page-hero<?php echo $img_html ? ' twb-page-hero--has-image' : ''; ?>">
		<div class="twb-page-hero__panel">
			<div class="twb-page-hero__panel-inner">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="twb-page-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<h1 class="twb-page-hero__heading"><?php echo esc_html( $heading ); ?></h1>
				<?php endif; ?>
				<?php if ( '' !== $intro ) : ?>
					<p class="twb-page-hero__intro"><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $img_html ) : ?>
			<div class="twb-page-hero__media">
				<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — wp_get_attachment_image output. ?>
			</div>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_page_hero', 'twb_page_hero_render' );
