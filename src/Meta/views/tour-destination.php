<?php
/** @var int[] $selected_ids */
/** @var string $rating */
/** @var int $review_count */
/** @var \WP_Term[] $destinations */
if (!defined('ABSPATH')) {
    exit;
}
?>
<p>
    <label for="tour_destination_id"><strong><?php esc_html_e('Điểm đến', 'jankx'); ?></strong></label><br />
    <select id="tour_destination_id" name="tour_destination_id[]" class="widefat" multiple size="6">
        <?php foreach ($destinations as $destination): ?>
            <option value="<?php echo esc_attr($destination->term_id); ?>" <?php selected(in_array((int) $destination->term_id, $selected_ids, true), true); ?>>
                <?php echo esc_html($destination->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span class="description"><?php esc_html_e('Giữ Ctrl (Cmd) để chọn nhiều điểm đến.', 'jankx'); ?></span>
</p>
<p>
    <label for="tour_rating"><strong><?php esc_html_e('Đánh giá (0-5 sao)', 'jankx'); ?></strong></label><br />
    <input type="number" id="tour_rating" name="tour_rating" min="0" max="5" step="0.1"
        value="<?php echo esc_attr($rating); ?>" class="small-text" />
</p>
<p>
    <label for="tour_review_count"><strong><?php esc_html_e('Số lượng đánh giá', 'jankx'); ?></strong></label><br />
    <input type="number" id="tour_review_count" name="tour_review_count" min="0" step="1"
        value="<?php echo esc_attr($review_count); ?>" class="regular-text" />
    <span class="description"><?php esc_html_e('VD: 30000 (hiển thị "30K đánh giá")', 'jankx'); ?></span>
</p>
