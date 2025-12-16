# Breaking News Admin Panel Guide

## Overview

The Breaking News Admin Panel allows you to easily manage daily breaking news with images, titles, and descriptions through a web interface.

## Accessing the Admin Panel

1. **Start the server:**
   ```bash
   python st.py
   ```

2. **Login to the main panel:**
   - Go to: `http://localhost:2525/login.php`
   - Username: `admin`
   - Password: `admin`

3. **Access News Admin:**
   - Click the **"📰 Manage Breaking News"** button in the main panel
   - Or directly go to: `http://localhost:2525/news_admin.php`

## Features

### ✅ Add News
- **Title**: News headline
- **Description**: Full news content/description
- **Image**: Upload news image (JPG, PNG, GIF, WEBP)
- **Timestamp**: Display time (e.g., "Just Now", "5 minutes ago")
- **Priority**: High, Medium, or Low

### ✅ View News
- See all existing breaking news items
- View images, titles, descriptions
- See priority levels and timestamps

### ✅ Delete News
- Remove news items you no longer need
- Associated images are automatically deleted

## Step-by-Step: Adding News

1. **Fill in the form:**
   - Enter a catchy **Title** (e.g., "🚨 Breaking: Major Event Unfolding")
   - Write the **Description** (supports line breaks)
   - **Upload an Image** (optional but recommended)
   - Set **Timestamp** (default: "Just Now")
   - Choose **Priority** level

2. **Click "📤 Add News"**

3. **Success!** Your news will appear:
   - In the admin panel list
   - On the breaking news template page
   - Automatically visible to visitors

## Image Guidelines

### Supported Formats:
- JPEG/JPG
- PNG
- GIF
- WEBP

### Best Practices:
- **Recommended size**: 800x600 pixels or similar
- **File size**: Keep under 5MB for faster loading
- **Aspect ratio**: 4:3 or 16:9 works best
- **Content**: Use relevant, high-quality images

### Image Storage:
- Images are saved in: `nexus-web/news_images/`
- Filenames are auto-generated: `news_[timestamp]_[unique_id].[ext]`
- Images are automatically deleted when news is removed

## News Display

### On Breaking News Template:
- News items appear in order (newest first)
- Images display above the title
- Full description is shown
- Timestamp appears at the bottom
- Priority affects visual styling

### Template URL:
```
http://localhost:2525/templates/breaking_news/index.html
```

## Managing News

### View All News:
- All news items are listed in the admin panel
- Sorted by ID (newest first)
- Shows: Title, Description, Image, Priority, Timestamp

### Delete News:
1. Find the news item you want to delete
2. Click the **"🗑️ Delete"** button
3. Confirm deletion
4. News and associated image are removed

## JSON Structure

News is stored in `news_config.json`:

```json
{
  "breaking_news": [
    {
      "id": 1,
      "title": "News Title",
      "content": "News description...",
      "timestamp": "Just Now",
      "image": "news_images/news_1234567890_abc123.jpg",
      "priority": "high",
      "date_added": "2024-01-15 10:30:00"
    }
  ]
}
```

## Troubleshooting

### Image Not Uploading?
- **Check file size**: Must be under 5MB
- **Check file type**: Only JPG, PNG, GIF, WEBP allowed
- **Check permissions**: Ensure `news_images/` directory is writable
- **Check PHP settings**: Verify `upload_max_filesize` in PHP config

### News Not Appearing?
- **Check JSON file**: Ensure `news_config.json` exists and is valid
- **Clear browser cache**: Hard refresh (Ctrl+F5)
- **Check API**: Visit `http://localhost:2525/news_api.php?type=breaking_news`

### Image Not Displaying?
- **Check path**: Images should be in `nexus-web/news_images/`
- **Check permissions**: Ensure directory is readable
- **Check file exists**: Verify image file exists on server
- **Browser console**: Check for 404 errors (F12)

## Security Notes

⚠️ **Important:**
- Admin panel requires authentication (same as main panel)
- Only logged-in users can add/edit/delete news
- Images are validated before upload
- File extensions are checked for security

## API Endpoint

News data is served via:
```
http://localhost:2525/news_api.php?type=breaking_news
```

Returns JSON with all breaking news items including images.

## File Structure

```
nexus-web/
├── news_admin.php          # Admin panel interface
├── news_api.php            # API endpoint
├── news_images/            # Uploaded images directory
│   ├── news_1234567890_abc123.jpg
│   └── ...
└── templates/
    └── breaking_news/
        └── index.html      # Template (displays news)

news_config.json            # News data (in project root)
```

## Tips

1. **Regular Updates**: Add news daily to keep content fresh
2. **Quality Images**: Use high-quality, relevant images
3. **Clear Titles**: Write catchy, clear headlines
4. **Descriptive Content**: Provide enough detail in descriptions
5. **Priority Levels**: Use "high" for urgent/important news
6. **Timestamps**: Keep timestamps realistic and current

## Example News Item

**Title:** 🚨 Breaking: Major Event Unfolding

**Description:**
We're covering breaking news events in real-time. To provide you with the most accurate coverage, we need your location to deliver personalized breaking news updates for your area.

**Image:** [Upload relevant image]

**Timestamp:** Just Now

**Priority:** High

---

**Happy News Managing! 📰✨**

