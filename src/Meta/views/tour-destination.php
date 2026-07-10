<?php
/** @var int $selected */
/** @var string $rating */
/** @var \WP_Post[] $destinations */
if (!defined('ABSPATH')) {
    exit;
}
?>
<p>
    <label for="tour_destination_id"><strong><?php esc_html_e('Điểm đến', 'jankx'); ?></strong></label><br />
    <select id="tour_destination_id" name="tour_destination_id" class="widefat">
        <option value="0"><?php esc_html_e('— Chọn điểm đến —', 'jankx'); ?></option>
        <?php foreach ($destinations as $destination) : ?>
            <option value="<?php echo esc_attr($destination->ID); ?>" <?php selected($selected, $destination->ID); ?>>
                <?php echo esc_html($destination->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>
</p>
<p>
    <label for="tour_rating"><strong><?php esc_html_e('Đánh giá (0-5 sao)', 'jankx'); ?></strong></label><br />
    <input type="number" id="tour_rating" name="tour_rating" min="0" max="5" step="0.1" value="<?php echo esc_attr($rating); ?>" class="small-text" />
</p>
