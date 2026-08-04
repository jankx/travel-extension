<?php

namespace Jankx\Extensions\Travel;

use Jankx\Extensions\AbstractExtension;
use Jankx\Extensions\Travel\PostTypes\DestinationPostType;
use Jankx\Extensions\Travel\PostTypes\TourPostType;
use Jankx\Extensions\Travel\PostTypes\BookingRequestPostType;
use Jankx\Extensions\Travel\Taxonomies\TourCategoryTaxonomy;
use Jankx\Extensions\Travel\Taxonomies\DestinationRegionTaxonomy;
use Jankx\Extensions\Travel\Meta\TourMetaBoxes;
use Jankx\Extensions\Travel\Meta\DestinationMetaBoxes;
use Jankx\Extensions\Travel\Forms\BookingRequestHandler;
use Jankx\Extensions\Travel\Query\TourQuery;

/**
 * Travel Extension
 *
 * Adds travel-industry features to the Jankx theme: Destinations, Tours,
 * itineraries, departure dates, and a booking-request (quote request) form.
 *
 * @package Jankx\Extensions\Travel
 */
class TravelExtension extends AbstractExtension
{
    protected static $instance;

    public function __construct()
    {
        $this->register_autoloader();
        parent::__construct();
    }

    protected function register_autoloader()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Jankx\\Extensions\\Travel\\';
            $base_dir = __DIR__ . '/src/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }

    public function init(): void
    {
        self::$instance = $this;
    }

    public static function get_instance(): ?self
    {
        return self::$instance;
    }

    public function register_hooks(): void
    {
        // Post types & taxonomies must be registered on every request (admin + frontend + rest).
        (new DestinationPostType())->register();
        (new TourPostType())->register();
        (new BookingRequestPostType())->register();

        (new TourCategoryTaxonomy())->register();
        (new DestinationRegionTaxonomy())->register();

        // Admin meta boxes for editing tour/destination details.
        if (is_admin()) {
            (new TourMetaBoxes())->register();
            (new DestinationMetaBoxes())->register();
        }

        // Frontend booking-request form handling (AJAX + REST).
        (new BookingRequestHandler())->register();

        // Tour archive filtering (region, category, price, duration).
        (new TourQuery())->register();

        // Always register blocks so ServerSideRender works in editor
        $this->register_blocks();

        // Frontend styles for the extension's blocks.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);

        // Editor script so the block editor knows how to render/select these
        // dynamic (server-rendered) blocks, instead of showing the
        // "Your site doesn't include support for this block" placeholder.
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    public function enqueue_editor_assets(): void
    {
        $screen = get_current_screen();
        if (!$screen) {
            return;
        }

        // Only load on pages/posts or travel-related screens
        $allowed = ['post', 'page', 'edit'];
        if (!in_array($screen->base, $allowed) && strpos($screen->id, 'jankx-travel') === false) {
            return;
        }

        wp_enqueue_script(
            'jankx-travel-editor',
            $this->get_extension_url() . '/assets/editor.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n'],
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'jankx-travel-frontend',
            $this->get_extension_url() . '/assets/frontend.css',
            [],
            '1.0.0'
        );
    }

    public function enqueue_frontend_assets(): void
    {
        wp_enqueue_style(
            'jankx-travel-frontend',
            $this->get_extension_url() . '/assets/frontend.css',
            [],
            '1.0.0'
        );
    }

    public function register_blocks(): void
    {
        $blocksDir = $this->get_extension_path() . '/blocks';
        if (!is_dir($blocksDir)) {
            return;
        }

        foreach (glob($blocksDir . '/*', GLOB_ONLYDIR) as $blockDir) {
            if (!file_exists($blockDir . '/block.json')) {
                continue;
            }

            $blockJson = json_decode(file_get_contents($blockDir . '/block.json'), true);
            $blockName = $blockJson['name'] ?? '';
            
            $blockClass = null;
            if ($blockName === 'jankx/account-tab-orders') {
                $blockClass = new \Jankx\Extensions\Travel\Blocks\AccountTabOrdersBlock($blockDir);
            }

            if ($blockClass) {
                $blockClass->setBlockPath($blockDir);
                $blockClass->boot();
                $blockClass->register();
            }
        }
    }
}
