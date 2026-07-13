<?php

namespace Jankx\Extensions\Travel\PostTypes;

/**
 * Registers the "tour" Custom Post Type.
 */
class TourPostType
{
    const POST_TYPE = 'tour';

    public function register(): void
    {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type(): void
    {
        $labels = [
            'name'                  => __('Tour', 'jankx'),
            'singular_name'         => __('Tour', 'jankx'),
            'menu_name'             => __('Tour du lịch', 'jankx'),
            'add_new'               => __('Thêm tour', 'jankx'),
            'add_new_item'          => __('Thêm tour mới', 'jankx'),
            'edit_item'             => __('Sửa tour', 'jankx'),
            'new_item'              => __('Tour mới', 'jankx'),
            'view_item'             => __('Xem tour', 'jankx'),
            'view_items'            => __('Xem các tour', 'jankx'),
            'search_items'          => __('Tìm tour', 'jankx'),
            'not_found'             => __('Không tìm thấy tour nào', 'jankx'),
            'not_found_in_trash'    => __('Không có tour nào trong thùng rác', 'jankx'),
            'all_items'             => __('Tất cả tour', 'jankx'),
            'archives'              => __('Lưu trữ tour', 'jankx'),
            'featured_image'        => __('Ảnh đại diện tour', 'jankx'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels'       => $labels,
            'public'       => true,
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-airplane',
            'menu_position' => 20,
            'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
            'has_archive'  => 'tours',
            'rewrite'      => ['slug' => 'tour', 'with_front' => false],
            'show_in_menu' => true,
        ]);

        // Allow this CPT to use the theme's block templates (templates/single-tour.html, archive-tour.html)
        // instead of falling back to the legacy PageRenderer.
        add_post_type_support(self::POST_TYPE, 'block-templates');
    }
}
