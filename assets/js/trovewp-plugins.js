( function ( $ ) {
	'use strict';

	if ( typeof trovewpPlugins === 'undefined' ) {
		return;
	}

	var labels = trovewpPlugins.i18n || {};

	function stripHtml( message ) {
		if ( ! message ) {
			return '';
		}

		return $( '<div>' ).html( message ).text();
	}

	function setBusy( $btn, busy ) {
		if ( busy ) {
			if ( ! $btn.data( 'trovewp-label' ) ) {
				$btn.data( 'trovewp-label', $btn.text() );
			}
			$btn.prop( 'disabled', true ).addClass( 'is-busy' ).text( labels.waiting || '…' );
			return;
		}

		$btn.prop( 'disabled', false ).removeClass( 'is-busy' );

		if ( $btn.data( 'trovewp-label' ) ) {
			$btn.text( $btn.data( 'trovewp-label' ) );
		}
	}

	function showNotice( message, type ) {
		var $grid = $( '.trovewp-plugins-grid' );
		var $notice = $grid.find( '.trovewp-plugins-notice' );

		if ( ! $notice.length ) {
			$notice = $( '<div class="trovewp-plugins-notice notice" />' ).prependTo( $grid );
		}

		$notice
			.removeClass( 'notice-success notice-error' )
			.addClass( type === 'success' ? 'notice-success' : 'notice-error' )
			.empty()
			.append( $( '<p></p>' ).text( stripHtml( message ) ) )
			.show();
	}

	function parseResponse( response ) {
		if ( typeof response === 'string' ) {
			try {
				return JSON.parse( response );
			} catch ( e ) {
				return null;
			}
		}

		return response;
	}

	$( document ).on( 'click', '.trovewp-plugin-action', function ( event ) {
		event.preventDefault();

		var $btn = $( this );

		if ( $btn.prop( 'disabled' ) || $btn.hasClass( 'is-busy' ) ) {
			return;
		}

		var slug = $btn.data( 'slug' );
		var action = $btn.data( 'action' );

		if ( ! slug || ! action ) {
			return;
		}

		setBusy( $btn, true );

		var ajaxUrl =
			typeof ajaxurl !== 'undefined' ? ajaxurl : trovewpPlugins.ajaxUrl;

		$.ajax( {
			url: ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'trovewp_plugin_action',
				nonce: trovewpPlugins.nonce,
				slug: slug,
				plugin_action: action,
			},
		} )
			.done( function ( response ) {
				response = parseResponse( response );

				if ( ! response || ! response.success ) {
					showNotice(
						( response && response.data && response.data.message ) ||
							labels.error,
						'error'
					);
					setBusy( $btn, false );
					return;
				}

				var replaced = false;

				if ( response.data && response.data.html ) {
					var $card = $btn.closest( '.plugin-card' );

					if ( $card.length ) {
						$card.replaceWith( response.data.html );
						replaced = true;
					}
				}

				if ( ! replaced ) {
					setBusy( $btn, false );
				}

				if ( response.data && response.data.message ) {
					showNotice( response.data.message, 'success' );
					$( '.trovewp-plugins-notice.notice-success' )
						.delay( 2500 )
						.fadeOut( 200 );
				}
			} )
			.fail( function ( xhr ) {
				var message = labels.error;
				var parsed = parseResponse( xhr.responseText );

				if ( parsed && parsed.data && parsed.data.message ) {
					message = parsed.data.message;
				} else if (
					xhr.responseJSON &&
					xhr.responseJSON.data &&
					xhr.responseJSON.data.message
				) {
					message = xhr.responseJSON.data.message;
				}

				showNotice( message, 'error' );
				setBusy( $btn, false );
			} );
	} );
} )( jQuery );
