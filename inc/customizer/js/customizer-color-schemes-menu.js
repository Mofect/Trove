/**
 * Color Schemes heading: presets + saved schemes (localStorage), native DOM APIs.
 */
( function ( wp ) {
	'use strict';

	var activeScheme = null;
	var swatchRefreshPending = false;

	function cfg() {
		return typeof troveCzColorSchemes !== 'undefined' ? troveCzColorSchemes : null;
	}

	function closeAllMenus() {
		document.querySelectorAll( '.trove-cz-heading-menu' ).forEach( function ( menu ) {
			menu.setAttribute( 'hidden', 'hidden' );
		} );
		document.querySelectorAll( '.trove-cz-heading-menu__toggle' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-expanded', 'false' );
		} );
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

	function syncSelectedSchemeStyles() {
		var selector;
		var active;
		document.querySelectorAll( '#customize-theme-controls .trove-cz-scheme-item' ).forEach( function ( item ) {
			item.classList.remove( 'trove-cz-scheme-item--selected' );
			item.removeAttribute( 'aria-current' );
		} );
		if ( ! activeScheme ) {
			return;
		}
		selector = activeScheme.kind === 'preset'
			? 'button[data-trove-scheme-kind="preset"][data-scheme-index="' + activeScheme.index + '"]'
			: 'button[data-trove-scheme-kind="saved"][data-saved-index="' + activeScheme.index + '"]';
		active = document.querySelector( '#customize-theme-controls .trove-cz-heading-menu ' + selector );
		if ( active ) {
			active.classList.add( 'trove-cz-scheme-item--selected' );
			active.setAttribute( 'aria-current', 'true' );
		}
	}

	function setActiveScheme( kind, index ) {
		if ( ( kind !== 'preset' && kind !== 'saved' ) || typeof index !== 'number' || index < 0 || isNaN( index ) ) {
			activeScheme = null;
			syncSelectedSchemeStyles();
			return;
		}
		activeScheme = { kind: kind, index: index };
		syncSelectedSchemeStyles();
	}

	function readStoredSchemes() {
		var c = cfg();
		var raw;
		if ( ! c || ! c.storageKey ) {
			return [];
		}
		try {
			raw = window.localStorage.getItem( c.storageKey );
			return raw ? ( JSON.parse( raw ) || [] ) : [];
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
		} catch ( _err ) {}
	}

	function sanitizeScheme( scheme, allowedKeys ) {
		var label;
		var colors = {};
		if ( ! scheme || typeof scheme.label !== 'string' || ! scheme.colors || typeof scheme.colors !== 'object' ) {
			return null;
		}
		label = String( scheme.label ).trim().slice( 0, 120 );
		if ( ! label ) {
			return null;
		}
		allowedKeys.forEach( function ( key ) {
			var v = scheme.colors[ key ];
			if ( v != null && String( v ).trim() !== '' ) {
				colors[ key ] = String( v ).trim();
			}
		} );
		return Object.keys( colors ).length ? { label: label, colors: colors } : null;
	}

	function buildSwatches( wrap, colors, keys ) {
		wrap.innerHTML = '';
		keys.forEach( function ( key ) {
			var sw = document.createElement( 'span' );
			sw.className = 'trove-cz-scheme-swatch';
			sw.style.backgroundColor = colors[ key ] && String( colors[ key ] ).trim() !== '' ? String( colors[ key ] ) : 'transparent';
			wrap.appendChild( sw );
		} );
	}

	function buildSavedRow( scheme, savedIndex ) {
		var c = cfg();
		var li = document.createElement( 'li' );
		var row = document.createElement( 'div' );
		var btn = document.createElement( 'button' );
		var label = document.createElement( 'span' );
		var sw = document.createElement( 'span' );
		var del = document.createElement( 'button' );
		var icon = document.createElement( 'span' );
		li.className = 'trove-cz-heading-menu__item trove-cz-heading-menu__item--saved';
		li.setAttribute( 'role', 'none' );
		row.className = 'trove-cz-saved-row';
		btn.type = 'button';
		btn.className = 'trove-cz-scheme-item trove-cz-scheme-item--saved';
		btn.setAttribute( 'role', 'menuitem' );
		btn.setAttribute( 'data-trove-scheme-kind', 'saved' );
		btn.setAttribute( 'data-saved-index', String( savedIndex ) );
		btn.setAttribute( 'data-trove-scheme-colors', encodeURIComponent( JSON.stringify( scheme.colors ) ) );
		label.className = 'trove-cz-scheme-item__label';
		label.textContent = scheme.label;
		sw.className = 'trove-cz-scheme-item__swatches';
		sw.setAttribute( 'aria-hidden', 'true' );
		buildSwatches( sw, scheme.colors, c.keys || [] );
		btn.appendChild( label );
		btn.appendChild( sw );
		del.type = 'button';
		del.className = 'trove-cz-scheme-delete';
		del.setAttribute( 'tabindex', '0' );
		del.setAttribute( 'data-saved-index', String( savedIndex ) );
		del.setAttribute( 'aria-label', ( c.i18n && c.i18n.deleteScheme ) || 'Remove color scheme' );
		icon.className = 'trove-cz-scheme-delete__icon';
		icon.setAttribute( 'aria-hidden', 'true' );
		icon.textContent = '×';
		del.appendChild( icon );
		row.appendChild( btn );
		row.appendChild( del );
		li.appendChild( row );
		return li;
	}

	function loadSavedSchemesIntoMenus() {
		var c = cfg();
		var list;
		var cleaned = [];
		if ( ! c || ! c.keys ) {
			return;
		}
		list = readStoredSchemes();
		list.forEach( function ( item ) {
			var ok = sanitizeScheme( item, c.keys );
			if ( ok ) {
				cleaned.push( ok );
			}
		} );
		if ( cleaned.length !== list.length ) {
			writeStoredSchemes( cleaned );
		}
		document.querySelectorAll( '.trove-cz-heading-menu__list' ).forEach( function ( ul ) {
			ul.querySelectorAll( '.trove-cz-heading-menu__item--saved' ).forEach( function ( node ) {
				node.remove();
			} );
			cleaned.forEach( function ( scheme, idx ) {
				ul.appendChild( buildSavedRow( scheme, idx ) );
			} );
		} );
		syncSelectedSchemeStyles();
	}

	function refreshActiveSchemeSwatches() {
		var c = cfg();
		var selector;
		var item;
		var wrap;
		if ( ! c || ! c.keys || ! activeScheme ) {
			return;
		}
		selector = activeScheme.kind === 'preset'
			? 'button[data-trove-scheme-kind="preset"][data-scheme-index="' + activeScheme.index + '"]'
			: 'button[data-trove-scheme-kind="saved"][data-saved-index="' + activeScheme.index + '"]';
		item = document.querySelector( '#customize-theme-controls .trove-cz-heading-menu ' + selector );
		if ( ! item ) {
			return;
		}
		wrap = item.querySelector( '.trove-cz-scheme-item__swatches' );
		if ( wrap ) {
			buildSwatches( wrap, collectCurrentColors(), c.keys );
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
				window.requestAnimationFrame( refreshActiveSchemeSwatches );
			} );
		} );
	}

	function bindColorSettingListeners() {
		var c = cfg();
		var keySet = {};
		if ( ! c || ! c.keys || ! wp || ! wp.customize ) {
			return;
		}
		c.keys.forEach( function ( key ) { keySet[ key ] = true; } );
		wp.customize.bind( 'change', function ( setting ) {
			if ( setting && typeof setting.id === 'string' && keySet[ setting.id ] ) {
				scheduleSchemeMenuSwatchRefresh();
			}
		} );
	}

	function applyPresetFromButton( button ) {
		var row = button.closest( '.trove-cz-heading-row' );
		var json = row ? row.querySelector( '.trove-cz-heading-row__schemes-json' ) : null;
		var idx = parseInt( button.getAttribute( 'data-scheme-index' ), 10 );
		var schemes;
		if ( ! json || isNaN( idx ) ) {
			return;
		}
		try {
			schemes = JSON.parse( json.textContent || '' );
		} catch ( _err ) {
			return;
		}
		if ( schemes && schemes[ idx ] && schemes[ idx ].colors ) {
			setActiveScheme( 'preset', idx );
			applyColorsObject( schemes[ idx ].colors );
		}
	}

	function persistActiveSavedSchemeAfterCustomizeSave() {
		var c = cfg();
		var list;
		var idx;
		var prev;
		var next;
		if ( ! activeScheme || activeScheme.kind !== 'saved' || ! c || ! c.keys ) {
			return;
		}
		list = readStoredSchemes();
		idx = activeScheme.index;
		if ( idx < 0 || idx >= list.length ) {
			return;
		}
		prev = list[ idx ];
		next = sanitizeScheme( { label: prev.label, colors: collectCurrentColors() }, c.keys );
		if ( ! next ) {
			return;
		}
		list[ idx ] = next;
		writeStoredSchemes( list );
		loadSavedSchemesIntoMenus();
	}

	document.addEventListener( 'click', function ( e ) {
		var toggle = e.target.closest( '.trove-cz-heading-menu__toggle' );
		var schemeItem = e.target.closest( '.trove-cz-scheme-item' );
		var addBtn = e.target.closest( '.trove-cz-scheme-add' );
		var delBtn = e.target.closest( '.trove-cz-scheme-delete' );
		var c;
		var name;
		var scheme;
		var idx;
		var list;
		if ( toggle ) {
			var wrap = toggle.closest( '.trove-cz-heading-menu-wrap' );
			var panel = wrap ? wrap.querySelector( '.trove-cz-heading-menu' ) : null;
			var wasHidden = panel ? panel.hasAttribute( 'hidden' ) : false;
			e.preventDefault();
			e.stopPropagation();
			closeAllMenus();
			if ( panel && wasHidden ) {
				panel.removeAttribute( 'hidden' );
				toggle.setAttribute( 'aria-expanded', 'true' );
				refreshActiveSchemeSwatches();
				syncSelectedSchemeStyles();
			}
			return;
		}

		if ( schemeItem ) {
			var embedded = schemeItem.getAttribute( 'data-trove-scheme-colors' );
			var kind = schemeItem.getAttribute( 'data-trove-scheme-kind' );
			e.preventDefault();
			e.stopPropagation();
			if ( embedded && kind === 'saved' ) {
				try {
					idx = parseInt( schemeItem.getAttribute( 'data-saved-index' ), 10 );
					setActiveScheme( 'saved', idx );
					applyColorsObject( JSON.parse( decodeURIComponent( embedded ) ) );
				} catch ( _err ) {}
				return;
			}
			applyPresetFromButton( schemeItem );
			return;
		}

		if ( addBtn ) {
			e.preventDefault();
			e.stopPropagation();
			c = cfg();
			if ( ! c || ! c.i18n || ! wp || ! wp.customize ) {
				return;
			}
			name = window.prompt( c.i18n.promptName, '' );
			if ( name === null ) {
				return;
			}
			name = String( name ).trim();
			if ( ! name ) {
				window.alert( c.i18n.emptyName );
				return;
			}
			scheme = sanitizeScheme( { label: name, colors: collectCurrentColors() }, c.keys || [] );
			if ( ! scheme ) {
				return;
			}
			list = readStoredSchemes();
			list.push( scheme );
			writeStoredSchemes( list );
			loadSavedSchemesIntoMenus();
			setActiveScheme( 'saved', list.length - 1 );
			refreshActiveSchemeSwatches();
			closeAllMenus();
			return;
		}

		if ( delBtn ) {
			e.preventDefault();
			e.stopPropagation();
			c = cfg();
			if ( ! window.confirm( ( c && c.i18n && c.i18n.confirmDelete ) || 'Delete this color scheme?' ) ) {
				return;
			}
			idx = parseInt( delBtn.getAttribute( 'data-saved-index' ), 10 );
			list = readStoredSchemes();
			if ( isNaN( idx ) || idx < 0 || idx >= list.length ) {
				return;
			}
			list.splice( idx, 1 );
			writeStoredSchemes( list );
			if ( activeScheme && activeScheme.kind === 'saved' ) {
				if ( activeScheme.index === idx ) {
					activeScheme = null;
				} else if ( activeScheme.index > idx ) {
					activeScheme.index -= 1;
				}
			}
			loadSavedSchemesIntoMenus();
			return;
		}

		if ( ! e.target.closest( '.trove-cz-heading-menu-wrap' ) ) {
			closeAllMenus();
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			closeAllMenus();
		}
	} );

	if ( wp && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			bindColorSettingListeners();
			loadSavedSchemesIntoMenus();
			syncSelectedSchemeStyles();
			refreshActiveSchemeSwatches();
			wp.customize.bind( 'saved', persistActiveSavedSchemeAfterCustomizeSave );
		} );
	}
}( window.wp ) );
