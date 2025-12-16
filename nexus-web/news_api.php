<?php
/**
 * News API Endpoint
 * Serves news items from news_config.json
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$config_file = __DIR__ . '/../news_config.json';

if (!file_exists($config_file)) {
    echo json_encode(['error' => 'News configuration file not found']);
    exit;
}

$news_config = json_decode(file_get_contents($config_file), true);

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    
    if (isset($news_config[$type])) {
        echo json_encode($news_config[$type]);
    } else {
        echo json_encode(['error' => 'Invalid news type']);
    }
} else {
    // Return all news
    echo json_encode($news_config);
}

?>

