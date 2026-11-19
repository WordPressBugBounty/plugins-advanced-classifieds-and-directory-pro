'use strict';

(function( $ ) {

	/**
     * Init reCaptcha.
	 */
	const initReCaptcha = () => {
		if ( document.querySelector( '#acadp-form-control-recaptcha' ) !== null ) {
			// reCAPTCHA v3 has no widget to render. The token is fetched on submit.
			if ( acadp.recaptcha_registration > 0 && acadp.recaptcha_type !== 'v3' ) {
				acadp.recaptchas['registration'] = grecaptcha.render( 'acadp-form-control-recaptcha', {
					'sitekey': acadp.recaptcha_site_key
				});
			}
		} else {
			acadp.recaptcha_registration = 0;
		}
	}

	/**
	 * Is reCAPTCHA v3 protecting this form?
	 */
	const isReCaptchaV3 = () => {
		return acadp.recaptcha_registration > 0 && acadp.recaptcha_type === 'v3';
	}

	/**
	 * reCAPTCHA v3: the submit button that submitted the form.
	 *
	 * event.submitter where the browser supports it. Otherwise the submit button
	 * that was last clicked, and failing that the form's first submit button, which
	 * is the one browsers use when Enter is pressed in a field.
	 */
	const getReCaptchaSubmitter = ( event, formEl, clickedEl ) => {
		if ( event.submitter ) {
			return event.submitter;
		}

		if ( clickedEl ) {
			return clickedEl;
		}

		return formEl.querySelector( 'input[type="submit"], button[type="submit"], button:not([type])' );
	}

	/**
	 * Add a hidden field that carries a value the browser would otherwise post.
	 */
	const addReCaptchaHiddenField = ( formEl, name, value ) => {
		const inputEl = document.createElement( 'input' );

		inputEl.type = 'hidden';
		inputEl.className = 'acadp-recaptcha-hidden-field';
		inputEl.name = name;
		inputEl.value = value;

		formEl.appendChild( inputEl );
	}

	/**
	 * reCAPTCHA v3: get a token, then submit the form.
	 *
	 * The form is submitted from code, which does not post the clicked button, so
	 * its name and value are copied into a hidden field. HTMLFormElement.prototype.submit
	 * is used because the register button is named "submit", which hides the form's
	 * own submit() method.
	 */
	const submitWithReCaptchaV3 = ( formEl, submitterEl, onFailure ) => {
		const $errorEl = $( '#acadp-form-error-recaptcha' );
		$errorEl.html( '' ).prop( 'hidden', true );

		ACADPGetReCaptchaToken( 'registration' ).then(( token ) => {
			// Fields from an earlier, failed attempt.
			formEl.querySelectorAll( '.acadp-recaptcha-hidden-field' ).forEach(( el ) => {
				el.remove();
			});

			addReCaptchaHiddenField( formEl, 'g-recaptcha-response', token );

			if ( submitterEl && submitterEl.name ) {
				addReCaptchaHiddenField( formEl, submitterEl.name, submitterEl.value );
			}

			HTMLFormElement.prototype.submit.call( formEl );
		}).catch(() => {
			$errorEl.html( acadp.i18n.recaptcha_failed ).prop( 'hidden', false );

			$( 'html, body' ).animate({
				scrollTop: $errorEl.offset().top - 50
			}, 500 );

			onFailure();
		});
	}

	/**
	 * Called when the page has loaded.
	 */
	$(function() {	

		const formEl = document.querySelector( '#acadp-register-form' );		

		if ( formEl !== null ) {
			// ReCaptcha
			if ( window.isACADPReCaptchaLoaded ) {
				initReCaptcha();			
			} else {
				document.addEventListener( 'acadp.recaptcha.loaded', initReCaptcha );
			}	

			// reCAPTCHA v3: remember the submit button that was clicked, for browsers without event.submitter.
			let clickedSubmitEl = null;

			formEl.addEventListener( 'click', ( event ) => {
				const el = event.target.closest( 'button, input' );
				if ( el !== null && el.type === 'submit' ) {
					clickedSubmitEl = el;
				}
			});

			// Form Validation
			let formSubmitted = false;
			
			ACADPLoadScript( acadp.plugin_url + 'public/assets/js/validate.js' ).then(() => {
				ACADPInitForm( '#acadp-register-form' );

				// Handle form submit validation via JS instead.
				formEl.addEventListener( 'submit', ( event ) => {
					// A click belongs to this submit only. Forget it, so a later Enter press does not reuse it.
					const clickedEl = clickedSubmitEl;
					clickedSubmitEl = null;

					if ( formSubmitted ) {
						// reCAPTCHA v3: a token is still being fetched, so stop this extra submit.
						if ( isReCaptchaV3() ) {
							event.preventDefault();
						}

						return false;
					}
			
					formSubmitted = true;
			
					// The isFormValid boolean respresents all inputs that can
					// be validated with the Constraint Validation API.
					let isFormValid = ACADPCheckValidity( formEl );
				
					// Fields that cannot be validated with the Constraint Validation API need
					// to be validated manually.
					const isPasswordValid = ACADPValidatePassword( formEl );
					if ( ! isPasswordValid ) {
						isFormValid = isPasswordValid;
					}
			
					let recaptchaResponse = null;
					if ( acadp.recaptcha_registration > 0 && acadp.recaptcha_type !== 'v3' ) {
						recaptchaResponse = grecaptcha.getResponse( acadp.recaptchas['registration'] );
			
						if ( recaptchaResponse.length == 0 ) {
							$( '#acadp-form-control-recaptcha' ).addClass( 'is-invalid' );			
							$( '#acadp-form-error-recaptcha' ).html( acadp.i18n.invalid_recaptcha ).prop( 'hidden', false );
			
							grecaptcha.reset( acadp.recaptchas['registration'] );		
							isFormValid = false;
						} else {
							$( '#acadp-form-control-recaptcha' ).removeClass( 'is-invalid' );			
							$( '#acadp-form-error-recaptcha' ).html( '' ).prop( 'hidden', true );
						}		
					}	
			
					// Prevent form submission if any of the validation checks fail.
					if ( ! isFormValid ) {
						event.preventDefault();
						formSubmitted = false;
					} else if ( isReCaptchaV3() ) {
						// reCAPTCHA v3: hold the submit until a token arrives.
						event.preventDefault();

						submitWithReCaptchaV3( formEl, getReCaptchaSubmitter( event, formEl, clickedEl ), () => {
							formSubmitted = false;
						});
					}
			
					// Set the focus to the first invalid input.
					const firstInvalidInputEl = formEl.querySelector( '.is-invalid' );										
					if ( firstInvalidInputEl !== null ) {
						$( 'html, body' ).animate({
							scrollTop: $( firstInvalidInputEl ).offset().top - 50
						}, 500 );				
					}	
				});
			});
		}

	});

})( jQuery );
