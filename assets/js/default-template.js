let page = 1;

async function getJSON( url, callback ) {
	var xhr = new XMLHttpRequest();

	if ( is_subscribed && -1 === url.indexOf( 'user_is_subscribed=true' ) ) {
		url += '&user_is_subscribed=true';
	}

	xhr.open( 'GET', url, true );

	if ( is_subscribed ) {
		xhr.setRequestHeader( 'x-ml-is-user-subscribed', 'true' );
	}

	xhr.responseType = 'text';
	xhr.onload       = function() {
		var status = xhr.status;
		if ( status == 200 ) {
			callback( null, xhr.response );
		} else {
			callback( status );
		}
	};

	xhr.send();
	return 1;
};

var nextOffset = pppage;
var shouldLoadMore = true;

async function getNewlyPublishedArticles( url, offset, params ) {
	return new Promise(
		function ( resolve, reject ) {
			getJSON(
				url + '/ml-api/v2/list' + ( params || '?' ) + '&dt=true&offset=' + offset, // without "?" after "v2/posts".
				function( x, response ) {
					const parser = new DOMParser();
					const dom = parser.parseFromString( response, 'text/html' );
					const postList = dom.querySelector( '.post-list-default-template' );

					if ( ! postList ) {
						return;
					}

					if ( 0 === response.length ) {
						shouldLoadMore = false;
					}
					document.querySelector( '.page__content > .post-list-default-template' ).append( ...postList.childNodes );
					nextOffset += pppage;
					resolve();
				}
			)
		}
	);
}

document.querySelector('ons-page').onInfiniteScroll = function( done ) {
	if ( ! shouldLoadMore ) {
		return;
	}

	getNewlyPublishedArticles( ml_site_url, nextOffset, window.location.search ).then( function() {
		done();
	} );
}

/** Pull to refresh list default template. */

const pullHook = document.getElementById( 'pull-to-refresh-list' );
pullHook.addEventListener( 'changestate', function ( event ) {
	let message = '';

	switch ( event.state ) {
		case 'initial':
			message = 'Pull to refresh';
			break;
		case 'preaction':
			message = 'Release';
			break;
		case 'action':
			message = 'Loading...';
			break;
	}

	pullHook.innerHTML = message;
});

pullHook.onAction = function ( done ) {
	nativeFunctions.reloadWebview();
	setTimeout( done, 3000 );
};