import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from '../block.json';
import './style.scss';
import './editor.scss';

function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({
		style: buildInlineStyles(attributes),
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Starting Price Settings', 'jankx')}>
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
					{attributes.showWhenEmpty && (
						<TextControl
							label={__('Empty Text', 'jankx')}
							value={attributes.emptyText || ''}
							onChange={(emptyText) => setAttributes({ emptyText })}
						/>
					)}
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

function buildInlineStyles(attributes: Record<string, any>): React.CSSProperties {
	const styles: Record<string, any> = {};
	const attrStyle = attributes?.style;

	// Typography
	if (attrStyle?.typography?.fontSize) {
		styles.fontSize = attrStyle.typography.fontSize;
	}
	if (attrStyle?.typography?.lineHeight) {
		styles.lineHeight = attrStyle.typography.lineHeight;
	}
	if (attrStyle?.typography?.fontFamily) {
		styles.fontFamily = attrStyle.typography.fontFamily;
	}
	if (attrStyle?.typography?.fontWeight) {
		styles.fontWeight = attrStyle.typography.fontWeight;
	}
	if (attrStyle?.typography?.fontStyle) {
		styles.fontStyle = attrStyle.typography.fontStyle;
	}
	if (attrStyle?.typography?.textTransform) {
		styles.textTransform = attrStyle.typography.textTransform;
	}
	if (attrStyle?.typography?.textDecoration) {
		styles.textDecoration = attrStyle.typography.textDecoration;
	}
	if (attrStyle?.typography?.letterSpacing) {
		styles.letterSpacing = attrStyle.typography.letterSpacing;
	}

	// Color
	if (attrStyle?.color?.text) {
		styles.color = attrStyle.color.text;
	}
	if (attrStyle?.color?.background) {
		styles.backgroundColor = attrStyle.color.background;
	}

	// Border
	if (attrStyle?.border) {
		const border = attrStyle.border;
		if (border.color) styles.borderColor = border.color;
		if (border.radius) styles.borderRadius = border.radius;
		if (border.style) styles.borderStyle = border.style;
		if (border.width) styles.borderWidth = border.width;
	}

	return styles;
}

registerBlockType(metadata.name, {
	...metadata,
	edit: Edit,
	save: () => null,
});
