<?php
/**
 * Post Tour Type Block
 *
 * @package Jankx\Extensions\Travel\Blocks
 */

namespace Jankx\Extensions\Travel\Blocks;

use Jankx\Extensions\Travel\Block;

class PostTourTypeBlock extends Block
{
    protected $blockId = 'jankx/post-tour-type';

    const TOUR_TYPES = [
        'adventure' => 'Phiêu lưu',
        'cultural' => 'Văn hóa',
        'nature' => 'Thiên nhiên',
        'beach' => 'Biển đảo',
        'city' => 'Thành phố',
        'food' => 'Ẩm thực',
        'wellness' => 'Sức khỏe',
        'family' => 'Gia đình',
        'luxury' => 'Sang trọng',
        'budget' => 'Tiết kiệm',
        'group' => 'Nhóm',
        'solo' => 'Đơn thân',
        'honeymoon' => 'Trăng mật',
    ];

    public function render($attributes, $content = '', $block = null)
    {
        $postId = $this->resolvePostId($block);
        if (!$postId) {
            return '';
        }

        $prefix = $attributes['prefix'] ?? '';
        $suffix = $attributes['suffix'] ?? '';
        $showWhenEmpty = $attributes['showWhenEmpty'] ?? false;
        $emptyText = $attributes['emptyText'] ?? '';
        $tagName = $attributes['tagName'] ?? 'span';
        $displayStyle = $attributes['displayStyle'] ?? 'text';

        $allowedTags = ['span', 'div', 'p'];
        if (!in_array($tagName, $allowedTags, true)) {
            $tagName = 'span';
        }

        $tourType = get_post_meta($postId, '_experience_tour_type', true);

        if (empty($tourType)) {
            if (!$showWhenEmpty) {
                return '';
            }
            $label = esc_html($emptyText);
        } else {
            $label = esc_html(self::TOUR_TYPES[$tourType] ?? $tourType);
        }

        $wrapperClasses = [
            'wp-block-jankx-post-tour-type',
            'display-style-' . esc_attr($displayStyle),
        ];

        $wrapperAttrs = get_block_wrapper_attributes([
            'class' => implode(' ', $wrapperClasses),
        ]);

        ob_start();
        ?>
        <<?php echo esc_attr($tagName); ?> <?php echo $wrapperAttrs; ?>>
            <?php if (!empty($prefix)) : ?>
                <span class="post-tour-type__prefix"><?php echo esc_html($prefix); ?></span>
            <?php endif; ?>
            <span class="post-tour-type__label"><?php echo $label; ?></span>
            <?php if (!empty($suffix)) : ?>
                <span class="post-tour-type__suffix"><?php echo esc_html($suffix); ?></span>
            <?php endif; ?>
        </<?php echo esc_attr($tagName); ?>>
        <?php
        return ob_get_clean();
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
