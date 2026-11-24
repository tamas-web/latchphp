export default class loader {

	#actions = [ 'show', 'hide' ];
	#selector;

	constructor( selector ){

		this.#selector = selector || '.app-dependency';

		this.#registerEvents();

	}

	#registerEvents(){

		let appDependencies = document.querySelectorAll( this.#selector );

		if( appDependencies.length ){

			appDependencies.forEach( ( elem, index ) => {

				elem.addEventListener( 'change', this.#handleEvent.bind( this ), true );

				this.#handleEvent( { target: elem } );

			} );

		}

	}

	#handleEvent( event ){

		let elem = event?.target;

		if( ! elem ) return;

		let targets = elem?.dataset?.targets;

		if( targets ){

			try {

				targets = JSON.parse( targets );

				targets.forEach( condition => {

					this.#resolve( elem, condition?.action, condition?.target, condition?.equals );

				} );

			} catch( e ){

				console.log( elem, e );
				throw `App dependencies: Multiple target configuration detected, please verify configuration.`;

			}

		} else {

			let action = elem?.dataset?.action || this.#actions[0];
			let equals = elem?.dataset?.equals || '';
			let target = elem?.dataset?.target;

			this.#resolve( elem, action, target, equals );

		}

	}

	#resolve( elem, action, target, equals ){

		let targetElems = document.querySelectorAll( target );

		if( ! target ){

			console.log( elem );
			throw `App dependencies: Element doesn't have a target data attribute.`;

		} else if( ! targetElems ){

			console.log( elem );
			throw `App dependencies: Element can't find target "${target}".`;

		} else if( -1 === this.#actions.indexOf( action ) ){

			console.log( elem );
			throw `App dependencies: Element invalid action "${action}".`;

		} else {

			let _actions = [ ...this.#actions ];

			if( 'hide' == action ){

				_actions.reverse();

			}

			if( equals !== elem.value ){

				_actions.reverse();

			}

			let display = ( 'show' == _actions[0] )
				? 'inherit'
				: 'none';

			targetElems.forEach( _elem => {

				_elem.style.display = display;

			} );

		}

	}

}
