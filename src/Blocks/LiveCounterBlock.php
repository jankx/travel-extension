<?php
/**
 * Live Counter Block
 *
 * @package Jankx\Extensions\Travel\Blocks
 */

namespace Jankx\Extensions\Travel\Blocks;

use Jankx\Extensions\Travel\Block;

class LiveCounterBlock extends Block
{
    protected $blockId = 'jankx/live-counter';

    public function render($attributes, $content = '', $block = null)
    {
        $counterType = $attributes['counterType'] ?? 'viewing';
        $prefix = $attributes['prefix'] ?? '';
        $suffix = $attributes['suffix'] ?? 'đang xem';
        $min = $attributes['min'] ?? 1;
        $max = $attributes['max'] ?? 15;
        $interval = $attributes['interval'] ?? 3;
        $tagName = $attributes['tagName'] ?? 'span';

        $allowedTags = ['span', 'div', 'p'];
        if (!in_array($tagName, $allowedTags, true)) {
            $tagName = 'span';
        }

        $wrapperAttrs = get_block_wrapper_attributes([
            'class' => 'wp-block-jankx-live-counter',
            'data-min' => $min,
            'data-max' => $max,
            'data-interval' => $interval,
            'data-type' => $counterType,
        ]);

        ob_start();
        ?>
        <<?php echo esc_attr($tagName); ?> <?php echo $wrapperAttrs; ?>>
            <?php if (!empty($prefix)) : ?>
                <span class="live-counter__prefix"><?php echo esc_html($prefix); ?></span>
            <?php endif; ?>
            <span class="live-counter__value" data-counter="<?php echo esc_attr($counterType); ?>">5</span>
            <?php if (!empty($suffix)) : ?>
                <span class="live-counter__suffix"> <?php echo esc_html($suffix); ?></span>
            <?php endif; ?>
        </<?php echo esc_attr($tagName); ?>>
        <?php
        return ob_get_clean();
    }
}
