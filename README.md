=== Custom Article API ===

A WordPress plugin to manage 'articles' (custom post type) via REST API, designed for integration with Laravel or other external apps.

== Description ==
Custom Article API registers a custom post type "article" and exposes secure REST API endpoints for creating, retrieving, updating articles, and uploading media. All endpoints require JWT authentication.

== Installation ==
1. Upload the `custom-article-api` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Install and configure the 'JWT Authentication for WP REST API' plugin for secure access.
4. Add your JWT secret key to `wp-config.php` as described in the JWT plugin documentation.

== Endpoints ==

* Authenticate: `POST /wp-json/jwt-auth/v1/token`
* Refresh Token: `POST /wp-json/jwt-auth/v1/token/refresh`
* Create Article: `POST /wp-json/custom-api/v1/article`
* Get Article: `GET /wp-json/custom-api/v1/article/{id}`
* Update Article: `PUT /wp-json/custom-api/v1/article/{id}`
* Upload Media: `POST /wp-json/custom-api/v1/upload-media` (file field: `file`, accepts pdf, jpg, png)

== Article Parameters ==
- title (string, required)
- content (string, required)
- status (string, optional: publish, draft, pending; default: publish)
- slug (string, optional)
- categories (array<int>, optional)
- featured_media (int, optional)
- meta_title (string, optional)
- meta_description (string, optional)

== Security ==
All endpoints require JWT authentication. See https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/ for setup.

== Changelog ==
= 1.0.0 =
* Initial release. 

== API Testing with Postman ==

1. **Get JWT Token**
   - Method: POST
   - URL: http://your-wp-site.test/wp-json/jwt-auth/v1/token
   - Body (x-www-form-urlencoded):
     - username: your WP username
     - password: your WP password

2. **Use the token in the Authorization header for all further requests:**
   - Key: Authorization
   - Value: Bearer YOUR_TOKEN

3. **Create Article**
   - Method: POST
   - URL: http://your-wp-site.test/wp-json/custom-api/v1/article
   - Headers: Authorization: Bearer YOUR_TOKEN, Content-Type: application/json
   - Body (raw JSON):
     {
       "title": "Test Article",
       "content": "<p>Body</p>"
     }

4. **Get Article**
   - Method: GET
   - URL: http://your-wp-site.test/wp-json/custom-api/v1/article/{id}
   - Headers: Authorization: Bearer YOUR_TOKEN

5. **Update Article**
   - Method: PUT
   - URL: http://your-wp-site.test/wp-json/custom-api/v1/article/{id}
   - Headers: Authorization: Bearer YOUR_TOKEN, Content-Type: application/json
   - Body (raw JSON):
     {
       "title": "Updated Title"
     }

6. **Upload Media**
   - Method: POST
   - URL: http://your-wp-site.test/wp-json/custom-api/v1/upload-media
   - Headers: Authorization: Bearer YOUR_TOKEN
   - Body: form-data, key 'file' (type: File), select a jpg/png/pdf file

Replace `your-wp-site.test` with your actual local or live site URL. 
