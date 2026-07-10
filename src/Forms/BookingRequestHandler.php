<?php

namespace Jankx\Extensions\Travel\Forms;

use Jankx\Extensions\Travel\PostTypes\BookingRequestPostType;
use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Handles the frontend "quote request" (booking request) form submission
 * via AJAX. No online payment — this only records the request and
 * notifies the site admin + sends a confirmation email to the customer.
 */
class BookingRequestHandler
{
    const ACTION = 'jankx_travel_booking_request';

    public function register(): void
    {
        add_action('wp_ajax_' . self::ACTION, [$this, 'handle']);
        add_action('wp_ajax_nopriv_' . self::ACTION, [$this, 'handle']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_columns_assets']);

        // Admin list table: show key request fields as columns.
        add_filter('manage_' . BookingRequestPostType::POST_TYPE . '_posts_columns', [$this, 'add_admin_columns']);
        add_action('manage_' . BookingRequestPostType::POST_TYPE . '_posts_custom_column', [$this, 'render_admin_column'], 10, 2);
    }

    public function enqueue_assets(): void
    {
        wp_enqueue_script(
            'jankx-travel-booking-form',
            get_template_directory_uri() . '/extensions/travel/assets/booking-form.js',
            [],
            '1.0.0',
            true
        );
        wp_localize_script('jankx-travel-booking-form', 'jankxTravelBooking', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'action'  => self::ACTION,
            'nonce'   => wp_create_nonce(self::ACTION),
            'i18n'    => [
                'sending' => __('Đang gửi...', 'jankx'),
                'success' => __('Cảm ơn bạn! Chúng tôi sẽ liên hệ báo giá sớm nhất.', 'jankx'),
                'error'   => __('Có lỗi xảy ra, vui lòng thử lại.', 'jankx'),
            ],
        ]);
    }

    public function enqueue_admin_columns_assets(): void
    {
        // Placeholder for future admin-side enhancements (kept lightweight for now).
    }

    public function handle(): void
    {
        check_ajax_referer(self::ACTION, 'nonce');

        $name  = sanitize_text_field($_POST['customer_name'] ?? '');
        $phone = sanitize_text_field($_POST['customer_phone'] ?? '');
        $email = sanitize_email($_POST['customer_email'] ?? '');
        $guests = absint($_POST['guests'] ?? 0);
        $departure_date = sanitize_text_field($_POST['departure_date'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');
        $tour_id = absint($_POST['tour_id'] ?? 0);

        if (empty($name) || empty($phone)) {
            wp_send_json_error([
                'message' => __('Vui lòng nhập họ tên và số điện thoại.', 'jankx'),
            ]);
        }

        $tour_title = $tour_id ? get_the_title($tour_id) : __('(Không xác định)', 'jankx');

        $post_id = wp_insert_post([
            'post_type'   => BookingRequestPostType::POST_TYPE,
            'post_title'  => sprintf('%s - %s', $name, $tour_title),
            'post_status' => 'publish',
        ], true);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => __('Không thể lưu yêu cầu, vui lòng thử lại.', 'jankx')]);
        }

        update_post_meta($post_id, '_booking_customer_name', $name);
        update_post_meta($post_id, '_booking_customer_phone', $phone);
        update_post_meta($post_id, '_booking_customer_email', $email);
        update_post_meta($post_id, '_booking_guests', $guests);
        update_post_meta($post_id, '_booking_departure_date', $departure_date);
        update_post_meta($post_id, '_booking_message', $message);
        update_post_meta($post_id, '_booking_tour_id', $tour_id);
        update_post_meta($post_id, '_booking_status', 'new');

        $this->notify_admin($post_id, compact('name', 'phone', 'email', 'guests', 'departure_date', 'message', 'tour_title'));

        do_action('jankx/travel/booking_request_created', $post_id, $tour_id);

        wp_send_json_success([
            'message' => __('Cảm ơn bạn! Chúng tôi sẽ liên hệ báo giá sớm nhất.', 'jankx'),
        ]);
    }

    protected function notify_admin(int $post_id, array $data): void
    {
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('[Yêu cầu báo giá tour] %s', 'jankx'), $data['tour_title']);

        $body = sprintf(
            "Có yêu cầu báo giá tour mới:\n\nTour: %s\nHọ tên: %s\nSĐT: %s\nEmail: %s\nSố khách: %s\nNgày mong muốn khởi hành: %s\nGhi chú: %s\n\nXem chi tiết: %s",
            $data['tour_title'],
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['guests'],
            $data['departure_date'],
            $data['message'],
            admin_url('post.php?post=' . $post_id . '&action=edit')
        );

        wp_mail($admin_email, $subject, $body);
    }

    public function add_admin_columns(array $columns): array
    {
        $new_columns = [];
        foreach ($columns as $key => $label) {
            $new_columns[$key] = $label;
            if ($key === 'title') {
                $new_columns['booking_phone'] = __('SĐT', 'jankx');
                $new_columns['booking_tour']  = __('Tour', 'jankx');
                $new_columns['booking_date']  = __('Ngày mong muốn', 'jankx');
                $new_columns['booking_status'] = __('Trạng thái', 'jankx');
            }
        }
        return $new_columns;
    }

    public function render_admin_column(string $column, int $post_id): void
    {
        switch ($column) {
            case 'booking_phone':
                echo esc_html(get_post_meta($post_id, '_booking_customer_phone', true));
                break;
            case 'booking_tour':
                $tour_id = (int) get_post_meta($post_id, '_booking_tour_id', true);
                echo $tour_id ? esc_html(get_the_title($tour_id)) : '—';
                break;
            case 'booking_date':
                echo esc_html(get_post_meta($post_id, '_booking_departure_date', true));
                break;
            case 'booking_status':
                echo esc_html(get_post_meta($post_id, '_booking_status', true) ?: 'new');
                break;
        }
    }
}
