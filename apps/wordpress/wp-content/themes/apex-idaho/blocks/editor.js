/**
 * Editor UI for the theme's dynamic blocks.
 *
 * Plain ES5 against the wp.* globals so it ships without a build step. Each
 * block previews through ServerSideRender, i.e. the same render.php visitors
 * get. Attributes, titles and icons come from each block.json on the server.
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var ToggleControl = wp.components.ToggleControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	function setter( props, key ) {
		return function ( value ) {
			var next = {};
			next[ key ] = value;
			props.setAttributes( next );
		};
	}

	function preview( name, props ) {
		return el(
			'div',
			useBlockProps(),
			el( Disabled, null, el( ServerSideRender, { block: name, attributes: props.attributes } ) )
		);
	}

	function define( name, controls ) {
		registerBlockType( name, {
			edit: function ( props ) {
				return el(
					Fragment,
					null,
					el( InspectorControls, null, el( PanelBody, { title: __( 'Settings', 'apex-idaho' ) }, controls( props ) ) ),
					preview( name, props )
				);
			},
			save: function () {
				return null;
			},
		} );
	}

	define( 'apex/upcoming-events', function ( props ) {
		var a = props.attributes;
		return [
			el( SelectControl, {
				key: 'layout',
				label: __( 'Layout', 'apex-idaho' ),
				value: a.layout,
				options: [
					{ label: __( 'Cards', 'apex-idaho' ), value: 'cards' },
					{ label: __( 'Timing sheet (list)', 'apex-idaho' ), value: 'list' },
					{ label: __( 'Ticker (inline, for the header strip)', 'apex-idaho' ), value: 'ticker' },
				],
				onChange: setter( props, 'layout' ),
			} ),
			el( RangeControl, {
				key: 'count',
				label: __( 'Number of events', 'apex-idaho' ),
				value: a.count,
				min: 1,
				max: 12,
				onChange: setter( props, 'count' ),
			} ),
			el( TextControl, {
				key: 'category',
				label: __( 'Event category slug (optional)', 'apex-idaho' ),
				value: a.category,
				onChange: setter( props, 'category' ),
			} ),
			a.layout === 'cards' &&
				el( ToggleControl, {
					key: 'image',
					label: __( 'Show featured images', 'apex-idaho' ),
					checked: a.showImage,
					onChange: setter( props, 'showImage' ),
				} ),
			a.layout === 'cards' &&
				el( TextControl, {
					key: 'cta',
					label: __( 'Card button text', 'apex-idaho' ),
					value: a.ctaLabel,
					onChange: setter( props, 'ctaLabel' ),
				} ),
			el( TextareaControl, {
				key: 'empty',
				label: __( 'Message when no events are scheduled', 'apex-idaho' ),
				value: a.emptyMessage,
				onChange: setter( props, 'emptyMessage' ),
			} ),
		];
	} );

	define( 'apex/sponsors', function ( props ) {
		var a = props.attributes;
		return [
			el( SelectControl, {
				key: 'layout',
				label: __( 'Layout', 'apex-idaho' ),
				value: a.layout,
				options: [
					{ label: __( 'Scrolling board', 'apex-idaho' ), value: 'marquee' },
					{ label: __( 'Grid', 'apex-idaho' ), value: 'grid' },
				],
				onChange: setter( props, 'layout' ),
			} ),
			a.layout === 'grid' &&
				el( ToggleControl, {
					key: 'names',
					label: __( 'Show sponsor names', 'apex-idaho' ),
					checked: a.showNames,
					onChange: setter( props, 'showNames' ),
				} ),
			el( 'p', { key: 'help', className: 'components-base-control__help' }, __( 'Logos, links and colour treatment are edited per sponsor under Sponsors in the admin menu. Order follows each sponsor\'s Order field.', 'apex-idaho' ) ),
		];
	} );

	define( 'apex/countdown', function ( props ) {
		var a = props.attributes;
		return [
			el( SelectControl, {
				key: 'source',
				label: __( 'Count down to', 'apex-idaho' ),
				value: a.source,
				options: [
					{ label: __( 'Next event on the calendar', 'apex-idaho' ), value: 'next-event' },
					{ label: __( 'A specific date and time', 'apex-idaho' ), value: 'date' },
				],
				onChange: setter( props, 'source' ),
			} ),
			a.source === 'date' &&
				el( TextControl, {
					key: 'date',
					type: 'datetime-local',
					label: __( 'Date and time (site timezone)', 'apex-idaho' ),
					value: a.date,
					onChange: setter( props, 'date' ),
				} ),
			el( TextControl, {
				key: 'label',
				label: __( 'Label', 'apex-idaho' ),
				value: a.label,
				onChange: setter( props, 'label' ),
			} ),
			el( TextControl, {
				key: 'title',
				label: __( 'Title (defaults to the event name)', 'apex-idaho' ),
				value: a.title,
				onChange: setter( props, 'title' ),
			} ),
			el( TextControl, {
				key: 'url',
				type: 'url',
				label: __( 'Link (defaults to the event page)', 'apex-idaho' ),
				value: a.url,
				onChange: setter( props, 'url' ),
			} ),
		];
	} );

	define( 'apex/partner-photo', function ( props ) {
		var a = props.attributes;
		return [
			el( TextControl, {
				key: 'slug',
				label: __( 'Media Partner slug', 'apex-idaho' ),
				help: __( 'The post slug, e.g. jordan-van-diest. The featured image and focal point come from that post.', 'apex-idaho' ),
				value: a.slug,
				onChange: setter( props, 'slug' ),
			} ),
			el( TextControl, {
				key: 'alt',
				label: __( 'Alt text', 'apex-idaho' ),
				value: a.alt,
				onChange: setter( props, 'alt' ),
			} ),
			el( TextControl, {
				key: 'initials',
				label: __( 'Initials shown when there is no photo', 'apex-idaho' ),
				value: a.initials,
				onChange: setter( props, 'initials' ),
			} ),
		];
	} );

	define( 'apex/cart-link', function ( props ) {
		return [
			el( TextControl, {
				key: 'label',
				label: __( 'Label', 'apex-idaho' ),
				value: props.attributes.label,
				onChange: setter( props, 'label' ),
			} ),
		];
	} );
} )( window.wp );
