<?php
/**
 * Server-side render for jankx-travel/itinerary.
 * Uses native <details>/<summary> accordion — no JS required.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = $block->context['postId'] ?? get_the_ID();

if (($block->context['postType'] ?? get_post_type($post_id)) !== 'tour') {
    return;
}

$itinerary = get_post_meta($post_id, '_tour_itinerary', true);
$itinerary = is_array($itinerary) ? $itinerary : [];

if (empty($itinerary)) {
    return;
}

$open_first = !empty($attributes['openFirstByDefault']);
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'jankx-itinerary']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ($itinerary as $index => $day) : ?>
        <details class="jankx-itinerary__day" <?php echo ($open_first && $index === 0) ? 'open' : ''; ?>>
            <summary class="jankx-itinerary__day-title">
                <?php echo esc_html($day['title'] ?? ''); ?>
            </summary>
            <div class="jankx-itinerary__day-description">
                <?php echo wp_kses_post(wpautop($day['description'] ?? '')); ?>
            </div>
        </details>
    <?php endforeach; ?>
</div>
