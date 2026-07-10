<?php
/**
 * Server-side render for jankx-travel/booking-form.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = $block->context['postId'] ?? get_the_ID();
$post_type = $block->context['postType'] ?? get_post_type($post_id);
$tour_id = ($post_type === 'tour') ? (int) $post_id : 0;

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'jankx-travel-booking-form']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if (!empty($attributes['title'])) : ?>
        <h3 class="jankx-travel-booking-form__title"><?php echo esc_html($attributes['title']); ?></h3>
    <?php endif; ?>

    <form class="jankx-travel-booking-form__form">
        <input type="hidden" name="tour_id" value="<?php echo esc_attr($tour_id); ?>" />

        <div class="jankx-travel-booking-form__field">
            <label for="jankx-booking-name-<?php echo esc_attr($tour_id); ?>"><?php esc_html_e('Họ tên', 'jankx'); ?> *</label>
            <input type="text" id="jankx-booking-name-<?php echo esc_attr($tour_id); ?>" name="customer_name" required />
        </div>

        <div class="jankx-travel-booking-form__field">
            <label for="jankx-booking-phone-<?php echo esc_attr($tour_id); ?>"><?php esc_html_e('Số điện thoại', 'jankx'); ?> *</label>
            <input type="tel" id="jankx-booking-phone-<?php echo esc_attr($tour_id); ?>" name="customer_phone" required />
        </div>

        <div class="jankx-travel-booking-form__field">
            <label for="jankx-booking-email-<?php echo esc_attr($tour_id); ?>"><?php esc_html_e('Email', 'jankx'); ?></label>
            <input type="email" id="jankx-booking-email-<?php echo esc_attr($tour_id); ?>" name="customer_email" />
        </div>

        <div class="jankx-travel-booking-form__row">
            <div class="jankx-travel-booking-form__field">
                <label for="jankx-booking-guests-<?php echo esc_attr($tour_id); ?>"><?php esc_html_e('Số khách', 'jankx'); ?></label>
                <input type="number" id="jankx-booking-guests-<?php echo esc_attr($tour_id); ?>" name="guests" min="1" value="1" />
            </div>
            <div class="jankx-travel-booking-form__field">
                <label for="jankx-booking-date-<?php echo esc_attr($tour_id); ?>"><?php esc_html_e('Ngày mong muốn', 'jankx'); ?></label>
                <input type="date" id="jankx-booking-date-<?php echo esc_attr($tour_id); ?>" name="departure_date" />
            </div>
        </div>

        <div class="jankx-travel-booking-form__field">
            <label for="jankx-booking-message-<?php echo esc_attr($tour_id); ?>"><?php esc_html_e('Ghi chú', 'jankx'); ?></label>
            <textarea id="jankx-booking-message-<?php echo esc_attr($tour_id); ?>" name="message" rows="3"></textarea>
        </div>

        <button type="submit" class="jankx-travel-booking-form__submit">
            <?php esc_html_e('Gửi yêu cầu báo giá', 'jankx'); ?>
        </button>
        <p class="jankx-travel-booking-status" role="status"></p>
    </form>
</div>
