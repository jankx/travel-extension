<?php
/**
 * Server-side render for jankx-travel/tour-search.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if (!defined('ABSPATH')) {
    exit;
}

$destinations = get_terms([
    'taxonomy'   => 'destination',
    'hide_empty' => false,
]);
$categories = get_terms([
    'taxonomy'   => 'tour_category',
    'hide_empty' => false,
]);

$current_destination = isset($_GET['tour_destination']) ? sanitize_text_field($_GET['tour_destination']) : '';
$current_category    = isset($_GET['tour_category']) ? sanitize_text_field($_GET['tour_category']) : '';
$current_price_max   = isset($_GET['tour_price_max']) ? absint($_GET['tour_price_max']) : '';
$current_days        = isset($_GET['tour_duration_days']) ? absint($_GET['tour_duration_days']) : '';

$archive_url = get_post_type_archive_link('tour');
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'jankx-tour-search']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <form method="get" action="<?php echo esc_url($archive_url); ?>" class="jankx-tour-search__form">
        <div class="jankx-tour-search__field">
            <label for="jankx-tour-destination"><?php esc_html_e('Điểm đến', 'jankx'); ?></label>
            <select id="jankx-tour-destination" name="tour_destination">
                <option value=""><?php esc_html_e('Tất cả điểm đến', 'jankx'); ?></option>
                <?php foreach ($destinations as $destination) : ?>
                    <option value="<?php echo esc_attr($destination->slug); ?>" <?php selected($current_destination, $destination->slug); ?>>
                        <?php echo esc_html($destination->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="jankx-tour-search__field">
            <label for="jankx-tour-category"><?php esc_html_e('Loại tour', 'jankx'); ?></label>
            <select id="jankx-tour-category" name="tour_category">
                <option value=""><?php esc_html_e('Tất cả loại tour', 'jankx'); ?></option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($current_category, $category->slug); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (!empty($attributes['showPriceFilter'])) : ?>
            <div class="jankx-tour-search__field">
                <label for="jankx-tour-price"><?php esc_html_e('Giá tối đa (VNĐ)', 'jankx'); ?></label>
                <input type="number" id="jankx-tour-price" name="tour_price_max" min="0" step="100000" value="<?php echo esc_attr($current_price_max); ?>" placeholder="<?php esc_attr_e('VD: 10000000', 'jankx'); ?>" />
            </div>
        <?php endif; ?>

        <?php if (!empty($attributes['showDurationFilter'])) : ?>
            <div class="jankx-tour-search__field">
                <label for="jankx-tour-days"><?php esc_html_e('Số ngày tối đa', 'jankx'); ?></label>
                <input type="number" id="jankx-tour-days" name="tour_duration_days" min="0" value="<?php echo esc_attr($current_days); ?>" placeholder="<?php esc_attr_e('VD: 5', 'jankx'); ?>" />
            </div>
        <?php endif; ?>

        <div class="jankx-tour-search__actions">
            <button type="submit" class="jankx-tour-search__submit">
                <?php esc_html_e('Tìm tour', 'jankx'); ?>
            </button>
        </div>
    </form>
</div>
