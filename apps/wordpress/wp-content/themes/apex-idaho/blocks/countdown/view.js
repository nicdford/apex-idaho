/**
 * apex/countdown — ticks every rendered countdown once a second.
 */
( function () {
	'use strict';

	function pad( n ) {
		return n < 10 ? '0' + n : String( n );
	}

	function start( el ) {
		var target = parseInt( el.getAttribute( 'data-apex-countdown' ), 10 );
		if ( ! target ) {
			return;
		}
		var units = {};
		[ 'd', 'h', 'm', 's' ].forEach( function ( key ) {
			units[ key ] = el.querySelector( '[data-unit="' + key + '"]' );
		} );

		function tick() {
			var left = Math.floor( ( target - Date.now() ) / 1000 );
			if ( left <= 0 ) {
				el.classList.add( 'is-live' );
				var clock = el.querySelector( '.apex-countdown__clock' );
				if ( clock ) {
					clock.textContent = el.getAttribute( 'data-apex-done-label' ) || '';
				}
				return false;
			}
			if ( units.d ) units.d.textContent = String( Math.floor( left / 86400 ) );
			if ( units.h ) units.h.textContent = pad( Math.floor( left / 3600 ) % 24 );
			if ( units.m ) units.m.textContent = pad( Math.floor( left / 60 ) % 60 );
			if ( units.s ) units.s.textContent = pad( left % 60 );
			return true;
		}

		if ( tick() ) {
			var timer = setInterval( function () {
				if ( ! tick() ) {
					clearInterval( timer );
				}
			}, 1000 );
		}
	}

	function init() {
		document.querySelectorAll( '[data-apex-countdown]' ).forEach( start );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
