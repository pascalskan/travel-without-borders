/**
 * TWB Outbound Partner Link Tracking
 *
 * Reports clicks on outbound partner links to Google Analytics, via the
 * site's existing Google Tag Manager container.
 *
 * Why dataLayer rather than gtag():
 *
 *   Analytics on this site is not loaded directly — both GA properties are
 *   configured inside GTM (container GTM-TCZM2CR), and GTM itself is held
 *   dormant until the visitor accepts cookies (see inc/cookie-consent.php).
 *   There is therefore no `gtag` function to call, and there may be no GTM
 *   at all at the moment of the click.
 *
 *   `window.dataLayer` is a plain array that the consent component defines in
 *   <head> before anything else runs. Pushing to it always succeeds. If GTM
 *   later loads, it replays everything already queued, so a click that
 *   happens before GTM is ready is still reported. If the visitor declined
 *   cookies, GTM never loads and the queued event is simply never sent —
 *   which is the correct behaviour, and means this needs no consent check of
 *   its own.
 *
 * Links opt in with data attributes rather than by URL, so adding another
 * partner link later needs no change here:
 *
 *   <a href="https://example.com/"
 *      data-twb-track="partner"
 *      data-twb-partner="Example Ltd">Example Ltd</a>
 */
( function () {
	'use strict';

	var EVENT = 'partner_link_click';

	window.dataLayer = window.dataLayer || [];

	function trackedAncestor( el ) {
		while ( el && el !== document.body ) {
			if ( el.tagName === 'A' && el.getAttribute( 'data-twb-track' ) === 'partner' ) {
				return el;
			}
			el = el.parentElement;
		}
		return null;
	}

	function report( link ) {
		var url = link.getAttribute( 'href' ) || '';
		var partner = link.getAttribute( 'data-twb-partner' ) || '';

		window.dataLayer.push( {
			event: EVENT,
			partner_name: partner,
			link_url: url,
			link_domain: hostOf( url ),
			link_text: ( link.textContent || '' ).trim().slice( 0, 100 ),
			source_page: window.location.pathname
		} );
	}

	function hostOf( url ) {
		try {
			return new URL( url, window.location.href ).hostname;
		} catch ( e ) {
			return '';
		}
	}

	// Left click. This also covers keyboard activation: pressing Enter on a
	// focused link makes the browser synthesise a click event, so a separate
	// keydown handler is not needed — and adding one double-counts every
	// keyboard visitor.
	document.addEventListener( 'click', function ( e ) {
		var link = trackedAncestor( e.target );
		if ( link ) {
			report( link );
		}
	}, true );

	// Middle click ("open in new tab") raises auxclick and no click event,
	// so it would otherwise go unrecorded.
	document.addEventListener( 'auxclick', function ( e ) {
		if ( e.button !== 1 ) {
			return;
		}
		var link = trackedAncestor( e.target );
		if ( link ) {
			report( link );
		}
	}, true );
}() );
