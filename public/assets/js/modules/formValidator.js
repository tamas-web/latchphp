export const rules = {

	required( value ){

		return '' !== value.trim();

	},

	email( value ){

		return '' === value || /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i.test( value );

	},

	number( value ){

		return '' === value || /^-?[0-9]+(\.[0-9]+)?$/.test( value );

	},

	phone( value ){

		return '' === value || /^\+?[0-9 ()-]{8,}$/.test( value );

	},

	password( value ){

		return '' === value || /^(?=.*\d)(?=.*[A-Za-z])(?=.*[^A-Za-z0-9]).+$/.test( value );

	},

	min( value, length ){

		return '' === value || value.length >= Number( length );

	},

	max( value, length ){

		return '' === value || value.length <= Number( length );

	},

};

export const messages = {
	required: 'This field is required',
	email:    'The email address is not valid',
	number:   'Enter a valid number',
	phone:    'The phone number is not valid',
	password: 'The password must contain at least one letter, one digit and one special character',
	min:      'The entered value is too short',
	max:      'The entered value is too long',
	regexp:   'The entered value is not valid',
	matches:  'The entered values do not match',
};

export default class formValidator {

	#errorClassName = 'is-invalid';
	#validClassName = 'is-valid';

	constructor( selector = '.dynamic-form' ){

		document.querySelectorAll( selector ).forEach( form => {

			form.setAttribute( 'novalidate', 'novalidate' );

			form.addEventListener( 'submit',   this.#onSubmit );
			form.addEventListener( 'focusout', this.#onValidate );
			form.addEventListener( 'change',   this.#onValidate );
			form.addEventListener( 'input',    this.#onValidate );

		} );

	}

	#onSubmit = event => {

		event.preventDefault();

		const form = event.currentTarget;

		if( this.#validateForm( form ) ){

			document.dispatchEvent( new CustomEvent( 'dynamicFormsSubmit', {
				detail: { form: form },
				cancelable: true,
			} ) );

		} else {

			this.#focusFirstError( form );

		}

	};

	#onValidate = event => {

		if( event.target.matches?.( '[data-rule]' ) ){

			this.#validateField( event.target, event.currentTarget );

		}

	};

	#validateForm( form ){

		let valid = true;

		form.querySelectorAll( '[data-rule]' ).forEach( field => {

			if( ! this.#validateField( field, form ) ){

				valid = false;

			}

		} );

		return valid;

	}

	#focusFirstError( form ){

		const field = form.querySelector( '.' + this.#errorClassName );

		if( field ){

			field.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			field.focus( { preventScroll: true } );

		}

	}

	#valueOf( field, form ){

		if( 'checkbox' === field.type ){

			return field.checked ? ( field.value || 'on' ) : '';

		}

		if( 'radio' === field.type ){

			return form.querySelector( `input[name="${ field.name }"]:checked` ) ? 'on' : '';

		}

		return ( field.value ?? '' ).toString();

	}

	#validateField( field, form ){

		const value = this.#valueOf( field, form );

		for( const token of field.dataset.rule.split( '|' ) ){

			const [ name, arg ] = token.split( ':' );

			if( ! this.#check( name, value, arg, field, form ) ){

				this.#fail( field, this.#message( field, name ) );

				return false;

			}

		}

		this.#pass( field, value );

		return true;

	}

	#check( name, value, arg, field, form ){

		if( 'matches' === name ){

			const other = form.querySelector( `[name="${ arg }"]` );

			return ! other || value === ( other.value ?? '' ).toString();

		}

		if( 'regexp' === name ){

			const pattern = field.dataset.regexp ?? '';

			return '' === value || '' === pattern || new RegExp( pattern ).test( value );

		}

		const rule = rules[ name ];

		return rule ? rule( value, arg ) : true;

	}

	#message( field, name ){

		const key = 'msg' + name.charAt( 0 ).toUpperCase() + name.slice( 1 );

		return field.dataset[ key ] ?? field.dataset.msg ?? messages[ name ] ?? messages.regexp;

	}

	#fail( field, message ){

		field.classList.remove( this.#validClassName );
		field.classList.add( this.#errorClassName );

		const feedback = this.#feedback( field, true );

		feedback.textContent = message;
		feedback.style.display = 'block';

	}

	#pass( field, value ){

		field.classList.remove( this.#errorClassName );
		field.classList.toggle( this.#validClassName, '' !== value.trim() );

		const feedback = this.#feedback( field, false );

		if( feedback ){

			feedback.style.display = 'none';

		}

	}

	#anchor( field ){

		return field.closest( '.switch-row' ) || field.closest( '.input-group' ) || field.closest( '.form-check' ) || field;

	}

	#feedback( field, create ){

		const anchor = this.#anchor( field );
		const next   = anchor.nextElementSibling;

		if( next && next.classList.contains( 'invalid-feedback' ) ){

			return next;

		}

		if( ! create ){

			return null;

		}

		const feedback = document.createElement( 'div' );

		feedback.className = 'invalid-feedback';

		anchor.insertAdjacentElement( 'afterend', feedback );

		return feedback;

	}

}
