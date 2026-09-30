<?php

namespace Jankx\Extensions\Travel\Taxonomies;

use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Registers the "tour_tag" taxonomy (Trải nghiệm) used to tag tours by the kind
 * of experience they offer, e.g. "Trải nghiệm thiên nhiên", "Trải nghiệm văn hóa".
 *
 * Unlike `tour_category` / `tour_duration` this is a flat tag taxonomy, so a tour
 * can belong to several experience groups at once.
 *
 * The archive URL is /trai-nghiem/<slug>/.
 */
class TourTagTaxonomy
{
    const TAXONOMY = 'tour_tag';

    /**
     * Experience tags seeded on first run. slug => name.
     */
    private static array $defaultTerms = [
        'trai-nghiem-thien-nhien' => 'Trải nghiệm thiên nhiên',
        'trai-nghiem-van-hoa'      => 'Trải nghiệm văn hóa',
        'trai-nghiem-am-thuc'      => 'Trải nghiệm ẩm thực',
        'trai-nghiem-phieu-luu'    => 'Trải nghiệm phiêu lưu',
        'trai-nghiem-bien-dao'     => 'Trải nghiệm biển đảo',
        'trai-nghiem-thanh-pho'    => 'Trải nghiệm thành phố',
        'trai-nghiem-nuoc-mat'     => 'Trải nghiệm nước mát',
        'trai-nghiem-su-khoe'      => 'Trải nghiệm sức khỏe',
        'trai-nghiem-gia-dinh'     => 'Trải nghiệm gia đình',
        'trai-nghiem-sang-trong'   => 'Trải nghiệm sang trọng',
        'trai-nghiem-tiet-kiem'    => 'Trải nghiệm tiết kiệm',
    ];

    public function register(): void
    {
        add_action('init', [$this, 'register_taxonomy']);
    }

    public function register_taxonomy(): void
    {
        register_taxonomy(self::TAXONOMY, [TourPostType::POST_TYPE], [
            'labels' => [
                'name'          => __('Trải nghiệm', 'jankx'),
                'singular_name' => __('Trải nghiệm', 'jankx'),
                'search_items'  => __('Tìm trải nghiệm', 'jankx'),
                'popular_items' => __('Trải nghiệm phổ biến', 'jankx'),
                'all_items'     => __('Tất cả trải nghiệm', 'jankx'),
                'edit_item'     => __('Sửa trải nghiệm', 'jankx'),
                'add_new_item'  => __('Thêm trải nghiệm mới', 'jankx'),
                'new_item_name' => __('Tên trải nghiệm mới', 'jankx'),
                'menu_name'     => __('Trải nghiệm', 'jankx'),
                'not_found'     => __('Không tìm thấy trải nghiệm nào', 'jankx'),
            ],
            'hierarchical'      => false,
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'rewrite'           => ['slug' => 'trai-nghiem', 'with_front' => false],
        ]);

        add_action('init', [$this, 'seedDefaultTerms']);
    }

    /**
     * Create the built-in experience tags once, so editors can just pick them.
     */
    public function seedDefaultTerms(): void
    {
        foreach (self::$defaultTerms as $slug => $name) {
            if (!term_exists($slug, self::TAXONOMY)) {
                wp_insert_term($name, self::TAXONOMY, ['slug' => $slug]);
            }
        }
    }

    public static function getDefaultTerms(): array
    {
        return self::$defaultTerms;
    }
}
