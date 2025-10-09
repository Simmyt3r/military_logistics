<?php
// File: military_logistics/api/track.php
// This API endpoint provides the real-time data for the asset tracking map.

// Set header to output JSON
header('Content-Type: application/json');

// Bootstrap the application
require_once '../config/config.php';
require_once '../helpers/session_helper.php';
require_once '../classes/AssetTracking.php';

// Ensure user is logged in to access this API
if (!isLoggedIn()) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Authentication required.']);
    exit;
}

try {
    $tracking = new AssetTracking();
    
    // Get user role and unit ID from session to provide role-based data
    $user_role = $_SESSION['user_role'] ?? 'field_unit';
    $user_unit_id = $_SESSION['user_unit_id'] ?? null;

    // Fetch live asset locations based on user's role
    $live_assets = $tracking->getLiveAssetLocations($user_role, $user_unit_id);
    
    // Fetch all map zones (all users can see all zones for situational awareness)
    $map_zones = $tracking->getMapZones();

    // Combine data into a single response object
    $response_data = [
        'assets' => $live_assets,
        'zones' => $map_zones,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response_data);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred while fetching tracking data.', 'details' => DEBUG_MODE ? $e->getMessage() : '']);
}
