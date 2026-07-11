<?php
/**
 * TWB Hero Carousel
 *
 * A native Ave / WPBakery hero carousel that replaces the homepage dependency
 * on Slider Revolution. Built on Flickity (already bundled with the Ave parent
 * theme) so it stays theme-consistent and adds no new front-end dependency.
 *
 * The element is fully client-editable from the WPBakery builder. Each slide is
 * a repeatable group of: Image, Image alt text, Title, Description, Destination
 * URL and (reserved for future use) Button text / Button URL.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the element with WPBakery (Visual Composer) once the builder is ready.
 */
function twb_hero_carousel_vc_map() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => __( 'TWB Hero Carousel', 'ave' ),
			'base'        => 'twb_hero_carousel',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-images-carousel',
			'description' => __( 'Full-width auto-rotating image carousel with a title and description shown below the active slide.', 'ave' ),
			'params'      => array(
				array(
					'type'        => 'param_group',
					'heading'     => __( 'Slides', 'ave' ),
					'param_name'  => 'slides',
					'value'       => '',
					'description' => __( 'Add, remove and reorder slides.', 'ave' ),
					'params'      => array(
						array(
							'type'        => 'attach_image',
							'heading'     => __( 'Image', 'ave' ),
							'param_name'  => 'image',
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Image alt text', 'ave' ),
							'param_name'  => 'alt',
							'description' => __( 'Describes the image for screen readers and SEO. Leave blank to use the alt text stored in the Media Library.', 'ave' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Title', 'ave' ),
							'param_name'  => 'title',
							'admin_label' => true,
						),
						array(
							'type'       => 'textarea',
							'heading'    => __( 'Description', 'ave' ),
							'param_name' => 'description',
						),
						array(
							'type'        => 'vc_link',
							'heading'     => __( 'Destination URL', 'ave' ),
							'param_name'  => 'link',
							'description' => __( 'Optional. If set, the whole slide image becomes a link to this page. Leave blank for no link.', 'ave' ),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Open destination in new tab', 'ave' ),
							'param_name'  => 'new_tab',
							'value'       => array( __( 'Open in a new tab', 'ave' ) => 'yes' ),
							'std'         => '',
							'dependency'  => array(
								'element'   => 'link',
								'not_empty' => true,
							),
							'description' => __( 'Adds target="_blank" with rel="noopener noreferrer".', 'ave' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Button text (future)', 'ave' ),
							'param_name'  => 'button_text',
							'description' => __( 'Reserved for a future call-to-action button. Stored but not yet displayed.', 'ave' ),
						),
						array(
							'type'        => 'vc_link',
							'heading'     => __( 'Button URL (future)', 'ave' ),
							'param_name'  => 'button_link',
							'description' => __( 'Reserved for a future call-to-action button. Stored but not yet displayed.', 'ave' ),
						),
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Auto-rotate', 'ave' ),
					'param_name' => 'autoplay',
					'value'      => array( __( 'Enable automatic rotation', 'ave' ) => 'yes' ),
					'std'        => 'yes',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Auto-rotate interval (ms)', 'ave' ),
					'param_name'  => 'autoplay_speed',
					'value'       => '5000',
					'dependency'  => array(
						'element'   => 'autoplay',
						'not_empty' => true,
					),
					'description' => __( 'Time each slide is shown, in milliseconds. Default 5000 (5s).', 'ave' ),
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'twb_hero_carousel_vc_map' );

/**
 * Parse a WPBakery vc_link value into a normalised array.
 *
 * @param string $raw The stored vc_link value.
 * @return array{url:string,target:string,rel:string,title:string}
 */
function twb_hero_parse_link( $raw ) {
	$out = array(
		'url'    => '',
		'target' => '',
		'rel'    => '',
		'title'  => '',
	);

	if ( empty( $raw ) ) {
		return $out;
	}

	if ( function_exists( 'vc_build_link' ) ) {
		$parsed = vc_build_link( $raw );
		if ( is_array( $parsed ) ) {
			$out['url']    = isset( $parsed['url'] ) ? $parsed['url'] : '';
			$out['target'] = isset( $parsed['target'] ) ? trim( $parsed['target'] ) : '';
			$out['rel']    = isset( $parsed['rel'] ) ? trim( $parsed['rel'] ) : '';
			$out['title']  = isset( $parsed['title'] ) ? $parsed['title'] : '';
		}
	} elseif ( false === strpos( $raw, 'url:' ) ) {
		// Plain URL fallback.
		$out['url'] = $raw;
	}

	return $out;
}

/**
 * Render callback for the [twb_hero_carousel] shortcode.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner content (unused).
 * @return string
 */
function twb_hero_carousel_render( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'slides'         => '',
			'autoplay'       => 'yes',
			'autoplay_speed' => 5000,
		),
		$atts,
		'twb_hero_carousel'
	);

	// WPBakery stores param_group rows as a URL-encoded JSON string.
	$slides = array();
	if ( ! empty( $atts['slides'] ) ) {
		$decoded = json_decode( rawurldecode( $atts['slides'] ), true );
		if ( is_array( $decoded ) ) {
			$slides = $decoded;
		}
	}

	if ( empty( $slides ) ) {
		return '';
	}

	// Assets are bundled with the parent Ave theme and registered there; we only
	// need to request them so they are printed for this page. (flickity-fade is
	// dequeued in functions.php so Flickity's reliable slide transition works.)
	wp_enqueue_style( 'flickity' );
	wp_enqueue_script( 'flickity' );
	wp_enqueue_style( 'twb-hero-carousel' );
	wp_enqueue_script( 'twb-hero-carousel' );

	$autoplay = ( 'yes' === $atts['autoplay'] );
	$speed    = absint( $atts['autoplay_speed'] );
	if ( $speed < 1000 ) {
		$speed = 5000;
	}

	$options = array(
		'autoPlay'             => $autoplay ? $speed : false,
		'wrapAround'           => true,   // Infinite loop.
		'pageDots'             => true,   // Pagination dots.
		'prevNextButtons'      => true,   // Previous / next arrows.
		'draggable'            => true,   // Manual navigation.
		'pauseAutoPlayOnHover' => true,   // Pause on interaction.
		'cellAlign'            => 'center',
		'imagesLoaded'         => true,
		'adaptiveHeight'       => true,
	);

	// Ave force-lazyloads every wp_get_attachment_image. Bypass that for the
	// first (LCP) slide so it loads eagerly; remaining slides stay lazy.
	$lazy_cb       = 'liquid_filter_gallery_img_atts';
	$lazy_priority = has_filter( 'wp_get_attachment_image_attributes', $lazy_cb );
	$is_first      = true;

	ob_start();
	?>
	<div class="twb-hero" data-twb-hero="<?php echo esc_attr( wp_json_encode( $options ) ); ?>">
		<div class="twb-hero__carousel">
			<?php foreach ( $slides as $slide ) : ?>
				<?php
				$image_id    = isset( $slide['image'] ) ? absint( $slide['image'] ) : 0;
				$title       = isset( $slide['title'] ) ? $slide['title'] : '';
				$description = isset( $slide['description'] ) ? $slide['description'] : '';
				$alt         = isset( $slide['alt'] ) ? trim( $slide['alt'] ) : '';

				if ( ! $image_id ) {
					continue;
				}

				$img_attr = array( 'class' => 'twb-hero__img' );
				if ( '' !== $alt ) {
					$img_attr['alt'] = $alt;
				}

				if ( $is_first ) {
					// Eager-load the first slide and drop Ave's JS lazyload for it.
					$img_attr['loading']       = 'eager';
					$img_attr['fetchpriority'] = 'high';
					$img_attr['decoding']      = 'async';
					if ( false !== $lazy_priority ) {
						remove_filter( 'wp_get_attachment_image_attributes', $lazy_cb, $lazy_priority );
					}
					$img_html = wp_get_attachment_image( $image_id, 'full', false, $img_attr );
					if ( false !== $lazy_priority ) {
						add_filter( 'wp_get_attachment_image_attributes', $lazy_cb, $lazy_priority, 2 );
					}
				} else {
					// Lazy-load the rest (native; Ave's filter also applies if active).
					$img_attr['loading']  = 'lazy';
					$img_attr['decoding'] = 'async';
					$img_html             = wp_get_attachment_image( $image_id, 'full', false, $img_attr );
				}

				// Optional destination link wraps the image.
				$link      = twb_hero_parse_link( isset( $slide['link'] ) ? $slide['link'] : '' );
				$has_link  = '' !== $link['url'];
				$new_tab   = ! empty( $slide['new_tab'] );
				$media_tag = $has_link ? 'a' : 'div';

				$media_atts = array( 'class' => 'twb-hero__media' . ( $has_link ? ' twb-hero__media--link' : '' ) );
				if ( $has_link ) {
					$media_atts['href'] = esc_url( $link['url'] );

					$target = $new_tab ? '_blank' : $link['target'];
					if ( '' !== $target ) {
						$media_atts['target'] = $target;
					}

					$rel_parts = array();
					if ( '' !== $link['rel'] ) {
						$rel_parts[] = $link['rel'];
					}
					if ( '_blank' === $target ) {
						$rel_parts[] = 'noopener';
						$rel_parts[] = 'noreferrer';
					}
					$rel = trim( implode( ' ', array_unique( array_filter( explode( ' ', implode( ' ', $rel_parts ) ) ) ) ) );
					if ( '' !== $rel ) {
						$media_atts['rel'] = $rel;
					}

					// Give the link an accessible name (the image alt may be empty).
					$label = '' !== $title ? $title : $alt;
					if ( '' !== $label ) {
						$media_atts['aria-label'] = $label;
					}
				}

				$is_first = false;

				$media_attr_str = '';
				foreach ( $media_atts as $k => $v ) {
					$media_attr_str .= ' ' . $k . '="' . esc_attr( $v ) . '"';
				}
				?>
				<div class="twb-hero__cell">
					<<?php echo esc_html( $media_tag ) . $media_attr_str; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</<?php echo esc_html( $media_tag ); ?>>
					<?php if ( '' !== $title || '' !== $description ) : ?>
						<div class="twb-hero__caption">
							<?php if ( '' !== $title ) : ?>
								<h2 class="twb-hero__title"><?php echo esc_html( $title ); ?></h2>
							<?php endif; ?>
							<?php if ( '' !== $description ) : ?>
								<p class="twb-hero__desc"><?php echo esc_html( $description ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_hero_carousel', 'twb_hero_carousel_render' );
