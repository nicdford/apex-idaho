/**
 * APEX Idaho front-end behaviour. Small, dependency-free, deferred.
 *
 * 1. Ticker groups (Group block, style "Ticker"): duplicate the content once
 *    so the scroll loops seamlessly. Every copy is aria-hidden and inert, so
 *    screen readers and keyboards meet each item once.
 * 2. Header shadow once the page has scrolled.
 * 3. Links and buttons pointing at "#print" open the print dialog.
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function hideCopy( node ) {
		node.setAttribute( 'aria-hidden', 'true' );
		node.setAttribute( 'inert', '' );
		node.querySelectorAll( 'a, button' ).forEach( function ( el ) {
			el.setAttribute( 'tabindex', '-1' );
		} );
		return node;
	}

	function initTicker( group ) {
		if ( group.querySelector( '.apex-ticker__track' ) ) {
			return;
		}

		var track = document.createElement( 'div' );
		track.className = 'apex-ticker__track';
		while ( group.firstChild ) {
			track.appendChild( group.firstChild );
		}
		group.appendChild( track );

		if ( reduceMotion ) {
			return;
		}

		var originals = Array.prototype.slice.call( track.children );
		if ( ! originals.length ) {
			return;
		}

		// Repeat until one pass is at least as wide as the strip, so short
		// content never leaves a gap, then add the duplicate half.
		var guard = 0;
		while ( track.scrollWidth < group.clientWidth && guard < 8 ) {
			originals.forEach( function ( node ) {
				track.appendChild( hideCopy( node.cloneNode( true ) ) );
			} );
			guard++;
		}
		var half = Array.prototype.slice.call( track.children );
		half.forEach( function ( node ) {
			track.appendChild( hideCopy( node.cloneNode( true ) ) );
		} );

		// Exact loop distance: from the first item to its first copy.
		var first = track.children[ 0 ];
		var mirror = track.children[ half.length ];
		var shift = mirror.offsetLeft - first.offsetLeft;
		var speed = parseFloat( group.getAttribute( 'data-speed' ) ) || 60; // px per second
		track.style.setProperty( '--apex-ticker-shift', -shift + 'px' );
		group.style.setProperty( '--apex-ticker-duration', Math.max( 12, shift / speed ) + 's' );
		track.classList.add( 'is-measured' );
	}

	function initTickers() {
		document.querySelectorAll( '.wp-block-group.is-style-apex-ticker' ).forEach( initTicker );
	}

	function initHeader() {
		var root = document.documentElement;
		var ticking = false;
		function update() {
			root.classList.toggle( 'is-scrolled', window.scrollY > 8 );
			ticking = false;
		}
		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					ticking = true;
					window.requestAnimationFrame( update );
				}
			},
			{ passive: true }
		);
		update();
	}

	function initPrintLinks() {
		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest && event.target.closest( 'a[href="#print"]' );
			if ( link ) {
				event.preventDefault();
				window.print();
			}
		} );
	}

	function init() {
		initTickers();
		initHeader();
		initPrintLinks();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
