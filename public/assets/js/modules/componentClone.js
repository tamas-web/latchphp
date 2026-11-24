addEventListener( 'DOMContentLoaded', () => {

	let counter = 0;

	addEventListener( 'click', e => {

		let target = e.target;

		let cloneBtn = target?.classList?.contains( 'component-clone' )
			? target
			: (
				target?.parentNode?.classList?.contains( 'component-clone' )
					? target.parentNode
					: null
			);

		let removeBtn = target?.classList?.contains( 'component-remove' )
			? target
			: (
				target?.parentNode?.classList?.contains( 'component-remove' )
					? target.parentNode
					: null
			);

		// clone row
		if( cloneBtn ){

			let target = document.querySelector( cloneBtn?.dataset?.target );

			if( target ){

				let clone = target.cloneNode( true );
				let input = clone.querySelector( 'input' );
				let button = clone.querySelector( 'button' );
				let icon = button.querySelector( 'i' );

				clone.id = `component-clone-id-${++counter}`;

				input.value = '';

				button.classList.remove( 'component-clone' );
				button.classList.remove( 'btn-primary' );
				button.classList.add( 'component-remove' );
				button.classList.add( 'btn-danger' );
				button.dataset.counter = counter;

				icon.classList.remove( 'icon-plus' );
				icon.classList.add( 'icon-minus' );

				target.parentNode.appendChild( clone );

			}

		}

		if( removeBtn ){

			document.querySelector( `#component-clone-id-${removeBtn?.dataset?.counter}` )?.remove();

		}

	} )

} );