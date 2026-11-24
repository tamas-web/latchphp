class clickConfirmAction {

	constructor(){

		document.addEventListener( 'click', this.#handle );

	}

	#handle( event ){

		const elem = event.target.matches( '[data-confirm]' )
			? event.target
			: event.target.closest( '[data-confirm]' );

		const confirmText = elem?.dataset?.confirm;

		if( confirmText ){

			if( ! confirm( confirmText ) ){

				event.preventDefault();
				event.stopPropagation();

			}

		}

	}

}

export default new clickConfirmAction();
