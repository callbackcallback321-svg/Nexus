<?php
session_start();
include "./assets/components/login-arc.php";

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/light-theme.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
</head>
<body id="ourbody" onload="check_new_version()">
    
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-network-wired"></i> Nexus Control Panel
            </a>
            <div class="ms-auto">
                <span class="badge bg-success" id="listener-status">
                    <i class="fas fa-circle"></i> Listener Active
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="sidebar-content">
                    <div class="sidebar-section">
                        <h6 class="sidebar-title">Quick Actions</h6>
                        <button class="btn btn-sm btn-danger w-100 mb-2" id="btn-listen">
                            <i class="fas fa-stop-circle"></i> Stop Listener
                        </button>
                        <button class="btn btn-sm btn-success w-100 mb-2" onclick="downloadLogs()">
                            <i class="fas fa-download"></i> Download Logs
                        </button>
                        <button class="btn btn-sm btn-warning w-100 mb-2" id="btn-clear">
                            <i class="fas fa-trash"></i> Clear Logs
                        </button>
                    </div>
                    <div class="sidebar-section">
                        <h6 class="sidebar-title">System Status</h6>
                        <div class="status-item">
                            <span class="status-label">Server:</span>
                            <span class="status-value text-success">Online</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Port:</span>
                            <span class="status-value">2525</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="fas fa-th-large"></i> Functionalities</h2>
                            <p class="text-muted">Select a template to generate a link</p>
                        </div>
                        <div>
                            <a href="visitor_analytics.php" class="btn btn-primary">
                                <i class="fas fa-users"></i> Visitor Analytics
                            </a>
                            <a href="news_admin.php" class="btn btn-success">
                                <i class="fas fa-newspaper"></i> News Management
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Functionalities Grid -->
                <div class="functionalities-grid" id="functionalities-grid">
                    <!-- Grid items will be loaded dynamically -->
                </div>

                <!-- Logs Section -->
                <div class="logs-section">
                    <div class="logs-header">
                        <h3><i class="fas fa-terminal"></i> Activity Logs</h3>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleLogs()">
                            <i class="fas fa-chevron-down" id="logs-toggle-icon"></i>
                        </button>
                    </div>
                    <div class="logs-content" id="logs-content">
                        <textarea class="form-control logs-textarea" id="result" rows="10" placeholder="Activity logs will appear here..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Link Modal -->
    <div class="modal fade" id="templateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-link"></i> Template Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="template-link" readonly>
                        <button class="btn btn-primary" onclick="copyTemplateLink()">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/sweetalert2.min.js"></script>
    <script src="./assets/js/growl-notification.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/dashboard.js"></script>
</body>
</html>
