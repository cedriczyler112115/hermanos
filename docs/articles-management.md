# Article Management Feature

## Overview
The Article Management feature allows administrators to create and publish news, stories, and insights for the Cantores Hermanos choir community. It includes a full CRUD interface in the admin panel and a paginated listing page on the public site, with support for Facebook content embedding.

## Database Schema
The `articles` table includes:
- `title`: Article headline.
- `slug`: URL-friendly identifier (unique).
- `author`: Name of the writer.
- `category`: Grouping for articles (e.g., News, Reflections).
- `description`: The main content (supports HTML/Text).
- `fb_link`: URL to a Facebook post or video for embedding.
- `posted_at`: Publication date and time.
- `featured_image_path`: Path to the full-size featured image.
- `featured_image_thumb_path`: Path to the optimized thumbnail.
- `status`: `draft` or `published`.

## Admin Panel Usage
1. **Access**: Navigate to the "Articles" section in the admin sidebar or use the "Add article" shortcut in the top navigation.
2. **Creating**:
   - Fill in the required fields: Title, Date Posted, and Status.
   - **Facebook Embed**: Paste a direct link to a Facebook post or video (e.g., `https://www.facebook.com/username/posts/12345`). The system will automatically generate the embed code for the public view.
   - **Featured Image**: Upload an image (max 5MB). It will be automatically resized and a thumbnail generated.
3. **Status**: Set to `published` to make it visible on the public site. Set to `draft` for internal work.

## Public Site Features
- **Listing Page**: Accessible via the "Articles" link in the "Home" menu.
- **Search**: Filter articles by title, author, category, or content.
- **Pagination**: Default 10 articles per page (configurable).
- **Detail View**: Each article has a dedicated page showing the full content and the Facebook embed.

## Technical Details
- **Facebook Embedding**: Uses the Facebook JavaScript SDK for standard post URLs and `<iframe>` for plugin URLs.
- **Image Optimization**: Images are processed using the `ImageStorage` service, resizing to a maximum of 2000px for the full image and 720px for the thumbnail.
- **Caching**: Public results are cached for 5 minutes. Cache is automatically invalidated when articles are created, updated, or deleted.
- **Security**: All inputs are validated. Description content is escaped in the detail view using `nl2br(e($article->description))`. *Note: If Rich Text support is added in the future, a HTML purifier should be implemented.*

## Maintenance
- **Cache Invalidation**: The `articles_public_version` cache key is incremented whenever articles change.
- **Storage**: Images are stored in the `public` disk under the `articles` directory.
