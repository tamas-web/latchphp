addEventListener( 'click', e => {

	// get element or parent element
	const element = e.target.matches( '.app-generate-password' )
		? e.target
		: e.target.closest( '.app-generate-password' )
			? e.target.closest( '.app-generate-password' )
			: null;

	// if we clicked on element with specific class name
	if( element && element?.dataset?.target ){

		let targets  = document.querySelectorAll( element?.dataset?.target );
		let password = '';
		let match    = /^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\s).*$/;

		// for each targets
		targets.forEach( target => {

			// target must be input
			if( target.nodeName == 'INPUT' ){

				// loop until password matches
				while( ! match.test( password ) ){

					// generate password
					password = new Array( 12 )
						.fill()
						.map( () => String.fromCharCode( Math.random() * 86 + 40 ) )
						.join( '' );

				}

				// set password to input
				target.value = password;

				// formvalidation.io has events on "input"
				target.dispatchEvent( new Event( 'input' ) )

			}

		} );

	}

} );
