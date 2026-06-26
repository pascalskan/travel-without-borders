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
		fade: true,
		cellAlign: 'center',
		imagesLoaded: true,
		adaptiveHeight: true,
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
		new Flickity( carousel, options );
		/* eslint-enable no-new */
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
