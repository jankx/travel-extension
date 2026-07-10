<?php
/** @var int[] $gallery_ids */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="jankx-destination-gallery-field">
    <input type="hidden" id="destination_gallery_ids" name="destination_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>" />
    <div id="jankx-destination-gallery-preview" class="jankx-destination-gallery-preview">
        <?php foreach ($gallery_ids as $id) :
            $thumb = wp_get_attachment_image_src($id, 'thumbnail');
            if (!$thumb) {
                continue;
            }
            ?>
            <span class="jankx-gallery-thumb" data-id="<?php echo esc_attr($id); ?>">
                <img src="<?php echo esc_url($thumb[0]); ?>" alt="" />
                <button type="button" class="jankx-gallery-remove">&times;</button>
            </span>
        <?php endforeach; ?>
    </div>
    <p>
        <button type="button" class="button" id="jankx-destination-gallery-select">
            <?php esc_html_e('Chọn ảnh từ thư viện', 'jankx'); ?>
        </button>
    </p>
</div>
<style>
    .jankx-destination-gallery-preview { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
    .jankx-gallery-thumb { position:relative; width:80px; height:80px; border-radius:4px; overflow:hidden; border:1px solid #dcdcde; }
    .jankx-gallery-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .jankx-gallery-remove { position:absolute; top:0; right:0; background:#b32d2e; color:#fff; border:0; width:20px; height:20px; line-height:1; cursor:pointer; }
</style>
