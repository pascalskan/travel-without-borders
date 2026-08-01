<?php
/**
 * TWB Cookie Consent
 *
 * Google Tag Manager (container GTM-TCZM2CR) was previously hardcoded into
 * header.php and fired unconditionally on every page load — including the two
 * Google Analytics tags configured inside it — before any visitor consent was
 * captured. Under UK PECR/GDPR, non-essential analytics cookies must not be
 * set until the visitor has actively consented.
 *
 * This component:
 *  1. Prints GTM's loader as a dormant `twbLoadGTM()` JS function in
 *     `wp_head` (see `twb_cookie_consent_head()`) instead of auto-executing
 *     it — GTM, and everything configured inside it, cannot load at all until
 *     something calls that function.
 *  2. Renders a bottom-of-page consent banner (own CSS/JS, site-wide) that
 *     calls `twbLoadGTM()` only when the visitor clicks Accept, and remembers
 *     the choice (localStorage + cookie fallback) so returning visitors who
 *     already accepted get analytics loaded straight away without being
 *     asked again.
 *
 * Site-wide (not on-demand like the shortcode-based components elsewhere in
 * inc/) because consent has to be asked on every page, not just ones using a
 * particular WPBakery element.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The site's GTM container ID. Single source of truth — also referenced by
 * the inline snippet this replaces the old hardcoded copy of in header.php.
 */
if ( ! defined( 'TWB_GTM_CONTAINER_ID' ) ) {
	define( 'TWB_GTM_CONTAINER_ID', 'GTM-TCZM2CR' );
}

/**
 * Register + enqueue the banner's CSS/JS on every front-end page.
 */
function twb_cookie_consent_register_assets() {
	if ( is_admin() ) {
		return;
	}

	$dir  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();

	$css     = $path . '/assets/css/cookie-consent.css';
	$js      = $path . '/assets/js/cookie-consent.js';
	$css_ver = file_exists( $css ) ? filemtime( $css ) : false;
	$js_ver  = file_exists( $js ) ? filemtime( $js ) : false;

	wp_enqueue_style(
		'twb-cookie-consent',
		$dir . '/assets/css/cookie-consent.css',
		array( 'twb-tokens' ),
		$css_ver
	);

	wp_enqueue_script(
		'twb-cookie-consent',
		$dir . '/assets/js/cookie-consent.js',
		array(),
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'twb_cookie_consent_register_assets' );

/**
 * Print the dormant GTM loader in <head>.
 *
 * Defines `window.dataLayer` and a `twbLoadGTM()` function containing GTM's
 * standard snippet, but does not call it — GTM, and every tag configured
 * inside it (both Google Analytics properties currently in this container),
 * stays completely unloaded until the consent banner's JS calls this
 * function after an "Accept" click (or immediately, on a later page view, for
 * a visitor who already accepted).
 */
function twb_cookie_consent_head() {
	if ( is_admin() ) {
		return;
	}
	$id = esc_js( TWB_GTM_CONTAINER_ID );
	?>
<!-- Google Tag Manager — loaded only after cookie consent is accepted; see assets/js/cookie-consent.js -->
<script>
window.dataLayer = window.dataLayer || [];
function twbLoadGTM(){
	if ( window.twbGTMLoaded ) { return; }
	window.twbGTMLoaded = true;
	dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
	var f = document.getElementsByTagName('script')[0], j = document.createElement('script'), dl = '&l=dataLayer';
	j.async = true;
	j.src = 'https://www.googletagmanager.com/gtm.js?id=<?php echo $id; ?>' + dl;
	f.parentNode.insertBefore(j, f);
	var ns = document.createElement('noscript');
	var ifr = document.createElement('iframe');
	ifr.src = 'https://www.googletagmanager.com/ns.html?id=<?php echo $id; ?>';
	ifr.height = 0; ifr.width = 0; ifr.style.display = 'none'; ifr.style.visibility = 'hidden';
	ns.appendChild(ifr);
	document.body.insertBefore(ns, document.body.firstChild);
}
</script>
<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'twb_cookie_consent_head', 1 );

/**
 * Render the consent banner + "Cookie Settings" reopen tab in the footer.
 *
 * Both start hidden (banner via CSS `display:none` until JS adds
 * `is-visible`; the tab via the `hidden` attribute, which JS un-hides once a
 * choice has been recorded) so nothing shows before cookie-consent.js has run
 * and checked for an existing choice.
 */
function twb_cookie_consent_footer() {
	if ( is_admin() ) {
		return;
	}
	?>
	<div id="twb-cookie-consent" class="twb-cookie-consent" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e( 'Cookie consent', 'ave' ); ?>">
		<div class="twb-cookie-consent__inner">
			<p class="twb-cookie-consent__text">
				<?php
				printf(
					/* translators: %s: link to the Privacy Policy page. */
					esc_html__( 'We use essential cookies to make our website work. With your consent, we\'d also like to use Google Analytics cookies to help us understand how visitors use our website. See our %s for details.', 'ave' ),
					'<a href="' . esc_url( home_url( '/twb-privacy-policy/' ) ) . '">' . esc_html__( 'Privacy Policy', 'ave' ) . '</a>'
				);
				?>
			</p>
			<div class="twb-cookie-consent__actions">
				<button type="button" class="twb-cookie-consent__btn twb-cookie-consent__btn--reject" data-twb-consent="reject"><?php esc_html_e( 'Reject Non-Essential', 'ave' ); ?></button>
				<button type="button" class="twb-cookie-consent__btn twb-cookie-consent__btn--accept" data-twb-consent="accept"><?php esc_html_e( 'Accept All', 'ave' ); ?></button>
			</div>
		</div>
	</div>
	<button type="button" id="twb-cookie-settings-tab" class="twb-cookie-settings-tab" hidden><?php esc_html_e( 'Cookie Settings', 'ave' ); ?></button>
	<?php
}
add_action( 'wp_footer', 'twb_cookie_consent_footer' );
