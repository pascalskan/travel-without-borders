/* global Flickity */
/**
 * TWB Testimonials initialiser.
 *
 * Reuses Ave's bundled Flickity. Each `.twb-testimonials` is initialised exactly
 * once; options come from the carousel's `data-twb-testimonials` attribute (set
 * server-side). Honours prefers-reduced-motion by disabling autoplay and the
 * crossfade.
 */
( function () {
	'use strict';

	var DEFAULTS = {
		autoPlay: 6000,
		wrapAround: true,
		pageDots: true,
		prevNextButtons: true,
		draggable: true,
		pauseAutoPlayOnHover: true,
		cellAlign: 'center',
		adaptiveHeight: true,
		accessibility: true /* tab focus + left/right arrow-key navigation */
	};

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function initOne( el ) {
		// Guard: never initialise the same element twice.
		if ( ! el || el.dataset.twbTestimonialsInit === 'true' ) {
			return;
		}

		var carousel = el.querySelector( '.twb-testimonials__carousel' );
		if ( ! carousel || typeof Flickity === 'undefined' ) {
			return;
		}

		// Already a Flickity instance? – do not duplicate.
		if ( Flickity.data( carousel ) ) {
			el.dataset.twbTestimonialsInit = 'true';
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
			var custom = JSON.parse( carousel.getAttribute( 'data-twb-testimonials' ) || '{}' );
			for ( key in custom ) {
				if ( Object.prototype.hasOwnProperty.call( custom, key ) ) {
					options[ key ] = custom[ key ];
				}
			}
		} catch ( e ) {
			/* Fall back to defaults on malformed JSON. */
		}

		// Respect reduced-motion: disable autoplay.
		if ( prefersReducedMotion() ) {
			options.autoPlay = false;
		}

		el.dataset.twbTestimonialsInit = 'true';
		/* eslint-disable no-new */
		new Flickity( carousel, options );
		/* eslint-enable no-new */
	}

	function initAll() {
		var nodes = document.querySelectorAll( '.twb-testimonials' );
		for ( var i = 0; i < nodes.length; i++ ) {
			initOne( nodes[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
}() );
