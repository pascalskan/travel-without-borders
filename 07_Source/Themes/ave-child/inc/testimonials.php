<?php
/**
 * TWB Testimonials
 *
 * A reusable, client-editable testimonials carousel built on the same
 * architecture as the TWB Hero Carousel: a WPBakery (vc_map) element whose
 * content is entered as repeatable element params, reusing Ave's bundled
 * Flickity (no new front-end dependency) and the shared token layer.
 *
 * Each testimonial is a repeatable group of: Quote, Author name, Author
 * location, Trip type, Region, Rating and Travel date. Display options
 * (auto-rotate, show rating, show badges, heading, background) are element
 * params, so the whole component is editable in the builder with no code.
 *
 * The component owns its own assets (registered here, enqueued on render) so
 * functions.php is not touched when adding it — only inc/loader.php gains a
 * single require_once line.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register this component's front-end assets.
 *
 * Registered (not enqueued) on every request; the render callback enqueues them
 * only on pages where the element is actually used. Versioned by filemtime so
 * edits always bust the cache. The stylesheet depends on the shared token layer
 * (twb-tokens, registered in functions.php) and Flickity's base CSS.
 */
function twb_testimonials_register_assets() {
	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();

	// Reuse Ave's bundled Flickity (ADR-001 D5 — no new library). The parent
	// theme only registers the Flickity handle on pages that already use a
	// carousel, so register the same bundled files here when they are missing.
	// This makes the component self-sufficient on any page without duplicating
	// the library (a handle that already exists is left untouched).
	//
	// Note: the child theme deliberately dequeues `flickity-fade` site-wide
	// (see functions.php → twb_remove_flickity_fade and 01_Documentation/
	// HERO_CAROUSEL.md), because it globally patches Flickity and breaks slide
	// transitions. This component therefore uses Flickity's standard slide
	// transition and must NOT depend on flickity-fade.
	$vendor = get_template_directory_uri() . '/assets/vendors/flickity/';
	if ( ! wp_style_is( 'flickity', 'registered' ) ) {
		wp_register_style( 'flickity', $vendor . 'flickity.min.css', array(), null );
	}
	if ( ! wp_script_is( 'flickity', 'registered' ) ) {
		wp_register_script( 'flickity', $vendor . 'flickity.pkgd.min.js', array( 'jquery' ), null, true );
	}

	$css     = $path . '/assets/css/testimonials.css';
	$js      = $path . '/assets/js/testimonials.js';
	$css_ver = file_exists( $css ) ? filemtime( $css ) : false;
	$js_ver  = file_exists( $js ) ? filemtime( $js ) : false;

	wp_register_style(
		'twb-testimonials',
		$dir . '/assets/css/testimonials.css',
		array( 'flickity', 'twb-tokens' ),
		$css_ver
	);

	wp_register_script(
		'twb-testimonials',
		$dir . '/assets/js/testimonials.js',
		array( 'flickity' ),
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_testimonials_register_assets' );

/**
 * Register the element with WPBakery (Visual Composer) once the builder is ready.
 */
function twb_testimonials_vc_map() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	$rating_options = array(
		__( 'No rating', 'ave' ) => '0',
		__( '5 stars', 'ave' )   => '5',
		__( '4 stars', 'ave' )   => '4',
		__( '3 stars', 'ave' )   => '3',
		__( '2 stars', 'ave' )   => '2',
		__( '1 star', 'ave' )    => '1',
	);

	vc_map(
		array(
			'name'        => __( 'TWB Testimonials', 'ave' ),
			'base'        => 'twb_testimonials',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-application-icon-large',
			'description' => __( 'Auto-rotating carousel of customer testimonials with optional rating and badges.', 'ave' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Eyebrow', 'ave' ),
					'param_name'  => 'eyebrow',
					'description' => __( 'Optional small label above the heading, e.g. "What our travellers say".', 'ave' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Heading', 'ave' ),
					'param_name'  => 'heading',
					'admin_label' => true,
					'description' => __( 'Optional section heading (rendered as an H2). Leave blank to hide.', 'ave' ),
				),
				array(
					'type'        => 'param_group',
					'heading'     => __( 'Testimonials', 'ave' ),
					'param_name'  => 'items',
					'value'       => '',
					'description' => __( 'Add, remove and reorder testimonials.', 'ave' ),
					'params'      => array(
						array(
							'type'        => 'textarea',
							'heading'     => __( 'Quote', 'ave' ),
							'param_name'  => 'quote',
							'admin_label' => true,
							'description' => __( 'The testimonial text. Required — empty quotes are skipped.', 'ave' ),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Author name', 'ave' ),
							'param_name' => 'author_name',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Author location', 'ave' ),
							'param_name' => 'author_location',
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Trip type', 'ave' ),
							'param_name'  => 'trip_type',
							'description' => __( 'Shown as a badge, e.g. "Tailor-made".', 'ave' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Region', 'ave' ),
							'param_name'  => 'region',
							'description' => __( 'Shown as a badge, e.g. "Bavaria".', 'ave' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Rating', 'ave' ),
							'param_name'  => 'rating',
							'value'       => $rating_options,
							'std'         => '0',
							'description' => __( 'Optional. Stars are only shown when a rating is set.', 'ave' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Travel date', 'ave' ),
							'param_name'  => 'travel_date',
							'description' => __( 'Free text, e.g. "May 2024".', 'ave' ),
						),
						array(
							'type'        => 'attach_image',
							'heading'     => __( 'Destination image', 'ave' ),
							'param_name'  => 'image',
							'description' => __( 'Optional photo of the destination shown to the left of the quote. Leave blank for a text-only card.', 'ave' ),
						),
						array(
							'type'        => 'vc_link',
							'heading'     => __( 'Destination link', 'ave' ),
							'param_name'  => 'image_link',
							'dependency'  => array(
								'element'   => 'image',
								'not_empty' => true,
							),
							'description' => __( 'Optional. If set, the destination image links to this page (e.g. the region/city it mentions).', 'ave' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Card width (grid only)', 'ave' ),
							'param_name'  => 'span',
							'value'       => array(
								__( 'Normal (1 column)', 'ave' ) => '1',
								__( 'Wide (2 columns)', 'ave' )  => '2',
								__( 'Full (3 columns)', 'ave' )  => '3',
							),
							'std'         => '1',
							'description' => __( 'Grid columns this card spans. Use Wide/Full for a long review so it reads across instead of running tall. Ignored in carousel mode.', 'ave' ),
						),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Layout', 'ave' ),
					'param_name'  => 'layout',
					'value'       => array(
						__( 'Carousel', 'ave' ) => 'carousel',
						__( 'Grid', 'ave' )     => 'grid',
					),
					'std'         => 'carousel',
					'description' => __( 'Carousel (one at a time, needs JS) or a static responsive grid (all at once).', 'ave' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Auto-rotate', 'ave' ),
					'param_name'  => 'autoplay',
					'value'       => array( __( 'Enable automatic rotation', 'ave' ) => 'yes' ),
					'std'         => 'yes',
					'dependency'  => array(
						'element' => 'layout',
						'value'   => array( 'carousel' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Auto-rotate interval (ms)', 'ave' ),
					'param_name'  => 'autoplay_speed',
					'value'       => '6000',
					'dependency'  => array(
						'element' => 'autoplay',
						'not_empty' => true,
					),
					'description' => __( 'Time each testimonial is shown, in milliseconds. Default 6000 (6s).', 'ave' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show rating stars', 'ave' ),
					'param_name' => 'show_rating',
					'value'      => array( __( 'Show stars when a rating is set', 'ave' ) => 'yes' ),
					'std'        => 'yes',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show badges', 'ave' ),
					'param_name' => 'show_badges',
					'value'      => array( __( 'Show region / trip-type badges', 'ave' ) => 'yes' ),
					'std'        => 'yes',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Background', 'ave' ),
					'param_name'  => 'background',
					'value'       => array(
						__( 'Surface (light grey)', 'ave' ) => 'surface',
						__( 'White', 'ave' )                => 'white',
					),
					'std'         => 'surface',
					'description' => __( 'Section background colour.', 'ave' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'CTA text', 'ave' ),
					'param_name'  => 'cta_text',
					'description' => __( 'Optional link shown centred below the carousel, e.g. "Read all testimonials". Leave blank to hide.', 'ave' ),
				),
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'CTA link', 'ave' ),
					'param_name'  => 'cta_link',
					'dependency'  => array(
						'element'   => 'cta_text',
						'not_empty' => true,
					),
					'description' => __( 'Where the CTA points (e.g. the future Testimonials page). Defaults to # if left blank.', 'ave' ),
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'twb_testimonials_vc_map' );

/**
 * Render a star rating with an accessible text equivalent.
 *
 * @param int $rating Rating from 1–5.
 * @return string
 */
function twb_testimonials_render_stars( $rating ) {
	$rating = max( 0, min( 5, (int) $rating ) );
	if ( $rating < 1 ) {
		return '';
	}

	$stars = '';
	for ( $i = 1; $i <= 5; $i++ ) {
		$on     = $i <= $rating;
		$stars .= '<span class="twb-testimonial-card__star' . ( $on ? ' is-on' : '' ) . '" aria-hidden="true">' . ( $on ? '★' : '☆' ) . '</span>';
	}

	return sprintf(
		'<div class="twb-testimonial-card__rating" role="img" aria-label="%s">%s</div>',
		esc_attr( sprintf( /* translators: %d: rating out of 5 */ __( 'Rated %d out of 5', 'ave' ), $rating ) ),
		$stars // Safe: built from escaped glyphs above.
	);
}

/**
 * Render the optional destination image column for a card.
 *
 * Lazy-loaded, `object-fit: cover`; wrapped in a link when a destination link is
 * set. Returns '' when there is no image.
 *
 * @param int    $image_id  Attachment ID (0 = none).
 * @param string $link_raw  Raw vc_link value (may be empty).
 * @param string $region    Region name, used for alt / link label.
 * @return string
 */
function twb_testimonials_render_media( $image_id, $link_raw, $region ) {
	if ( $image_id < 1 ) {
		return '';
	}

	$img = wp_get_attachment_image(
		$image_id,
		'medium_large',
		false,
		array(
			'class'    => 'twb-testimonial-card__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => ( '' !== $region ) ? $region : '',
		)
	);

	if ( '' === $img ) {
		return '';
	}

	// Optional link to the destination the testimonial mentions.
	$url    = '';
	$target = '';
	$rel    = '';
	if ( '' !== $link_raw && function_exists( 'vc_build_link' ) ) {
		$parsed = vc_build_link( $link_raw );
		if ( is_array( $parsed ) && ! empty( $parsed['url'] ) ) {
			$url    = $parsed['url'];
			$target = isset( $parsed['target'] ) ? trim( $parsed['target'] ) : '';
			$rel    = isset( $parsed['rel'] ) ? trim( $parsed['rel'] ) : '';
			if ( '_blank' === $target ) {
				$rel = trim( $rel . ' noopener noreferrer' );
			}
		}
	}

	if ( '' === $url ) {
		return '<div class="twb-testimonial-card__media">' . $img . '</div>';
	}

	$label = ( '' !== $region )
		/* translators: %s: destination/region name */
		? sprintf( __( 'View %s', 'ave' ), $region )
		: __( 'View destination', 'ave' );

	$attr = ' href="' . esc_url( $url ) . '"';
	if ( '' !== $target ) {
		$attr .= ' target="' . esc_attr( $target ) . '"';
	}
	if ( '' !== $rel ) {
		$attr .= ' rel="' . esc_attr( $rel ) . '"';
	}
	$attr .= ' aria-label="' . esc_attr( $label ) . '"';

	return '<a class="twb-testimonial-card__media twb-testimonial-card__media--link"' . $attr . '>' . $img . '</a>';
}

/**
 * Render a single testimonial card.
 *
 * The single source of card markup for this component (mirrors the hero's
 * "one renderer" approach). Returns a semantic figure/blockquote/figcaption,
 * optionally preceded by a destination image column.
 *
 * @param array $item Testimonial fields.
 * @param array $opts Display options (show_rating, show_badges).
 * @return string Card HTML, or '' when there is no quote to show.
 */
function twb_testimonials_render_card( $item, $opts ) {
	$quote = isset( $item['quote'] ) ? trim( $item['quote'] ) : '';
	if ( '' === $quote ) {
		return '';
	}

	$name     = isset( $item['author_name'] ) ? trim( $item['author_name'] ) : '';
	$location = isset( $item['author_location'] ) ? trim( $item['author_location'] ) : '';
	$trip     = isset( $item['trip_type'] ) ? trim( $item['trip_type'] ) : '';
	$region   = isset( $item['region'] ) ? trim( $item['region'] ) : '';
	$date     = isset( $item['travel_date'] ) ? trim( $item['travel_date'] ) : '';
	$rating   = isset( $item['rating'] ) ? (int) $item['rating'] : 0;

	$show_rating = ! empty( $opts['show_rating'] );
	$show_badges = ! empty( $opts['show_badges'] );

	// Optional destination image (shown to the left of the quote), which may link
	// to the place it mentions. Reuses the shared vc_link parsing.
	$media_html = twb_testimonials_render_media(
		isset( $item['image'] ) ? absint( $item['image'] ) : 0,
		isset( $item['image_link'] ) ? $item['image_link'] : '',
		$region
	);
	$has_media = ( '' !== $media_html );

	ob_start();
	?>
	<figure class="twb-testimonial-card<?php echo $has_media ? ' twb-testimonial-card--has-media' : ''; ?>">
		<?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — built from escaped values in twb_testimonials_render_media(). ?>
		<div class="twb-testimonial-card__body">
			<div class="twb-testimonial-card__top">
				<?php if ( $show_badges && ( '' !== $region || '' !== $trip ) ) : ?>
					<div class="twb-testimonial-card__badges">
						<?php if ( '' !== $region ) : ?>
							<span class="twb-testimonial-card__badge twb-testimonial-card__badge--region"><?php echo esc_html( $region ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $trip ) : ?>
							<span class="twb-testimonial-card__badge twb-testimonial-card__badge--trip"><?php echo esc_html( $trip ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php
				if ( $show_rating && $rating > 0 ) {
					echo twb_testimonials_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>

			<blockquote class="twb-testimonial-card__quote">
				<p><?php echo esc_html( $quote ); ?></p>
			</blockquote>

			<div class="twb-testimonial-card__foot"><figcaption class="twb-testimonial-card__caption">
				<span class="twb-testimonial-card__attribution">
					<?php if ( '' !== $name ) : ?>
						<cite class="twb-testimonial-card__name"><?php echo esc_html( $name ); ?></cite>
					<?php endif; ?>
					<?php if ( '' !== $location || '' !== $date ) : ?>
						<span class="twb-testimonial-card__meta">
							<?php
							$meta = array_filter( array( $location, $date ) );
							echo esc_html( implode( ' · ', $meta ) );
							?>
						</span>
					<?php endif; ?>
				</span>
			</figcaption>
			<button type="button" class="twb-testimonial-card__more" hidden>
				<span class="twb-testimonial-card__more-txt"><?php esc_html_e( 'Show more', 'ave' ); ?></span>
			</button>
				</div>
		</div>
	</figure>
	<?php
	return ob_get_clean();
}

/**
 * Render callback for the [twb_testimonials] shortcode.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner content (unused).
 * @return string
 */
function twb_testimonials_render( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'layout'         => 'carousel',
			'eyebrow'        => '',
			'heading'        => '',
			'items'          => '',
			'autoplay'       => 'yes',
			'autoplay_speed' => 6000,
			'show_rating'    => 'yes',
			'show_badges'    => 'yes',
			'background'     => 'surface',
			'cta_text'       => '',
			'cta_link'       => '',
		),
		$atts,
		'twb_testimonials'
	);

	// WPBakery stores param_group rows as a URL-encoded JSON string.
	$items = array();
	if ( ! empty( $atts['items'] ) ) {
		$decoded = json_decode( rawurldecode( $atts['items'] ), true );
		if ( is_array( $decoded ) ) {
			$items = $decoded;
		}
	}

	$opts = array(
		'show_rating' => ( 'yes' === $atts['show_rating'] ),
		'show_badges' => ( 'yes' === $atts['show_badges'] ),
	);

	// Build cards first so a block of empty quotes renders nothing at all.
	// Track whether any rendered card has a destination image, so the section can
	// widen to accommodate the two-column layout.
	$cards     = array();
	$any_media = false;
	foreach ( $items as $item ) {
		$card = twb_testimonials_render_card( $item, $opts );
		if ( '' !== $card ) {
			// Optional grid span (1–3 columns) so long reviews can read across
			// instead of running tall. Only used by the grid layout.
			$span    = isset( $item['span'] ) ? max( 1, min( 3, (int) $item['span'] ) ) : 1;
			$cards[] = array( 'html' => $card, 'span' => $span );
			if ( isset( $item['image'] ) && absint( $item['image'] ) > 0 ) {
				$any_media = true;
			}
		}
	}

	if ( empty( $cards ) ) {
		return '';
	}

	$is_grid = ( 'grid' === $atts['layout'] );

	// Component styles always; the carousel JS + Flickity only in carousel mode
	// (the grid is static markup and needs no JavaScript).
	wp_enqueue_style( 'twb-testimonials' );
	// The script handles both the carousel (Flickity) and the grid's "Show more"
	// modal, so it loads in both modes; Flickity is only needed for the carousel.
	wp_enqueue_script( 'twb-testimonials' );
	if ( ! $is_grid ) {
		wp_enqueue_style( 'flickity' );
		wp_enqueue_script( 'flickity' );
	}

	$autoplay = ( 'yes' === $atts['autoplay'] );
	$speed    = absint( $atts['autoplay_speed'] );
	if ( $speed < 1000 ) {
		$speed = 6000;
	}

	$options = array(
		'autoPlay'             => $autoplay ? $speed : false,
		'wrapAround'           => true,
		'pageDots'             => true,
		'prevNextButtons'      => true,
		'draggable'            => true,
		'pauseAutoPlayOnHover' => true,
		'cellAlign'            => 'center',
		'adaptiveHeight'       => true,
	);

	$bg_class = ( 'white' === $atts['background'] ) ? 'twb-bg-white' : 'twb-bg-surface';

	// Optional CTA below the carousel. Parse the vc_link; default to '#'.
	$cta_text = trim( (string) $atts['cta_text'] );
	$cta_url  = '#';
	$cta_rel  = '';
	$cta_tgt  = '';
	if ( '' !== $cta_text && '' !== $atts['cta_link'] && function_exists( 'vc_build_link' ) ) {
		$parsed = vc_build_link( $atts['cta_link'] );
		if ( is_array( $parsed ) ) {
			if ( ! empty( $parsed['url'] ) ) {
				$cta_url = $parsed['url'];
			}
			$cta_tgt = isset( $parsed['target'] ) ? trim( $parsed['target'] ) : '';
			$cta_rel = isset( $parsed['rel'] ) ? trim( $parsed['rel'] ) : '';
			if ( '_blank' === $cta_tgt ) {
				$cta_rel = trim( $cta_rel . ' noopener noreferrer' );
			}
		}
	}

	ob_start();
	?>
	<section class="twb-testimonials twb-section <?php echo esc_attr( $bg_class ); ?><?php echo $any_media ? ' twb-testimonials--has-media' : ''; ?><?php echo $is_grid ? ' twb-testimonials--grid' : ''; ?>">
		<div class="twb-container">
			<?php if ( '' !== $atts['eyebrow'] || '' !== $atts['heading'] ) : ?>
				<header class="twb-testimonials__header">
					<?php if ( '' !== $atts['eyebrow'] ) : ?>
						<p class="twb-testimonials__eyebrow"><?php echo esc_html( $atts['eyebrow'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $atts['heading'] ) : ?>
						<h2 class="twb-testimonials__heading"><?php echo wp_kses( $atts['heading'], array( 'br' => array() ) ); ?></h2>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php if ( $is_grid ) : ?>
				<div class="twb-testimonials__grid">
					<?php foreach ( $cards as $card ) : ?>
						<div class="twb-testimonials__grid-item<?php echo $card['span'] > 1 ? ' twb-testimonials__grid-item--span-' . (int) $card['span'] : ''; ?>">
							<?php echo $card['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — built from escaped fields in twb_testimonials_render_card(). ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php
				// Accessible name for the focusable carousel (tabindex is added by
				// Flickity). Uses the heading when present so a screen-reader user
				// tabbing onto the scroller hears what it is.
				$carousel_label = ( '' !== $atts['heading'] ) ? $atts['heading'] : __( 'Testimonials', 'ave' );
				?>
				<div class="twb-testimonials__carousel" aria-label="<?php echo esc_attr( $carousel_label ); ?>" aria-roledescription="carousel" data-twb-testimonials="<?php echo esc_attr( wp_json_encode( $options ) ); ?>">
					<?php foreach ( $cards as $card ) : ?>
						<div class="twb-testimonials__cell">
							<?php echo $card['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — built from escaped fields in twb_testimonials_render_card(). ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $is_grid ) : ?>
					<div class="twb-testimonials__modal" hidden>
						<div class="twb-testimonials__modal-backdrop" data-twb-modal-close></div>
						<div class="twb-testimonials__modal-dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Testimonial', 'ave' ); ?>">
							<button type="button" class="twb-testimonials__modal-close" data-twb-modal-close aria-label="<?php esc_attr_e( 'Close', 'ave' ); ?>">&times;</button>
							<div class="twb-testimonials__modal-body"></div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $cta_text ) : ?>
				<div class="twb-testimonials__cta-wrap">
					<a class="twb-testimonials__cta" href="<?php echo esc_url( $cta_url ); ?>"
						<?php
						if ( '' !== $cta_tgt ) {
							echo ' target="' . esc_attr( $cta_tgt ) . '"';
						}
						if ( '' !== $cta_rel ) {
							echo ' rel="' . esc_attr( $cta_rel ) . '"';
						}
						?>
					>
						<?php echo esc_html( $cta_text ); ?>
						<span class="twb-testimonials__cta-arrow" aria-hidden="true">&rarr;</span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_testimonials', 'twb_testimonials_render' );
