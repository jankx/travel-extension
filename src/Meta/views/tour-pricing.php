<?php
/** @var string $price */
/** @var bool $price_from */
/** @var int $days */
/** @var int $nights */
/** @var int $max_guests */
if (!defined('ABSPATH')) {
    exit;
}
?>
<table class="form-table jankx-travel-form-table">
    <tr>
        <th><label for="tour_price"><?php esc_html_e('Giá (VNĐ)', 'jankx'); ?></label></th>
        <td>
            <input type="number" step="1000" min="0" id="tour_price" name="tour_price" class="regular-text" value="<?php echo esc_attr($price); ?>" />
            <label style="margin-left:12px;">
                <input type="checkbox" name="tour_price_is_from" value="1" <?php checked($price_from, 1); ?> />
                <?php esc_html_e('Giá "từ" (giá khởi điểm)', 'jankx'); ?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="tour_duration_days"><?php esc_html_e('Thời lượng', 'jankx'); ?></label></th>
        <td>
            <input type="number" min="0" id="tour_duration_days" name="tour_duration_days" style="width:80px" value="<?php echo esc_attr($days); ?>" />
            <?php esc_html_e('ngày', 'jankx'); ?>
            &nbsp;
            <input type="number" min="0" id="tour_duration_nights" name="tour_duration_nights" style="width:80px" value="<?php echo esc_attr($nights); ?>" />
            <?php esc_html_e('đêm', 'jankx'); ?>
        </td>
    </tr>
    <tr>
        <th><label for="tour_max_guests"><?php esc_html_e('Số khách tối đa / đoàn', 'jankx'); ?></label></th>
        <td><input type="number" min="0" id="tour_max_guests" name="tour_max_guests" style="width:100px" value="<?php echo esc_attr($max_guests); ?>" /></td>
    </tr>
</table>
