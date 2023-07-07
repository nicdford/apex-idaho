import React from 'react'
import { render, createRoot } from 'react-dom'
import Renderer from './components/renderer'
import './style.scss'

const isAtLeastReact18 = 18 <= parseInt( React.version.split( '.' )[0] )
const mountNode = document.getElementById( 'bb-logic-root' )

try {
	if ( isAtLeastReact18 ) {
		createRoot( mountNode ).render( <Renderer /> )
	} else {
		render( <Renderer />, mountNode )
	}
} catch ( error ) {
	console.log( 'Failed to mount bb-logic-root', error )
}
