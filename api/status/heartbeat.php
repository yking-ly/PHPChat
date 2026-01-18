<?php
/**
 * Heartbeat API
 * Updates user's online status and last seen timestamp
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/security.php';

header('Content-Type: application/json');

// Require authentication
if (!isLoggedIn()) {
    jsonResponse(false, 'Not authenticated');
}

$userId = getCurrentUserId();

$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(false, 'Database connection failed');
}

// Update user status
$stmt = $pdo->prepare("UPDATE user_status SET is_online = 1, last_seen = NOW() WHERE user_id = ?");
$result = $stmt->execute([$userId]);

// Update session last activity
$_SESSION['last_activity'] = time();

// Mark users as offline who haven't sent heartbeat in 2 minutes
$stmt = $pdo->prepare("UPDATE user_status SET is_online = 0 WHERE last_seen < DATE_SUB(NOW(), INTERVAL 2 MINUTE)");
$stmt->execute();

if ($result) {
    jsonResponse(true, 'Heartbeat received');
} else {
    jsonResponse(false, 'Failed to update status');
}
?>