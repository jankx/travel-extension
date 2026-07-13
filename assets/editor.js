/**
 * Client-side editor registration for the Travel extension's dynamic blocks.
 *
 * Each block is rendered server-side (render.php). Attributes, supports and
 * category are already registered in PHP via block.json, so here we only
 * need to supply the `edit` (and a no-op `save`) so the block editor knows
 * how to display/select the block instead of showing the
 * "Your site doesn't include support for this block" placeholder.
 *
 * Plain browser JS on purpose — no build step required for this extension.
 */
( function ( blocks, element, blockEditor, serverSideRender, i18n ) {
	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var ServerSideRender = serverSideRender.default || serverSideRender;
	var __ = i18n.__;

	function makeEdit( blockName, label ) {
		return function ( props ) {
			var blockProps = useBlockProps( { className: 'jankx-travel-editor-preview' } );

			return el(
				'div',
				blockProps,
				el( 'div', { className: 'jankx-travel-editor-label' }, label ),
				el( ServerSideRender, {
					block: blockName,
					attributes: props.attributes,
				} )
			);
		};
	}

	var blockList = [
		[ 'jankx-travel/tour-search', __( 'Tour Search & Filter', 'jankx' ) ],
		[ 'jankx-travel/departure-calendar', __( 'Departure Calendar', 'jankx' ) ],
		[ 'jankx-travel/itinerary', __( 'Tour Itinerary', 'jankx' ) ],
		[ 'jankx-travel/booking-form', __( 'Booking Request Form', 'jankx' ) ],
		[ 'jankx-travel/tour-meta', __( 'Tour Meta', 'jankx' ) ],
	];

	blockList.forEach( function ( item ) {
		blocks.registerBlockType( item[ 0 ], {
			edit: makeEdit( item[ 0 ], item[ 1 ] ),
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender, window.wp.i18n );
