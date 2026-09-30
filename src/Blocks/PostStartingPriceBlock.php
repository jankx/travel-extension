<?php
/**
 * Post Starting Price Block
 *
 * @package Jankx\Extensions\Travel\Blocks
 */

namespace Jankx\Extensions\Travel\Blocks;

use Jankx\Extensions\Travel\Block;
use Jankx\Extensions\Ecommerce\Currency\CurrencyManager;
use Jankx\Extensions\Ecommerce\Registry\ProductRegistry;

class PostStartingPriceBlock extends Block
{
    protected $blockId = 'jankx/post-starting-price';

    public function render($attributes, $content = '', $block = null)
    {
        $isTemplateEditor = $this->isTemplateEditor();
        $postId = 0;
        $product = null;

        if ($isTemplateEditor) {
            $price = $this->getMockPrice();
        } else {
            $postId = $this->resolvePostId($attributes, $block);
            if (!$postId) {
                return '';
            }

            // Block này hiển thị giá khởi điểm ("Từ ..."), khác với giá bán
            // mà Add to Cart thu. Đọc starting price key do PriceMetaRegistry
            // khai báo cho post type trước; chỉ khi không có mới lấy
            // getPrice() để khớp với giá thanh toán.
            $postType = get_post_type($postId);
            $product = ProductRegistry::get_instance()->createProduct($postId);
            $price = (float) self::getFirstMetaValue($postId, self::getPriceMetaKeys($postType ?: ''));

            if ($price <= 0.0 && $product) {
                $price = $product->getPrice();
            }
        }

        // Allow business extensions (e.g. date-based tour pricing) to swap the
        // displayed starting price with their own logic.
        $price = apply_filters('jankx/travel/tour/starting_price', $price, $postId);

        // Giá lưu trong DB theo đơn vị mặc định của site (sourceCurrency).
        $sourceCurrency = CurrencyManager::getDefaultCurrency();

        $targetCurrency = CurrencyManager::getCurrentCurrency();

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
            $converterManager = \Jankx\Extensions\Ecommerce\Currency\Converters\CurrencyConverterManager::getInstance();
            $formattedPrice = $converterManager->formatPriceWithConversion(
                (float) $price,
                $sourceCurrency,   // Đồng tiền giá được nhập (thường = default currency)
                $targetCurrency    // Đồng tiền user đang xem
            );
        }

        $wrapperAttrs = get_block_wrapper_attributes([
            'class' => 'wp-block-jankx-post-starting-price',
            'style' => $this->buildInlineStyle($attributes),
        ]);

