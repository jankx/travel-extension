(function ($) {
    'use strict';

    $(function () {
        var frame;
        var $select = $('#jankx-destination-gallery-select');
        var $preview = $('#jankx-destination-gallery-preview');
        var $input = $('#destination_gallery_ids');

        if (!$select.length) {
            return;
        }

        function getIds() {
            var val = $input.val();
            return val ? val.split(',').filter(Boolean) : [];
        }

        function setIds(ids) {
            $input.val(ids.join(','));
        }

        $select.on('click', function (e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: 'Chọn ảnh cho thư viện điểm đến',
                button: { text: 'Thêm vào thư viện' },
                multiple: true,
            });
            frame.on('select', function () {
                var selection = frame.state().get('selection');
                var ids = getIds();
                selection.each(function (attachment) {
                    var id = String(attachment.id);
                    if (ids.indexOf(id) === -1) {
                        ids.push(id);
                        var thumb = attachment.attributes.sizes && attachment.attributes.sizes.thumbnail
                            ? attachment.attributes.sizes.thumbnail.url
                            : attachment.attributes.url;
                        $preview.append(
                            '<span class="jankx-gallery-thumb" data-id="' + id + '">' +
                            '<img src="' + thumb + '" alt="" />' +
                            '<button type="button" class="jankx-gallery-remove">&times;</button>' +
                            '</span>'
                        );
                    }
                });
                setIds(ids);
            });
            frame.open();
        });

        $preview.on('click', '.jankx-gallery-remove', function () {
            var $thumb = $(this).closest('.jankx-gallery-thumb');
            var id = String($thumb.data('id'));
            var ids = getIds().filter(function (existing) {
                return existing !== id;
            });
            setIds(ids);
            $thumb.remove();
        });
    });
})(jQuery);
