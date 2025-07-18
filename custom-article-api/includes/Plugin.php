<?php
namespace CustomArticleApi;

class Plugin {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Register custom post type
        add_action('init', [ $this, 'register_article_post_type' ]);
        // Register REST API routes
        add_action('rest_api_init', [ $this, 'register_rest_routes' ]);
    }

    public function register_article_post_type() {
        $labels = [
            'name' => 'Articles',
            'singular_name' => 'Article',
        ];
        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_rest' => true,
            'supports' => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ],
            'has_archive' => true,
            'rewrite' => [ 'slug' => 'article' ],
        ];
        register_post_type('article', $args);
    }

    public function register_rest_routes() {
        ArticleApi::register_routes();
        MediaApi::register_routes();
    }
} 