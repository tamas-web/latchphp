export default class themeToggle {

	#themeKey  = 'lp-theme';
	#accentKey = 'lp-accent';

	#closeTimer = null;

	constructor(){

		this.apply();

		document.addEventListener( 'click', this.#onClick );

		document.querySelectorAll( '[data-panel]' ).forEach( btn => {

			btn.addEventListener( 'mouseenter', this.#onRailEnter );
			btn.addEventListener( 'mouseleave', this.#scheduleClose );

		} );

		document.querySelectorAll( '.context-panel' ).forEach( panel => {

			panel.addEventListener( 'mouseenter', this.#cancelClose );
			panel.addEventListener( 'mouseleave', this.#scheduleClose );

		} );

	}

	apply(){

		const root = document.documentElement;

		root.setAttribute( 'data-bs-theme', localStorage.getItem( this.#themeKey ) || 'dark' );
		root.setAttribute( 'data-accent', localStorage.getItem( this.#accentKey ) || 'lime' );

		this.#markAccent( root.getAttribute( 'data-accent' ) );

	}

	#markAccent( accent ){

		document.querySelectorAll( '[data-accent-set]' ).forEach( opt => {

			opt.classList.toggle( 'active', opt.dataset.accentSet === accent );

		} );

	}

	#onClick = event => {

		if( event.target.closest?.( '[data-theme-toggle]' ) ){

			this.#toggleTheme();

			return;

		}

		const accentBtn = event.target.closest?.( '[data-accent-set]' );

		if( accentBtn ){

			this.#setAccent( accentBtn.dataset.accentSet );

			return;

		}

	};

	#onRailEnter = event => {

		this.#cancelClose();

		this.#openPanel( event.currentTarget );

	};

	#cancelClose = () => {

		clearTimeout( this.#closeTimer );

	};

	#scheduleClose = () => {

		this.#closeTimer = setTimeout( this.#closeOpen, 160 );

	};

	#closeOpen = () => {

		document.querySelectorAll( '.context-panel.open' ).forEach( panel => this.#closePanel( panel ) );

	};

	#toggleTheme(){

		const root = document.documentElement;
		const next = 'dark' === root.getAttribute( 'data-bs-theme' ) ? 'light' : 'dark';

		root.setAttribute( 'data-bs-theme', next );

		localStorage.setItem( this.#themeKey, next );

	}

	#setAccent( accent ){

		document.documentElement.setAttribute( 'data-accent', accent );

		localStorage.setItem( this.#accentKey, accent );

		this.#markAccent( accent );

	}

	#openPanel( btn ){

		const panel = document.getElementById( btn.dataset.panel );

		if( ! panel || panel.classList.contains( 'open' ) ){

			return;

		}

		document.querySelectorAll( '.context-panel.open' ).forEach( open => this.#closePanel( open ) );

		panel.classList.add( 'open' );
		btn.classList.add( 'active' );

	}

	#closePanel( panel ){

		panel.classList.remove( 'open' );

		const btn = document.querySelector( `[data-panel="${ panel.id }"]` );

		if( btn ){

			btn.classList.remove( 'active' );

		}

	}

}
