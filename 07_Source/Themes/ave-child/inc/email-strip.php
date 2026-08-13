<?php
/**
 * TWB Email Strip
 *
 * The site's standard closing contact band: a green section with a
 * yellow-bordered box, an envelope icon, a short label and the enquiry email.
 * Every inner page ends with this block; this is a clean, token-based
 * recreation so it renders consistently wherever it is dropped.
 *
 * Token-based, no JavaScript; assets enqueued only when the element renders.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register this component's stylesheet (enqueued on render).
 */
function twb_email_strip_register_assets() {
	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();
	$css  = $path . '/assets/css/email-strip.css';
	$ver  = file_exists( $css ) ? filemtime( $css ) : false;

	wp_register_style( 'twb-email-strip', $dir . '/assets/css/email-strip.css', array( 'twb-tokens' ), $ver );
}
add_action( 'wp_enqueue_scripts', 'twb_email_strip_register_assets' );

/**
 * Register the element with WPBakery.
 */
function twb_email_strip_vc_map() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => __( 'TWB Email Strip', 'ave' ),
			'base'        => 'twb_email_strip',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-application-icon-large',
			'description' => __( 'The standard green "e-mail us" closing band.', 'ave' ),
			'params'      => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Label', 'ave' ),
					'param_name' => 'label',
					'value'      => __( 'E-mail us for more information', 'ave' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Email address', 'ave' ),
					'param_name' => 'email',
					'value'      => 'mail@travelwithoutborders.co.uk',
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'twb_email_strip_vc_map' );

/**
 * Render callback for [twb_email_strip].
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner content (unused).
 * @return string
 */
function twb_email_strip_render( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'label' => __( 'E-mail us for more information', 'ave' ),
			'email' => 'mail@travelwithoutborders.co.uk',
		),
		$atts,
		'twb_email_strip'
	);

	$label = trim( $atts['label'] );
	$email = sanitize_email( $atts['email'] );
	if ( '' === $email ) {
		return '';
	}

	wp_enqueue_style( 'twb-email-strip' );

	$icon = '<svg class="twb-email-strip__icon" width="40" height="40" viewBox="0 0 24 24" fill="none" '
		. 'stroke="currentColor" stroke-width="1.5" aria-hidden="true" focusable="false">'
		. '<rect x="2.5" y="4.5" width="19" height="15" rx="1.5"></rect>'
		. '<path d="M3 6l9 7 9-7"></path></svg>';

	ob_start();
	?>
	<section class="twb-email-strip twb-section twb-bg-green">
		<div class="twb-email-strip__box">
			<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static inline SVG. ?>
			<?php if ( '' !== $label ) : ?>
				<p class="twb-email-strip__label"><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<a class="twb-email-strip__email" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_email_strip', 'twb_email_strip_render' );
