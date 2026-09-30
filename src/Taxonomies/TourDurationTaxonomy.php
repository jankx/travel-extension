<?php

namespace Jankx\Extensions\Travel\Taxonomies;

use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Registers the "tour_duration" taxonomy (Thời gian) used to group tours by
 * trip length, e.g. "Tour trong ngày", "Tour 2 ngày 1 đêm", "Tour 6 ngày 6 đêm".
 *
 * The archive URL is /thoi-gian/<slug>/.
 */
class TourDurationTaxonomy
{
    const TAXONOMY = 'tour_duration';

    public function register(): void
    {
        add_action('init', [$this, 'register_taxonomy']);
    }

    public function register_taxonomy(): void
    {
        register_taxonomy(self::TAXONOMY, [TourPostType::POST_TYPE], [
            'labels' => [
                'name'          => __('Thời gian', 'jankx'),
                'singular_name' => __('Thời gian', 'jankx'),
                'search_items'  => __('Tìm thời gian', 'jankx'),
                'all_items'     => __('Tất cả thời gian', 'jankx'),
                'edit_item'     => __('Sửa thời gian', 'jankx'),
                'add_new_item'  => __('Thêm thời gian mới', 'jankx'),
                'menu_name'     => __('Thời gian', 'jankx'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'thoi-gian'],
        ]);
    }
}
