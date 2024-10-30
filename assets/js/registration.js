/* @version 4.2.5 */
(function( $ ) {
	$(document).on('ready', function() {
		var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
		$('body').addClass( iOS ? 'is-ios' : 'is-android' );
		var $form     = $( '#reg_form' );
		var $errors   = $( '#reg_errors' );
		var $nonce    = $( '#reg_nonce' );
		var $username = $( '#reg_user' );
		var $password = $( '#reg_pass' );
		var $terms    = $( '#reg_terms' );
		var $receipt  = $( '#reg_receipt' );

		var username_is_invalid = function() {
			var value = $username.val();
			if ( ! value.length ) {
				$username.addClass( 'error' );
				return ml_registration.username_empty;
			}
			$username.removeClass( 'error' );
			return '';
		}
		var password_is_invalid = function() {
			var value = $password.val();
			if ( ! value.length ) {
				$password.addClass( 'error' );
				return ml_registration.password_empty;
			}
			$password.removeClass( 'error' );
			return '';
		}
		var terms_is_invalid = function() {
			var value = $terms.is(':checked');
			if ( ! value ) {
				$terms.addClass( 'error' );
				return ml_registration.terms_empty;
			}
			$terms.removeClass( 'error' );
			return '';
		}
		var form_validate = function() {
			$errors.empty();
			hide_errors();
			var list = [ username_is_invalid(), password_is_invalid(), terms_is_invalid() ].filter(e=>''!==e);
			if (list.length) {
				show_errors(list);
				return false;
			} else {
				hide_errors();
				return true;
			}
		}
		var show_errors = function( errors ) {
			if ( 'object' !== typeof errors ) {
				errors = [ errors ];
			}
			errors.map(e=>$errors.append( $('<p>').html( e ) ));
			$('body').addClass('has-errors');
			$('html, body').stop();
			$errors.show('swing',function(){
				$('html, body').animate({
				'scrollTop': $errors.offset().top
				}, 'fast');

			});
		}
		var hide_errors = function() {
			$('body').removeClass('has-errors');
			$errors.hide();
		}

		var show_loader = function() {
			$('body').addClass('is-loading');
		}
		var hide_loader = function() {
			$('body').removeClass('is-loading');
		}
		var show_success = function( data ) {
			$('#reg_success').html(data.message || 'Registered.').show();
			$('#reg_content').hide( 'slow' );
			if ( 'object' === typeof nativeFunctions ) {
				nativeFunctions.handleAction( 'login', data['cookies'] || '', data['token'] || '' );
				// nativeFunctions.handleButton( 'close_screen', null, null );
			} else {
				console.log( 'Error: nativeFunctions undefined.' );
			}
		}
		var send_request = function() {
			console.log( 'send request');
			var params = {
				n: $nonce.val(),
				u: $username.val(),
				p: $password.val(),
				t: $terms.val(),
				r: $receipt.val(),
			};
			$.ajax({
				url: ml_registration.endpoint,
				async: true,
				cache: false,
				dataType: 'json',
				timeout: 25000,
				method: 'post',
				data: params,
				beforeSend: function( jqXHR, settings ) {
					show_loader();
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log('error', jqXHR, textStatus, errorThrown);
					show_errors( ml_registration.unexpected_error );
				},
				success: function( data, textStatus, jqXHR ) {
					console.log('success', data, textStatus, jqXHR);
					if ( data && data.success ) {
						console.log('OK');
						show_success( data.data );
					} else {
						var errors = data.data && data.data.errors && 'object' === typeof( data.data.errors ) ? data.data.errors : 'Error';
						show_errors(errors);
					}
				},
				complete: function( jqXHR, textStatus ) {
					hide_loader();
				}
			});
		}


		$form.on('submit', function() {
			console.log('on submit');
			if ( form_validate() ) {
				send_request();
			}
			return false;
		})

		console.log('initialized');


	})
})(jQuery);