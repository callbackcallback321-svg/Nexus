<?php
session_start();
include "./assets/components/login-arc.php";

// Check authentication
if(isset($_COOKIE['logindata']) && $_COOKIE['logindata'] == $key['token'] && $key['expired'] == "no"){
    if(!isset($_SESSION['IAm-logined'])){
        $_SESSION['IAm-logined'] = 'yes';
    }
}
elseif(isset($_SESSION['IAm-logined'])){
    $client_token = generate_token();
    setcookie("logindata", $client_token, time() + (86400 * 30), "/");
    change_token($client_token);
}
else {
    header('location: login.php');
    exit;
}

$config_file = __DIR__ . '/../news_config.json';
$images_dir = __DIR__ . '/news_images/';

// Create images directory if it doesn't exist
if (!file_exists($images_dir)) {
    mkdir($images_dir, 0777, true);
}

// Load existing news
$news_config = [];
if (file_exists($config_file)) {
    $news_config = json_decode(file_get_contents($config_file), true);
}
if (!isset($news_config['breaking_news'])) {
    $news_config['breaking_news'] = [];
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_news') {
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = $_FILES['news_image']['type'];
                
                if (in_array($file_type, $allowed_types)) {
                    $file_extension = pathinfo($_FILES['news_image']['name'], PATHINFO_EXTENSION);
                    $new_filename = 'news_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $target_path = $images_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['news_image']['tmp_name'], $target_path)) {
                        $image_path = 'news_images/' . $new_filename;
                    } else {
                        $message = 'Error uploading image.';
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP';
                    $message_type = 'error';
                }
            }
            
            if ($message == '') {
                // Get next ID
                $max_id = 0;
                foreach ($news_config['breaking_news'] as $item) {
                    if (isset($item['id']) && $item['id'] > $max_id) {
                        $max_id = $item['id'];
                    }
                }
                
                $new_item = [
                    'id' => $max_id + 1,
                    'title' => $_POST['news_title'],
                    'content' => $_POST['news_description'],
                    'timestamp' => $_POST['news_timestamp'] ?: date('Y-m-d H:i:s'),
                    'image' => $image_path,
                    'priority' => $_POST['news_priority'] ?: 'medium',
                    'attribution' => $_POST['news_attribution'] ?? '',
                    'date_added' => date('Y-m-d H:i:s')
                ];
                
                $news_config['breaking_news'][] = $new_item;
                
                if (file_put_contents($config_file, json_encode($news_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                    $message = 'News added successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error saving news.';
                    $message_type = 'error';
                }
            }
        }
        elseif ($_POST['action'] == 'delete_news') {
            $delete_id = intval($_POST['delete_id']);
            
            // Find and remove the news item
            foreach ($news_config['breaking_news'] as $key => $item) {
                if (isset($item['id']) && $item['id'] == $delete_id) {
                    // Delete associated image
                    if (isset($item['image']) && $item['image'] && file_exists(__DIR__ . '/' . $item['image'])) {
                        unlink(__DIR__ . '/' . $item['image']);
                    }
                    unset($news_config['breaking_news'][$key]);
                    break;
                }
            }
            
            // Re-index array
            $news_config['breaking_news'] = array_values($news_config['breaking_news']);
            
            if (file_put_contents($config_file, json_encode($news_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $message = 'News deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error deleting news.';
                $message_type = 'error';
            }
        }
    }
}

// Reload news after operations
if (file_exists($config_file)) {
    $news_config = json_decode(file_get_contents($config_file), true);
    if (!isset($news_config['breaking_news'])) {
        $news_config['breaking_news'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management - Nexus</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap');
        
        :root {
            --orange-start: #ff6b35;
            --orange-mid: #ff8c42;
            --yellow-end: #ffd23f;
            --orange-glow: rgba(255, 107, 53, 0.5);
            --orange-glow-strong: rgba(255, 107, 53, 0.8);
            --yellow-glow: rgba(255, 210, 63, 0.5);
            --bg-black: #0a0a0a;
            --bg-dark: #111111;
            --bg-card: #1a1a1a;
            --bg-card-hover: #252525;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --text-muted: #666666;
            --border-color: #2a2a2a;
            --border-accent: #ff6b35;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --shadow-accent: 0 0 20px rgba(255, 107, 53, 0.3);
            --shadow-accent-lg: 0 0 40px rgba(255, 107, 53, 0.5);
            --shadow-dark: 0 8px 32px rgba(0, 0, 0, 0.8);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-black);
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 107, 53, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 210, 63, 0.05) 0%, transparent 50%);
            min-height: 100vh;
            padding: 20px;
            color: var(--text-primary);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 107, 53, 0.03) 2px,
                    rgba(255, 107, 53, 0.03) 4px
                );
            pointer-events: none;
            z-index: 0;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            background: var(--bg-card);
            border-radius: 0;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-dark), var(--shadow-red);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 100%);
            border-bottom: 2px solid var(--orange-start);
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .admin-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--orange-start), var(--yellow-end), transparent);
            box-shadow: 0 0 10px var(--orange-glow);
        }

        .admin-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 10px var(--orange-glow);
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-header h1::before {
            content: '▶';
            color: var(--orange-start);
            font-size: 20px;
            animation: blink 1.5s infinite;
            filter: drop-shadow(0 0 5px var(--orange-glow));
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }

        .admin-header .btn {
            background: var(--bg-dark);
            border: 1px solid var(--orange-start);
            color: var(--text-primary);
            padding: 10px 20px;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 0 10px var(--orange-glow);
        }

        .admin-header .btn:hover {
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            box-shadow: 0 0 20px var(--orange-glow-strong);
            transform: translateY(-2px);
            border-color: var(--yellow-end);
        }

        .admin-content {
            padding: 40px;
            background: var(--bg-dark);
        }

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-left: 3px solid var(--red-primary);
            border-radius: 0;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-dark);
            position: relative;
            transition: all 0.3s ease;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, var(--orange-start), var(--yellow-end));
            box-shadow: 0 0 10px var(--orange-glow);
        }

        .form-card:hover {
            border-color: var(--orange-start);
            box-shadow: var(--shadow-dark), var(--shadow-accent);
        }

        .form-card h3 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 22px;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-card h3 i {
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 5px var(--orange-glow));
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'JetBrains Mono', monospace;
        }

        .form-control {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 0;
            padding: 12px 16px;
            font-size: 14px;
            color: var(--text-primary);
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            border-color: var(--orange-start);
            box-shadow: 0 0 0 2px var(--orange-glow), 0 0 15px var(--orange-glow-strong);
            outline: none;
            background: var(--bg-card);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .image-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 0;
            padding: 40px;
            text-align: center;
            background: var(--bg-dark);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .image-upload-area::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .image-upload-area:hover {
            border-color: var(--red-primary);
            background: var(--bg-card);
        }

        .image-upload-area:hover::before {
            border-color: var(--red-primary);
            box-shadow: 0 0 15px var(--red-glow);
        }

        .image-upload-area.has-image {
            border-color: var(--red-primary);
            background: var(--bg-card);
            box-shadow: 0 0 20px var(--red-glow);
        }

        .image-upload-area i {
            color: var(--text-secondary);
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .image-upload-area:hover i {
            color: var(--red-primary);
            text-shadow: 0 0 10px var(--red-glow);
        }

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 0;
            margin-top: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-dark);
            display: none;
        }

        .preview-image.show {
            display: block;
        }

        .btn-primary {
            background: var(--bg-dark);
            border: 2px solid var(--orange-start);
            color: var(--text-primary);
            padding: 14px 35px;
            border-radius: 0;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px var(--orange-glow);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            box-shadow: 0 0 25px var(--orange-glow-strong);
            transform: translateY(-2px);
            border-color: var(--yellow-end);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .news-list-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-left: 3px solid var(--red-primary);
            border-radius: 0;
            padding: 35px;
            box-shadow: var(--shadow-dark);
            position: relative;
        }

        .news-list-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, var(--orange-start), var(--yellow-end));
            box-shadow: 0 0 10px var(--orange-glow);
        }

        .news-list-card h3 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 22px;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .news-list-card h3 i {
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 5px var(--orange-glow));
        }

        .news-item-card {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-left: 2px solid var(--border-color);
            border-radius: 0;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
        }

        .news-item-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-color);
            transition: all 0.3s ease;
        }

        .news-item-card:hover {
            border-color: var(--orange-start);
            box-shadow: var(--shadow-dark), var(--shadow-accent);
            transform: translateX(5px);
            background: var(--bg-card);
        }

        .news-item-card:hover::before {
            background: linear-gradient(180deg, var(--orange-start), var(--yellow-end));
            box-shadow: 0 0 10px var(--orange-glow);
            width: 3px;
        }

        .news-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            gap: 20px;
        }

        .news-item-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .news-item-meta {
            font-size: 12px;
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace;
        }

        .news-item-content {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .news-item-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0;
            margin-top: 15px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-dark);
        }

        .badge {
            padding: 5px 12px;
            border-radius: 0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'JetBrains Mono', monospace;
            border: 1px solid;
        }

        .badge-high {
            background: rgba(255, 107, 53, 0.15);
            color: var(--orange-start);
            border-color: var(--orange-start);
            box-shadow: 0 0 10px var(--orange-glow);
        }

        .badge-medium {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
            border-color: var(--warning-color);
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
        }

        .badge-low {
            background: rgba(100, 116, 139, 0.15);
            color: var(--text-secondary);
            border-color: var(--text-secondary);
        }

        .btn-danger {
            background: var(--bg-dark);
            border: 1px solid var(--orange-start);
            padding: 10px 20px;
            border-radius: 0;
            color: var(--text-primary);
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            box-shadow: 0 0 10px var(--orange-glow);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            box-shadow: 0 0 20px var(--orange-glow-strong);
            transform: translateY(-2px);
            border-color: var(--yellow-end);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
            border: 1px dashed var(--border-color);
            background: var(--bg-dark);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--text-muted);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            color: var(--text-primary);
            margin-top: 20px;
            font-family: 'JetBrains Mono', monospace;
        }

        .empty-state p {
            color: var(--text-secondary);
        }

        .alert {
            border-radius: 0;
            padding: 16px 20px;
            margin-bottom: 20px;
            border: 1px solid;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
            position: relative;
            border-left: 3px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-color: var(--success-color);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
        }

        .alert-danger {
            background: rgba(255, 107, 53, 0.15);
            color: var(--orange-start);
            border-color: var(--orange-start);
            box-shadow: 0 0 15px var(--orange-glow);
        }

        .alert i {
            margin-right: 10px;
        }

        small {
            color: var(--text-muted);
            font-size: 12px;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--orange-start), var(--yellow-end));
            border: 2px solid var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--orange-mid), var(--yellow-end));
            box-shadow: 0 0 10px var(--orange-glow);
        }

        /* Selection */
        ::selection {
            background: var(--orange-start);
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .admin-content {
                padding: 20px;
            }

            .admin-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 20px;
            }

            .admin-header h1 {
                font-size: 22px;
            }

            .news-item-header {
                flex-direction: column;
                gap: 15px;
            }

            .form-card, .news-list-card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-terminal"></i> NEWS MANAGEMENT SYSTEM</h1>
            <a href="panel.php" class="btn">
                <i class="fas fa-arrow-left"></i> BACK TO DASHBOARD
            </a>
        </div>

        <div class="admin-content">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()" style="float: right; background: none; border: none; font-size: 20px; cursor: pointer; color: inherit; opacity: 0.8; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Add News Form -->
            <div class="form-card">
                <h3><i class="fas fa-terminal"></i> ADD NEW BREAKING NEWS</h3>
                <form method="POST" enctype="multipart/form-data" id="newsForm">
                    <input type="hidden" name="action" value="add_news">
                    
                    <div class="form-group">
                        <label for="news_title">News Title *</label>
                        <input type="text" class="form-control" id="news_title" name="news_title" required placeholder="Enter breaking news headline">
                    </div>

                    <div class="form-group">
                        <label for="news_description">Description *</label>
                        <textarea class="form-control" id="news_description" name="news_description" rows="5" required placeholder="Enter detailed news description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="news_attribution">Attribution (Optional)</label>
                        <input type="text" class="form-control" id="news_attribution" name="news_attribution" placeholder="e.g., مفتی ندیم خان و رویش المهاجر">
                        <small style="color: var(--text-secondary); font-size: 12px;">Source or author attribution for the news</small>
                    </div>

                    <div class="form-group">
                        <label>News Image (Optional)</label>
                        <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('news_image').click()">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 10px;"></i>
                            <p style="color: var(--text-secondary); margin: 0;">Click to upload or drag and drop</p>
                            <p style="color: var(--text-secondary); font-size: 12px; margin-top: 5px;">JPG, PNG, GIF, WEBP (Max 5MB)</p>
                        </div>
                        <input type="file" class="d-none" id="news_image" name="news_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(this)">
                        <img id="imagePreview" class="preview-image" alt="Preview">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="news_timestamp">Timestamp</label>
                                <input type="text" class="form-control" id="news_timestamp" name="news_timestamp" placeholder="e.g., Just Now, 5 minutes ago" value="Just Now">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="news_priority">Priority</label>
                                <select class="form-control" id="news_priority" name="news_priority">
                                    <option value="high">High</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> SUBMIT NEWS
                    </button>
                </form>
            </div>

            <!-- Existing News List -->
            <div class="news-list-card">
                <h3><i class="fas fa-database"></i> EXISTING NEWS [<?php echo count($news_config['breaking_news']); ?>]</h3>
                
                <?php if (empty($news_config['breaking_news'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h4 style="margin-top: 20px; color: var(--text-secondary);">No news items yet</h4>
                        <p style="color: var(--text-secondary);">Add your first breaking news above!</p>
                    </div>
                <?php else: ?>
                    <?php 
                    // Sort by ID descending (newest first)
                    $news_items = $news_config['breaking_news'];
                    usort($news_items, function($a, $b) {
                        return ($b['id'] ?? 0) - ($a['id'] ?? 0);
                    });
                    foreach ($news_items as $item): 
                    ?>
                        <div class="news-item-card">
                            <div class="news-item-header">
                                <div style="flex: 1;">
                                    <div class="news-item-title">
                                        <?php echo htmlspecialchars($item['title'] ?? 'No Title'); ?>
                                        <span class="badge badge-<?php 
                                            $priority = $item['priority'] ?? 'medium';
                                            echo $priority;
                                        ?>">
                                            <?php echo strtoupper($priority); ?>
                                        </span>
                                    </div>
                                    <div class="news-item-meta">
                                        <i class="fas fa-calendar"></i> <?php echo $item['date_added'] ?? 'Unknown'; ?> | 
                                        <i class="fas fa-clock"></i> <?php echo htmlspecialchars($item['timestamp'] ?? 'N/A'); ?>
                                    </div>
                                </div>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this news item?');">
                                    <input type="hidden" name="action" value="delete_news">
                                    <input type="hidden" name="delete_id" value="<?php echo $item['id'] ?? 0; ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                            <div class="news-item-content">
                                <?php echo nl2br(htmlspecialchars($item['content'] ?? 'No description')); ?>
                            </div>
                            <?php if (isset($item['image']) && $item['image'] && file_exists(__DIR__ . '/' . $item['image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="News Image" class="news-item-image">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="./assets/js/jquery.min.js"></script>
    <script>
        function previewImage(input) {
            const file = input.files[0];
            const preview = document.getElementById('imagePreview');
            const uploadArea = document.getElementById('imageUploadArea');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                    uploadArea.classList.add('has-image');
                    uploadArea.querySelector('p').textContent = file.name;
                }
                reader.readAsDataURL(file);
            } else {
                preview.classList.remove('show');
                uploadArea.classList.remove('has-image');
            }
        }

        // Form reset after successful submission
        <?php if ($message_type == 'success'): ?>
            setTimeout(function() {
                document.getElementById('newsForm').reset();
                document.getElementById('imagePreview').classList.remove('show');
                document.getElementById('imageUploadArea').classList.remove('has-image');
                document.getElementById('imageUploadArea').querySelector('p').textContent = 'Click to upload or drag and drop';
            }, 100);
        <?php endif; ?>
    </script>
</body>
</html>
