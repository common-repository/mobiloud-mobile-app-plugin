// Body scroll lock
 ! function(e,t){if ("function" == typeof define && define.amd) {
		define( ["exports"],t );} else if ("undefined" != typeof exports) {
	 t( exports );} else {
			var o = {};t( o ),e.bodyScrollLock = o}}(this,function(exports){"use strict";function r(e){if (Array.isArray( e )) {
						for (var t = 0,o = Array( e.length );t < e.length;t++) {
								o[t] = e[t];
						}return o}return Array.from( e )}Object.defineProperty( exports,"__esModule",{value: ! 0} );var l = ! 1;if ("undefined" != typeof window) {
					var e = {get passive(){l = ! 0}};window.addEventListener( "testPassive",null,e ),window.removeEventListener( "testPassive",null,e )}var d = "undefined" != typeof window && window.navigator && window.navigator.platform && /iP(ad|hone|od)/.test( window.navigator.platform ),c = [],u = ! 1,a = -1,s = void 0,v = void 0,f = function(t){return c.some( function(e){return ! ( ! e.options.allowTouchMove || ! e.options.allowTouchMove( t ))} )},m = function(e){var t = e || window.event;return ! ! f( t.target ) || (1 < t.touches.length || (t.preventDefault && t.preventDefault(), ! 1))},o = function(){setTimeout( function(){void 0 !== v && (document.body.style.paddingRight = v,v = void 0),void 0 !== s && (document.body.style.overflow = s,s = void 0)} )};exports.disableBodyScroll = function(i,e){if (d) {
							if ( ! i) {
								return void console.error( "disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices." );
							}if (i && ! c.some( function(e){return e.targetElement === i} )) {
								var t = {targetElement:i,options:e || {}};c = [].concat( r( c ),[t] ),i.ontouchstart = function(e){1 === e.targetTouches.length && (a = e.targetTouches[0].clientY)},i.ontouchmove = function(e){var t,o,n,r;1 === e.targetTouches.length && (o = i,r = (t = e).targetTouches[0].clientY - a, ! f( t.target ) && (o && 0 === o.scrollTop && 0 < r ? m( t ) : (n = o) && n.scrollHeight - n.scrollTop <= n.clientHeight && r < 0 ? m( t ) : t.stopPropagation()))},u || (document.addEventListener( "touchmove",m,l ? {passive : ! 1} : void 0 ),u = ! 0)}} else {
						n       = e,setTimeout(
							function(){if (void 0 === v) {
									var e = ! ! n && ! 0 === n.reserveScrollBarGap,t = window.innerWidth - document.documentElement.clientWidth;e && 0 < t && (v = document.body.style.paddingRight,document.body.style.paddingRight = t + "px")}void 0 === s && (s = document.body.style.overflow,document.body.style.overflow = "hidden")}
						);var o = {targetElement :i,options :e || {}};c = [].concat( r( c ),[o] )}var n},exports.clearAllBodyScrollLocks                      = function(){d ? (c.forEach( function(e){e.targetElement.ontouchstart = null,e.targetElement.ontouchmove = null} ),u && (document.removeEventListener( "touchmove",m,l ? {passive : ! 1} : void 0 ),u = ! 1),c = [],a = -1) : (o(),c = [])},exports.enableBodyScroll = function(t){if (d) {
								if ( ! t) {
									return void console.error( "enableBodyScroll unsuccessful - targetElement must be provided when calling enableBodyScroll on IOS devices." );
								}t.ontouchstart = null,t.ontouchmove = null,c = c.filter( function(e){return e.targetElement !== t} ),u && 0 === c.length && (document.removeEventListener( "touchmove",m,l ? {passive : ! 1} : void 0 ),u = ! 1)} else {
							1 === c.length && c[0].targetElement === t ? (o(),c = []) : c = c.filter( function(e){return e.targetElement !== t} )}
						}});

			function submitOnEnter( event ) {
				if ( event.which === 13 && ! event.shiftKey ) {
					event.preventDefault(); // Prevents the addition of a new line in the text field (not needed in a lot of cases)
					submitCommentForm();
				}
			}

			function showError( message ) {
				errorMessage.innerHTML = message;
				errorToast.show();
			}

			function submitCommentForm() {
				var comment = document.getElementById( 'comment-form-text' ).value;
				var commentSubmitFormBtn = document.getElementById( 'comment-submit' );
				var commentProcessingSpinner = document.getElementById( 'comment-processing' );

				if ( comment != '' ) {
					commentSubmitFormBtn.style.display = 'none';
					commentProcessingSpinner.style.display = 'inline-block';
					textarea.setAttribute( 'disabled', true );
					postComment( comment ).then(
						function ( data ) {
							textarea.removeAttribute( 'disabled' );
							commentProcessingSpinner.style.display = 'none';
							commentSubmitFormBtn.style.display = 'inline-block';

							if ( data.id ) {
								switch ( data.status ) {
									case "approved":
										textarea.value = '';
										var list;
										if ( commentReplyTo !== '' ) {
											list = document.querySelector( '#comment-list .comment.comment_id_' + commentReplyTo + ' .list-item__center' );
										} else {
											list = document.querySelector( '#comment-list' );
										}
										var avatar     = ons.createElement( '<img src="' + data.avatar + '" class="avatar avatar-50 photo" />' );
										var body       = ons.createElement( '<div class="comment_body"><strong>' + data.author_name + '</strong> ' + data.content + ' <div class="comment_meta">' + ml_comments.just_now + '</div></div>' );
										var newComment = ons.createElement( '<ons-list-item class="comment ml_comment"></ons-list-item>' );
										if ( ! document.querySelector( 'body' ).classList.contains( 'no-avatars' ) ) {
											newComment.append( avatar );
										}
										newComment.append( body );
										list.appendChild( newComment ).scrollIntoView( false );
										var cmh4 = document.querySelector( '#comment-list > h4' )

										if ( null !== cmh4 ) {
											cmh4.remove();
										}

										break;
									case "unapproved":
										textarea.value = '';
										showError( ml_comments.awaiting );
										break;
									case "spam":
										textarea.value = '';
										showError( ml_comments.spam );
										break;
									case "forbidden":
										textarea.value = '';
										showError( ml_comments.forbidden );
										break;
								}

								textarea.blur();
							} else {
								showError( data.message );
							}
						}
					).catch(
						function( err ) {
							console.log( err.message );
						}
					);
				}
			}

			function postComment( comment ) {
				return new Promise(
					function ( resolve, reject ) {
						var xhr = new XMLHttpRequest();
						xhr.open( 'POST', commentsEndpoint, true );
						xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
						var mlValidation = document.getElementById( 'mlValidationHeader' );
						if ( mlValidation ) {
							xhr.setRequestHeader( 'X-ML-VALIDATION', mlValidation.value );
						}
						xhr.onload = function() {
							if ( xhr.status >= 500 && xhr.status < 600 ) {
								showError( `${ xhr.status }: ${ xhr.statusText }` );
								reject();
							}
							if ( 403 === xhr.status) {
								resolve( { message: ml_comments.forbidden } );
							} else {
								var resp = JSON.parse( xhr.responseText );
								if ( resp.message === 'notloggedin' ) {
									nativeFunctions.handleButton( 'login', null, null );
									reject( new Error( 'Not logged in' ) );
								} else {
									resolve( resp );
								}
							}
						};

						commentData = 'author_email=' + document.getElementById( 'ml-commenter-email' ).value +
						'&author_name=' + document.getElementById( 'ml-commenter-name' ).value +
						'&content=' + comment +
						'&post=' + document.querySelector( '#comment-list' ).getAttribute( 'data-post-id' ) +
						'&action=process_comments&do=insert' +
						'&nonce=' + document.getElementById( 'restNonce' ).value;

						if ( commentReplyTo !== '' ) {
							commentData += '&parent=' + commentReplyTo;
						} else {
							commentData += '&parent=0';
						}

						xhr.send( commentData );
					}
				);
			}

			function checkUserDetails() {
				if ( textarea.classList.contains( 'has-details' ) ) {
					var form = document.getElementById( 'ml-comment-form' );
					if ( form.classList.contains( 'logged-in' ) ) {
						saveUserDetails();
					}
				} else {
					textarea.blur();
					var modal = document.querySelector( 'ons-modal' );
					bodyScrollLock.disableBodyScroll( modal );
				}
				document.querySelector( '#comment-submit' ).classList.add( 'active' );
			}

			function validateEmail( email ) {
				var se = /^[\w\.\-_]{1,}@[\w\.\-]{6,}/
				return se.test( email );
			}

			function saveUserDetails() {
				var email         = document.getElementById( "commenter-email" ).value;
				var name          = document.getElementById( "commenter-name" ).value;
				var avatar        = document.getElementById( "form-avatar" );
				var avatarSpinner = document.getElementById( "form-avatar-processing" );

				if ( '' === name ) {
					showError( ml_comments.user_login );
				} else if ( ! validateEmail( email )) {
					showError( ml_comments.invalid_email );
				} else {
					try {
						var mlCommenter = name + '|' + email;
						nativeFunctions.setMLCommenter( mlCommenter );
					} catch( e ) {
						console.log( e );
					}

					if ( ! document.querySelector( 'body' ).classList.contains( 'no-avatars' ) ) {
						avatar.style.opacity = 0.35;
						avatarSpinner.style.opacity = 1;

						getAvatar( '', email )
						.then(
							function ( avSrc ) {
								document.getElementById( "form-avatar" ).src = avSrc;
								avatar.style.opacity = 1;
								avatarSpinner.style.opacity = 0;

								if ( window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT === true ) {
									showError( window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT_MESSAGE );
								}
							}
						);
					}

					document.getElementById( "comment-form-text" ).classList.add( 'has-details' );
					closeModal();
				}
			}

			async function process(email, callback) {
				var xhr = new XMLHttpRequest();
				xhr.open( 'POST', commentsEndpoint, true );
				xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

				xhr.onload = function() {
					var status = xhr.status;
					console.log(xhr)
					if ( xhr.status >= 500 && xhr.status < 600 ) {
						showError( `${ xhr.status }: ${ xhr.statusText }` );
						return;
					}
					if ( status == 200 ) {
						callback( null, xhr.response );
					} else {
						callback( status );
					}
				};

				var avatarData = 'action=process_comments&do=avatar&email=' + email + '&nonce=' + document.getElementById( 'restNonce' ).value;

				xhr.send( avatarData );
				return 1;
			}

			async function getAvatar(url, email) {
				return new Promise(
					function (resolve, reject) {
						process(
							email,
							function( err, data ) {
								if ( err != null ) {
									// console.log( 'Something went wrong: ' + err );
									resolve( 0 );
								} else {
									// return avatar url
									resolve( data );
								}
							}
						)
					}
				);
			}

			function closeModal() {
				bodyScrollLock.clearAllBodyScrollLocks();
				modal.hide();
			}

			function replyNow( cID ) {
				document.querySelector( '#comment-form-text' ).focus();
				commentReplyTo = cID;
			}

			var textarea     = document.getElementById( "comment-form-text" );
			var errorToast   = document.getElementById( "errorToast" );
			var errorMessage = document.getElementById( "err-message" );
			var modal        = document.getElementById( "infoModal" );
			textarea.addEventListener( "keypress", submitOnEnter );
			document.addEventListener( "DOMContentLoaded", checkUserDetails );
			textarea.addEventListener(
				"blur", function() {
					document.querySelector( '#comment-submit' ).classList.remove( 'active' );
				}
			);
			var avatarElem = document.querySelector( '#form-avatar:not(.logged-in)' );
			if ( avatarElem ) {
				avatarElem.addEventListener(
					"click", function() {
						window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT = false;
						modal.show();
						bodyScrollLock.disableBodyScroll( modal );
						document.getElementById( "commenter-name" ).value  = document.getElementById( 'ml-commenter-name' ).value;
						document.getElementById( "commenter-email" ).value = document.getElementById( 'ml-commenter-email' ).value;
					}
				);
			}

			document.addEventListener(
				'DOMContentLoaded', function() {
					if ( textarea.classList.contains( 'has-details' ) ) {
						textarea.focus();
					}
				}
			);

			textarea.addEventListener( 'focus', function() {
				if ( ! window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT ) {
					modal.show();
				}
			} );

			window.ml_comments    = window.ml_comments || {};
			ml_comments.awaiting  = ml_comments.awaiting || 'Your comment is awaiting moderation.';
			ml_comments.spam      = ml_comments.spam || 'Sorry, your comment was marked as spam!';
			ml_comments.forbidden = ml_comments.forbidden || 'Sorry, you are not allowed to do that.';
			ml_comments.just_now  = ml_comments.just_now || 'Just now';
