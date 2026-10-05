import { registerBlockType } from '@wordpress/blocks';
import { addFilter } from '@wordpress/hooks';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from '../block.json';
import './style.scss';
import './editor.scss';

const SLOT_ATTR = 'jankxSlot';
const SLOT_PREFIX = 'prefix';
const SLOT_SUFFIX = 'suffix';
const SLOT_BLOCKS = ['core/paragraph', 'core/heading'];

// core/paragraph và core/heading không có sẵn thuộc tính jankxSlot, và
// WP_Block_Type::prepare_attributes_for_render() loại bỏ mọi thuộc tính chưa
// khai báo - nên phải đăng ký thật thì PHP mới đọc được slot.
addFilter(
	'blocks.registerBlockType',
	'jankx/post-starting-price/slot-attribute',
	(settings: any, name: string) => {
		if (!SLOT_BLOCKS.includes(name)) {
			return settings;
		}
		return {
			...settings,
			attributes: {
				...settings.attributes,
				[SLOT_ATTR]: { type: 'string', default: SLOT_PREFIX },
			},
		};
	}
);

// Control chọn vị trí, chỉ hiện khi block con nằm trong post-starting-price
// để không đụng tới paragraph/heading ở các nơi khác trong editor.
const withSlotControl = (BlockEdit: any) => (props: any) => {
	const { name, attributes, setAttributes, clientId } = props;

	const parentName = useSelect((select: any) => {
		const parents = select(blockEditorStore).getBlockParents(clientId);
		return parents?.length
			? select(blockEditorStore).getBlockName(parents[0])
			: '';
	}, [clientId]);

	if (!SLOT_BLOCKS.includes(name) || parentName !== metadata.name) {
		return <BlockEdit {...props} />;
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Vị trí trong giá', 'jankx')}>
					<SelectControl
						label={__('Prefix hay Suffix', 'jankx')}
						value={attributes?.[SLOT_ATTR] || SLOT_PREFIX}
						options={[
							{ label: __('Prefix (trước giá)', 'jankx'), value: SLOT_PREFIX },
							{ label: __('Suffix (sau giá)', 'jankx'), value: SLOT_SUFFIX },
						]}
						onChange={(value: string) => setAttributes({ [SLOT_ATTR]: value })}
					/>
				</PanelBody>
			</InspectorControls>
			<BlockEdit {...props} />
		</>
	);
};

addFilter('editor.BlockEdit', 'jankx/post-starting-price/slot-control', withSlotControl);

function Edit({ attributes, setAttributes }: any) {
	const blockProps = useBlockProps({
		style: buildInlineStyles(attributes),
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Starting Price Settings', 'jankx')}>
					<ToggleControl
						label={__('Show when empty', 'jankx')}
						checked={attributes.showWhenEmpty}
						onChange={(showWhenEmpty: boolean) => setAttributes({ showWhenEmpty })}
					/>
					{attributes.showWhenEmpty && (
						<TextControl
							label={__('Empty Text', 'jankx')}
							value={attributes.emptyText || ''}
							onChange={(emptyText: string) => setAttributes({ emptyText })}
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
						onChange={(tagName: string) => setAttributes({ tagName })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div className="post-starting-price__editor-slots">
					<p className="post-starting-price__editor-hint">
						{__(
							'Thêm Paragraph hoặc Heading làm prefix / suffix. Đổi vị trí trong Inspector của từng block con.',
							'jankx'
						)}
					</p>
					<InnerBlocks
						allowedBlocks={SLOT_BLOCKS}
						template={[
							[
								'core/paragraph',
								{ content: __('Từ ', 'jankx'), [SLOT_ATTR]: SLOT_PREFIX },
							],
						]}
						templateInsertUpdatesSelection={false}
						renderAppender={InnerBlocks.ButtonBlockAppender}
					/>
				</div>

				<div className="post-starting-price__editor-price">
					<ServerSideRender
						block={metadata.name}
						attributes={{ ...attributes, editorPreview: true }}
					/>
				</div>
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
	save: () => <InnerBlocks.Content />,
});