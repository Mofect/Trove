/**
 * Header menu toggle for tablet + phone.
 */
( function () {
	'use strict';

	var MOBILE_BREAKPOINT = 1024;
	var PHONE_BREAKPOINT = 782;

	function isMobileHeaderViewport() {
		return window.innerWidth <= MOBILE_BREAKPOINT;
	}

	function isPhoneViewport() {
		return window.innerWidth <= PHONE_BREAKPOINT;
	}

	function getDirectChildByClass( parent, className ) {
		for ( var i = 0; i < parent.children.length; i++ ) {
			if ( parent.children[ i ].classList.contains( className ) ) {
				return parent.children[ i ];
			}
		}
		return null;
	}

	function getDirectChildLink( parent ) {
		for ( var i = 0; i < parent.children.length; i++ ) {
			if ( parent.children[ i ].tagName === 'A' ) {
				return parent.children[ i ];
			}
		}
		return null;
	}

	function assignMenuLevels( menu, level ) {
		menu.classList.add( 'site-header__menu-level' );
		menu.style.setProperty( '--trove-menu-level', String( level ) );

		var items = menu.children;
		for ( var i = 0; i < items.length; i++ ) {
			var sub = getDirectChildByClass( items[ i ], 'sub-menu' );
			if ( sub ) {
				assignMenuLevels( sub, level + 1 );
			}
		}
	}

	function resetOffcanvasMenuState( header ) {
		var nav = header.querySelector( '.site-header__nav' );
		if ( ! nav ) {
			return;
		}
		nav.style.setProperty( '--trove-menu-depth', '0' );
		var expandedLinks = nav.querySelectorAll( 'a[aria-expanded="true"]' );
		expandedLinks.forEach( function ( link ) {
			link.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	function teardownOffcanvasMenu( header ) {
		var nav = header.querySelector( '.site-header__nav' );
		if ( ! nav || nav.dataset.offcanvasReady !== '1' ) {
			return;
		}

		var submenus = nav.querySelectorAll( '.sub-menu[data-parent-item-id]' );
		submenus.forEach( function ( submenu ) {
			var parentItem = submenu.dataset.parentItemId
				? document.getElementById( submenu.dataset.parentItemId )
				: null;
			if ( parentItem ) {
				parentItem.appendChild( submenu );
			}
			delete submenu.dataset.parentItemId;
		} );

		var decoratedLinks = nav.querySelectorAll( 'a.site-header__menu-link--has-children' );
		decoratedLinks.forEach( function ( link ) {
			link.classList.remove( 'site-header__menu-link--has-children' );
			link.removeAttribute( 'aria-expanded' );
			link.removeAttribute( 'data-submenu-id' );
			var icon = link.querySelector( '.site-header__submenu-icon' );
			if ( icon ) { icon.remove(); }
		} );

		var backItems = nav.querySelectorAll( '.site-header__menu-back' );
		backItems.forEach( function ( backItem ) { backItem.remove(); } );

		var panels = nav.querySelectorAll( '.site-header__menu-level' );
		panels.forEach( function ( panel ) {
			panel.classList.remove( 'site-header__menu-level' );
			panel.style.removeProperty( '--trove-menu-level' );
			panel.removeAttribute( 'id' );
		} );

		var closeBtn = nav.querySelector( '.site-header__menu-close' );
		if ( closeBtn ) {
			closeBtn.remove();
		}

		nav.classList.remove( 'site-header__nav--offcanvas' );
		nav.style.removeProperty( '--trove-menu-depth' );
		delete nav.dataset.offcanvasReady;
	}

	function setupOffcanvasMenu( header ) {
		var nav = header.querySelector( '.site-header__nav' );
		if ( ! nav || nav.dataset.offcanvasReady === '1' || ! isPhoneViewport() ) {
			return;
		}

		var rootMenu = nav.querySelector( '.primary-menu' );
		if ( ! rootMenu ) {
			return;
		}

		nav.dataset.offcanvasReady = '1';
		nav.classList.add( 'site-header__nav--offcanvas' );
		assignMenuLevels( rootMenu, 0 );
		nav.style.setProperty( '--trove-menu-depth', '0' );
		if ( ! nav.querySelector( '.site-header__menu-close' ) ) {
			var closeBtn = document.createElement( 'button' );
			closeBtn.type = 'button';
			closeBtn.className = 'site-header__menu-close';
			closeBtn.setAttribute( 'aria-label', 'Close menu' );
			closeBtn.innerHTML =
				'<svg viewBox="0 0 1024 1024" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">' +
					'<path d="M176.662 817.173c-8.19 8.471-7.96 21.977 0.51 30.165 8.472 8.19 21.978 7.96 30.166-0.51l618.667-640c8.189-8.472 7.96-21.978-0.511-30.166-8.471-8.19-21.977-7.96-30.166 0.51l-618.666 640z" />' +
					'<path d="M795.328 846.827c8.19 8.471 21.695 8.7 30.166 0.511 8.471-8.188 8.7-21.694 0.511-30.165l-618.667-640c-8.188-8.471-21.694-8.7-30.165-0.511-8.471 8.188-8.7 21.694-0.511 30.165l618.666 640z" />' +
				'</svg>';
			nav.insertBefore( closeBtn, nav.firstChild );
		}

		var menuPanels = nav.querySelectorAll( '.site-header__menu-level' );
		menuPanels.forEach( function ( panel, index ) {
			if ( ! panel.id ) {
				panel.id = 'trove-menu-panel-' + String( index );
			}
		} );

		var menuItems = nav.querySelectorAll( 'li' );
		menuItems.forEach( function ( item ) {
			var submenu = getDirectChildByClass( item, 'sub-menu' );
			if ( ! submenu ) {
				return;
			}
			var parentMenu = item.parentElement;
			if ( parentMenu && parentMenu.classList.contains( 'site-header__menu-level' ) ) {
				submenu.dataset.parentMenuId = parentMenu.id;
			}
			if ( ! item.id ) {
				item.id = 'trove-menu-item-' + Math.random().toString( 36 ).slice( 2, 10 );
			}
			submenu.dataset.parentItemId = item.id;
			if ( ! submenu.id ) {
				submenu.id = 'trove-submenu-' + Math.random().toString( 36 ).slice( 2, 10 );
			}
			if ( submenu.parentElement !== nav ) {
				nav.appendChild( submenu );
			}

			var link = getDirectChildLink( item );
			if ( link ) {
				link.setAttribute( 'data-submenu-id', submenu.id );
				link.classList.add( 'site-header__menu-link--has-children' );
				link.setAttribute( 'aria-expanded', 'false' );
				if ( ! link.querySelector( '.site-header__submenu-icon' ) ) {
					var submenuIcon = document.createElement( 'span' );
					submenuIcon.className = 'site-header__submenu-icon';
					submenuIcon.setAttribute( 'aria-hidden', 'true' );
					submenuIcon.innerHTML =
						'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
							'<path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>' +
						'</svg>';
					link.appendChild( submenuIcon );
				}
				link.addEventListener( 'click', function ( event ) {
					if ( ! isPhoneViewport() ) {
						return;
					}
					event.preventDefault();
					var level = parseInt(
						submenu.style.getPropertyValue( '--trove-menu-level' ) || '1',
						10
					);
					nav.style.setProperty( '--trove-menu-depth', String( level ) );
					link.setAttribute( 'aria-expanded', 'true' );
				} );
			}

			if ( ! submenu.querySelector( '.site-header__menu-back' ) ) {
				var backItem = document.createElement( 'li' );
				backItem.className = 'site-header__menu-back';
				backItem.innerHTML =
					'<button type="button" class="site-header__menu-back-btn">Back</button>';
				submenu.insertBefore( backItem, submenu.firstChild );
				backItem
					.querySelector( '.site-header__menu-back-btn' )
					.addEventListener( 'click', function ( event ) {
						event.preventDefault();
						var parentMenu = submenu.dataset.parentMenuId
							? document.getElementById( submenu.dataset.parentMenuId )
							: null;
						var parentLevel = 0;
						if ( parentMenu && parentMenu.classList.contains( 'site-header__menu-level' ) ) {
							parentLevel = parseInt(
								parentMenu.style.getPropertyValue( '--trove-menu-level' ) || '0',
								10
							);
						}
						nav.style.setProperty( '--trove-menu-depth', String( parentLevel ) );
						if ( link ) {
							link.setAttribute( 'aria-expanded', 'false' );
						}
					} );
			}
		} );
	}

	function setExpanded( header, toggle, expanded ) {
		toggle.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
		header.classList.toggle( 'is-menu-open', expanded );
		if ( isPhoneViewport() ) {
			document.body.classList.toggle( 'has-header-drawer-open', expanded );
		} else {
			document.body.classList.remove( 'has-header-drawer-open' );
		}
		if ( ! expanded ) {
			resetOffcanvasMenuState( header );
		}
	}

	function initHeader( header ) {
		var toggle = header.querySelector( '.site-header__menu-toggle' );
		var nav = header.querySelector( '.site-header__nav' );
		if ( ! toggle ) {
			return;
		}
		setupOffcanvasMenu( header );

		toggle.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			if ( ! isMobileHeaderViewport() ) {
				return;
			}
			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
			setExpanded( header, toggle, ! expanded );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! isPhoneViewport() || ! header.classList.contains( 'is-menu-open' ) ) {
				return;
			}
			if (
				toggle.contains( event.target ) ||
				( nav && nav.contains( event.target ) )
			) {
				return;
			}
			setExpanded( header, toggle, false );
		} );

		header.addEventListener( 'click', function ( event ) {
			var closeBtn = event.target.closest( '.site-header__menu-close' );
			if ( ! closeBtn || ! nav || ! nav.contains( closeBtn ) ) {
				return;
			}
			event.preventDefault();
			setExpanded( header, toggle, false );
		} );

		header.addEventListener( 'keydown', function ( event ) {
			if ( event.key !== 'Escape' ) {
				return;
			}
			setExpanded( header, toggle, false );
		} );

		window.addEventListener( 'resize', function () {
			if ( isPhoneViewport() ) {
				setupOffcanvasMenu( header );
			} else {
				teardownOffcanvasMenu( header );
			}
			if ( ! isMobileHeaderViewport() ) {
				setExpanded( header, toggle, false );
				return;
			}
			if ( ! isPhoneViewport() ) {
				document.body.classList.remove( 'has-header-drawer-open' );
				resetOffcanvasMenuState( header );
			}
		} );
	}

	function init() {
		document.querySelectorAll( 'header.site-header' ).forEach( initHeader );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

