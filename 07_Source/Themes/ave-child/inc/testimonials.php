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
					'value'       => '6000',
					'dependency'  => array(
						'element'   => 'autoplay',
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
 * Render a single testimonial card.
 *
 * The single source of card markup for this component (mirrors the hero's
 * "one renderer" approach). Returns a semantic figure/blockquote/figcaption.
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

	// Initial-letter avatar fallback (no images stored in this element).
	$initial = '' !== $name ? mb_substr( $name, 0, 1 ) : '“';

	ob_start();
	?>
	<figure class="twb-testimonial-card">
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

		<figcaption class="twb-testimonial-card__caption">
			<span class="twb-testimonial-card__avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
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
			'eyebrow'        => '',
			'heading'        => '',
			'items'          => '',
			'autoplay'       => 'yes',
			'autoplay_speed' => 6000,
			'show_rating'    => 'yes',
			'show_badges'    => 'yes',
			'background'     => 'surface',
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
	$cards = array();
	foreach ( $items as $item ) {
		$card = twb_testimonials_render_card( $item, $opts );
		if ( '' !== $card ) {
			$cards[] = $card;
		}
	}

	if ( empty( $cards ) ) {
		return '';
	}

	// Request the on-demand assets (registered above + bundled by Ave).
	wp_enqueue_style( 'flickity' );
	wp_enqueue_script( 'flickity' );
	wp_enqueue_style( 'twb-testimonials' );
	wp_enqueue_script( 'twb-testimonials' );

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

	ob_start();
	?>
	<section class="twb-testimonials twb-section <?php echo esc_attr( $bg_class ); ?>">
		<div class="twb-container">
			<?php if ( '' !== $atts['eyebrow'] || '' !== $atts['heading'] ) : ?>
				<header class="twb-testimonials__header">
					<?php if ( '' !== $atts['eyebrow'] ) : ?>
						<p class="twb-testimonials__eyebrow"><?php echo esc_html( $atts['eyebrow'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $atts['heading'] ) : ?>
						<h2 class="twb-testimonials__heading"><?php echo esc_html( $atts['heading'] ); ?></h2>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="twb-testimonials__carousel" data-twb-testimonials="<?php echo esc_attr( wp_json_encode( $options ) ); ?>">
				<?php foreach ( $cards as $card ) : ?>
					<div class="twb-testimonials__cell">
						<?php echo $card; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — built from escaped fields in twb_testimonials_render_card(). ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_testimonials', 'twb_testimonials_render' );
