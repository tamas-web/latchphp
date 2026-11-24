import loader from './loader.js';

export default class formHandler {

	#errorClassName;
	#dynamicFormClassName;

	constructor(){

		this.#errorClassName = 'is-invalid';
		this.#dynamicFormClassName = 'dynamic-form';

		// add event listener to document
		// document.addEventListener( 'submit', this.#handleSubmit.bind( this ) );
		document.addEventListener( 'dynamicFormsSubmit', ( event ) => {

			this.#handleSubmit( {
				target: event.detail.form,
				preventDefault: () => 0,
			} );

		} );

	}

	#handleSubmit( event ){

		// the submitted form
		// we don't add the form as a private member of this object, because of asynchronous requests(for now)
		const form = event.target;

		// abort if it's not a dynamic form (ajax submission)
		if( ! form.classList.contains( this.#dynamicFormClassName ) ){

			return;

		}

		// prevent default form submission
		event.preventDefault();

		// clear errors
		this.#clearErrors( form );

		// submit the form
		this.#submit( form );

	}

	#clearErrors( form ){

		form.querySelectorAll( `.${this.#errorClassName}` ).forEach( el => {

			el.classList.remove( this.#errorClassName );

		} );

	}

	#generateFormData( form ){

		// new form data from submitted form
		let data = new FormData( form );

		let btn = this.#getSubmitButton( form );

		data.append( btn.name, btn.value );

		if( App.nonce && ! data.has( App.nonce.field ) ){

			data.append( App.nonce.field, App.nonce.value );

		}

		// return complete form data
		return data;

	}

	#getSubmitButton( form ){

		// check which element submitted the form
		var target = document.activeElement || { type: false, value: null };

		// if the submit came from a submit button
		if( 'submit' == target.type ){

			return target;

		}

		// if the submit came from other form elements
		return form.querySelector( '[type="submit"]' );

	}

	#submit( form ){

		// disable form interaction until we get response from server
		const loading = new loader( form );

		// generate form data
		const data = this.#generateFormData( form );

		// make request
		const request = new Request( form.action, {
			method: form.method,
			body: data,
		} );

		// any error parsing JSON response
		let errorParsingJSON = false;

		// initiate fetch
		fetch( request )
			// parse to json or throw error
			.then( response => {

				if( response.redirected ){

					window.location.href = response.url;

					return response;

				} else if( 200 === response.status ){

					return response.json();

				} else {

					errorParsingJSON = true;

					App.alerts.push( App.i18n.failed_to_fetch_response, App.alerts.TYPES.ERROR );

					throw new Error( App.i18n.failed_to_fetch_response );

				}

			} )
			// display form errors
			.then( response => {

				if( response.form_errors && response.form_errors.length ){

					response.form_errors.forEach( name => {

						let element = document.querySelector( `[name="${name}"]` );

						if( element ){

 							element.classList.add( this.#errorClassName );

							if( element?.nextElementSibling?.classList?.contains( 'select2' ) ){

								element.nextElementSibling.classList.add( this.#errorClassName );

							}

						}

					} );

				} else {

					this.#clearErrors( form );

				}

				return response;

			} )
			// display alerts
			.then( response => {

				if( response.alerts ){

					response.alerts.forEach( el => App.alerts.push( el.message, el.type ) );

				}

				return response;

			} )
			// maybe reset form
			.then( response => {

				if( response.form_reset ){

					form.reset();

				}

				return response;

			} )
			// emit event for other custom action upon complete
			.then( response => {

				const event = new CustomEvent( 'onResponseComplete', { detail: { response: response } } );

				// Dispatch the event.
				form.dispatchEvent( event );

				return response;

			} )
			// catch any errors
			.catch( ( error ) => {

				if( ! errorParsingJSON ){

					App.alerts.push( error, App.alerts.TYPES.ERROR );

				}

				console.error( error );

			} )
			// execute if either failed or resolved
			.finally( () => loading.destroy() );

	}

};
