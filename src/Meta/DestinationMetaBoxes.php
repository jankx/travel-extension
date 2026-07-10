<?php

namespace Jankx\Extensions\Travel\Meta;

use Jankx\Extensions\Travel\PostTypes\DestinationPostType;

/**
 * Admin meta box for the "destination" CPT: country, best season,
 * highlights, and a photo gallery.
 */
class DestinationMetaBoxes
{
    const NONCE_ACTION = 'jankx_travel_destination_meta';
    const NONCE_NAME   = 'jankx_travel_destination_meta_nonce';

    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . DestinationPostType::POST_TYPE, [$this, 'save']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(string $hook): void
    {
        global $post_type;
        if ($post_type !== DestinationPostType::POST_TYPE) {
            return;
        }
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style(
            'jankx-travel-admin',
            get_template_directory_uri() . '/extensions/travel/assets/admin.css',
            [],
            '1.0.0'
        );
        wp_enqueue_script(
            'jankx-travel-destination-gallery',
            get_template_directory_uri() . '/extensions/travel/assets/destination-gallery.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }

    public function add_meta_boxes(): void
    {
        add_meta_box(
            'jankx_destination_info',
            __('Thông tin điểm đến', 'jankx'),
            [$this, 'render_info_box'],
            DestinationPostType::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'jankx_destination_gallery',
            __('Thư viện ảnh', 'jankx'),
            [$this, 'render_gallery_box'],
            DestinationPostType::POST_TYPE,
            'normal',
            'default'
        );
    }

    public function render_info_box(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        $country    = get_post_meta($post->ID, '_destination_country', true);
        $best_season = get_post_meta($post->ID, '_destination_best_season', true);
        $highlights = get_post_meta($post->ID, '_destination_highlights', true);
        include __DIR__ . '/views/destination-info.php';
    }

    public function render_gallery_box(\WP_Post $post): void
    {
        $gallery_ids = get_post_meta($post->ID, '_destination_gallery', true);
        $gallery_ids = is_array($gallery_ids) ? $gallery_ids : [];
        include __DIR__ . '/views/destination-gallery.php';
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

        update_post_meta($post_id, '_destination_country', sanitize_text_field($_POST['destination_country'] ?? ''));
        update_post_meta($post_id, '_destination_best_season', sanitize_text_field($_POST['destination_best_season'] ?? ''));
        update_post_meta($post_id, '_destination_highlights', sanitize_textarea_field($_POST['destination_highlights'] ?? ''));

        $gallery_raw = sanitize_text_field($_POST['destination_gallery_ids'] ?? '');
        $gallery_ids = array_filter(array_map('absint', explode(',', $gallery_raw)));
        update_post_meta($post_id, '_destination_gallery', array_values($gallery_ids));
    }
}
