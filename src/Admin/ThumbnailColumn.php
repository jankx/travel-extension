<?php
namespace Jankx\Extensions\Travel\Admin;

class ThumbnailColumn
{
    public function register(): void
    {
        add_filter('manage_tour_posts_columns', [$this, 'addThumbnailColumn']);
        add_action('manage_tour_posts_custom_column', [$this, 'renderThumbnailColumn'], 10, 2);
        add_action('admin_head', [$this, 'addStyles']);
    }

    public function addThumbnailColumn(array $columns): array
    {
        $newColumns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $newColumns['thumbnail'] = __('Ảnh', 'jankx');
            }
            $newColumns[$key] = $value;
        }
        return $newColumns;
    }

    public function renderThumbnailColumn(string $column, int $postId): void
    {
        if ($column !== 'thumbnail') {
            return;
        }
        if (has_post_thumbnail($postId)) {
            echo get_the_post_thumbnail($postId, [60, 60], ['style' => 'border-radius:4px;']);
        } else {
            echo '<span style="color:#ccc;">—</span>';
        }
    }

    public function addStyles(): void
    {
        global $pagenow, $post_type;
        if ($pagenow !== 'edit.php' || $post_type !== 'tour') {
            return;
        }
        echo '<style>.wp-list-table .column-thumbnail img { width: 60px !important; height: 60px !important; }</style>';
    }
}
