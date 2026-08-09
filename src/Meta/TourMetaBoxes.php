<?php

namespace Jankx\Extensions\Travel\Meta;

use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Admin meta boxes for the "tour" CPT: pricing, duration, departure dates
 * (repeater), day-by-day itinerary (repeater), includes/excludes.
 */
class TourMetaBoxes
{
    const NONCE_ACTION = 'jankx_travel_tour_meta';
    const NONCE_NAME = 'jankx_travel_tour_meta_nonce';

    public function register(): void
    {
        add_action('init', [$this, 'register_meta']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . TourPostType::POST_TYPE, [$this, 'save']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /** Register all tour post meta for REST API & block editor visibility. */
    public function register_meta(): void
    {
        $string_keys = [
            '_tour_price' => 'Giá tour (VNĐ)',
            '_tour_price_is_from' => 'Giá là giá khởi điểm (0/1)',
            '_tour_meeting_point' => 'Địa điểm xuất phát / tập kết',
            '_tour_departure_type' => 'Loại khởi hành: tu_tuc hoặc co_dinh',
        ];
        foreach ($string_keys as $key => $desc) {
            register_meta('post', $key, [
                'object_subtype' => TourPostType::POST_TYPE,
                'type' => 'string',
                'description' => $desc,
                'single' => true,
                'default' => '',
                'show_in_rest' => true,
                'auth_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }

        $int_keys = [
            '_tour_duration_days' => 'Số ngày',
            '_tour_duration_nights' => 'Số đêm',
            '_tour_max_guests' => 'Số khách tối đa',
            '_tour_destination_id' => 'ID điểm đến liên kết',
            '_tour_review_count' => 'Tổng số đánh giá',
        ];
        foreach ($int_keys as $key => $desc) {
            register_meta('post', $key, [
                'object_subtype' => TourPostType::POST_TYPE,
                'type' => 'integer',
                'description' => $desc,
                'single' => true,
                'default' => 0,
                'show_in_rest' => true,
                'sanitize_callback' => 'absint',
                'auth_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }

        register_meta('post', '_tour_rating', [
            'object_subtype' => TourPostType::POST_TYPE,
            'type' => 'number',
            'description' => 'Điểm đánh giá trung bình (0–5)',
            'single' => true,
            'default' => 0,
            'show_in_rest' => true,
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);

        // Array types – must supply items schema for REST.
        $array_keys = [
            '_tour_departures' => 'Lịch khởi hành (repeater)',
            '_tour_itinerary' => 'Lịch trình chi tiết (repeater)',
            '_tour_highlights' => 'Điểm nổi bật của tour',
            '_tour_service_notes' => 'Ghi chú dịch vụ (sidebar)',
        ];
        foreach ($array_keys as $key => $desc) {
            register_meta('post', $key, [
                'object_subtype' => TourPostType::POST_TYPE,
                'type' => 'array',
                'description' => $desc,
                'single' => true,
                'default' => [],
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                    ],
                ],
                'auth_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }

        // Includes / excludes stored as plain text (newline-separated).
        foreach (['_tour_includes' => 'Bao gồm', '_tour_excludes' => 'Không bao gồm'] as $key => $desc) {
            register_meta('post', $key, [
                'object_subtype' => TourPostType::POST_TYPE,
                'type' => 'string',
                'description' => $desc,
                'single' => true,
                'default' => '',
                'show_in_rest' => true,
                'auth_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }
    }

    public function enqueue_assets(string $hook): void
    {
        global $post_type;
        if ($post_type !== TourPostType::POST_TYPE) {
            return;
        }
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        wp_enqueue_style(
            'jankx-travel-admin',
            get_template_directory_uri() . '/extensions/travel/assets/admin.css',
            [],
            '1.0.0'
        );
        wp_enqueue_script(
            'jankx-travel-repeater',
            get_template_directory_uri() . '/extensions/travel/assets/repeater.js',
            [],
            '1.0.0',
            true
        );
    }

    public function add_meta_boxes(): void
    {
        add_meta_box(
            'jankx_tour_pricing',
            __('Giá & Thông tin chung', 'jankx'),
            [$this, 'render_pricing_box'],
            TourPostType::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'jankx_tour_departures',
            __('Lịch khởi hành', 'jankx'),
            [$this, 'render_departures_box'],
            TourPostType::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'jankx_tour_itinerary',
            __('Lịch trình chi tiết', 'jankx'),
            [$this, 'render_itinerary_box'],
            TourPostType::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'jankx_tour_includes',
            __('Bao gồm / Không bao gồm', 'jankx'),
            [$this, 'render_includes_box'],
            TourPostType::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'jankx_tour_destination',
            __('Điểm đến & Đánh giá', 'jankx'),
            [$this, 'render_destination_box'],
            TourPostType::POST_TYPE,
            'side',
            'default'
        );

        add_meta_box(
            'jankx_tour_highlights',
            __('Điểm nổi bật', 'jankx'),
            [$this, 'render_highlights_box'],
            TourPostType::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'jankx_tour_extra',
            __('Thông tin xuất phát & Ghi chú', 'jankx'),
            [$this, 'render_extra_box'],
            TourPostType::POST_TYPE,
            'side',
            'default'
        );
    }

    protected function nonce_field(): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
    }

    public function render_pricing_box(\WP_Post $post): void
    {
        $this->nonce_field();
        $price = get_post_meta($post->ID, '_tour_price', true);
        $price_from = get_post_meta($post->ID, '_tour_price_is_from', true);
        $days = get_post_meta($post->ID, '_tour_duration_days', true);
        $nights = get_post_meta($post->ID, '_tour_duration_nights', true);
        $max_guests = get_post_meta($post->ID, '_tour_max_guests', true);
        include __DIR__ . '/views/tour-pricing.php';
    }

    public function render_departures_box(\WP_Post $post): void
    {
        $departures = get_post_meta($post->ID, '_tour_departures', true);
        $departures = is_array($departures) ? $departures : [];
        include __DIR__ . '/views/tour-departures.php';
    }

    public function render_itinerary_box(\WP_Post $post): void
    {
        $itinerary = get_post_meta($post->ID, '_tour_itinerary', true);
        $itinerary = is_array($itinerary) ? $itinerary : [];
        include __DIR__ . '/views/tour-itinerary.php';
    }

    public function render_includes_box(\WP_Post $post): void
    {
        $includes = get_post_meta($post->ID, '_tour_includes', true);
        $excludes = get_post_meta($post->ID, '_tour_excludes', true);
        include __DIR__ . '/views/tour-includes.php';
    }

    public function render_destination_box(\WP_Post $post): void
    {
        $selected = (int) get_post_meta($post->ID, '_tour_destination_id', true);
        $rating = get_post_meta($post->ID, '_tour_rating', true);
        $review_count = (int) get_post_meta($post->ID, '_tour_review_count', true);
        $destinations = get_posts([
            'post_type' => 'destination',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        include __DIR__ . '/views/tour-destination.php';
    }

    public function render_highlights_box(\WP_Post $post): void
    {
        $highlights = get_post_meta($post->ID, '_tour_highlights', true);
        $highlights = is_array($highlights) ? $highlights : [];
        include __DIR__ . '/views/tour-highlights.php';
    }

    public function render_extra_box(\WP_Post $post): void
    {
        $meeting_point = get_post_meta($post->ID, '_tour_meeting_point', true);
        $departure_type = get_post_meta($post->ID, '_tour_departure_type', true) ?: 'tu_tuc';
        $service_notes = get_post_meta($post->ID, '_tour_service_notes', true);
        $service_notes = is_array($service_notes) ? $service_notes : [];
        include __DIR__ . '/views/tour-extra.php';
    }

    public function save(int $post_id): void
    {
        if (
            !isset($_POST[self::NONCE_NAME]) ||
            !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)
        ) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Pricing & general info
        update_post_meta($post_id, '_tour_price', sanitize_text_field($_POST['tour_price'] ?? ''));
        update_post_meta($post_id, '_tour_price_is_from', !empty($_POST['tour_price_is_from']) ? 1 : 0);
        update_post_meta($post_id, '_tour_duration_days', absint($_POST['tour_duration_days'] ?? 0));
        update_post_meta($post_id, '_tour_duration_nights', absint($_POST['tour_duration_nights'] ?? 0));
        update_post_meta($post_id, '_tour_max_guests', absint($_POST['tour_max_guests'] ?? 0));

        // Destination & rating
        update_post_meta($post_id, '_tour_destination_id', absint($_POST['tour_destination_id'] ?? 0));
        $rating = isset($_POST['tour_rating']) ? (float) $_POST['tour_rating'] : 0;
        update_post_meta($post_id, '_tour_rating', max(0, min(5, $rating)));
        update_post_meta($post_id, '_tour_review_count', absint($_POST['tour_review_count'] ?? 0));

        // Includes / excludes (one item per line)
        update_post_meta($post_id, '_tour_includes', sanitize_textarea_field($_POST['tour_includes'] ?? ''));
        update_post_meta($post_id, '_tour_excludes', sanitize_textarea_field($_POST['tour_excludes'] ?? ''));

        // Meeting point & departure type
        update_post_meta($post_id, '_tour_meeting_point', sanitize_text_field($_POST['tour_meeting_point'] ?? ''));
        update_post_meta($post_id, '_tour_departure_type', sanitize_key($_POST['tour_departure_type'] ?? 'tu_tuc'));

        // Highlights repeater
        $highlights = [];
        if (!empty($_POST['tour_highlight']) && is_array($_POST['tour_highlight'])) {
            foreach ($_POST['tour_highlight'] as $item) {
                $item = sanitize_text_field($item);
                if ($item !== '') {
                    $highlights[] = $item;
                }
            }
        }
        update_post_meta($post_id, '_tour_highlights', $highlights);

        // Service notes repeater
        $service_notes = [];
        if (!empty($_POST['tour_service_note']) && is_array($_POST['tour_service_note'])) {
            foreach ($_POST['tour_service_note'] as $item) {
                $item = sanitize_text_field($item);
                if ($item !== '') {
                    $service_notes[] = $item;
                }
            }
        }
        update_post_meta($post_id, '_tour_service_notes', $service_notes);

        // Departures repeater
        $departures = [];
        if (!empty($_POST['tour_departure_date']) && is_array($_POST['tour_departure_date'])) {
            foreach ($_POST['tour_departure_date'] as $i => $date) {
                $date = sanitize_text_field($date);
                if (empty($date)) {
                    continue;
                }
                $departures[] = [
                    'date' => $date,
                    'slots' => absint($_POST['tour_departure_slots'][$i] ?? 0),
                    'note' => sanitize_text_field($_POST['tour_departure_note'][$i] ?? ''),
                ];
            }
        }
        update_post_meta($post_id, '_tour_departures', $departures);

        // Itinerary repeater
        $itinerary = [];
        if (!empty($_POST['tour_itinerary_title']) && is_array($_POST['tour_itinerary_title'])) {
            foreach ($_POST['tour_itinerary_title'] as $i => $title) {
                $title = sanitize_text_field($title);
                if (empty($title)) {
                    continue;
                }
                $itinerary[] = [
                    'title' => $title,
                    'description' => sanitize_textarea_field($_POST['tour_itinerary_description'][$i] ?? ''),
                ];
            }
        }
        update_post_meta($post_id, '_tour_itinerary', $itinerary);
    }
}
