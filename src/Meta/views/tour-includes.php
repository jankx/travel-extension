<?php
/** @var string $includes */
/** @var string $excludes */
if (!defined('ABSPATH')) {
    exit;
}
?>
<p class="description"><?php esc_html_e('Mỗi dòng là một mục.', 'jankx'); ?></p>
<div style="display:flex; gap:20px;">
    <div style="flex:1;">
        <label for="tour_includes"><strong><?php esc_html_e('Bao gồm', 'jankx'); ?></strong></label>
        <textarea id="tour_includes" name="tour_includes" rows="6" class="widefat" placeholder="Vé máy bay khứ hồi&#10;Khách sạn 4 sao&#10;Ăn 3 bữa/ngày"><?php echo esc_textarea($includes); ?></textarea>
    </div>
    <div style="flex:1;">
        <label for="tour_excludes"><strong><?php esc_html_e('Không bao gồm', 'jankx'); ?></strong></label>
        <textarea id="tour_excludes" name="tour_excludes" rows="6" class="widefat" placeholder="Chi phí cá nhân&#10;Bảo hiểm du lịch&#10;Tiền tip hướng dẫn viên"><?php echo esc_textarea($excludes); ?></textarea>
    </div>
</div>
