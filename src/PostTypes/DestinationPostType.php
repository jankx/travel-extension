<?php

namespace Jankx\Extensions\Travel\PostTypes;

/**
 * Registers the "destination" Custom Post Type (Điểm đến).
 */
class DestinationPostType
{
    const POST_TYPE = 'destination';

    public function register(): void
    {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type(): void
    {
        $labels = [
            'name'                  => __('Điểm đến', 'jankx'),
            'singular_name'         => __('Điểm đến', 'jankx'),
            'menu_name'             => __('Điểm đến', 'jankx'),
            'add_new'               => __('Thêm điểm đến', 'jankx'),
            'add_new_item'          => __('Thêm điểm đến mới', 'jankx'),
            'edit_item'             => __('Sửa điểm đến', 'jankx'),
            'new_item'              => __('Điểm đến mới', 'jankx'),
            'view_item'             => __('Xem điểm đến', 'jankx'),
            'view_items'            => __('Xem các điểm đến', 'jankx'),
            'search_items'          => __('Tìm điểm đến', 'jankx'),
            'not_found'             => __('Không tìm thấy điểm đến nào', 'jankx'),
            'not_found_in_trash'    => __('Không có điểm đến nào trong thùng rác', 'jankx'),
            'all_items'             => __('Tất cả điểm đến', 'jankx'),
            'archives'              => __('Lưu trữ điểm đến', 'jankx'),
            'featured_image'        => __('Ảnh đại diện điểm đến', 'jankx'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels'       => $labels,
            'public'       => true,
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-location-alt',
            'menu_position' => 21,
            'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
            'has_archive'  => 'destinations',
            'rewrite'      => ['slug' => 'diem-den', 'with_front' => false],
            'show_in_menu' => true,
        ]);
    }
}
