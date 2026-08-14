<?php
/**
 * Post Starting Price Block
 *
 * @package Jankx\Extensions\Travel\Blocks
 */

namespace Jankx\Extensions\Travel\Blocks;

use Jankx\Extensions\Travel\Block;

class PostStartingPriceBlock extends Block
{
    protected $blockId = 'jankx/post-starting-price';

    public function render($attributes, $content = '', $block = null)
    {
        $postId = $this->resolvePostId($block);
        if (!$postId) {
            return '';
        }

        $prefix = $attributes['prefix'] ?? 'Từ ';
        $suffix = $attributes['suffix'] ?? '/ người';
        $showWhenEmpty = $attributes['showWhenEmpty'] ?? true;
        $emptyText = $attributes['emptyText'] ?? 'Liên hệ';
        $tagName = $attributes['tagName'] ?? 'span';

        $allowedTags = ['span', 'div', 'p', 'strong'];
        if (!in_array($tagName, $allowedTags, true)) {
            $tagName = 'span';
        }

        $price = get_post_meta($postId, '_experience_starting_price', true);
        $currency = get_post_meta($postId, '_experience_currency', true) ?: 'VND';

        if (empty($price)) {
            if (!$showWhenEmpty) {
                return '';
            }
            $formattedPrice = esc_html($emptyText);
        } else {
            $formattedPrice = $this->formatPrice($price, $currency);
        }

        $wrapperAttrs = get_block_wrapper_attributes([
            'class' => 'wp-block-jankx-post-starting-price',
        ]);

        ob_start();
        ?>
        <<?php echo esc_attr($tagName); ?> <?php echo $wrapperAttrs; ?>>
            <?php if (!empty($prefix)) : ?>
                <span class="post-starting-price__prefix"><?php echo esc_html($prefix); ?></span>
            <?php endif; ?>
            <span class="post-starting-price__price"><?php echo $formattedPrice; ?></span>
            <?php if (!empty($suffix)) : ?>
                <span class="post-starting-price__suffix"> <?php echo esc_html($suffix); ?></span>
            <?php endif; ?>
        </<?php echo esc_attr($tagName); ?>>
        <?php
        return ob_get_clean();
    }

    protected function formatPrice($price, $currency = 'VND'): string
    {
        $price = (float) str_replace(['.', ','], '', $price);

        if ($currency === 'VND') {
            return esc_html(number_format($price, 0, '', '.') . '₫');
        }

        return esc_html('$' . number_format($price, 2, '.', ','));
    }

    protected function resolvePostId($block): int
    {
        if ($block instanceof \WP_Block && !empty($block->context['postId'])) {
            return (int) $block->context['postId'];
        }

        $postId = get_the_ID();
        if ($postId) {
            return (int) $postId;
        }

        global $post;
        if ($post && isset($post->ID)) {
            return (int) $post->ID;
        }

        return 0;
    }
}
