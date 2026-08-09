<?php
/** @var array $highlights */
if (!defined('ABSPATH')) {
    exit;
}
?>
<p class="description">
    <?php esc_html_e('Mỗi dòng là một điểm nổi bật của tour. Nhấn "Thêm" để thêm dòng mới.', 'jankx'); ?>
</p>
<div class="jankx-repeater" id="jankx-highlights-repeater">
    <?php foreach ($highlights as $i => $item): ?>
        <div class="jankx-repeater-row" style="display:flex;gap:8px;margin-bottom:6px;">
            <input type="text" name="tour_highlight[]" value="<?php echo esc_attr($item); ?>" style="flex:1;"
                placeholder="<?php esc_attr_e('VD: Chèo thuyền khám phá hang động', 'jankx'); ?>" />
            <button type="button" class="button jankx-remove-row">
                <?php esc_html_e('Xóa', 'jankx'); ?>
            </button>
        </div>
    <?php endforeach; ?>
</div>
<button type="button" class="button jankx-add-row" data-target="jankx-highlights-repeater" data-name="tour_highlight[]"
    data-placeholder="<?php esc_attr_e('Điểm nổi bật', 'jankx'); ?>">
    <?php esc_html_e('+ Thêm dòng', 'jankx'); ?>
</button>