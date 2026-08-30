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
        $isTemplateEditor = $this->isTemplateEditor();
        $postId = 0;

        if ($isTemplateEditor) {
            $price = $this->getMockPrice();
        } else {
            $postId = $this->resolvePostId($block);
            if (!$postId) {
                return '';
            }

            $price = get_post_meta($postId, '_experience_starting_price', true);
        }

        // Allow business extensions (e.g. date-based tour pricing) to swap the
        // displayed starting price with their own logic.
        $price = apply_filters('jankx/travel/tour/starting_price', $price, $postId);

        $currency = $isTemplateEditor
            ? 'VND'
            : (get_post_meta($postId, '_experience_currency', true) ?: 'VND');

        $prefix = $attributes['prefix'] ?? 'Từ ';
        $suffix = $attributes['suffix'] ?? '/ người';
        $showWhenEmpty = $attributes['showWhenEmpty'] ?? true;
        $emptyText = $attributes['emptyText'] ?? 'Liên hệ';
        $tagName = $attributes['tagName'] ?? 'span';

        $allowedTags = ['span', 'div', 'p', 'strong'];
        if (!in_array($tagName, $allowedTags, true)) {
            $tagName = 'span';
        }

        $price = $price ?? '';

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

    /**
     * Check whether the block is rendered inside the template editor.
     *
     * @return bool
     */
    protected function isTemplateEditor()
    {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($request_uri, '/wp-json/wp/v2/template') !== false ||
                strpos($request_uri, '/wp-json/wp/v2/template-part') !== false) {
                return true;
            }
        }

        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen && ($screen->id === 'site-editor' || $screen->id === 'appearance_page_gutenberg-edit-site')) {
                return true;
            }
        }

        if (isset($_GET['_wp-find-template']) ||
            (isset($_GET['postType']) && $_GET['postType'] === 'wp_template')) {
            return true;
        }

        global $post;
        if ((is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) &&
            (empty($post) || empty($post->post_content))) {
            return true;
        }

        return false;
    }

    /**
     * Get mock starting price for template editor preview.
     *
     * @return string
     */
    protected function getMockPrice()
    {
        return '4500000';
    }
}
