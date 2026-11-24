export default class Toasts {

	#container;

	constructor( alerts ){

		this.#container = this.#ensureContainer();

		alerts.forEach( alert => this.#show( alert.message, alert.type ) );

	}

	#ensureContainer(){

		let el = document.querySelector( '.toast-container' );

		if( ! el ){

			el = document.createElement( 'div' );

			el.className = 'toast-container position-fixed bottom-0 end-0 p-3';
			el.style.zIndex = '1090';

			document.body.appendChild( el );

		}

		return el;

	}

	#show( message, type ){

		const meta = this.#meta( type );

		const el = document.createElement( 'div' );

		el.className = 'toast ' + meta.variant;
		el.setAttribute( 'role', 'alert' );
		el.setAttribute( 'aria-live', 'assertive' );
		el.setAttribute( 'aria-atomic', 'true' );

		el.innerHTML = `<div class="toast-header">
			<i class="bi ${ meta.icon } toast-icon me-2"></i>
			<strong class="me-auto toast-title">${ meta.label }</strong>
			<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
		<div class="toast-body">${ message }</div>`;

		this.#container.appendChild( el );

		const toast = new bootstrap.Toast( el, { delay: 5000 } );

		el.addEventListener( 'hidden.bs.toast', () => el.remove() );

		toast.show();

	}

	#meta( type ){

		switch( type ){

			case App.alerts.TYPES.SUCCESS :
				return { variant : 'toast-primary', icon : 'bi-check-circle-fill', label : 'Success' };

			case App.alerts.TYPES.WARNING :
				return { variant : 'toast-warning', icon : 'bi-exclamation-triangle-fill', label : 'Warning' };

			case App.alerts.TYPES.ERROR :
				return { variant : 'toast-danger', icon : 'bi-x-circle-fill', label : 'Error' };

			default :
				return { variant : 'toast-info', icon : 'bi-info-circle-fill', label : 'Notice' };

		}

	}

}
