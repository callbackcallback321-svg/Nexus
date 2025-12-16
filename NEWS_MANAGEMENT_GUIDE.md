# News Template Management Guide

This guide explains how to add, edit, and manage custom news items for your news templates.

## Quick Start

### 1. Using the News Manager Tool (Recommended)

Run the Python script to manage news items:

```bash
python manage_news.py
```

This will open an interactive menu where you can:
- **List** all news items
- **Add** new news items
- **Edit** existing news items
- **Delete** news items

### 2. Manual Editing

You can also manually edit the `news_config.json` file directly:

```json
{
  "breaking_news": [
    {
      "id": 1,
      "title": "Your News Title",
      "content": "Your news content here...",
      "timestamp": "Just Now",
      "priority": "high"
    }
  ],
  "live_news": [
    {
      "id": 1,
      "title": "Live News Title",
      "content": "Live news content...",
      "timestamp": "Just Now",
      "is_live": true
    }
  ],
  "news_location": [
    {
      "id": 1,
      "title": "Location News Title",
      "content": "Location news content...",
      "timestamp": "Just Now"
    }
  ]
}
```

## Template Types

### 1. Breaking News (`breaking_news`)
- Used in: `templates/breaking_news/index.html`
- Fields:
  - `id`: Unique identifier (number)
  - `title`: News headline
  - `content`: News content (supports `\n` for line breaks)
  - `timestamp`: Time display (e.g., "Just Now", "5 minutes ago")
  - `priority`: Optional - "high", "medium", or "low"

### 2. Live News (`live_news`)
- Used in: `templates/live_news/index.html`
- Fields:
  - `id`: Unique identifier (number)
  - `title`: News headline
  - `content`: News content
  - `timestamp`: Time display
  - `is_live`: Boolean (true/false) - shows "🔴 LIVE" indicator

### 3. News Location (`news_location`)
- Used in: `templates/news_location/index.html`
- Fields:
  - `id`: Unique identifier (number)
  - `title`: News headline
  - `content`: News content
  - `timestamp`: Time display

## Step-by-Step: Adding News Items

### Method 1: Using the Manager Tool

1. **Run the manager:**
   ```bash
   python manage_news.py
   ```

2. **Select option 2** (Add news item)

3. **Choose template type:**
   - `1` for breaking_news
   - `2` for live_news
   - `3` for news_location

4. **Enter details:**
   - Title: Your news headline
   - Content: Your news content (press Enter twice when done)
   - Timestamp: When to display (e.g., "Just Now", "2 minutes ago")

5. **For breaking_news**, you'll also be asked for priority level

6. **Done!** The news item is automatically saved and will appear on the template

### Method 2: Manual JSON Editing

1. **Open `news_config.json`**

2. **Find the template type** you want to edit (e.g., `breaking_news`)

3. **Add a new item:**
   ```json
   {
     "id": 4,
     "title": "Your Custom News Title",
     "content": "Your custom news content here.\nYou can use line breaks.",
     "timestamp": "Just Now",
     "priority": "high"
   }
   ```

4. **Save the file**

5. **Refresh the template page** to see your changes

## Examples

### Example 1: Breaking News Item

```json
{
  "id": 1,
  "title": "🚨 Breaking: Major Event Unfolding",
  "content": "We're covering breaking news events in real-time. To provide you with the most accurate coverage, we need your location to deliver personalized breaking news updates for your area.",
  "timestamp": "Just Now",
  "priority": "high"
}
```

### Example 2: Live News Item

```json
{
  "id": 1,
  "title": "Breaking: Major developments in your area",
  "content": "We're covering live events happening right now. Enable location to get personalized live news updates.",
  "timestamp": "Just Now",
  "is_live": true
}
```

### Example 3: Multi-line Content

```json
{
  "id": 3,
  "title": "📰 Latest Breaking Stories",
  "content": "• Emergency alerts in your region\n• Traffic and road closures\n• Weather warnings\n• Community safety updates\n• Local government announcements",
  "timestamp": "5 minutes ago",
  "priority": "low"
}
```

## Editing Existing News

### Using the Manager Tool:

1. Run `python manage_news.py`
2. Select option **3** (Edit news item)
3. Choose template type
4. View the list of items
5. Enter the ID of the item you want to edit
6. Update the fields you want to change
7. Press Enter to keep current values unchanged

### Manual Editing:

1. Open `news_config.json`
2. Find the item by its `id`
3. Modify the fields
4. Save the file

## Deleting News Items

### Using the Manager Tool:

1. Run `python manage_news.py`
2. Select option **4** (Delete news item)
3. Choose template type
4. View the list of items
5. Enter the ID to delete
6. Confirm deletion

### Manual Editing:

1. Open `news_config.json`
2. Find the item in the array
3. Remove the entire object `{...}`
4. Save the file

## Tips & Best Practices

1. **Use Emojis**: Add emojis to titles for visual appeal (🚨, 📰, 🔴, etc.)

2. **Line Breaks**: Use `\n` in JSON for line breaks, or use bullet points with `•`

3. **Timestamps**: Keep them realistic:
   - "Just Now"
   - "2 minutes ago"
   - "5 minutes ago"
   - "1 hour ago"

4. **Content Length**: Keep content concise but engaging

5. **Priority Levels**: For breaking_news, use:
   - `high`: Most important/urgent news
   - `medium`: Regular news
   - `low`: Less urgent updates

6. **Backup**: Always backup `news_config.json` before making major changes

## Troubleshooting

### News items not showing?

1. **Check JSON syntax**: Make sure `news_config.json` is valid JSON
2. **Check file location**: Ensure `news_config.json` is in the project root
3. **Check API**: Visit `http://localhost:2525/news_api.php?type=breaking_news` to test
4. **Browser console**: Check for JavaScript errors (F12)

### JSON errors?

- Use a JSON validator: https://jsonlint.com/
- Make sure all strings are in quotes
- Check for trailing commas (not allowed in JSON)
- Ensure all brackets and braces are properly closed

### Manager tool not working?

- Make sure you have `colorama` installed: `pip install colorama`
- Check Python version: `python --version` (should be 3.6+)
- Ensure `news_config.json` exists in the project root

## API Endpoint

The news items are served via a PHP API endpoint:

- **All news**: `http://localhost:2525/news_api.php`
- **Breaking news**: `http://localhost:2525/news_api.php?type=breaking_news`
- **Live news**: `http://localhost:2525/news_api.php?type=live_news`
- **Location news**: `http://localhost:2525/news_api.php?type=news_location`

You can test these URLs directly in your browser to see the JSON response.

## Updating Templates

The following templates have been updated to load news dynamically:

- ✅ `templates/breaking_news/index.html`
- ✅ `templates/live_news/index.html`
- ⚠️ `templates/news_location/index.html` (can be updated similarly)

To update `news_location` template, follow the same pattern as `breaking_news`.

## Need Help?

If you encounter any issues:

1. Check the JSON file syntax
2. Verify the API endpoint is accessible
3. Check browser console for errors
4. Ensure the server is running (`python st.py`)

---

**Happy News Managing! 📰**