        ob_start();
        ?>
        <<?php echo esc_attr($tagName); ?>         <?php echo $wrapperAttrs; ?>>
            <?php
            if (!empty($prefix)) {
                echo '<span class="post-starting-price__prefix">' . esc_html($prefix) . '</span>';
            }
            ?><span
                class="post-starting-price__price"><?php echo $formattedPrice; /* Đã bao gồm custom currency format từ CurrencyManager */ ?></span>
            <?php
            if (!empty($suffix)) {
                echo '<span class="post-starting-price__suffix">' . esc_html($suffix) . '</span>';
            }
            ?>
        </<?php echo esc_attr($tagName); ?>>
        <?php
        return ob_get_clean();
    }

    /**
     * Build inline style string from block attributes (typography, color, border).
     */
    protected function buildInlineStyle(array $attributes): string
    {
        $parts = [];
        $style = $attributes['style'] ?? [];

        // Typography
        $typo = $style['typography'] ?? [];
        if (!empty($typo['fontSize']))       $parts[] = 'font-size: ' . esc_attr($typo['fontSize']);
        if (!empty($typo['lineHeight']))     $parts[] = 'line-height: ' . esc_attr($typo['lineHeight']);
        if (!empty($typo['fontFamily']))     $parts[] = 'font-family: ' . esc_attr($typo['fontFamily']);
        if (!empty($typo['fontWeight']))     $parts[] = 'font-weight: ' . esc_attr($typo['fontWeight']);
        if (!empty($typo['fontStyle']))      $parts[] = 'font-style: ' . esc_attr($typo['fontStyle']);
        if (!empty($typo['textTransform']))  $parts[] = 'text-transform: ' . esc_attr($typo['textTransform']);
        if (!empty($typo['textDecoration'])) $parts[] = 'text-decoration: ' . esc_attr($typo['textDecoration']);
        if (!empty($typo['letterSpacing']))  $parts[] = 'letter-spacing: ' . esc_attr($typo['letterSpacing']);

        // Color
        $color = $style['color'] ?? [];
        if (!empty($color['text']))        $parts[] = 'color: ' . esc_attr($color['text']);
        if (!empty($color['background']))  $parts[] = 'background-color: ' . esc_attr($color['background']);

        // Border
        $border = $style['border'] ?? [];
        if (!empty($border['color']))   $parts[] = 'border-color: ' . esc_attr($border['color']);
        if (!empty($border['radius']))  $parts[] = 'border-radius: ' . esc_attr($border['radius']);
        if (!empty($border['style']))   $parts[] = 'border-style: ' . esc_attr($border['style']);
        if (!empty($border['width']))   $parts[] = 'border-width: ' . esc_attr($border['width']);

        return implode('; ', $parts);
    }

    /**
     * Resolve the ordered list of price meta keys for a post type.
     *
     * Each product type declares its own price meta key(s) via
     * AbstractProduct::PRICE_META_KEY + LEGACY_PRICE_META_KEYS. This is the
     * single API consumed by blocks and admin meta boxes so every consumer
     * reads/writes the same key per post type.
     *
     * @param string $postType
     * @return string[]
     */
    public static function getPriceMetaKeys(string $postType): array
    {
        $keys = class_exists('\Jankx\Extensions\Ecommerce\Registry\PriceMetaRegistry')
            ? \Jankx\Extensions\Ecommerce\Registry\PriceMetaRegistry::get_instance()->getStartingPriceKeys($postType)
            : ['_' . $postType . '_price', '_price'];

        return (array) apply_filters('jankx/travel/post_starting_price/meta_keys', $keys, $postType);
    }

    /**
     * Resolve the canonical meta key that admin should write for a post type.
     *
     * Prefers the first legacy "sell price" key (e.g. _tour_price,
     * _product_price) over the generic _jankx_price so external
     * readers (search, tour pricing) keep seeing the updated value.
     *
     * @param string $postType
     * @return string
     */
    public static function getPriceMetaKey(string $postType): string
    {
        $key = class_exists('\Jankx\Extensions\Ecommerce\Registry\PriceMetaRegistry')
            ? \Jankx\Extensions\Ecommerce\Registry\PriceMetaRegistry::get_instance()->getStartingPriceKey($postType)
            : '_price';

        return (string) apply_filters('jankx/travel/post_starting_price/meta_key', $key, $postType);
    }

    /**
     * Read the first non-empty meta value from an ordered list of keys.
     *
     * @param int    $postId
     * @param array  $keys
     * @return string
     */
    protected static function getFirstMetaValue(int $postId, array $keys): string
    {
        foreach ($keys as $key) {
            $value = get_post_meta($postId, $key, true);
            if ($value !== '' && $value !== false) {
                return (string) $value;
            }
        }

        return '';
    }

    protected function resolvePostId($attributes, $block): int
    {
        $postId = (int) ($attributes['postId'] ?? 0);
        if ($postId > 0) {
            return $postId;
        }

        if (is_object($block) && !empty($block->context['postId'])) {
            return (int) $block->context['postId'];
        } elseif (is_array($block) && !empty($block['context']['postId'])) {
            return (int) $block['context']['postId'];
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
            if (
                strpos($request_uri, '/wp-json/wp/v2/template') !== false ||
                strpos($request_uri, '/wp-json/wp/v2/template-part') !== false
            ) {
                return true;
            }
        }

        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen && ($screen->id === 'site-editor' || $screen->id === 'appearance_page_gutenberg-edit-site')) {
                return true;
            }
        }

        if (
            isset($_GET['_wp-find-template']) ||
            (isset($_GET['postType']) && $_GET['postType'] === 'wp_template')
        ) {
            return true;
        }

        global $post;
        if (
            (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) &&
            (empty($post) || empty($post->post_content))
        ) {
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
