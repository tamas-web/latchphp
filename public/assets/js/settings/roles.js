addEventListener( 'DOMContentLoaded', () => {

	// On the role editor, the parent/group checkbox acts as a "select all" for its
	// child action checkboxes: ticking the group grants (and locks) every action
	// under it; unticking releases them for individual selection.
	document.querySelectorAll( '.acl-group-toggle' ).forEach( toggle => {

		toggle.addEventListener( 'change', () => {

			const group = toggle.closest( '.acl-group' );

			if( ! group ){

				return;

			}

			group.querySelectorAll( '.acl-child' ).forEach( child => {

				child.disabled = toggle.checked;

				if( toggle.checked ){

					child.checked = true;

				}

			} );

		} );

	} );

} );
