import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from '../block.json';
import './style.scss';
import './editor.scss';

function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Tour Type Settings', 'jankx')}>
					<TextControl
						label={__('Prefix', 'jankx')}
						value={attributes.prefix || ''}
						onChange={(prefix) => setAttributes({ prefix })}
					/>
					<TextControl
						label={__('Suffix', 'jankx')}
						value={attributes.suffix || ''}
						onChange={(suffix) => setAttributes({ suffix })}
					/>
					<ToggleControl
						label={__('Show when empty', 'jankx')}
						checked={attributes.showWhenEmpty}
						onChange={(showWhenEmpty) => setAttributes({ showWhenEmpty })}
					/>
					{!attributes.showWhenEmpty && (
						<TextControl
							label={__('Empty Text', 'jankx')}
							value={attributes.emptyText || ''}
							onChange={(emptyText) => setAttributes({ emptyText })}
						/>
					)}
					<SelectControl
						label={__('Display Style', 'jankx')}
						value={attributes.displayStyle || 'text'}
						options={[
							{ label: __('Text', 'jankx'), value: 'text' },
							{ label: __('Badge', 'jankx'), value: 'badge' },
							{ label: __('Dot', 'jankx'), value: 'dot' },
						]}
						onChange={(displayStyle) => setAttributes({ displayStyle })}
					/>
					<SelectControl
						label={__('HTML Tag', 'jankx')}
						value={attributes.tagName || 'span'}
						options={[
							{ label: 'span', value: 'span' },
							{ label: 'div', value: 'div' },
							{ label: 'p', value: 'p' },
						]}
						onChange={(tagName) => setAttributes({ tagName })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<ServerSideRender
					block={metadata.name}
					attributes={attributes}
				/>
			</div>
		</>
	);
}

registerBlockType(metadata.name, {
	...metadata,
	edit: Edit,
	save: () => null,
});
