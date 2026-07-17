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

	function debounce( fn, wait ) {
		var t;
		return function () {
			clearTimeout( t );
			t = setTimeout( fn, wait );
		};
	}

	/**
	 * Grid "Show more" + modal.
	 *
	 * A grid card clamps its quote to the card height (CSS). Where the quote is
	 * clipped, reveal a "Show more" button that opens a modal showing the whole
	 * review at a comfortable size, rather than letting one card grow huge.
	 */
	function setupGridModal( el ) {
		var modal = el.querySelector( '.twb-testimonials__modal' );
		if ( ! modal ) {
			return; // carousel section, no modal
		}
		// Portal the modal to <body> so it overlays everything. Left in place, an
		// ancestor row's transform traps it in a stacking context and the sticky
		// header bleeds over the top.
		if ( modal.parentNode !== document.body ) {
			document.body.appendChild( modal );
		}
		var body   = modal.querySelector( '.twb-testimonials__modal-body' );
		var cards  = el.querySelectorAll( '.twb-testimonials__grid .twb-testimonial-card' );
		var opener = null;

		function refresh() {
			for ( var i = 0; i < cards.length; i++ ) {
				var quote = cards[ i ].querySelector( '.twb-testimonial-card__quote' );
				var btn   = cards[ i ].querySelector( '.twb-testimonial-card__more' );
				if ( ! quote || ! btn ) {
					continue;
				}
				var clipped = ( quote.scrollHeight - quote.clientHeight ) > 4;
				btn.hidden = ! clipped;
				cards[ i ].classList.toggle( 'is-clamped', clipped );
			}
		}

		function open( card ) {
			opener = card;
			body.innerHTML = '';
			var media = card.querySelector( '.twb-testimonial-card__media' );
			var img   = media ? media.querySelector( 'img' ) : null;
			if ( img ) {
				var wrap = document.createElement( 'div' );
				wrap.className = 'twb-testimonials__modal-media';
				var ci = img.cloneNode( true );
				ci.removeAttribute( 'loading' );
				ci.classList.remove( 'ld-lazyload' );
				var ds = ci.getAttribute( 'data-src' );
				if ( ds ) {
					ci.setAttribute( 'src', ds );
				}
				wrap.appendChild( ci );
				body.appendChild( wrap );
			}
			var content = document.createElement( 'div' );
			content.className = 'twb-testimonials__modal-content';
			[ '.twb-testimonial-card__top', '.twb-testimonial-card__quote', '.twb-testimonial-card__caption' ].forEach( function ( sel ) {
				var node = card.querySelector( sel );
				if ( node ) {
					content.appendChild( node.cloneNode( true ) );
				}
			} );
			body.appendChild( content );

			modal.hidden = false;
			document.documentElement.classList.add( 'twb-modal-open' );
			var close = modal.querySelector( '.twb-testimonials__modal-close' );
			if ( close ) {
				close.focus();
			}
		}

		function close() {
			modal.hidden = true;
			document.documentElement.classList.remove( 'twb-modal-open' );
			if ( opener ) {
				var b = opener.querySelector( '.twb-testimonial-card__more' );
				if ( b ) {
					b.focus();
				}
				opener = null;
			}
		}

		for ( var j = 0; j < cards.length; j++ ) {
			( function ( card ) {
				var btn = card.querySelector( '.twb-testimonial-card__more' );
				if ( btn ) {
					btn.addEventListener( 'click', function () {
						open( card );
					} );
				}
			} )( cards[ j ] );
		}

		var closers = modal.querySelectorAll( '[data-twb-modal-close]' );
		for ( var k = 0; k < closers.length; k++ ) {
			closers[ k ].addEventListener( 'click', close );
		}
		document.addEventListener( 'keydown', function ( e ) {
			if ( ( e.key === 'Escape' || e.keyCode === 27 ) && ! modal.hidden ) {
				close();
			}
		} );

		refresh();
		window.addEventListener( 'load', refresh );
		window.addEventListener( 'resize', debounce( refresh, 200 ) );
	}

	function initAll() {
		var nodes = document.querySelectorAll( '.twb-testimonials' );
		for ( var i = 0; i < nodes.length; i++ ) {
			initOne( nodes[ i ] );
			setupGridModal( nodes[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
}() );
