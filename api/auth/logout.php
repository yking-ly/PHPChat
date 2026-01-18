<?php
/**
 * User Logout API
 * Handles user logout and session destruction
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/security.php';

// Update user status to offline before destroying session
if (isLoggedIn()) {
    $pdo = getDBConnection();
    if ($pdo) {
        $stmt = $pdo->prepare("UPDATE user_status SET is_online = 0, last_seen = NOW() WHERE user_id = ?");
        $stmt->execute([getCurrentUserId()]);
    }
}

// Destroy session
destroySession();

// Check if this is an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit();
}

// Redirect to login page
header('Location: ../../login.php');
exit();
?>