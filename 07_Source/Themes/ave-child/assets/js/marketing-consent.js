/**
 * TWB Marketing Consent Sync
 *
 * The "Marketing Email Consent" checkbox (quform_1_8) only ever carries a
 * value in the admin notification email when it's ticked — Quform's
 * {all_form_data} / {element} tokens render an unchecked checkbox as an empty
 * string, so the admin email would say nothing at all if a client leaves it
 * unticked. To guarantee the email always states the outcome explicitly
 * either way, a hidden field (quform_1_9) carries a fixed sentence that this
 * script keeps in sync with the checkbox's checked state.
 */
( function () {
	'use strict';

	var AGREED = "Yes, the client agreed to receive marketing emails.";
	var NOT_AGREED = 'No, the client did NOT agree to receive marketing emails.';

	function sync( checkbox ) {
		var hidden = document.querySelector( '.quform-field-1_9' );
		if ( ! hidden ) {
			return;
		}
		hidden.value = checkbox.checked ? AGREED : NOT_AGREED;
	}

	function init() {
		var checkbox = document.querySelector( '.quform-field-1_8' );
		if ( ! checkbox ) {
			return;
		}
		sync( checkbox ); // set the initial state (unticked on load).
		checkbox.addEventListener( 'change', function () {
			sync( checkbox );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
