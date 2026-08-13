<?php
/**
 * TWB Page Hero / Split Band
 *
 * The site's signature split section: a photo on one side and a dark charcoal
 * panel (heading + intro + optional yellow underline link) on the other. Used
 * for inner-page heroes AND closing CTA bands (e.g. the Groups page's "Our
 * professionalism is our main asset"). This mirrors the existing site pattern —
 * it is NOT the homepage hero carousel.
 *
 * Reusable on any inner page. Owns its own stylesheet, enqueued only when the
 * element renders; no JavaScript.
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
			'name'        => __( 'TWB Page Hero / Split Band', 'ave' ),
			'base'        => 'twb_page_hero',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-application-icon-large',
			'description' => __( 'Photo beside a charcoal panel (heading + intro + link). Inner-page hero or CTA band.', 'ave' ),
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
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Heading level', 'ave' ),
					'param_name'  => 'heading_tag',
					'value'       => array(
						__( 'H1 (page hero)', 'ave' )  => 'h1',
						__( 'H2 (CTA band)', 'ave' )   => 'h2',
					),
					'std'         => 'h1',
					'description' => __( 'Use H1 once per page (the hero); H2 for a closing CTA band.', 'ave' ),
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
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Image side', 'ave' ),
					'param_name'  => 'image_side',
					'value'       => array(
						__( 'Left', 'ave' )  => 'left',
						__( 'Right', 'ave' ) => 'right',
					),
					'std'         => 'left',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Link text', 'ave' ),
					'param_name'  => 'cta_text',
					'description' => __( 'Optional yellow underline link (e.g. "Start your enquiry"). Leave blank to hide.', 'ave' ),
				),
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'Link URL', 'ave' ),
					'param_name'  => 'cta_link',
					'dependency'  => array(
						'element'   => 'cta_text',
						'not_empty' => true,
					),
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
			'eyebrow'     => '',
			'heading'     => '',
			'heading_tag' => 'h1',
			'intro'       => '',
			'image'       => '',
			'image_side'  => 'left',
			'cta_text'    => '',
			'cta_link'    => '',
		),
		$atts,
		'twb_page_hero'
	);

	$eyebrow  = trim( $atts['eyebrow'] );
	$heading  = trim( $atts['heading'] );
	$intro    = trim( $atts['intro'] );
	$image_id = absint( $atts['image'] );
	$tag      = ( 'h2' === $atts['heading_tag'] ) ? 'h2' : 'h1';

	if ( '' === $heading && ! $image_id ) {
		return '';
	}

	wp_enqueue_style( 'twb-page-hero' );

	// Hero image is the LCP element: load it eagerly (bypass Ave's forced
	// lazyload for this one image, as the hero carousel does for its first slide).
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

	// Optional yellow underline link (the site's CTA idiom).
	$cta_text = trim( $atts['cta_text'] );
	$cta_html = '';
	if ( '' !== $cta_text ) {
		$url    = '#';
		$target = '';
		$rel    = '';
		if ( '' !== $atts['cta_link'] && function_exists( 'vc_build_link' ) ) {
			$parsed = vc_build_link( $atts['cta_link'] );
			if ( is_array( $parsed ) ) {
				if ( ! empty( $parsed['url'] ) ) {
					$url = $parsed['url'];
				}
				$target = isset( $parsed['target'] ) ? trim( $parsed['target'] ) : '';
				$rel    = isset( $parsed['rel'] ) ? trim( $parsed['rel'] ) : '';
				if ( '_blank' === $target ) {
					$rel = trim( $rel . ' noopener noreferrer' );
				}
			}
		}
		$attr_str = ' href="' . esc_url( $url ) . '"';
		if ( '' !== $target ) {
			$attr_str .= ' target="' . esc_attr( $target ) . '"';
		}
		if ( '' !== $rel ) {
			$attr_str .= ' rel="' . esc_attr( $rel ) . '"';
		}
		$cta_html = '<a class="twb-page-hero__link"' . $attr_str . '>' . esc_html( $cta_text ) . '</a>';
	}

	$side_class = ( 'right' === $atts['image_side'] ) ? ' twb-page-hero--image-right' : '';

	ob_start();
	?>
	<section class="twb-page-hero<?php echo esc_attr( $side_class ); ?><?php echo $img_html ? ' twb-page-hero--has-image' : ''; ?>">
		<div class="twb-page-hero__panel">
			<div class="twb-page-hero__panel-inner">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="twb-page-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<<?php echo esc_html( $tag ); ?> class="twb-page-hero__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>
				<?php if ( '' !== $intro ) : ?>
					<p class="twb-page-hero__intro"><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
				<?php echo $cta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — built from escaped parts above. ?>
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
