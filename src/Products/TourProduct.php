<?php

namespace Jankx\Extensions\Travel\Products;

use Jankx\Extensions\Ecommerce\Abstracts\AbstractProduct;
use Jankx\Extensions\Travel\PostTypes\TourPostType;

class TourProduct extends AbstractProduct
{
    const PRICE_META_KEY            = '_jankx_price';
    const REGULAR_PRICE_META_KEY    = '_jankx_regular_price';
    const SALE_PRICE_META_KEY       = '_jankx_sale_price';
    const LEGACY_PRICE_META_KEYS    = ['_tour_price'];
    const LEGACY_REGULAR_PRICE_META_KEYS = ['_tour_regular_price'];
    const LEGACY_SALE_PRICE_META_KEYS    = ['_tour_sale_price'];

    public function getProductType(): string
    {
        return TourPostType::POST_TYPE;
    }
}
