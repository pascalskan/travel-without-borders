<?php
/**
 * TWB Enquiry Toggle
 *
 * A segmented "Get in touch" switch that swaps between two Quform forms on the
 * Contact page: Individual enquiries (the existing tailor-made contact form) and
 * Business enquiries (the labelled "Trade Enquiry" duplicate). Individual is the
 * default. Both forms are rendered server-side; the toggle only shows/hides them,
 * so no form state is lost when switching and there is no network round-trip.
 *
 * The two forms are embedded by calling `do_shortcode()` on the Quform shortcode
 * ourselves, so this does not depend on WPBakery's raw-HTML shortcode handling.
 *
 * Assets are enqueued only when the element renders.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register this component's assets (enqueued on render).
 */
function twb_enquiry_toggle_register_assets() {
	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();
	$css  = $path . '/assets/css/enquiry-toggle.css';
	$js   = $path . '/assets/js/enquiry-toggle.js';
	$css_ver = file_exists( $css ) ? filemtime( $css ) : false;
	$js_ver  = file_exists( $js ) ? filemtime( $js ) : false;

	wp_register_style( 'twb-enquiry-toggle', $dir . '/assets/css/enquiry-toggle.css', array( 'twb-tokens' ), $css_ver );
	wp_register_script( 'twb-enquiry-toggle', $dir . '/assets/js/enquiry-toggle.js', array(), $js_ver, true );
}
add_action( 'wp_enqueue_scripts', 'twb_enquiry_toggle_register_assets' );

/**
 * Register the element with WPBakery.
 */
function twb_enquiry_toggle_vc_map() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => __( 'TWB Enquiry Toggle', 'ave' ),
			'base'        => 'twb_enquiry_toggle',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-application-icon-large',
			'description' => __( 'Individual / Business enquiry form switch.', 'ave' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Individual form ID', 'ave' ),
					'param_name'  => 'individual',
					'value'       => '1',
					'description' => __( 'Quform ID of the existing individual contact form.', 'ave' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Business form ID', 'ave' ),
					'param_name'  => 'business',
					'value'       => '3',
					'description' => __( 'Quform ID of the trade / business enquiry form.', 'ave' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Individual tab label', 'ave' ),
					'param_name' => 'individual_label',
					'value'      => __( 'Individual enquiries', 'ave' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Business tab label', 'ave' ),
					'param_name' => 'business_label',
					'value'      => __( 'Business enquiries', 'ave' ),
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'twb_enquiry_toggle_vc_map' );

/**
 * Render callback for [twb_enquiry_toggle].
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function twb_enquiry_toggle_render( $atts ) {
	$atts = shortcode_atts(
		array(
			'individual'       => '1',
			'business'         => '3',
			'individual_label' => __( 'Individual enquiries', 'ave' ),
			'business_label'   => __( 'Business enquiries', 'ave' ),
		),
		$atts,
		'twb_enquiry_toggle'
	);

	$ind_id = (int) $atts['individual'];
	$bus_id = (int) $atts['business'];
	if ( $ind_id < 1 || $bus_id < 1 ) {
		return '';
	}

	wp_enqueue_style( 'twb-enquiry-toggle' );
	wp_enqueue_script( 'twb-enquiry-toggle' );

	// Unique id base so multiple toggles on one page don't collide.
	static $instance = 0;
	$instance++;
	$base = 'twb-enq-' . $instance;

	$ind_label = trim( $atts['individual_label'] );
	$bus_label = trim( $atts['business_label'] );

	// Render both forms up front. Quform handles its own asset enqueuing.
	$ind_form = do_shortcode( sprintf( '[quform id="%d"]', $ind_id ) );
	$bus_form = do_shortcode( sprintf( '[quform id="%d"]', $bus_id ) );

	ob_start();
	?>
	<div class="twb-enquiry" data-twb-enquiry>
		<div class="twb-enquiry__switch" role="tablist" aria-label="<?php esc_attr_e( 'Enquiry type', 'ave' ); ?>">
			<button type="button" class="twb-enquiry__tab is-active" data-twb-target="individual"
				id="<?php echo esc_attr( $base . '-tab-ind' ); ?>"
				aria-controls="<?php echo esc_attr( $base . '-panel-ind' ); ?>"
				role="tab" aria-selected="true">
				<?php echo esc_html( $ind_label ); ?>
			</button>
			<button type="button" class="twb-enquiry__tab" data-twb-target="business"
				id="<?php echo esc_attr( $base . '-tab-bus' ); ?>"
				aria-controls="<?php echo esc_attr( $base . '-panel-bus' ); ?>"
				role="tab" aria-selected="false">
				<?php echo esc_html( $bus_label ); ?>
			</button>
			<span class="twb-enquiry__thumb" aria-hidden="true"></span>
		</div>

		<div class="twb-enquiry__panel is-active" data-twb-panel="individual"
			id="<?php echo esc_attr( $base . '-panel-ind' ); ?>"
			role="tabpanel" aria-labelledby="<?php echo esc_attr( $base . '-tab-ind' ); ?>">
			<?php echo $ind_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — Quform shortcode output. ?>
		</div>

		<div class="twb-enquiry__panel" data-twb-panel="business" hidden
			id="<?php echo esc_attr( $base . '-panel-bus' ); ?>"
			role="tabpanel" aria-labelledby="<?php echo esc_attr( $base . '-tab-bus' ); ?>">
			<?php echo $bus_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — Quform shortcode output. ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_enquiry_toggle', 'twb_enquiry_toggle_render' );
