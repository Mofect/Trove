/**
 * Blog archive infinite scroll: fetch next page HTML and append posts.
 */
(function () {
	'use strict';

	function qs( sel, root ) {
		return ( root || document ).querySelector( sel );
	}

	/** Same next URL as the page we just requested — treat as end (avoids infinite fetch). */
	function urlsPointToSameRequest( a, b ) {
		if ( ! a || ! b ) {
			return false;
		}
		var s1 = String( a ).trim();
		var s2 = String( b ).trim();
		if ( s1 === s2 ) {
			return true;
		}
		try {
			var base = window.location.href;
			return new URL( s1, base ).href === new URL( s2, base ).href;
		} catch ( err ) {
			return false;
		}
	}

	function init() {
		var loop = qs( '.trove-blog-loop[data-trove-infinite="1"]' );
		if ( ! loop ) {
			return;
		}

		var sentinel = document.getElementById( 'trove-blog-infinite-sentinel' );
		var statusEl = qs( '.trove-blog-infinite-status' );
		if ( ! sentinel ) {
			return;
		}

		var loading = false;
		var finished = false;
		var strings = window.troveInfiniteScroll || {};
		var io = null;

		function getNextUrl() {
			return ( loop.getAttribute( 'data-trove-next-url' ) || '' ).trim();
		}

		function setStatus( msg ) {
			if ( statusEl ) {
				statusEl.textContent = msg || '';
			}
		}

		function parseNextFromDoc( doc ) {
			var newLoop = doc.querySelector( '.trove-blog-loop' );
			if ( ! newLoop ) {
				return null;
			}
			var articles = newLoop.querySelectorAll( 'article.entry' );
			var nextUrl = ( newLoop.getAttribute( 'data-trove-next-url' ) || '' ).trim();
			var maxPages = parseInt( newLoop.getAttribute( 'data-trove-max-pages' ), 10 ) || 0;
			var currentPage = parseInt( newLoop.getAttribute( 'data-trove-current-page' ), 10 ) || 0;
			return {
				articles: articles,
				nextUrl: nextUrl,
				maxPages: maxPages,
				currentPage: currentPage,
			};
		}

		function clearLoadingState() {
			loading = false;
			if ( sentinel.parentNode ) {
				sentinel.classList.remove( 'is-loading' );
			}
		}

		function teardown() {
			if ( finished ) {
				return;
			}
			finished = true;
			clearLoadingState();
			setStatus( '' );
			if ( io ) {
				io.disconnect();
				io = null;
			}
			loop.removeAttribute( 'data-trove-next-url' );
			loop.setAttribute( 'data-trove-infinite', '0' );
			if ( sentinel.parentNode ) {
				sentinel.parentNode.removeChild( sentinel );
			}
			if ( statusEl && statusEl.parentNode ) {
				statusEl.parentNode.removeChild( statusEl );
			}
		}

		function endLoad() {
			clearLoadingState();
			if ( finished ) {
				return;
			}
			if ( ! getNextUrl() ) {
				teardown();
				return;
			}
			if ( io ) {
				io.observe( sentinel );
			}
		}

		function loadMore() {
			var url = getNextUrl();
			if ( finished || ! url || loading ) {
				if ( ! finished && ! url ) {
					teardown();
				}
				return;
			}
			loading = true;
			if ( io ) {
				io.unobserve( sentinel );
			}
			sentinel.classList.add( 'is-loading' );
			setStatus( strings.loading || '' );

			fetch( url, {
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
			} )
				.then( function ( res ) {
					if ( ! res.ok ) {
						throw new Error( 'HTTP ' + res.status );
					}
					return res.text();
				} )
				.then( function ( html ) {
					var doc = new DOMParser().parseFromString( html, 'text/html' );
					var parsed = parseNextFromDoc( doc );
					if ( ! parsed || ! parsed.articles.length ) {
						throw new Error( 'parse' );
					}
					for ( var i = 0; i < parsed.articles.length; i++ ) {
						loop.appendChild( document.importNode( parsed.articles[ i ], true ) );
					}

					if ( parsed.currentPage ) {
						loop.setAttribute( 'data-trove-current-page', String( parsed.currentPage ) );
					}
					if ( parsed.maxPages ) {
						loop.setAttribute( 'data-trove-max-pages', String( parsed.maxPages ) );
					}

					var doneByPage =
						parsed.maxPages > 0 &&
						parsed.currentPage > 0 &&
						parsed.currentPage >= parsed.maxPages;

					var next = parsed.nextUrl;
					var duplicateNext = next && urlsPointToSameRequest( next, url );

					if ( doneByPage || duplicateNext || ! next ) {
						loop.removeAttribute( 'data-trove-next-url' );
						teardown();
						return;
					}

					loop.setAttribute( 'data-trove-next-url', next );
					setStatus( '' );
				} )
				.catch( function () {
					setStatus( strings.error || '' );
				} )
				.then( function () {
					endLoad();
				} );
		}

		io = new IntersectionObserver(
			function ( entries ) {
				if ( finished ) {
					return;
				}
				if ( ! getNextUrl() ) {
					teardown();
					return;
				}
				var i;
				for ( i = 0; i < entries.length; i++ ) {
					if ( entries[ i ].isIntersecting ) {
						loadMore();
						break;
					}
				}
			},
			{ rootMargin: '240px 0px 240px 0px' }
		);
		io.observe( sentinel );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
