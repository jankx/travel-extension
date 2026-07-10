<?php
/** @var array $departures */
if (!defined('ABSPATH')) {
    exit;
}
if (empty($departures)) {
    $departures = [['date' => '', 'slots' => '', 'note' => '']];
}
?>
<p class="description"><?php esc_html_e('Thêm các ngày khởi hành sắp tới cho tour này.', 'jankx'); ?></p>
<div id="jankx-tour-departures" class="jankx-travel-repeater">
    <div class="jankx-travel-repeater-rows">
        <?php foreach ($departures as $row) : ?>
            <div class="jankx-travel-repeater-row">
                <input type="date" name="tour_departure_date[]" value="<?php echo esc_attr($row['date'] ?? ''); ?>" />
                <input type="number" min="0" placeholder="<?php esc_attr_e('Số chỗ còn', 'jankx'); ?>" name="tour_departure_slots[]" value="<?php echo esc_attr($row['slots'] ?? ''); ?>" style="width:120px" />
                <input type="text" placeholder="<?php esc_attr_e('Ghi chú (VD: Còn ít chỗ)', 'jankx'); ?>" name="tour_departure_note[]" value="<?php echo esc_attr($row['note'] ?? ''); ?>" class="regular-text" />
                <button type="button" class="button jankx-travel-remove-row">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button jankx-travel-add-row" data-target="jankx-tour-departures" data-template="departure">
        <?php esc_html_e('+ Thêm ngày khởi hành', 'jankx'); ?>
    </button>
</div>
<template id="jankx-travel-template-departure">
    <div class="jankx-travel-repeater-row">
        <input type="date" name="tour_departure_date[]" value="" />
        <input type="number" min="0" placeholder="<?php esc_attr_e('Số chỗ còn', 'jankx'); ?>" name="tour_departure_slots[]" value="" style="width:120px" />
        <input type="text" placeholder="<?php esc_attr_e('Ghi chú (VD: Còn ít chỗ)', 'jankx'); ?>" name="tour_departure_note[]" value="" class="regular-text" />
        <button type="button" class="button jankx-travel-remove-row">&times;</button>
    </div>
</template>
