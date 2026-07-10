<?php

namespace Jankx\Extensions\Travel\Query;

use Jankx\Extensions\Travel\PostTypes\TourPostType;
use Jankx\Extensions\Travel\Taxonomies\TourCategoryTaxonomy;
use Jankx\Extensions\Travel\Taxonomies\DestinationRegionTaxonomy;

/**
 * Filters the main "tour" archive query using GET params submitted by the
 * Tour Search & Filter block: region, category, price range, duration.
 */
class TourQuery
{
    public function register(): void
    {
        add_action('pre_get_posts', [$this, 'apply_filters']);
    }

    public function apply_filters(\WP_Query $query): void
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }
        if (!$query->is_post_type_archive(TourPostType::POST_TYPE) && !$query->is_search()) {
            return;
        }

        $tax_query = [];

        if (!empty($_GET['tour_region'])) {
            $tax_query[] = [
                'taxonomy' => DestinationRegionTaxonomy::TAXONOMY,
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['tour_region']),
            ];
        }

        if (!empty($_GET['tour_category'])) {
            $tax_query[] = [
                'taxonomy' => TourCategoryTaxonomy::TAXONOMY,
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['tour_category']),
            ];
        }

        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'AND';
            }
            $query->set('tax_query', $tax_query);
        }

        $meta_query = [];

        if (!empty($_GET['tour_price_max']) || !empty($_GET['tour_price_min'])) {
            $price_compare = [
                'key'     => '_tour_price',
                'type'    => 'NUMERIC',
            ];
            if (!empty($_GET['tour_price_min']) && !empty($_GET['tour_price_max'])) {
                $price_compare['value'] = [
                    absint($_GET['tour_price_min']),
                    absint($_GET['tour_price_max']),
                ];
                $price_compare['compare'] = 'BETWEEN';
            } elseif (!empty($_GET['tour_price_max'])) {
                $price_compare['value'] = absint($_GET['tour_price_max']);
                $price_compare['compare'] = '<=';
            } else {
                $price_compare['value'] = absint($_GET['tour_price_min']);
                $price_compare['compare'] = '>=';
            }
            $meta_query[] = $price_compare;
        }

        if (!empty($_GET['tour_duration_days'])) {
            $meta_query[] = [
                'key'     => '_tour_duration_days',
                'value'   => absint($_GET['tour_duration_days']),
                'type'    => 'NUMERIC',
                'compare' => '<=',
            ];
        }

        if (!empty($meta_query)) {
            if (count($meta_query) > 1) {
                $meta_query['relation'] = 'AND';
            }
            $query->set('meta_query', $meta_query);
        }
    }
}
