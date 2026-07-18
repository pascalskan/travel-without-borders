/**
 * Special Interest listing - equalise card heights across both rows.
 *
 * CSS flexbox already stretches every card to the tallest in its own row, but
 * the eight cards live in two separate WPBakery rows, so a row containing a
 * long title ("British - German Royal Heritage Route" wraps onto five lines at
 * 768px) still ends up taller than the other. How many lines a title takes
 * changes with the viewport, so a fixed min-height can't keep them level
 * without padding every short card with dead space.
 *
 * Instead, measure the tallest title across all the cards and reserve that much
 * room in each one. Flex then pins the MORE/ENQUIRE footers to a common bottom.
 * If this script fails to run the cards still equalise within each row.
 */
( function () {
	'use strict';

	var SELECTOR = '.fancy-box-tour .fancy-box-header h3';
	var MIN_WIDTH = 768; // Below this the cards stack, so equalising is moot.

	function equalise() {
		var titles = document.querySelectorAll( SELECTOR );
		if ( ! titles.length ) {
			return;
		}

		// Clear previous values first, so shrinking the window can measure the
		// titles at their natural height rather than the last reserved one.
		var i;
		for ( i = 0; i < titles.length; i++ ) {
			titles[ i ].style.minHeight = '';
		}

		if ( window.innerWidth < MIN_WIDTH ) {
			return;
		}

		var tallest = 0;
		for ( i = 0; i < titles.length; i++ ) {
			tallest = Math.max( tallest, titles[ i ].getBoundingClientRect().height );
		}
		for ( i = 0; i < titles.length; i++ ) {
			titles[ i ].style.minHeight = tallest + 'px';
		}
	}

	function onReady() {
		equalise();
		// Re-run once the images have loaded, in case they shift the layout.
		window.addEventListener( 'load', equalise );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', onReady );
	} else {
		onReady();
	}

	var timer = null;
	window.addEventListener( 'resize', function () {
		window.clearTimeout( timer );
		timer = window.setTimeout( equalise, 150 );
	} );
}() );
