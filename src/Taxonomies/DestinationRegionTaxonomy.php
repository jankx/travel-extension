<?php

namespace Jankx\Extensions\Travel\Taxonomies;

use Jankx\Extensions\Travel\PostTypes\TourPostType;
use Jankx\Extensions\Travel\PostTypes\DestinationPostType;

/**
 * Registers the "destination_region" taxonomy (e.g. Miền Bắc, Miền Trung, Miền Nam, Đông Nam Á...).
 * Shared between Tour and Destination so a tour can be filtered/linked by region too.
 */
class DestinationRegionTaxonomy
{
    const TAXONOMY = 'destination_region';

    public function register(): void
    {
        add_action('init', [$this, 'register_taxonomy']);
    }

    public function register_taxonomy(): void
    {
        register_taxonomy(self::TAXONOMY, [
            DestinationPostType::POST_TYPE,
            TourPostType::POST_TYPE,
        ], [
            'labels' => [
                'name'          => __('Khu vực', 'jankx'),
                'singular_name' => __('Khu vực', 'jankx'),
                'search_items'  => __('Tìm khu vực', 'jankx'),
                'all_items'     => __('Tất cả khu vực', 'jankx'),
                'edit_item'     => __('Sửa khu vực', 'jankx'),
                'add_new_item'  => __('Thêm khu vực mới', 'jankx'),
                'menu_name'     => __('Khu vực', 'jankx'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'khu-vuc'],
        ]);
    }
}
