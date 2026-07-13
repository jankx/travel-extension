<?php
/**
 * Server-side render for jankx-travel/tour-meta.
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

$price       = get_post_meta($post_id, '_tour_price', true);
$price_from  = (bool) get_post_meta($post_id, '_tour_price_is_from', true);
$days        = (int) get_post_meta($post_id, '_tour_duration_days', true);
$nights      = (int) get_post_meta($post_id, '_tour_duration_nights', true);
$rating      = (float) get_post_meta($post_id, '_tour_rating', true);

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'jankx-tour-meta']);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if (!empty($attributes['showPrice']) && $price !== '') : ?>
        <span class="jankx-tour-meta__price">
            <?php if ($price_from) : ?><span class="jankx-tour-meta__price-label"><?php esc_html_e('Từ', 'jankx'); ?></span><?php endif; ?>
            <?php echo esc_html(number_format((float) $price, 0, ',', '.')); ?>đ
        </span>
    <?php endif; ?>

    <?php if (!empty($attributes['showDuration']) && ($days || $nights)) : ?>
        <span class="jankx-tour-meta__duration">
            <?php echo esc_html(sprintf('%dN%dĐ', $days, $nights)); ?>
        </span>
    <?php endif; ?>

    <?php if (!empty($attributes['showRating']) && $rating > 0) : ?>
        <span class="jankx-tour-meta__rating" aria-label="<?php echo esc_attr(sprintf(__('%s trên 5 sao', 'jankx'), $rating)); ?>">
            <?php echo esc_html(number_format($rating, 1)); ?> ★
        </span>
    <?php endif; ?>
</div>
