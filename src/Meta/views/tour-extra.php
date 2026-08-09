<?php
/**
 * Meta box view: Thông tin xuất phát & Ghi chú dịch vụ
 *
 * @var string $meeting_point
 * @var string $departure_type  'tu_tuc' | 'co_dinh'
 * @var array  $service_notes
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<table class="form-table jankx-travel-form-table">
    <tr>
        <th><label for="tour_meeting_point">
                <?php esc_html_e('Địa điểm xuất phát', 'jankx'); ?>
            </label></th>
        <td>
            <input type="text" id="tour_meeting_point" name="tour_meeting_point" class="widefat"
                value="<?php echo esc_attr($meeting_point); ?>"
                placeholder="<?php esc_attr_e('VD: Số 10 Đinh Tiên Hoàng, Hoàn Kiếm, Hà Nội', 'jankx'); ?>" />
        </td>
    </tr>
    <tr>
        <th><label for="tour_departure_type">
                <?php esc_html_e('Loại khởi hành', 'jankx'); ?>
            </label></th>
        <td>
            <select id="tour_departure_type" name="tour_departure_type">
                <option value="tu_tuc" <?php selected($departure_type, 'tu_tuc'); ?>>
                    <?php esc_html_e('Tự túc (theo yêu cầu)', 'jankx'); ?>
                </option>
                <option value="co_dinh" <?php selected($departure_type, 'co_dinh'); ?>>
                    <?php esc_html_e('Cố định (theo lịch)', 'jankx'); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <?php esc_html_e('Ghi chú dịch vụ', 'jankx'); ?>
        </th>
        <td>
            <div class="jankx-repeater" id="jankx-service-notes-repeater">
                <?php foreach ($service_notes as $note): ?>
                    <div class="jankx-repeater-row" style="display:flex;gap:8px;margin-bottom:6px;">
                        <input type="text" name="tour_service_note[]" value="<?php echo esc_attr($note); ?>"
                            style="flex:1;" />
                        <button type="button" class="button jankx-remove-row">
                            <?php esc_html_e('Xóa', 'jankx'); ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button jankx-add-row" data-target="jankx-service-notes-repeater"
                data-name="tour_service_note[]" data-placeholder="<?php esc_attr_e('Ghi chú', 'jankx'); ?>">
                <?php esc_html_e('+ Thêm ghi chú', 'jankx'); ?>
            </button>
        </td>
    </tr>
</table>