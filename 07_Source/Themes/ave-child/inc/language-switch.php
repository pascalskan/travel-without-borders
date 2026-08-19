<?php
/**
 * TWB Language Switch
 *
 * Renders a small fixed pill that swaps between the English and German
 * version of a page. It appears only on pages that actually have a
 * counterpart (see `twb_language_switch_pairs()`), so it never offers a
 * translation that does not exist.
 *
 * Deliberately a plain link, not a JS toggle: it navigates between two real
 * WordPress pages, so it works without JavaScript and each language version
 * keeps its own URL for sharing and search engines.
 *
 * Styled to match the cookie-settings tab but pinned top-RIGHT, so the two
 * fixed controls never overlap (the consent banner sits bottom-left). Its
 * vertical offset is managed by assets/js/language-switch.js, which keeps it
 * clear of the non-sticky header — see that file for why CSS can't do it.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map of translated page pairs: English page ID => German page ID.
 *
 * Add a line here when another page gains a German version.
 *
 * Page ids differ between the local rebuild and production, because the Trade
 * pages were created on live long after the local ones and picked up whatever
 * ids were free. Both pairs are listed so the switch works in either
 * environment; a filter can override this per-site if they ever diverge again.
 *
 *   local  7263 => 7539
 *   live   7338 => 7339
 *
 * @return array
 */
function twb_language_switch_pairs() {
	return apply_filters(
		'twb_language_switch_pairs',
		array(
			7263 => 7539, // local rebuild:  Trade => Trade (Deutsch)
			7338 => 7339, // production:     Trade => Trade (Deutsch)
		)
	);
}

/**
 * Resolve the counterpart for the page being viewed.
 *
 * @return array|false { id, lang, label } for the OTHER language, or false.
 */
function twb_language_switch_target() {
	if ( ! is_page() ) {
		return false;
	}

	$current = get_queried_object_id();
	$pairs   = twb_language_switch_pairs();

	// Viewing the English page -> offer the German one.
	if ( isset( $pairs[ $current ] ) ) {
		return array(
			'id'    => (int) $pairs[ $current ],
			'lang'  => 'de',
			'label' => 'Deutsch',
		);
	}

	// Viewing the German page -> offer the English one.
	$flipped = array_flip( $pairs );
	if ( isset( $flipped[ $current ] ) ) {
		return array(
			'id'    => (int) $flipped[ $current ],
			'lang'  => 'en',
			'label' => 'English',
		);
	}

	return false;
}

/**
 * Enqueue the switch assets, only where the control will render.
 */
function twb_language_switch_assets() {
	if ( is_admin() || ! twb_language_switch_target() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$css_path = $dir . '/assets/css/language-switch.css';
	$css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : false;

	wp_enqueue_style(
		'twb-language-switch',
		$uri . '/assets/css/language-switch.css',
		array( 'twb-tokens' ),
		$css_ver
	);

	// Keeps the pill clear of the (non-sticky) header at the top of the page
	// while still holding position once the header has scrolled away.
	$js_path = $dir . '/assets/js/language-switch.js';
	$js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : false;

	wp_enqueue_script(
		'twb-language-switch',
		$uri . '/assets/js/language-switch.js',
		array(),
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_language_switch_assets' );

/**
 * Render the switch in the footer.
 */
function twb_language_switch_render() {
	if ( is_admin() ) {
		return;
	}

	$target = twb_language_switch_target();
	if ( ! $target ) {
		return;
	}

	$url = get_permalink( $target['id'] );
	if ( ! $url ) {
		return;
	}

	$icon = '<svg class="twb-lang-switch__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'
		. '<circle cx="12" cy="12" r="9"></circle>'
		. '<path d="M3 12h18"></path>'
		. '<path d="M12 3c2.5 2.7 3.8 5.8 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.8-3.8-9S9.5 5.7 12 3z"></path>'
		. '</svg>';
	?>
	<a class="twb-lang-switch" href="<?php echo esc_url( $url ); ?>"
		hreflang="<?php echo esc_attr( $target['lang'] ); ?>"
		lang="<?php echo esc_attr( $target['lang'] ); ?>"
		rel="alternate"
		title="<?php echo esc_attr( $target['label'] ); ?>">
		<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static inline SVG. ?>
		<span><?php echo esc_html( $target['label'] ); ?></span>
	</a>
	<?php
}
add_action( 'wp_footer', 'twb_language_switch_render' );

/**
 * Tell search engines the two pages are translations of one another, so the
 * German version is not treated as duplicate content.
 */
function twb_language_switch_hreflang() {
	if ( ! is_page() ) {
		return;
	}

	$current = get_queried_object_id();
	$pairs   = twb_language_switch_pairs();
	$flipped = array_flip( $pairs );

	if ( isset( $pairs[ $current ] ) ) {
		$en = $current;
		$de = (int) $pairs[ $current ];
	} elseif ( isset( $flipped[ $current ] ) ) {
		$de = $current;
		$en = (int) $flipped[ $current ];
	} else {
		return;
	}

	printf( '<link rel="alternate" hreflang="en" href="%s" />' . "\n", esc_url( get_permalink( $en ) ) );
	printf( '<link rel="alternate" hreflang="de" href="%s" />' . "\n", esc_url( get_permalink( $de ) ) );
}
add_action( 'wp_head', 'twb_language_switch_hreflang' );

/**
 * Set <html lang="de"> on the German page so screen readers and browsers
 * pronounce / treat the content as German rather than English.
 */
function twb_language_switch_html_lang( $output ) {
	if ( ! is_page() ) {
		return $output;
	}
	$flipped = array_flip( twb_language_switch_pairs() );
	if ( isset( $flipped[ get_queried_object_id() ] ) ) {
		$output = 'lang="de-DE"';
	}
	return $output;
}
add_filter( 'language_attributes', 'twb_language_switch_html_lang' );
