export default class attachmentPreview {

	constructor(){

		document.querySelectorAll( '.app__attachment-preview' ).forEach( ( elem, index ) => {

			elem.addEventListener( 'mouseenter', this.#mouseenter );

		} );

	}

	#mouseenter( event ){

		let bounds = event.target.getBoundingClientRect();

		let view = document.querySelector( '.app__attachment-view' );

		if( ! view ){

			view = new Image;

			view.className = 'app_attachment-view';
			view.style = `position:absolute;top:${bounds.top}px;left:${bounds.left}px`;

			document.body.append( view );

		}

		view.src = event.target.href;

	}

}
