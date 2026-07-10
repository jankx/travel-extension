<?php

namespace Jankx\Extensions\Travel\PostTypes;

/**
 * Registers the "tour_booking" Custom Post Type used to store
 * quote/booking requests submitted from the frontend form.
 *
 * This is intentionally NOT public — it is an internal record for admins
 * to review in wp-admin, not a public content type.
 */
class BookingRequestPostType
{
    const POST_TYPE = 'tour_booking';

    public function register(): void
    {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type(): void
    {
        $labels = [
            'name'               => __('Yêu cầu báo giá', 'jankx'),
            'singular_name'      => __('Yêu cầu báo giá', 'jankx'),
            'menu_name'          => __('Yêu cầu báo giá', 'jankx'),
            'add_new_item'       => __('Thêm yêu cầu', 'jankx'),
            'edit_item'          => __('Xem yêu cầu', 'jankx'),
            'view_item'          => __('Xem yêu cầu', 'jankx'),
            'search_items'       => __('Tìm yêu cầu', 'jankx'),
            'not_found'          => __('Chưa có yêu cầu báo giá nào', 'jankx'),
            'not_found_in_trash' => __('Không có yêu cầu nào trong thùng rác', 'jankx'),
            'all_items'          => __('Tất cả yêu cầu', 'jankx'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels'        => $labels,
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => 'edit.php?post_type=' . TourPostType::POST_TYPE,
            'show_in_rest'  => true,
            'supports'      => ['title'],
            'capability_type' => 'post',
            'map_meta_cap'  => true,
        ]);
    }
}
