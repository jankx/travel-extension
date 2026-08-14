import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, SelectControl, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';
import './style.scss';
import './editor.scss';

function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Live Counter Settings', 'jankx')}>
					<SelectControl
						label={__('Counter Type', 'jankx')}
						value={attributes.counterType || 'viewing'}
						options={[
							{ label: __('Currently Viewing', 'jankx'), value: 'viewing' },
							{ label: __('Total Visitors', 'jankx'), value: 'visitors' },
							{ label: __('Custom', 'jankx'), value: 'custom' },
						]}
						onChange={(counterType) => setAttributes({ counterType })}
					/>
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
					{attributes.counterType === 'custom' && (
						<>
							<RangeControl
								label={__('Min Value', 'jankx')}
								value={attributes.min || 1}
								onChange={(min) => setAttributes({ min })}
								min={0}
								max={100}
							/>
							<RangeControl
								label={__('Max Value', 'jankx')}
								value={attributes.max || 15}
								onChange={(max) => setAttributes({ max })}
								min={1}
								max={100}
							/>
						</>
					)}
					<RangeControl
						label={__('Update Interval (seconds)', 'jankx')}
						value={attributes.interval || 3}
						onChange={(interval) => setAttributes({ interval })}
						min={1}
						max={30}
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
				<span className="live-counter__value">
					{attributes.prefix || ''}5{attributes.suffix || 'đang xem'}
				</span>
			</div>
		</>
	);
}

registerBlockType(metadata.name, {
	...metadata,
	edit: Edit,
	save: () => null,
});
