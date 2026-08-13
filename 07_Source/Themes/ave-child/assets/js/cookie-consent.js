/**
 * TWB Cookie Consent Banner
 *
 * Google Tag Manager is defined (in wp_head, see inc/cookie-consent.php) as a
 * dormant `twbLoadGTM()` function rather than auto-executing — nothing in GTM
 * (including Google Analytics) can load or set a cookie until this script
 * actually calls it, which only happens once the visitor accepts.
 *
 * Consent is remembered in both localStorage and a plain cookie (so it
 * survives visits even if one storage mechanism is unavailable), and a small
 * "Cookie Settings" tab lets a visitor reopen the banner to change their mind
 * at any time.
 */
( function () {
	'use strict';

	var CONSENT_KEY = 'twb_cookie_consent'; // 'accepted' | 'rejected'
	var CONSENT_MAX_AGE = 60 * 60 * 24 * 365; // 1 year, in seconds

	function readConsent() {
		try {
			var stored = window.localStorage.getItem( CONSENT_KEY );
			if ( stored ) {
				return stored;
			}
		} catch ( e ) {
			/* localStorage unavailable (privacy mode etc.) — fall through to cookie. */
		}
		var match = document.cookie.match( new RegExp( '(?:^|; )' + CONSENT_KEY + '=([^;]*)' ) );
		return match ? decodeURIComponent( match[ 1 ] ) : null;
	}

	function writeConsent( value ) {
		try {
			window.localStorage.setItem( CONSENT_KEY, value );
		} catch ( e ) {
			/* Ignore — the cookie fallback below still records the choice. */
		}
		document.cookie = CONSENT_KEY + '=' + value + '; path=/; max-age=' + CONSENT_MAX_AGE + '; SameSite=Lax';
	}

	function loadAnalytics() {
		if ( typeof window.twbLoadGTM === 'function' ) {
			window.twbLoadGTM();
		}
	}

	function init() {
		var banner = document.getElementById( 'twb-cookie-consent' );
		var settingsTab = document.getElementById( 'twb-cookie-settings-tab' );
		if ( ! banner ) {
			return;
		}

		var acceptBtn = banner.querySelector( '[data-twb-consent="accept"]' );
		var rejectBtn = banner.querySelector( '[data-twb-consent="reject"]' );

		function showBanner() {
			if ( settingsTab ) {
				settingsTab.hidden = true;
			}
			banner.classList.add( 'is-visible' );
			// Add is-shown a frame later so the CSS transform transition runs
			// (adding both classes in the same frame skips straight to the end state).
			window.requestAnimationFrame( function () {
				window.requestAnimationFrame( function () {
					banner.classList.add( 'is-shown' );
				} );
			} );
		}

		function hideBanner() {
			banner.classList.remove( 'is-shown' );
			window.setTimeout( function () {
				banner.classList.remove( 'is-visible' );
				if ( settingsTab ) {
					settingsTab.hidden = false;
				}
			}, 350 );
		}

		if ( acceptBtn ) {
			acceptBtn.addEventListener( 'click', function () {
				writeConsent( 'accepted' );
				loadAnalytics();
				hideBanner();
			} );
		}
		if ( rejectBtn ) {
			rejectBtn.addEventListener( 'click', function () {
				writeConsent( 'rejected' );
				hideBanner();
			} );
		}
		if ( settingsTab ) {
			settingsTab.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				showBanner();
			} );
		}

		var consent = readConsent();
		if ( consent === 'accepted' ) {
			loadAnalytics();
			if ( settingsTab ) {
				settingsTab.hidden = false;
			}
		} else if ( consent === 'rejected' ) {
			if ( settingsTab ) {
				settingsTab.hidden = false;
			}
		} else {
			showBanner();
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
