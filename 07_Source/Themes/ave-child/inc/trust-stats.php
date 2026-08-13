<?php
/**
 * TWB Trust Stats
 *
 * A compact credibility strip: a row of "number + label" stats (e.g.
 * "30+ · Years of expertise"). Surfaces the site's trust assets (experience,
 * financial protection, breadth of destinations) into the journey.
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
function twb_trust_stats_register_assets() {
	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();
	$css  = $path . '/assets/css/trust-stats.css';
	$ver  = file_exists( $css ) ? filemtime( $css ) : false;

	wp_register_style( 'twb-trust-stats', $dir . '/assets/css/trust-stats.css', array( 'twb-tokens' ), $ver );
}
add_action( 'wp_enqueue_scripts', 'twb_trust_stats_register_assets' );

/**
 * Register the element with WPBakery.
 */
function twb_trust_stats_vc_map() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => __( 'TWB Trust Stats', 'ave' ),
			'base'        => 'twb_trust_stats',
			'category'    => __( 'Travel Without Borders', 'ave' ),
			'icon'        => 'icon-wpb-application-icon-large',
			'description' => __( 'A credibility strip of number + label stats.', 'ave' ),
			'params'      => array(
				array(
					'type'        => 'param_group',
					'heading'     => __( 'Stats', 'ave' ),
					'param_name'  => 'items',
					'value'       => '',
					'params'      => array(
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Number', 'ave' ),
							'param_name'  => 'number',
							'admin_label' => true,
							'description' => __( 'e.g. "30+", "100%", "60+".', 'ave' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Label', 'ave' ),
							'param_name'  => 'label',
							'admin_label' => true,
						),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Background', 'ave' ),
					'param_name'  => 'background',
					'value'       => array(
						__( 'White', 'ave' )                => 'white',
						__( 'Surface (light grey)', 'ave' ) => 'surface',
					),
					'std'         => 'white',
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'twb_trust_stats_vc_map' );

/**
 * Render callback for [twb_trust_stats].
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner content (unused).
 * @return string
 */
function twb_trust_stats_render( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'items'      => '',
			'background' => 'white',
		),
		$atts,
		'twb_trust_stats'
	);

	$items = array();
	if ( ! empty( $atts['items'] ) ) {
		$decoded = json_decode( rawurldecode( $atts['items'] ), true );
		if ( is_array( $decoded ) ) {
			$items = $decoded;
		}
	}

	// Keep only stats with at least a number or a label.
	$stats = array();
	foreach ( $items as $item ) {
		$number = isset( $item['number'] ) ? trim( $item['number'] ) : '';
		$label  = isset( $item['label'] ) ? trim( $item['label'] ) : '';
		if ( '' !== $number || '' !== $label ) {
			$stats[] = array( $number, $label );
		}
	}

	if ( empty( $stats ) ) {
		return '';
	}

	wp_enqueue_style( 'twb-trust-stats' );

	$bg_class = ( 'surface' === $atts['background'] ) ? 'twb-bg-surface' : 'twb-bg-white';

	ob_start();
	?>
	<section class="twb-trust-stats <?php echo esc_attr( $bg_class ); ?>">
		<ul class="twb-trust-stats__list twb-container">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="twb-trust-stats__item">
					<?php if ( '' !== $stat[0] ) : ?>
						<span class="twb-trust-stats__number"><?php echo esc_html( $stat[0] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $stat[1] ) : ?>
						<span class="twb-trust-stats__label"><?php echo esc_html( $stat[1] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'twb_trust_stats', 'twb_trust_stats_render' );
