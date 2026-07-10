<?php
/** @var string $country */
/** @var string $best_season */
/** @var string $highlights */
if (!defined('ABSPATH')) {
    exit;
}
?>
<table class="form-table jankx-travel-form-table">
    <tr>
        <th><label for="destination_country"><?php esc_html_e('Quốc gia', 'jankx'); ?></label></th>
        <td><input type="text" id="destination_country" name="destination_country" class="regular-text" value="<?php echo esc_attr($country); ?>" placeholder="Việt Nam" /></td>
    </tr>
    <tr>
        <th><label for="destination_best_season"><?php esc_html_e('Mùa đẹp nhất', 'jankx'); ?></label></th>
        <td><input type="text" id="destination_best_season" name="destination_best_season" class="regular-text" value="<?php echo esc_attr($best_season); ?>" placeholder="Tháng 3 - Tháng 5" /></td>
    </tr>
    <tr>
        <th><label for="destination_highlights"><?php esc_html_e('Điểm nổi bật', 'jankx'); ?></label></th>
        <td>
            <textarea id="destination_highlights" name="destination_highlights" rows="5" class="widefat" placeholder="Mỗi dòng một điểm nổi bật"><?php echo esc_textarea($highlights); ?></textarea>
        </td>
    </tr>
</table>
