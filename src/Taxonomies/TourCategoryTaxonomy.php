<?php

namespace Jankx\Extensions\Travel\Taxonomies;

use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Registers the "tour_category" taxonomy (e.g. Trong nước, Nước ngoài, Nghỉ dưỡng, Mạo hiểm).
 */
class TourCategoryTaxonomy
{
    const TAXONOMY = 'tour_category';

    public function register(): void
    {
        add_action('init', [$this, 'register_taxonomy']);
    }

    public function register_taxonomy(): void
    {
        register_taxonomy(self::TAXONOMY, [TourPostType::POST_TYPE], [
            'labels' => [
                'name'          => __('Loại tour', 'jankx'),
                'singular_name' => __('Loại tour', 'jankx'),
                'search_items'  => __('Tìm loại tour', 'jankx'),
                'all_items'     => __('Tất cả loại tour', 'jankx'),
                'edit_item'     => __('Sửa loại tour', 'jankx'),
                'add_new_item'  => __('Thêm loại tour mới', 'jankx'),
                'menu_name'     => __('Loại tour', 'jankx'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'loai-tour'],
        ]);
    }
}
