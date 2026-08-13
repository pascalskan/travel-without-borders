/**
 * TWB Marketing Consent Sync
 *
 * The "Marketing Email Consent" checkbox on the Contact page's forms only
 * ever carries a value in the admin notification email when it's ticked —
 * Quform's {all_form_data} / {element} tokens render an unchecked checkbox
 * as an empty string, so the admin email would say nothing at all if a
 * client leaves it unticked. To guarantee the email always states the
 * outcome explicitly either way, a paired hidden field carries a fixed
 * sentence that this script keeps in sync with the checkbox's checked state.
 *
 * Both the Individual form (quform_1_*) and the Business form (quform_3_*)
 * have this pair of fields at the same element IDs (checkbox 8, hidden 9),
 * so both are wired up generically by class-name pattern rather than
 * hardcoding a single form's field names.
 */
( function () {
	'use strict';

	var AGREED = "Yes, the client agreed to receive marketing emails.";
	var NOT_AGREED = 'No, the client did NOT agree to receive marketing emails.';
	var CHECKBOX_CLASS_RE = /^quform-field-(\d+)_8$/;

	function sync( checkbox, formId ) {
		var hidden = document.querySelector( '.quform-field-' + formId + '_9' );
		if ( ! hidden ) {
			return;
		}
		hidden.value = checkbox.checked ? AGREED : NOT_AGREED;
	}

	function wire( checkbox, formId ) {
		sync( checkbox, formId ); // set the initial state (unticked on load).
		checkbox.addEventListener( 'change', function () {
			sync( checkbox, formId );
		} );
	}

	function init() {
		var checkboxes = document.querySelectorAll( '[class*="quform-field-"][class*="_8"]' );
		for ( var i = 0; i < checkboxes.length; i++ ) {
			var classes = checkboxes[ i ].className.split( /\s+/ );
			for ( var j = 0; j < classes.length; j++ ) {
				var m = classes[ j ].match( CHECKBOX_CLASS_RE );
				if ( m ) {
					wire( checkboxes[ i ], m[ 1 ] );
					break;
				}
			}
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
