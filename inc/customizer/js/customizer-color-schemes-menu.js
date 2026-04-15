/**
 * Color Schemes heading: ellipsis menu, presets + saved schemes (localStorage), add from current colors.
 * Preset/saved rows show each scheme’s own colors; only the selected row’s swatches update live when global colors change.
 * When the Customizer is published, the active saved scheme (if any) is written back to localStorage so the menu matches.
 * On load, if current colors match a saved or preset scheme, that row is treated as active so swatches keep updating while you edit.
 */
( function ( $, wp ) {
	'use strict';

	/** @type {{ kind: 'preset'|'saved', index: number }|null} */
	var activeScheme = null;

	var swatchRefreshPending = false;

	function cfg() {
		return typeof troveCzColorSchemes !== 'undefined' ? troveCzColorSchemes : null;
	}

	function syncSelectedSchemeStyles() {
		var $menu = $( '#customize-theme-controls .trove-cz-heading-menu' );
		$menu.find( '.trove-cz-scheme-item' ).each( function () {
			$( this ).removeClass( 'trove-cz-scheme-item--selected' ).removeAttr( 'aria-current' );
		} );
		if ( ! activeScheme ) {
			return;
		}
		var sel =
			activeScheme.kind === 'preset'
				? 'button[data-trove-scheme-kind="preset"][data-scheme-index="' + activeScheme.index + '"]'
				: 'button[data-trove-scheme-kind="saved"][data-saved-index="' + activeScheme.index + '"]';
		var $active = $menu.find( sel ).first();
		if ( $active.length ) {
			$active.addClass( 'trove-cz-scheme-item--selected' ).attr( 'aria-current', 'true' );
		}
	}

	function setActiveScheme( kind, index ) {
		if ( kind !== 'preset' && kind !== 'saved' ) {
			activeScheme = null;
			syncSelectedSchemeStyles();
			return;
		}
		if ( typeof index !== 'number' || index < 0 || isNaN( index ) ) {
			activeScheme = null;
			syncSelectedSchemeStyles();
			return;
		}
		activeScheme = { kind: kind, index: index };
		syncSelectedSchemeStyles();
	}

	function closeAllMenus() {
		$( '.trove-cz-heading-menu' ).each( function () {
			this.setAttribute( 'hidden', 'hidden' );
		} );
		$( '.trove-cz-heading-menu__toggle' ).attr( 'aria-expanded', 'false' );
	}

	function applyColorsObject( colors ) {
		if ( ! wp || ! wp.customize || ! colors ) {
			return;
		}
		Object.keys( colors ).forEach( function ( key ) {
			var s = wp.customize( key );
			if ( s ) {
				s.set( colors[ key ] );
			}
		} );
		closeAllMenus();
	}

	function applyPresetScheme( $row, index ) {
		var $json = $row.find( '.trove-cz-heading-row__schemes-json' );
		if ( ! $json.length ) {
			return;
		}
		var text = $json.text();
		if ( ! text ) {
			return;
		}
		var schemes;
		try {
			schemes = JSON.parse( text );
		} catch ( _err ) {
			return;
		}
		if ( ! schemes || ! schemes[ index ] || ! schemes[ index ].colors ) {
			return;
		}
		setActiveScheme( 'preset', index );
		applyColorsObject( schemes[ index ].colors );
	}

	function collectCurrentColors() {
		var c = cfg();
		var out = {};
		if ( ! c || ! c.keys || ! wp || ! wp.customize ) {
			return out;
		}
		c.keys.forEach( function ( key ) {
			var s = wp.customize( key );
			if ( s ) {
				out[ key ] = s();
			}
		} );
		return out;
	}

	/** Compare two color tokens for scheme equality (hex case, short hex). */
	function normalizeColorToken( v ) {
		if ( v == null ) {
			return '';
		}
		var s = String( v ).trim();
		if ( s === '' ) {
			return '';
		}
		s = s.toLowerCase();
		var m3 = s.match( /^#([0-9a-f]{3})$/ );
		if ( m3 ) {
			var h = m3[1];
			return '#' + h[ 0 ] + h[ 0 ] + h[ 1 ] + h[ 1 ] + h[ 2 ] + h[ 2 ];
		}
		return s;
	}

	function colorsMatchForKeys( schemeColors, current, keys ) {
		if ( ! schemeColors || ! current || ! keys ) {
			return false;
		}
		var i, k, a, b;
		for ( i = 0; i < keys.length; i++ ) {
			k = keys[ i ];
			a = schemeColors[ k ] != null ? String( schemeColors[ k ] ).trim() : '';
			b = current[ k ] != null ? String( current[ k ] ).trim() : '';
			if ( normalizeColorToken( a ) !== normalizeColorToken( b ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * If current theme_mods match a saved or preset scheme, select it so live swatch updates run while editing.
	 */
	function inferActiveSchemeFromCurrentColors() {
		if ( ! wp || ! wp.customize ) {
			return;
		}
		var c = cfg();
		if ( ! c || ! c.keys ) {
			return;
		}
		var keys = c.keys;
		var current = collectCurrentColors();
		var list = readStoredSchemes();
		var i, ok, j, schemes, $json, text;
		for ( i = 0; i < list.length; i++ ) {
			ok = sanitizeScheme( list[ i ], keys );
			if ( ok && colorsMatchForKeys( ok.colors, current, keys ) ) {
				setActiveScheme( 'saved', i );
				return;
			}
		}
		$( '#customize-theme-controls .trove-cz-heading-row' ).each( function () {
			$json = $( this ).find( '.trove-cz-heading-row__schemes-json' ).first();
			if ( ! $json.length ) {
				return;
			}
			text = $json.text();
			if ( ! text ) {
				return;
			}
			try {
				schemes = JSON.parse( text );
			} catch ( _err ) {
				return;
			}
			if ( ! schemes || ! schemes.length ) {
				return;
			}
			for ( j = 0; j < schemes.length; j++ ) {
				if (
					schemes[ j ].colors &&
					colorsMatchForKeys( schemes[ j ].colors, current, keys )
				) {
					setActiveScheme( 'preset', j );
					return false;
				}
			}
		} );
	}

	function paintSwatchWrap( $wrap, col, keys ) {
		if ( ! $wrap || ! $wrap.length || ! keys || ! keys.length ) {
			return;
		}
		$wrap.empty();
		keys.forEach( function ( key ) {
			var val = col[ key ];
			var bg = val != null && String( val ).trim() !== '' ? String( val ).trim() : 'transparent';
			$( '<span class="trove-cz-scheme-swatch">' )
				.css( 'background-color', bg )
				.appendTo( $wrap );
		} );
	}

	/**
	 * Repaint only the active scheme row’s swatches from current theme_mod values (live preview for the selection).
	 */
	function refreshActiveSchemeSwatches() {
		if ( ! wp || ! wp.customize ) {
			return;
		}
		var c = cfg();
		if ( ! c || ! c.keys || ! activeScheme ) {
			return;
		}
		var sel =
			activeScheme.kind === 'preset'
				? 'button[data-trove-scheme-kind="preset"][data-scheme-index="' + activeScheme.index + '"]'
				: 'button[data-trove-scheme-kind="saved"][data-saved-index="' + activeScheme.index + '"]';
		var $wrap = $( '#customize-theme-controls .trove-cz-heading-menu' )
			.find( sel )
			.first()
			.find( '.trove-cz-scheme-item__swatches' );
		if ( $wrap.length ) {
			paintSwatchWrap( $wrap, collectCurrentColors(), c.keys );
		}
	}

	function scheduleSchemeMenuSwatchRefresh() {
		if ( swatchRefreshPending ) {
			return;
		}
		swatchRefreshPending = true;
		window.requestAnimationFrame( function () {
			window.requestAnimationFrame( function () {
				swatchRefreshPending = false;
				refreshActiveSchemeSwatches();
				/* One more paint next frame — Gutenberg / Iris sometimes commit value after bind order. */
				window.requestAnimationFrame( function () {
					refreshActiveSchemeSwatches();
				} );
			} );
		} );
	}

	function bindColorSettingListeners() {
		var c = cfg();
		if ( ! c || ! c.keys || ! wp || ! wp.customize ) {
			return;
		}
		var keySet = {};
		c.keys.forEach( function ( key ) {
			keySet[ key ] = true;
		} );
		wp.customize.bind( 'change', function ( setting ) {
			if ( ! setting || typeof setting.id !== 'string' || ! keySet[ setting.id ] ) {
				return;
			}
			scheduleSchemeMenuSwatchRefresh();
		} );
	}

	function readStoredSchemes() {
		var c = cfg();
		if ( ! c || ! c.storageKey ) {
			return [];
		}
		try {
			var raw = window.localStorage.getItem( c.storageKey );
			if ( ! raw ) {
				return [];
			}
			var parsed = JSON.parse( raw );
			return Array.isArray( parsed ) ? parsed : [];
		} catch ( _err ) {
			return [];
		}
	}

	function writeStoredSchemes( list ) {
		var c = cfg();
		if ( ! c || ! c.storageKey ) {
			return;
		}
		try {
			window.localStorage.setItem( c.storageKey, JSON.stringify( list ) );
		} catch ( _err ) {
			/* ignore quota */
		}
	}

	function sanitizeScheme( scheme, allowedKeys ) {
		if ( ! scheme || typeof scheme.label !== 'string' || ! scheme.colors || typeof scheme.colors !== 'object' ) {
			return null;
		}
		var label = String( scheme.label ).trim().slice( 0, 120 );
		if ( '' === label ) {
			return null;
		}
		var colors = {};
		allowedKeys.forEach( function ( key ) {
			if ( scheme.colors[ key ] != null && String( scheme.colors[ key ] ).trim() !== '' ) {
				colors[ key ] = String( scheme.colors[ key ] ).trim();
			}
		} );
		if ( Object.keys( colors ).length === 0 ) {
			return null;
		}
		return { label: label, colors: colors };
	}

	function buildSchemeRow( scheme, savedIndex ) {
		var c = cfg();
		if ( ! c || ! c.keys ) {
			return null;
		}
		var enc = encodeURIComponent( JSON.stringify( scheme.colors ) );
		var $btn = $( '<button type="button" class="trove-cz-scheme-item trove-cz-scheme-item--saved" role="menuitem">' );
		$btn.attr( 'data-trove-scheme-kind', 'saved' );
		$btn.attr( 'data-saved-index', String( savedIndex ) );
		$btn.attr( 'data-trove-scheme-colors', enc );
		$btn.append( $( '<span class="trove-cz-scheme-item__label">' ).text( scheme.label ) );
		var $sw = $( '<span class="trove-cz-scheme-item__swatches" aria-hidden="true">' );
		c.keys.forEach( function ( key ) {
			if ( scheme.colors[ key ] ) {
				$( '<span class="trove-cz-scheme-swatch">' )
					.css( 'background-color', scheme.colors[ key ] )
					.appendTo( $sw );
			}
		} );
		$btn.append( $sw );
		var delLabel =
			c.i18n && c.i18n.deleteScheme ? c.i18n.deleteScheme : 'Remove color scheme';
		var $del = $( '<button type="button" class="trove-cz-scheme-delete" tabindex="0">' );
		$del.attr( 'data-saved-index', String( savedIndex ) );
		$del.attr( 'aria-label', delLabel );
		$del.append(
			$( '<span class="trove-cz-scheme-delete__icon" aria-hidden="true">' ).text( '\u00D7' )
		);
		var $inner = $( '<div class="trove-cz-saved-row">' );
		$inner.append( $btn ).append( $del );
		return $( '<li class="trove-cz-heading-menu__item trove-cz-heading-menu__item--saved" role="none">' ).append( $inner );
	}

	function loadSavedSchemesIntoMenus() {
		var c = cfg();
		if ( ! c || ! c.keys ) {
			return;
		}
		var list = readStoredSchemes();
		var sanitized = [];
		list.forEach( function ( s ) {
			var ok = sanitizeScheme( s, c.keys );
			if ( ok ) {
				sanitized.push( ok );
			}
		} );
		if ( sanitized.length !== list.length ) {
			writeStoredSchemes( sanitized );
		}
		$( '.trove-cz-heading-menu__list' ).each( function () {
			var $ul = $( this );
			$ul.find( '.trove-cz-heading-menu__item--saved' ).remove();
			sanitized.forEach( function ( scheme, savedIndex ) {
				var $row = buildSchemeRow( scheme, savedIndex );
				if ( $row && $row.length ) {
					$ul.append( $row );
				}
			} );
		} );
		syncSelectedSchemeStyles();
	}

	/**
	 * After Publish, keep the selected saved scheme in sync with theme_mod colors in localStorage + menu markup.
	 */
	function persistActiveSavedSchemeAfterCustomizeSave() {
		if ( ! wp || ! wp.customize ) {
			return;
		}
		if ( ! activeScheme || activeScheme.kind !== 'saved' ) {
			return;
		}
		var c = cfg();
		if ( ! c || ! c.keys ) {
			return;
		}
		var idx = activeScheme.index;
		var list = readStoredSchemes();
		if ( idx < 0 || idx >= list.length ) {
			return;
		}
		var prev = list[ idx ];
		if ( ! prev || typeof prev.label !== 'string' ) {
			return;
		}
		var candidate = {
			label: prev.label,
			colors: collectCurrentColors(),
		};
		var ok = sanitizeScheme( candidate, c.keys );
		if ( ! ok ) {
			return;
		}
		list[ idx ] = ok;
		writeStoredSchemes( list );
		loadSavedSchemesIntoMenus();
	}

	function deleteSavedSchemeAt( index ) {
		var list = readStoredSchemes();
		if ( index < 0 || index >= list.length ) {
			return;
		}
		list.splice( index, 1 );
		writeStoredSchemes( list );
		if ( activeScheme && activeScheme.kind === 'saved' ) {
			if ( activeScheme.index === index ) {
				activeScheme = null;
			} else if ( activeScheme.index > index ) {
				activeScheme = { kind: 'saved', index: activeScheme.index - 1 };
			}
		}
		loadSavedSchemesIntoMenus();
	}

	function saveAndAppendScheme( $row, scheme ) {
		var c = cfg();
		if ( ! c || ! c.keys ) {
			return;
		}
		var ok = sanitizeScheme( scheme, c.keys );
		if ( ! ok ) {
			return;
		}
		var list = readStoredSchemes();
		list.push( ok );
		writeStoredSchemes( list );
		var newIndex = list.length - 1;
		var $ul = $row.find( '.trove-cz-heading-menu__list' ).first();
		var $newRow = buildSchemeRow( ok, newIndex );
		if ( $newRow && $newRow.length ) {
			$ul.append( $newRow );
		}
		setActiveScheme( 'saved', newIndex );
		refreshActiveSchemeSwatches();
		closeAllMenus();
	}

	$( document ).on( 'click', function ( e ) {
		if ( $( e.target ).closest( '.trove-cz-heading-menu-wrap' ).length ) {
			return;
		}
		closeAllMenus();
	} );

	$( document ).on( 'click', '.trove-cz-heading-menu__toggle', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		var $btn = $( this );
		var $wrap = $btn.closest( '.trove-cz-heading-menu-wrap' );
		var $panel = $wrap.find( '.trove-cz-heading-menu' );
		var el = $panel[ 0 ];
		var wasHidden = el && el.hasAttribute( 'hidden' );
		closeAllMenus();
		if ( wasHidden ) {
			$panel.removeAttr( 'hidden' );
			$btn.attr( 'aria-expanded', 'true' );
			refreshActiveSchemeSwatches();
			syncSelectedSchemeStyles();
		}
	} );

	$( document ).on( 'click', '.trove-cz-scheme-item', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		var $item = $( this );
		var embedded = $item.attr( 'data-trove-scheme-colors' );
		var kind = $item.attr( 'data-trove-scheme-kind' );
		if ( embedded && kind === 'saved' ) {
			try {
				var colors = JSON.parse( decodeURIComponent( embedded ) );
				var sidx = parseInt( $item.attr( 'data-saved-index' ), 10 );
				if ( ! isNaN( sidx ) ) {
					setActiveScheme( 'saved', sidx );
				}
				applyColorsObject( colors );
			} catch ( _err ) {
				return;
			}
			return;
		}
		var $row = $item.closest( '.trove-cz-heading-row' );
		var idx = parseInt( $item.attr( 'data-scheme-index' ), 10 );
		if ( isNaN( idx ) ) {
			return;
		}
		applyPresetScheme( $row, idx );
	} );

	$( document ).on( 'click', '.trove-cz-scheme-add', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		var c = cfg();
		if ( ! c || ! c.i18n || ! wp || ! wp.customize ) {
			return;
		}
		var name = window.prompt( c.i18n.promptName, '' );
		if ( name === null ) {
			return;
		}
		name = String( name ).trim();
		if ( '' === name ) {
			window.alert( c.i18n.emptyName );
			return;
		}
		var scheme = {
			label: name,
			colors: collectCurrentColors(),
		};
		saveAndAppendScheme( $( this ).closest( '.trove-cz-heading-row' ), scheme );
	} );

	$( document ).on( 'click', '.trove-cz-scheme-delete', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		var c = cfg();
		var msg =
			c && c.i18n && c.i18n.confirmDelete
				? c.i18n.confirmDelete
				: 'Delete this color scheme?';
		if ( ! window.confirm( msg ) ) {
			return;
		}
		var idx = parseInt( $( this ).attr( 'data-saved-index' ), 10 );
		if ( isNaN( idx ) ) {
			return;
		}
		deleteSavedSchemeAt( idx );
	} );

	$( document ).on( 'keydown', function ( e ) {
		if ( e.keyCode === 27 ) {
			closeAllMenus();
		}
	} );

	if ( wp && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			bindColorSettingListeners();
			loadSavedSchemesIntoMenus();
			inferActiveSchemeFromCurrentColors();
			syncSelectedSchemeStyles();
			refreshActiveSchemeSwatches();
			wp.customize.bind( 'saved', function () {
				persistActiveSavedSchemeAfterCustomizeSave();
			} );
		} );
	}
}( jQuery, window.wp ) );
