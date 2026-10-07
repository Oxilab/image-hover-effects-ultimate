( function () {
	'use strict';

	if ( typeof wp === 'undefined' || ! wp.blocks || ! wp.element || ! wp.components || ! wp.blockEditor ) {
		return;
	}

	var el                = wp.element.createElement;
	var Fragment          = wp.element.Fragment;
	var useBlockProps     = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var SelectControl     = wp.components.SelectControl;
	var PanelBody         = wp.components.PanelBody;
	var Spinner           = wp.components.Spinner;
	var data              = window.iheuBlockData || {};
	var shortcodes        = data.shortcodes || [];
	var editUrls          = data.editUrls   || {};

	var blockIcon = el( 'svg',
		{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24' },
		el( 'circle', { cx: '12', cy: '12', r: '12', fill: '#ee326e' } ),
		el( 'rect', {
			x: '7.28', y: '5.34', width: '11.97', height: '11.97', rx: '2.46', ry: '2.46',
			fill: 'none', stroke: '#fff', strokeWidth: '0.77', strokeMiterlimit: '10',
		} ),
		el( 'path', {
			d: 'M7.31,7.47l-1.14.4c-1.27.44-1.95,1.85-1.5,3.12l2.24,6.4c.44,1.27,1.85,1.95,3.12,1.5l4.51-1.57',
			fill: 'none', stroke: '#fff', strokeWidth: '0.77', strokeMiterlimit: '10',
		} ),
		el( 'rect', {
			x: '8.99', y: '7.01', width: '2.54', height: '2.65', rx: '0.53', ry: '0.53',
			fill: 'none', stroke: '#fff', strokeWidth: '0.58', strokeMiterlimit: '10',
		} ),
		el( 'polygon', {
			points: '15.2 13.66 14.19 14.67 13.82 10.49 17.32 12.33 16.15 12.79 17.67 14.89 16.72 15.61 15.2 13.66',
			fill: 'none', stroke: '#fff', strokeWidth: '0.58', strokeLinecap: 'round', strokeLinejoin: 'round',
		} ),
		el( 'polyline', {
			points: '7.28 14.07 10.12 11.74 12.74 13.49',
			fill: 'none', stroke: '#fff', strokeWidth: '0.77', strokeLinecap: 'round', strokeLinejoin: 'round',
		} )
	);

	wp.blocks.registerBlockType( 'iheu/image-hover', {

		icon: blockIcon,

		edit: function ( props ) {
			var styleId    = props.attributes.styleId || '';
			var blockProps = useBlockProps();
			var options    = [ { value: '', label: 'Choose a style' } ].concat(
				shortcodes.map( function ( s ) {
					return { value: s.value, label: s.label };
				} )
			);
			var editUrl = styleId ? ( editUrls[ styleId ] || '' ) : '';

			return el(
				Fragment,
				null,
				el( InspectorControls, null,
					el( PanelBody, { title: 'Image Hover Effects', initialOpen: true },
						el( SelectControl, {
							label:    'Select a Style',
							value:    styleId,
							options:  options,
							onChange: function ( val ) {
								props.setAttributes( { styleId: val } );
							},
						} ),
						styleId && editUrl ? el(
							'a',
							{
								href:   editUrl,
								target: '_blank',
								rel:    'noreferrer',
								style:  {
									display:        'inline-block',
									marginTop:      '8px',
									padding:        '6px 12px',
									background:     '#1A73E8',
									color:          '#fff',
									borderRadius:   '4px',
									textDecoration: 'none',
									fontSize:       '12px',
								},
							},
							'✏ Edit Style'
						) : null
					)
				),
				el( 'div', blockProps,
					styleId
						? el( wp.serverSideRender, {
							block:      'iheu/image-hover',
							attributes: props.attributes,
							LoadingResponsePlaceholder: function () {
								return el( 'div', { style: { textAlign: 'center', padding: '20px' } }, el( Spinner ) );
							},
						} )
						: el( 'p', {
							style: {
								margin:     '0',
								padding:    '16px',
								color:      '#999',
								textAlign:  'center',
								fontStyle:  'italic',
							},
						}, 'Select an Image Hover style from the block settings panel.' )
				)
			);
		},

		save: function () {
			return null;
		},
	} );

} )();
