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

== Testing ==

### Manual Testing with cURL

#### 1. Authentication Testing

**Get JWT Token:**
```bash
curl -X POST http://your-site.com/wp-json/jwt-auth/v1/token \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=your_username&password=your_password"
```

**Expected Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user_email": "admin@example.com",
  "user_nicename": "admin",
  "user_display_name": "Admin User"
}
```

#### 2. Article CRUD Testing

**Create Article:**
```bash
curl -X POST http://your-site.com/wp-json/custom-api/v1/article \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Article",
    "content": "<p>This is a test article content.</p>",
    "status": "publish",
    "slug": "test-article",
    "categories": [1],
    "meta_title": "Test SEO Title",
    "meta_description": "Test SEO description"
  }'
```

**Expected Response:**
```json
{
  "id": 123
}
```

**Get Article:**
```bash
curl -X GET http://your-site.com/wp-json/custom-api/v1/article/123 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Expected Response:**
```json
{
  "id": 123,
  "title": "Test Article",
  "content": "<p>This is a test article content.</p>",
  "status": "publish",
  "slug": "test-article",
  "categories": [1],
  "featured_media": null,
  "meta_title": "Test SEO Title",
  "meta_description": "Test SEO description"
}
```

**Update Article:**
```bash
curl -X PUT http://your-site.com/wp-json/custom-api/v1/article/123 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Test Article",
    "content": "<p>Updated content here.</p>",
    "status": "draft"
  }'
```

**Expected Response:**
```json
{
  "id": 123
}
```

#### 3. Media Upload Testing

**Upload Media File:**
```bash
curl -X POST http://your-site.com/wp-json/custom-api/v1/upload-media \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "file=@/path/to/your/image.jpg"
```

**Expected Response:**
```json
{
  "id": 456,
  "url": "http://your-site.com/wp-content/uploads/2024/01/image.jpg"
}
```

### Postman Testing

#### Setup Instructions

1. **Import the Postman Collection**: Import `Custom-Article-API.postman_collection.json` into Postman
2. **Configure Environment Variables**:
   - `base_url`: Your WordPress site URL (e.g., `http://localhost`)
   - `wp_username`: Your WordPress username
   - `wp_password`: Your WordPress password
   - `jwt_token`: Will be auto-populated after authentication
   - `article_id`: Will be auto-populated after creating an article

#### Testing Workflow

1. **Authentication**: Run "Get JWT Token" to authenticate and get a token
2. **Create Article**: Use "Create Article" or "Create Article (Minimal)" to create a test article
3. **Get Article**: Retrieve the created article using "Get Article"
4. **Update Article**: Modify the article using "Update Article"
5. **Upload Media**: Test file upload with "Upload Media"

#### Postman Collection Features

- **Auto-extraction scripts** that automatically capture JWT tokens and article IDs
- **Environment variables** for easy configuration
- **Example requests** with proper headers and bodies
- **Organized folders** for Authentication, Articles, and Media

### Prerequisites

1. **WordPress Installation**: Ensure WordPress is installed and running
2. **JWT Plugin**: Install and configure "JWT Authentication for WP REST API" plugin
3. **Custom Article API Plugin**: Install and activate the Custom Article API plugin
4. **JWT Secret**: Add JWT secret to `wp-config.php`:
   ```php
   define('JWT_AUTH_SECRET_KEY', 'your-secret-key-here');
   define('JWT_AUTH_CORS_ENABLE', true);
   ```

### Troubleshooting

#### Common Issues

1. **JWT Authentication Fails**
   - Verify JWT plugin is installed and activated
   - Check JWT secret is set in wp-config.php
   - Ensure CORS is enabled for JWT

2. **404 Errors**
   - Verify plugin is activated
   - Check permalink settings
   - Ensure REST API is enabled

3. **Permission Denied**
   - Verify user has proper WordPress permissions
   - Check JWT token is valid and not expired
   - Ensure Authorization header is properly formatted

4. **File Upload Issues**
   - Check file size limits in WordPress
   - Verify file type is allowed (jpg, png, pdf)
   - Ensure upload directory is writable 
