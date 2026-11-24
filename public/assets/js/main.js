import Toasts from './modules/toasts.js';
import formHandler from './modules/formHandler.js';
import _clickConfirmActionInst from './modules/clickConfirmAction.js';
import formValidator from './modules/formValidator.js';
import themeToggle from './modules/themeToggle.js';

addEventListener( 'load', e => {

	// theme + accent toggles
	new themeToggle;

	// clear initial alerts
	fetch( App.url.api( 'alerts/clear' ) );

	// register event to programmatically trigger alerts from js
	document.addEventListener( 'app.alert', _ => {

		new Toasts( App.alerts.all );

		App.alerts.all = [];

	});

	document.dispatchEvent( new Event( 'app.alert' ) );

	// handle dynamic form events
	new formValidator;
	new formHandler;

	// self submiting forms
	const selfSubmitForm = document.querySelectorAll( 'form.self-submit input,form.self-submit select' );

	let timeout;

	selfSubmitForm.forEach( element => {

		element.addEventListener( 'change', e => {

			clearTimeout( timeout );

			timeout = setTimeout( () => {

				e.target.closest( 'form.self-submit' ).submit()

			}, 500 );

		} );

	});

} );
