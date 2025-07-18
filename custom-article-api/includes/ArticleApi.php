<?php
namespace CustomArticleApi;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class ArticleApi {
    public static function register_routes() {
        register_rest_route('custom-api/v1', '/article', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'create_article'],
            'permission_callback' => [__CLASS__, 'check_jwt_auth'],
        ]);
        register_rest_route('custom-api/v1', '/article/(?P<id>\\d+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_article'],
            'permission_callback' => [__CLASS__, 'check_jwt_auth'],
        ]);
        register_rest_route('custom-api/v1', '/article/(?P<id>\\d+)', [
            'methods' => 'PUT',
            'callback' => [__CLASS__, 'update_article'],
            'permission_callback' => [__CLASS__, 'check_jwt_auth'],
        ]);
    }

    public static function check_jwt_auth() {
        // JWT plugin sets user if valid
        return is_user_logged_in();
    }

    public static function create_article(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $required = ['title', 'content'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return new WP_Error('missing_field', $field.' is required', ['status' => 400]);
            }
        }
        $postarr = [
            'post_title'   => $params['title'],
            'post_content' => $params['content'],
            'post_status'  => $params['status'] ?? 'publish',
            'post_type'    => 'article',
        ];
        if (!empty($params['slug'])) {
            $postarr['post_name'] = $params['slug'];
        }
        if (!empty($params['categories'])) {
            $postarr['post_category'] = $params['categories'];
        }
        if (!empty($params['featured_media'])) {
            $postarr['meta_input']['_thumbnail_id'] = $params['featured_media'];
        }
        if (!empty($params['meta_title'])) {
            $postarr['meta_input']['meta_title'] = $params['meta_title'];
        }
        if (!empty($params['meta_description'])) {
            $postarr['meta_input']['meta_description'] = $params['meta_description'];
        }
        $post_id = wp_insert_post($postarr, true);
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        // Set categories if provided
        if (!empty($params['categories'])) {
            wp_set_post_categories($post_id, $params['categories']);
        }
        // Set featured image if provided
        if (!empty($params['featured_media'])) {
            set_post_thumbnail($post_id, $params['featured_media']);
        }
        return new WP_REST_Response(['id' => $post_id], 201);
    }

    public static function get_article(WP_REST_Request $request) {
        $id = (int) $request['id'];
        $post = get_post($id);
        if (!$post || $post->post_type !== 'article') {
            return new WP_Error('not_found', 'Article not found', ['status' => 404]);
        }
        $data = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'status' => $post->post_status,
            'slug' => $post->post_name,
            'categories' => wp_get_post_categories($post->ID),
            'featured_media' => get_post_thumbnail_id($post->ID),
            'meta_title' => get_post_meta($post->ID, 'meta_title', true),
            'meta_description' => get_post_meta($post->ID, 'meta_description', true),
        ];
        return new WP_REST_Response($data, 200);
    }

    public static function update_article(WP_REST_Request $request) {
        $id = (int) $request['id'];
        $post = get_post($id);
        if (!$post || $post->post_type !== 'article') {
            return new WP_Error('not_found', 'Article not found', ['status' => 404]);
        }
        $params = $request->get_json_params();
        $postarr = [ 'ID' => $id ];
        if (!empty($params['title'])) $postarr['post_title'] = $params['title'];
        if (!empty($params['content'])) $postarr['post_content'] = $params['content'];
        if (!empty($params['status'])) $postarr['post_status'] = $params['status'];
        if (!empty($params['slug'])) $postarr['post_name'] = $params['slug'];
        if (!empty($params['categories'])) $postarr['post_category'] = $params['categories'];
        if (!empty($params['featured_media'])) $postarr['meta_input']['_thumbnail_id'] = $params['featured_media'];
        if (!empty($params['meta_title'])) $postarr['meta_input']['meta_title'] = $params['meta_title'];
        if (!empty($params['meta_description'])) $postarr['meta_input']['meta_description'] = $params['meta_description'];
        $result = wp_update_post($postarr, true);
        if (is_wp_error($result)) {
            return $result;
        }
        // Set categories if provided
        if (!empty($params['categories'])) {
            wp_set_post_categories($id, $params['categories']);
        }
        // Set featured image if provided
        if (!empty($params['featured_media'])) {
            set_post_thumbnail($id, $params['featured_media']);
        }
        return new WP_REST_Response(['id' => $id], 200);
    }
} 