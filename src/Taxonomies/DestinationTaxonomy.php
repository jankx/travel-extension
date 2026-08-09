<?php

namespace Jankx\Extensions\Travel\Taxonomies;

use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Registers the "destination" taxonomy (Điểm đến), e.g. Hà Nội, Đà Nẵng, Ninh Bình...
 * Shared by the tour, experience and place post types; the archive URL is /diem-den/<slug>/.
 */
class DestinationTaxonomy
{
    const TAXONOMY = 'destination';

    /** Post types that can be tagged with a destination. */
    const OBJECT_TYPES = [
        'tour',
        'experience',
        'place',
    ];

    public function register(): void
    {
        add_action('init', [$this, 'register_taxonomy']);
        // Link the taxonomy to post types registered after ours (experience/place
        // live in separate extensions that may load later).
        add_action('registered_post_type', [$this, 'register_for_object_type']);
    }

    public function register_taxonomy(): void
    {
        register_taxonomy(self::TAXONOMY, self::OBJECT_TYPES, [
            'labels' => [
                'name'          => __('Điểm đến', 'jankx'),
                'singular_name' => __('Điểm đến', 'jankx'),
                'search_items'  => __('Tìm điểm đến', 'jankx'),
                'all_items'     => __('Tất cả điểm đến', 'jankx'),
                'edit_item'     => __('Sửa điểm đến', 'jankx'),
                'add_new_item'  => __('Thêm điểm đến mới', 'jankx'),
                'menu_name'     => __('Điểm đến', 'jankx'),
            ],
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'diem-den'],
        ]);
    }

    /**
     * Attach the taxonomy to a post type that got registered after ours.
     */
    public function register_for_object_type(string $post_type): void
    {
        if (!in_array($post_type, self::OBJECT_TYPES, true)) {
            return;
        }
        if (post_type_exists($post_type)) {
            register_taxonomy_for_object_type(self::TAXONOMY, $post_type);
        }
    }
}
