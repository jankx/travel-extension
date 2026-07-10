<?php
/** @var array $itinerary */
if (!defined('ABSPATH')) {
    exit;
}
if (empty($itinerary)) {
    $itinerary = [['title' => '', 'description' => '']];
}
?>
<p class="description"><?php esc_html_e('Lịch trình theo từng ngày. Ví dụ tiêu đề: "Ngày 1: Hà Nội - Hạ Long".', 'jankx'); ?></p>
<div id="jankx-tour-itinerary" class="jankx-travel-repeater jankx-travel-repeater-itinerary">
    <div class="jankx-travel-repeater-rows">
        <?php foreach ($itinerary as $row) : ?>
            <div class="jankx-travel-repeater-row jankx-travel-itinerary-row">
                <input type="text" placeholder="<?php esc_attr_e('Tiêu đề ngày', 'jankx'); ?>" name="tour_itinerary_title[]" value="<?php echo esc_attr($row['title'] ?? ''); ?>" class="regular-text" />
                <button type="button" class="button jankx-travel-remove-row">&times;</button>
                <textarea name="tour_itinerary_description[]" rows="3" placeholder="<?php esc_attr_e('Mô tả chi tiết trong ngày', 'jankx'); ?>" class="widefat"><?php echo esc_textarea($row['description'] ?? ''); ?></textarea>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button jankx-travel-add-row" data-target="jankx-tour-itinerary" data-template="itinerary">
        <?php esc_html_e('+ Thêm ngày', 'jankx'); ?>
    </button>
</div>
<template id="jankx-travel-template-itinerary">
    <div class="jankx-travel-repeater-row jankx-travel-itinerary-row">
        <input type="text" placeholder="<?php esc_attr_e('Tiêu đề ngày', 'jankx'); ?>" name="tour_itinerary_title[]" value="" class="regular-text" />
        <button type="button" class="button jankx-travel-remove-row">&times;</button>
        <textarea name="tour_itinerary_description[]" rows="3" placeholder="<?php esc_attr_e('Mô tả chi tiết trong ngày', 'jankx'); ?>" class="widefat"></textarea>
    </div>
</template>
