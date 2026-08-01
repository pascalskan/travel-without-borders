/* global Flickity */
/**
 * TWB Hero Carousel initialiser.
 *
 * Reuses Ave's bundled Flickity. Each `.twb-hero` is initialised exactly once;
 * options come from the element's `data-twb-hero` attribute (set server-side).
 */
( function () {
	'use strict';

	var DEFAULTS = {
		autoPlay: 5000,
		wrapAround: true,
		pageDots: true,
		prevNextButtons: true,
		draggable: true,
		pauseAutoPlayOnHover: true,
		cellAlign: 'center',
		imagesLoaded: true,
		/* The image height is already reserved up-front via the fixed
		 * --twb-img-h CSS variable, so adaptive height isn't needed for
		 * layout - and combined with wrapAround + async lazy-loaded images,
		 * it was causing cell widths/positions to drift further out of sync
		 * with every autoplay cycle (slides ending up stacked or blank). */
		adaptiveHeight: false,
		accessibility: true /* tab focus + left/right arrow-key navigation */
	};

	function initHero( el ) {
		// Guard: never initialise the same element twice.
		if ( ! el || el.dataset.twbHeroInit === 'true' ) {
			return;
		}

		var carousel = el.querySelector( '.twb-hero__carousel' );
		if ( ! carousel || typeof Flickity === 'undefined' ) {
			return;
		}

		// Already a Flickity instance? (e.g. another script) – do not duplicate.
		if ( Flickity.data( carousel ) ) {
			el.dataset.twbHeroInit = 'true';
			return;
		}

		var options = {};
		var key;
		for ( key in DEFAULTS ) {
			if ( Object.prototype.hasOwnProperty.call( DEFAULTS, key ) ) {
				options[ key ] = DEFAULTS[ key ];
			}
		}

		try {
			var custom = JSON.parse( el.getAttribute( 'data-twb-hero' ) || '{}' );
			for ( key in custom ) {
				if ( Object.prototype.hasOwnProperty.call( custom, key ) ) {
					options[ key ] = custom[ key ];
				}
			}
		} catch ( e ) {
			/* Fall back to defaults on malformed JSON. */
		}

		el.dataset.twbHeroInit = 'true';
		/* eslint-disable no-new */
		var flkty = new Flickity( carousel, options );
		/* eslint-enable no-new */

		// Slide images are lazy-loaded (their real `src` is swapped in later by
		// the theme's lazy-loader), so Flickity's own `imagesLoaded` option only
		// ever sees the tiny inline placeholder and lays out cells before the
		// real image has a size. Re-measure once real images finish loading so
		// cell/caption positioning is correct instead of stale. Several images
		// tend to finish loading in a burst, and with `wrapAround` enabled,
		// calling `resize()` once per image (rather than once for the whole
		// burst) can desync Flickity's wrap-around cloned cell positions - so
		// this is debounced to a single resize after the burst settles.
		var imgs = carousel.querySelectorAll( 'img' );
		var resizeTimer = null;
		function scheduleResize() {
			if ( resizeTimer ) {
				clearTimeout( resizeTimer );
			}
			resizeTimer = setTimeout( function () {
				flkty.resize();
			}, 150 );
		}
		for ( var j = 0; j < imgs.length; j++ ) {
			imgs[ j ].addEventListener( 'load', scheduleResize );
		}
	}

	function initAll() {
		var heroes = document.querySelectorAll( '.twb-hero' );
		for ( var i = 0; i < heroes.length; i++ ) {
			initHero( heroes[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
}() );
