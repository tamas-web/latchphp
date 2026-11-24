export default class loader {

	#loader;

	constructor( node ){

		this.#loader = document.createElement( 'div' );

		this.#loader.className = 'loader';

		this.#loader.innerHTML = `<div class="lds-grid">
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
		</div>`;

		node.append( this.#loader );

	}

	destroy(){

		this.#loader.remove();

	}

}
