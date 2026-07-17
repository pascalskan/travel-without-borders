/**
 * TWB Enquiry Toggle.
 *
 * Swaps between the Individual and Business enquiry panels under the "Get in
 * touch" heading. Both forms are rendered server-side; this only shows/hides
 * them and moves the sliding pill. Individual is the default (server-rendered
 * as active), so with JS disabled the individual form is still shown.
 */
( function () {
	'use strict';

	function initOne( root ) {
		if ( root.dataset.twbEnquiryInit === 'true' ) {
			return;
		}
		root.dataset.twbEnquiryInit = 'true';

		var tabs   = root.querySelectorAll( '.twb-enquiry__tab' );
		var panels = root.querySelectorAll( '.twb-enquiry__panel' );

		function activate( target ) {
			var i;
			for ( i = 0; i < tabs.length; i++ ) {
				var isTab = tabs[ i ].getAttribute( 'data-twb-target' ) === target;
				tabs[ i ].classList.toggle( 'is-active', isTab );
				tabs[ i ].setAttribute( 'aria-selected', isTab ? 'true' : 'false' );
			}
			for ( i = 0; i < panels.length; i++ ) {
				var isPanel = panels[ i ].getAttribute( 'data-twb-panel' ) === target;
				panels[ i ].classList.toggle( 'is-active', isPanel );
				if ( isPanel ) {
					panels[ i ].removeAttribute( 'hidden' );
				} else {
					panels[ i ].setAttribute( 'hidden', '' );
				}
			}
			root.setAttribute( 'data-active', target );
		}

		for ( var t = 0; t < tabs.length; t++ ) {
			( function ( tab ) {
				tab.addEventListener( 'click', function () {
					activate( tab.getAttribute( 'data-twb-target' ) );
				} );
			} )( tabs[ t ] );
		}

		// Establish the initial state attribute for the sliding pill.
		root.setAttribute( 'data-active', 'individual' );
	}

	function initAll() {
		var nodes = document.querySelectorAll( '[data-twb-enquiry]' );
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
