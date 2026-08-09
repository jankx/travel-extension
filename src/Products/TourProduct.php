<?php

namespace Jankx\Extensions\Travel\Products;

use Jankx\Extensions\Ecommerce\Abstracts\AbstractProduct;
use Jankx\Extensions\Travel\PostTypes\TourPostType;

/**
 * Concrete product for the "tour" post type.
 *
 * Registers tours into the shared ecommerce flow provided by the
 * base-ecommerce extension (cart, checkout, payment, order).
 *
 * Prices are read from tour post meta:
 *   _tour_price         — selling price (per guest)
 *   _tour_regular_price — optional regular (compare-at) price
 *   _tour_sale_price    — optional sale price
 *
 * @package Jankx\Extensions\Travel
 */
class TourProduct extends AbstractProduct
{
    public function getPrice(): float
    {
        $sale = $this->getSalePrice();
        $price = (float) get_post_meta($this->id, '_tour_price', true);

        return $sale > 0 ? $sale : $price;
    }

    public function getRegularPrice(): float
    {
        return (float) get_post_meta($this->id, '_tour_regular_price', true);
    }

    public function getSalePrice(): float
    {
        return (float) get_post_meta($this->id, '_tour_sale_price', true);
    }

    public function isPurchasable(): bool
    {
        return $this->post
            && $this->post->post_status === 'publish'
            && $this->getPrice() > 0;
    }

    public function isInStock(): bool
    {
        return (bool) $this->post;
    }

    public function getProductType(): string
    {
        return TourPostType::POST_TYPE;
    }
}
