<?php
/**
 * Get Users API
 * Fetches all users except current user with their online status
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/security.php';

header('Content-Type: application/json');

// Require authentication
if (!isLoggedIn()) {
    jsonResponse(false, 'Not authenticated');
}

$currentUserId = getCurrentUserId();

$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(false, 'Database connection failed');
}

// Get all users with their status and last message
$sql = "SELECT 
            u.id,
            u.username,
            u.avatar_color,
            us.is_online,
            us.last_seen,
            (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count,
            (SELECT message FROM messages 
             WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id)
             ORDER BY timestamp DESC LIMIT 1) as last_message,
            (SELECT timestamp FROM messages 
             WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id)
             ORDER BY timestamp DESC LIMIT 1) as last_message_time
        FROM users u
        LEFT JOIN user_status us ON u.id = us.user_id
        WHERE u.id != ?
        ORDER BY last_message_time DESC, u.username ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId]);
$users = $stmt->fetchAll();

// Format users data
foreach ($users as &$user) {
    // Check if user is offline based on last activity (2 minutes threshold)
    $lastSeen = strtotime($user['last_seen']);
    $isOnline = $user['is_online'] && (time() - $lastSeen) < 120;

    $user['is_online'] = $isOnline;
    $user['last_seen_formatted'] = $isOnline ? 'Online' : formatLastSeen($user['last_seen']);
    $user['last_message'] = $user['last_message'] ? sanitizeOutput(substr($user['last_message'], 0, 50)) . (strlen($user['last_message']) > 50 ? '...' : '') : '';
}

jsonResponse(true, '', ['users' => $users]);

/**
 * Format last seen timestamp
 * @param string $timestamp
 * @return string
 */
function formatLastSeen($timestamp)
{
    if (!$timestamp)
        return 'Never';

    $lastSeen = strtotime($timestamp);
    $diff = time() - $lastSeen;

    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $lastSeen);
    }
}
?>