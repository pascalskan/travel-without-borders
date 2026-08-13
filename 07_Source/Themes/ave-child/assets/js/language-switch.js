/**
 * TWB Language Switch positioning
 *
 * Keeps the switch pill tucked just below the header at every scroll position.
 *
 * This needs JS because the header's height is not constant. The theme sticks
 * `.mainbar-wrap` (see liquidStickyHeader in the parent theme's theme.js),
 * toggling `.is-stuck` on it — which is `position: fixed; top: 0` and shorter
 * than the full header, since the secondary bar scrolls away. So there are
 * three distinct states to sit below:
 *
 *   1. At the top of the page  — the whole header is in flow (~190px desktop).
 *   2. Scrolled, header stuck  — only the pinned mainbar is on screen.
 *   3. Scrolled, not yet stuck — nothing up there; rest at MIN_TOP.
 *
 * Rather than hardcode any of those heights, this measures whichever element
 * is currently occupying the top of the viewport and positions below it. That
 * covers all three states, both breakpoints, and the logged-in admin bar
 * (which pushes `.is-stuck` down to top: 32px) with no special cases.
 *
 * CSS `position: sticky` can't express this: the pill renders in wp_footer at
 * the end of <body>, so it has no scrolling ancestor to stick within.
 */
( function () {
	'use strict';

	var GAP = 20; // clearance between the header's bottom edge and the pill.
	var MIN_TOP = 16; // resting offset when nothing is pinned above.

	var pill = null;
	var header = null;
	var lastTop = null;

	/**
	 * Bottom edge, in viewport coordinates, of whatever header element is
	 * currently on screen. `.is-stuck` lives inside #header even while fixed,
	 * so it is found here; when absent the full in-flow header is measured,
	 * which goes negative once it has scrolled away.
	 */
	function headerBottom() {
		var stuck = header.querySelector( '.is-stuck' );

		if ( ! stuck ) {
			return header.getBoundingClientRect().bottom;
		}

		// Deliberately NOT getBoundingClientRect(): `.is-stuck` animates in
		// with `transform: translateY(-100%) -> 0` over 0.65s, and the rect
		// reflects that transform. Measuring it as it sticks would report a
		// bottom edge of ~0 and park the pill under the bar's final resting
		// place — and if the scroll stops right then, nothing would ever
		// correct it. offsetHeight is layout height and ignores transforms,
		// and the computed `top` is its resting offset (0, or 32px when the
		// admin bar is present), so this is the settled position from the
		// first frame.
		return ( parseFloat( window.getComputedStyle( stuck ).top ) || 0 ) + stuck.offsetHeight;
	}

	function position() {
		var next = Math.round( Math.max( MIN_TOP, headerBottom() + GAP ) );

		// Only write when the value actually changes: the read above would
		// otherwise be forced to re-resolve the layout this handler just
		// invalidated, on every single scroll event.
		if ( next !== lastTop ) {
			pill.style.top = next + 'px';
			lastTop = next;
		}
	}

	function init() {
		pill = document.querySelector( '.twb-lang-switch' );
		header = document.getElementById( 'header' );
		if ( ! pill || ! header ) {
			return;
		}

		position();

		window.addEventListener( 'scroll', position, { passive: true } );
		window.addEventListener( 'resize', position );
		// The header's height settles only after the logo and webfonts load,
		// which can land after DOMContentLoaded.
		window.addEventListener( 'load', position );
		// Fired by the parent theme as the mainbar sticks/unsticks. The swap
		// changes the header's height, so re-position off the back of it
		// rather than waiting for the next scroll tick.
		document.addEventListener( 'lqd-header-sticky-change', function () {
			// The class toggle is applied by the theme's own listener; defer
			// so this runs after it, measuring the settled state.
			window.setTimeout( position, 0 );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
