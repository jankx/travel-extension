<?php
/**
 * Seed script – Bích Động kayaking tour
 *
 * Run via WP-CLI:
 *   studio wp eval-file wp-content/themes/nibitour/extensions/travel/seed-bich-dong-tour.php
 *
 * Creates (or updates, if slug already exists):
 *  - 1 destination: Bích Động / Tràng An (Ninh Bình)
 *  - 1 tour_category term: Trải nghiệm – Sông nước
 *  - 1 tour_category term: Tham quan
 *  - 1 tour post with all meta extracted from the design mockup
 */

if (!defined('ABSPATH')) {
    exit;
}

// ── helpers ────────────────────────────────────────────────────────────────

function _seed_log(string $msg): void
{
    echo '[seed] ' . $msg . PHP_EOL;
}

function _seed_term(string $name, string $taxonomy, string $slug, ?int $parent = 0): int
{
    $existing = get_term_by('slug', $slug, $taxonomy);
    if ($existing) {
        _seed_log("Term already exists: {$taxonomy}/{$slug} (#{$existing->term_id})");
        return (int) $existing->term_id;
    }
    $result = wp_insert_term($name, $taxonomy, [
        'slug' => $slug,
        'parent' => $parent ?? 0,
    ]);
    if (is_wp_error($result)) {
        _seed_log("ERROR inserting term {$name}: " . $result->get_error_message());
        return 0;
    }
    _seed_log("Created term: {$name} (#{$result['term_id']})");
    return (int) $result['term_id'];
}

function _seed_post(array $args): int
{
    $existing = get_page_by_path($args['post_name'], OBJECT, $args['post_type']);
    if ($existing) {
        _seed_log("Post already exists: {$args['post_name']} (#{$existing->ID})");
        return (int) $existing->ID;
    }
    $id = wp_insert_post($args, true);
    if (is_wp_error($id)) {
        _seed_log("ERROR inserting post: " . $id->get_error_message());
        return 0;
    }
    _seed_log("Created post: {$args['post_title']} (#{$id})");
    return $id;
}

// ── 1. Destination: Bích Động – Tràng An (Ninh Bình) ─────────────────────

$destination_id = _seed_post([
    'post_type' => 'destination',
    'post_status' => 'publish',
    'post_title' => 'Bích Động – Tràng An, Ninh Bình',
    'post_name' => 'bich-dong-trang-an-ninh-binh',
    'post_content' => 'Bích Động là một quần thể hang động đá vôi nằm trong Khu du lịch Tràng An – Di sản thiên nhiên thế giới tại Ninh Bình. Nơi đây nổi tiếng với hệ thống sông ngòi uốn lượn giữa những dãy núi đá vôi hùng vĩ, là điểm đến lý tưởng cho các tour chèo thuyền, khám phá hang động và trải nghiệm thiên nhiên.',
    'post_excerpt' => 'Quần thể hang động và sông nước tuyệt đẹp tại Di sản thiên nhiên thế giới Tràng An, Ninh Bình.',
]);

if ($destination_id) {
    update_post_meta($destination_id, '_destination_region', 'Ninh Bình');
    update_post_meta($destination_id, '_destination_country', 'Việt Nam');
    update_post_meta($destination_id, '_destination_coordinates', '20.2289° B, 105.9244° Đ');
    _seed_log("Destination meta saved.");
}

// ── 2. Taxonomy terms ─────────────────────────────────────────────────────

$cat_trai_nghiem = _seed_term(
    'Trải nghiệm – Sông nước',
    'tour_category',
    'trai-nghiem-song-nuoc'
);

$cat_tham_quan = _seed_term(
    'Tham quan',
    'tour_category',
    'tham-quan'
);

// Region taxonomy (destination_region)
$region_ninh_binh = _seed_term(
    'Ninh Bình',
    'destination_region',
    'ninh-binh'
);

// ── 3. The tour post ──────────────────────────────────────────────────────

$tour_content = <<<HTML
<!-- wp:paragraph -->
<p>Đặt chân vào Hội An là bắt đầu bước vào một không gian du lịch thú vị đầy kỳ diệu với vô vàn cơ hội tham quan, thưởng thức ẩm thực và mua sắm. Quần đảo Cù Lao Chàm ngoài khơi của tỉnh Quảng Nam và Hội An mang lại cho bạn nhiều điều thú vị. <em>Coriodney</em> sẽ giới thiệu các điểm nổi tiếng của Hội An đến bạn: "Đi vào Coriodney Hội An" sẽ rất thú vị để bắt đầu đi khám phá từng di tích lịch sử nhiều từ thời phong kiến, ngắm nhìn trời và thưởng thức ẩm thực vừa thơm đặc trưng quán nghen mà xem thêm ngay.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Tour Ngày Ninh Bình Tuyệt Nhất: Hào Lư, Tràng An, Tam Cốc, Hang Múa</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Phát hiện sự lựa chọn từ tất cả người xuất Viowander Ninh Bình.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Đến với Ninh Bình Viowander Hội An là khoảnh khắc đỉnh cao được biết đến thú vị hơn với Viowanders Ninh Bình từ thị trấn kỳ 70 tốt nhất của tổng quan, điểm đến chứ như các bạn bản trải thành phố chính phủ. Tại đây, gần các trò chơi giải trí đến tổ hợp, các trò chơi giải trí đến đón, trải nghiệm khi sẽ các địa điểm lưu địa hướng tự các trò chơi dây. Thể giàu ngoan: Vừng ngay, đủng đỉnh, sớm chờ xế chiều. Phụ thu phụ giá xế. Còn lắc để ngọc hàng ngàn giải thưởng.</p>
<!-- /wp:paragraph -->
HTML;

