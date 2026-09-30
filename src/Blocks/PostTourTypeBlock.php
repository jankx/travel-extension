<?php
/**
 * Post Tour Type Block
 *
 * @package Jankx\Extensions\Travel\Blocks
 */

namespace Jankx\Extensions\Travel\Blocks;

use Jankx\Extensions\Travel\Block;
use Jankx\Extensions\Travel\Taxonomies\TourTagTaxonomy;

class PostTourTypeBlock extends Block
{
    protected $blockId = 'jankx/post-tour-type';

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

        // Source: the tour_tag taxonomy (Trải nghiệm). It is a flat tag taxonomy,
        // so a tour can carry several experience groups at once.
        $tourTypeLabel = '';
        $terms = get_the_terms($postId, TourTagTaxonomy::TAXONOMY);
        if ($terms && !is_wp_error($terms)) {
            $tourTypeLabel = implode(', ', wp_list_pluck($terms, 'name'));
        }

        if (empty($tourTypeLabel)) {
            if (!$showWhenEmpty) {
                return '';
            }
            $label = esc_html($emptyText);
        } else {
            $label = esc_html($tourTypeLabel);
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
