(function( $ ) {
	'use strict';

	/**
	 * Show only the fields of the selected reCAPTCHA type.
	 *
	 * Hidden rows stay in the form, so the keys of the other type are saved
	 * unchanged and survive switching back and forth.
	 */
	const toggleReCaptchaFields = ( formEl ) => {
		const checkedEl = formEl.querySelector( 'input[name="acadp_recaptcha_settings[type]"]:checked' );

		// No selection is saved as no type, which the plugin reads as v2.
		const type = ( checkedEl !== null && checkedEl.value === 'v3' ) ? 'v3' : 'v2';

		formEl.querySelectorAll( 'tr.site_key, tr.secret_key' ).forEach(( el ) => {
			el.hidden = ( type !== 'v2' );
		});

		formEl.querySelectorAll( 'tr.site_key_v3, tr.secret_key_v3, tr.score_threshold' ).forEach(( el ) => {
			el.hidden = ( type !== 'v3' );
		});
	}

	/**
	 * Called when the page has loaded.
	 */
	$(function() {

		// Section: reCAPTCHA
		const reCaptchaTypeEls = document.querySelectorAll( 'input[name="acadp_recaptcha_settings[type]"]' );

		if ( reCaptchaTypeEls.length > 0 ) {
			const formEl = reCaptchaTypeEls[0].closest( 'form' );

			if ( formEl !== null ) {
				reCaptchaTypeEls.forEach(( el ) => {
					el.addEventListener( 'change', () => {
						toggleReCaptchaFields( formEl );
					});
				});

				toggleReCaptchaFields( formEl );
			}
		}

	});

})( jQuery );
