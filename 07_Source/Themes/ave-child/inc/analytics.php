<?php
/**
 * TWB — GA4 Google tag, installed directly on the site.
 *
 * WHY THIS EXISTS, AND WHY IT DOES NOT GO THROUGH GTM
 *
 * The GTM container (GTM-TCZM2CR) contains **no GA4 tag at all**. Inspected
 * 2026-09-10 by fetching the published container directly; its seven tags are:
 *
 *     __ua  x4   pageview, and events for form / phone / email
 *     __fsl x1   form-submit listener
 *     __cl  x2   click listeners
 *
 * All four measurement tags are Universal Analytics, which Google stopped
 * processing in July 2023. Every figure currently reaching the GA4 property
 * arrives via Google's "connected site tags" relay, which forwards legacy UA
 * hits into GA4. That relay carries `event_category` and nothing else, which
 * is why `link_url`, `link_domain`, `link_text` and `outbound` have never
 * appeared anywhere on the property — and therefore why the configured
 * `cw_sports_travel_click` event has never been able to match anything.
 *
 * It is also the documented cause of the outbound-click failure. Google's own
 * tag diagnostic on the container says it outright: legacy UA tags with
 * connected site tags "could prevent access to Google tag features like
 * Enhanced Measurement events". Page views work; Enhanced Measurement does
 * not. That is exactly what was observed over three rounds of testing.
 *
 * Removing those UA tags requires access to the container, which this account
 * does not have and has not been able to obtain. Google's remedy names two
 * options — "install the Google tag directly on your site **or** use Google
 * Tag Manager" — and only the first is available to us. So this file installs
 * gtag.js for a NEW data stream, directly, bypassing GTM and the UA relay
 * entirely. Nothing here depends on container access.
 *
 * See 01_Documentation/PARTNER_LINK_TRACKING.md for the full diagnosis.
 *
 * CONSENT
 *
 * This tag is gated exactly as GTM is: `twbLoadGA4()` is defined dormant in
 * <head> and never runs until assets/js/cookie-consent.js calls it after an
 * Accept. Nothing loads and no cookie is set for a visitor who declines or
 * who has not chosen yet. Both loaders are called from the same place in that
 * file, so the two can never drift apart.
 *
 * DOUBLE COUNTING — EXPECTED, AND HOW TO READ AROUND IT
 *
 * The legacy UA tags keep firing until someone with container access removes
 * them, so page views will be reported twice at property level: once relayed
 * onto the old stream, once directly onto the new one. Events are not
 * affected — the relay only ever produced the three UA events above.
 *
 * Reports can be read cleanly in the meantime by filtering on the **Stream
 * name / Stream ID** dimension, which separates the two sources. That is a
 * reporting workaround, not a fix; the fix is deleting the UA tags.
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The measurement ID of the new, directly-installed data stream.
 *
 * Deliberately NOT the property's existing ID (G-484B6GT6CW). That one is
 * already claimed by the UA relay, and a second gtag instance pointed at a
 * measurement ID something else already owns defers to the incumbent and
 * sends nothing — tested on 2026-08-20 and recorded in PARTNER_LINK_TRACKING.md.
 * A new stream has no incumbent, which is the point of creating one.
 *
 * Stream created 2026-09-10: "TWB Direct (gtag)", stream id 15755605316, on
 * property 386233391 (account 135755883) alongside the existing
 * "www.travelwithoutborders.co.uk - GA4" stream 5487567775.
 *
 * Its detail page reports **Manage connected site tags: 0 connected** — which
 * is exactly the condition absent from the old stream and the reason this
 * approach is expected to work. Enhanced measurement is on, with Outbound
 * clicks confirmed enabled in the panel before the stream was created.
 */
if ( ! defined( 'TWB_GA4_MEASUREMENT_ID' ) ) {
	define( 'TWB_GA4_MEASUREMENT_ID', 'G-HY9C6Z86W9' );
}

/**
 * Whether the tag is configured and should be printed.
 *
 * @return bool
 */
function twb_ga4_is_configured() {
	$id = (string) TWB_GA4_MEASUREMENT_ID;

	// A GA4 measurement ID is "G-" followed by an alphanumeric block. Checking
	// the shape rather than just emptiness means a half-finished edit (a
	// pasted stream ID, say, which is numeric) fails loudly here rather than
	// silently loading a tag that reports nowhere.
	return (bool) preg_match( '/^G-[A-Z0-9]{6,}$/i', $id );
}

/**
 * Print the dormant GA4 loader in <head>.
 *
 * Mirrors twb_cookie_consent_head() deliberately: same pattern, same guard
 * flag convention, same "define but do not call" shape. Priority 2 puts it
 * immediately after the GTM loader (priority 1) so the two sit together in
 * the source and `window.dataLayer` is already defined.
 *
 * `send_page_view` is left at its default (true). This stream is a fresh
 * install with nothing else reporting to it, so it needs to send its own page
 * view — the relayed one lands on the *other* stream.
 */
function twb_ga4_head() {
	if ( is_admin() || ! twb_ga4_is_configured() ) {
		return;
	}
	$id = esc_js( TWB_GA4_MEASUREMENT_ID );
	?>
<!-- Google tag (gtag.js) — installed directly, NOT via GTM; see inc/analytics.php.
     Loaded only after cookie consent is accepted; see assets/js/cookie-consent.js -->
<script>
window.dataLayer = window.dataLayer || [];
function twbLoadGA4(){
	if ( window.twbGA4Loaded ) { return; }
	window.twbGA4Loaded = true;
	// gtag() must exist before the library loads: calls made now are queued on
	// dataLayer and replayed once gtag.js parses. This is Google's own snippet
	// order and the reason config can be issued on the line after the tag.
	window.gtag = window.gtag || function(){ dataLayer.push(arguments); };
	gtag('js', new Date());
	gtag('config', '<?php echo $id; ?>');
	var s = document.createElement('script');
	s.async = true;
	s.src = 'https://www.googletagmanager.com/gtag/js?id=<?php echo $id; ?>';
	var f = document.getElementsByTagName('script')[0];
	f.parentNode.insertBefore(s, f);
}
</script>
<!-- End Google tag -->
	<?php
}
add_action( 'wp_head', 'twb_ga4_head', 2 );