$tour_id = _seed_post([
    'post_type' => 'tour',
    'post_status' => 'publish',
    'post_title' => 'Chèo thuyền trải nghiệm trên sông trên sông khám phá Bích Động',
    'post_name' => 'cheo-thuyen-trai-nghiem-song-kham-pha-bich-dong',
    'post_content' => $tour_content,
    'post_excerpt' => 'Khám phá vẻ đẹp huyền bí của sông Bích Động và quần thể Tràng An bằng chuyến chèo thuyền trải nghiệm không thể bỏ lỡ khi đến Ninh Bình.',
]);

if (!$tour_id) {
    _seed_log("Aborting – could not create tour.");
    return;
}

// ── 3a. Pricing & duration ────────────────────────────────────────────────

update_post_meta($tour_id, '_tour_price', 900000);
update_post_meta($tour_id, '_tour_price_is_from', 0);       // exact price
update_post_meta($tour_id, '_tour_duration_days', 3);
update_post_meta($tour_id, '_tour_duration_nights', 2);
update_post_meta($tour_id, '_tour_max_guests', 30);

// ── 3b. Rating & review count ─────────────────────────────────────────────
// _tour_review_count is an extra meta not yet in TourMetaBoxes; we register
// it properly below via register_meta() and write it here.

update_post_meta($tour_id, '_tour_rating', 4.6);
update_post_meta($tour_id, '_tour_review_count', 30000);

// ── 3c. Destination & meeting point ──────────────────────────────────────

update_post_meta($tour_id, '_tour_destination_id', $destination_id);
// Meeting-point / departure is a new meta; registered below.
update_post_meta($tour_id, '_tour_meeting_point', 'Vinh Hạ Long, Hội An – đường Yết Kiêu, Giao Phong, Hội An, Thăng Bình, Quảng Nam');

// ── 3d. Departure type (tự túc / cố định) ────────────────────────────────

update_post_meta($tour_id, '_tour_departure_type', 'tu_tuc'); // "tự túc" = flexible

// ── 3e. Departure dates ───────────────────────────────────────────────────

$departures = [
    [
        'date' => '2026-09-01',
        'slots' => 20,
        'note' => 'Ngày đi: Hà Nội – Hè em tất em',
    ],
    [
        'date' => '2026-09-15',
        'slots' => 20,
        'note' => 'Ngày đi: Hà Nội',
    ],
    [
        'date' => '2026-10-01',
        'slots' => 25,
        'note' => 'Ngày đi: Hà Nội – Hềm tất niên',
    ],
    [
        'date' => '2026-10-15',
        'slots' => 25,
        'note' => '',
    ],
    [
        'date' => '2026-11-01',
        'slots' => 30,
        'note' => '',
    ],
    [
        'date' => '2026-11-15',
        'slots' => 30,
        'note' => '',
    ],
    [
        'date' => '2026-12-01',
        'slots' => 30,
        'note' => 'Dịp lễ - giới hạn số lượng',
    ],
];
update_post_meta($tour_id, '_tour_departures', $departures);

// ── 3f. Itinerary ─────────────────────────────────────────────────────────

$itinerary = [
    [
        'title' => 'Ngày 1 – Hà Nội → Ninh Bình',
        'description' => 'Xuất phát từ Hà Nội, di chuyển về Ninh Bình (khoảng 90 km). Nhận phòng khách sạn, nghỉ ngơi buổi chiều, khám phá chợ đêm Ninh Bình. Ăn tối với các đặc sản địa phương.',
    ],
    [
        'title' => 'Ngày 2 – Tràng An – Bích Động – Hang Múa',
        'description' => 'Sáng: chèo thuyền Tràng An khám phá các hang động và sông ngòi tuyệt đẹp. Chiều: leo Hang Múa ngắm toàn cảnh đồng bằng và núi đá vôi. Tối: thưởng thức bữa tối ngoài trời.',
    ],
    [
        'title' => 'Ngày 3 – Tam Cốc – Hào Lư → Hà Nội',
        'description' => 'Sáng sớm: chèo thuyền Tam Cốc qua ba hang động tự nhiên. Thăm Cố đô Hoa Lư, kinh đô đầu tiên của Việt Nam. Buổi chiều trở về Hà Nội.',
    ],
];
update_post_meta($tour_id, '_tour_itinerary', $itinerary);

// ── 3g. Includes / Excludes ───────────────────────────────────────────────

