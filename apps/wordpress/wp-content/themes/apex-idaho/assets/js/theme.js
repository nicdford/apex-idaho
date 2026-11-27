/**
 * APEX Idaho front-end behaviour. Small, dependency-free, deferred.
 *
 * 1. Ticker groups (Group block, style "Ticker"): duplicate the content once
 *    so the scroll loops seamlessly. The copy is aria-hidden and inert.
 * 2. Header shadow once the page has scrolled.
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

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
				track.appendChild( node.cloneNode( true ) );
			} );
			guard++;
		}
		var half = Array.prototype.slice.call( track.children );
		half.forEach( function ( node ) {
			var copy = node.cloneNode( true );
			copy.setAttribute( 'aria-hidden', 'true' );
			copy.setAttribute( 'inert', '' );
			copy.querySelectorAll( 'a, button' ).forEach( function ( el ) {
				el.setAttribute( 'tabindex', '-1' );
			} );
			track.appendChild( copy );
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

	function init() {
		initTickers();
		initHeader();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
