<?php
namespace CustomArticleApi;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class MediaApi {
    public static function register_routes() {
        register_rest_route('custom-api/v1', '/upload-media', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'upload_media'],
            'permission_callback' => [__CLASS__, 'check_jwt_auth'],
        ]);
    }

    public static function check_jwt_auth() {
        return is_user_logged_in();
    }

    public static function upload_media(WP_REST_Request $request) {
        if (empty($_FILES['file'])) {
            return new WP_Error('no_file', 'No file uploaded', ['status' => 400]);
        }
        $file = $_FILES['file'];
        $allowed = ['image/jpeg', 'image/png', 'application/pdf', 'image/jpg'];
        if (!in_array($file['type'], $allowed)) {
            return new WP_Error('invalid_type', 'Invalid file type', ['status' => 400]);
        }
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $upload = media_handle_upload('file', 0);
        if (is_wp_error($upload)) {
            return $upload;
        }
        $url = wp_get_attachment_url($upload);
        return new WP_REST_Response(['id' => $upload, 'url' => $url], 201);
    }
} 