$includes = implode("\n", [
    'Xe đưa đón từ Hà Nội – Ninh Bình khứ hồi',
    'Phương tiện tham quan tại địa phương',
    'Thuyền chèo Tràng An và Tam Cốc (phí thuyền)',
    'Vé tham quan các điểm trong chương trình',
    'Khách sạn 2 đêm (2 người/phòng)',
    'Bữa ăn theo chương trình (2 bữa sáng, 2 bữa trưa, 1 bữa tối)',
    'Hướng dẫn viên tiếng Việt nhiệt tình',
    'Bảo hiểm du lịch',
    'Nước uống trên xe',
]);

$excludes = implode("\n", [
    'Vé máy bay (nếu có)',
    'Chi phí cá nhân (mua sắm, ăn uống ngoài chương trình)',
    'Tiền tip cho hướng dẫn viên và tài xế',
    'Đồ uống trong bữa ăn',
    'Dịch vụ massage, spa',
]);

update_post_meta($tour_id, '_tour_includes', $includes);
update_post_meta($tour_id, '_tour_excludes', $excludes);

// ── 3h. Highlights (new meta) ────────────────────────────────────────────

$highlights = [
    'Khám phá di đặc Coniodney Honeymoon với trải nghiệm trên bờ cối phần bình',
    'Chèo thuyền tận hưởng không khí trong lành của sông ngòi Tràng An bằng thuyền nan',
    'Trải nghiệm du lịch khác lạ khác lạ và văn hoá văn bản truyền thống trong nhóm',
    'Tìm hiểu về lịch sử quan trọng đang quan lịch sử du lịch thí nghiệm của nhóm à quan',
];
update_post_meta($tour_id, '_tour_highlights', $highlights);

// ── 3i. Service note (sidebar info) ──────────────────────────────────────

$service_notes = [
    'Thời gian sử dụng: Ngày Đi – Hà Nội / Thế em – Tất em',
    'Khách hàng áp dụng: Trẻ em – Tất em',
    'Địa chỉ xuất phát: Vinh Hạ Long, Hội An – Đường Mỹ Chi Công, Giám Mục, Hội An, Thăng Bình, Quảng Nam',
    'Bao gồm: Vé tàu vào vào Coniodney Ninh Hội An và khoảng nhiều phong cách khác nhau',
    'Trong game đất trồng của Việt Nam và lịch sử thể thao của Việt Nam',
    'Trải nghiệm nhiều phong trào chuyên sâu tại bờ biển, Bờ Biển, Ấu trĩ điễn Đánh Giá Giao Thoa',
    'Thưởng thức nhiều trò chơi từ phong trào dân gian và nhiễu âm thanh thú vị',
];
update_post_meta($tour_id, '_tour_service_notes', $service_notes);

// ── 3j. Taxonomy assignments ──────────────────────────────────────────────

$cat_ids = array_filter([$cat_trai_nghiem, $cat_tham_quan]);
if ($cat_ids) {
    wp_set_post_terms($tour_id, $cat_ids, 'tour_category');
    _seed_log("Assigned " . count($cat_ids) . " tour_category term(s).");
}

if ($region_ninh_binh) {
    wp_set_post_terms($tour_id, [$region_ninh_binh], 'destination_region');
    _seed_log("Assigned destination_region: Ninh Bình.");
}

// ── 4. register_meta() for the new keys ──────────────────────────────────
// This ensures the fields appear in the REST API and are properly typed.

$new_metas = [
    '_tour_review_count' => [
        'type' => 'integer',
        'description' => 'Tổng số đánh giá của tour',
        'default' => 0,
    ],
    '_tour_meeting_point' => [
        'type' => 'string',
        'description' => 'Địa điểm xuất phát / tập kết của tour',
        'default' => '',
    ],
    '_tour_departure_type' => [
        'type' => 'string',
        'description' => 'Loại khởi hành: tu_tuc (tự túc) hoặc co_dinh (cố định)',
        'default' => 'tu_tuc',
    ],
    '_tour_highlights' => [
        'type' => 'array',
        'description' => 'Danh sách điểm nổi bật của tour',
        'default' => [],
    ],
    '_tour_service_notes' => [
        'type' => 'array',
        'description' => 'Ghi chú dịch vụ hiển thị trong sidebar',
        'default' => [],
    ],
];

foreach ($new_metas as $key => $schema) {
    register_meta('post', $key, [
        'object_subtype' => 'tour',
        'type' => $schema['type'],
        'description' => $schema['description'],
        'single' => true,
        'default' => $schema['default'],
        'show_in_rest' => true,
        'sanitize_callback' => $schema['type'] === 'integer' ? 'absint' : null,
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    _seed_log("Registered meta: {$key}");
}

// ── Done ──────────────────────────────────────────────────────────────────

_seed_log("✅ Seed complete!");
_seed_log("   Tour ID      : {$tour_id}");
_seed_log("   Destination  : {$destination_id}");
_seed_log("   Tour URL     : " . get_permalink($tour_id));
