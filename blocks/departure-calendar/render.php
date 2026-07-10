<?php
/**
 * Server-side render for jankx-travel/departure-calendar.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = $block->context['postId'] ?? get_the_ID();
$post_type = $block->context['postType'] ?? get_post_type($post_id);

$departures = [];

if ($post_type === 'tour' && $post_id) {
    $raw = get_post_meta($post_id, '_tour_departures', true);
    $departures = is_array($raw) ? $raw : [];
} else {
    // Not on a single tour (e.g. used on archive/home) — aggregate upcoming
    // departures across all tours.
    $tours = get_posts([
        'post_type'      => 'tour',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);
    foreach ($tours as $tour_id) {
        $raw = get_post_meta($tour_id, '_tour_departures', true);
        if (!is_array($raw)) {
            continue;
        }
        foreach ($raw as $row) {
            $row['tour_id'] = $tour_id;
            $departures[] = $row;
        }
    }
}

$today = current_time('Y-m-d');
$show_past = !empty($attributes['showPastDates']);

$departures = array_filter($departures, function ($row) use ($today, $show_past) {
    if ($show_past) {
        return true;
    }
    return !empty($row['date']) && $row['date'] >= $today;
});

usort($departures, function ($a, $b) {
    return strcmp($a['date'] ?? '', $b['date'] ?? '');
});

$limit = !empty($attributes['limit']) ? (int) $attributes['limit'] : 6;
$departures = array_slice($departures, 0, $limit);

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'jankx-departure-calendar']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if (empty($departures)) : ?>
        <p class="jankx-departure-calendar__empty"><?php esc_html_e('Chưa có lịch khởi hành sắp tới.', 'jankx'); ?></p>
    <?php else : ?>
        <ul class="jankx-departure-calendar__list">
            <?php foreach ($departures as $row) :
                $date = $row['date'] ?? '';
                $formatted = $date ? date_i18n(get_option('date_format'), strtotime($date)) : '';
                $slots = isset($row['slots']) ? (int) $row['slots'] : null;
                $note = $row['note'] ?? '';
                $tour_title = isset($row['tour_id']) ? get_the_title($row['tour_id']) : '';
                $tour_link = isset($row['tour_id']) ? get_permalink($row['tour_id']) : '';
                ?>
                <li class="jankx-departure-calendar__item">
                    <span class="jankx-departure-calendar__date"><?php echo esc_html($formatted); ?></span>
                    <?php if ($tour_title) : ?>
                        <a href="<?php echo esc_url($tour_link); ?>" class="jankx-departure-calendar__tour"><?php echo esc_html($tour_title); ?></a>
                    <?php endif; ?>
                    <?php if ($slots !== null) : ?>
                        <span class="jankx-departure-calendar__slots">
                            <?php echo esc_html(sprintf(_n('Còn %d chỗ', 'Còn %d chỗ', $slots, 'jankx'), $slots)); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($note) : ?>
                        <span class="jankx-departure-calendar__note"><?php echo esc_html($note); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
