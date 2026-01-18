<?php
/**
 * Get Messages API
 * Fetches messages between current user and specified user
 * Supports pagination for loading older messages
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
$otherUserId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$beforeId = filter_input(INPUT_GET, 'before_id', FILTER_VALIDATE_INT);
$afterId = filter_input(INPUT_GET, 'after_id', FILTER_VALIDATE_INT);
$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 20;

if (!$otherUserId) {
    jsonResponse(false, 'Invalid user ID');
}

$limit = min($limit, 50); // Cap at 50 messages

$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(false, 'Database connection failed');
}

// Build query based on pagination type
if ($afterId) {
    // Polling for new messages
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.timestamp, m.is_read,
                   u.username as sender_username, u.avatar_color as sender_color
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.id > ?
            ORDER BY m.id ASC
            LIMIT ?";
    $params = [$userId, $otherUserId, $otherUserId, $userId, $afterId, $limit];
} elseif ($beforeId) {
    // Loading older messages
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.timestamp, m.is_read,
                   u.username as sender_username, u.avatar_color as sender_color
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.id < ?
            ORDER BY m.id DESC
            LIMIT ?";
    $params = [$userId, $otherUserId, $otherUserId, $userId, $beforeId, $limit];
} else {
    // Initial load - get latest messages
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.timestamp, m.is_read,
                   u.username as sender_username, u.avatar_color as sender_color
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            ORDER BY m.id DESC
            LIMIT ?";
    $params = [$userId, $otherUserId, $otherUserId, $userId, $limit];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Reverse for proper order if loading initial or older messages
if (!$afterId) {
    $messages = array_reverse($messages);
}

// Sanitize messages for output
foreach ($messages as &$msg) {
    $msg['message'] = sanitizeOutput($msg['message']);
}

// Check if there are more older messages
$hasMore = false;
if (!empty($messages)) {
    $firstMessageId = $messages[0]['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages 
                           WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
                           AND id < ?");
    $stmt->execute([$userId, $otherUserId, $otherUserId, $userId, $firstMessageId]);
    $hasMore = $stmt->fetchColumn() > 0;
}

// Mark received messages as read
$stmt = $pdo->prepare("UPDATE messages SET is_read = 1 
                       WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
$stmt->execute([$otherUserId, $userId]);

jsonResponse(true, '', [
    'messages' => $messages,
    'has_more' => $hasMore,
    'current_user_id' => $userId
]);
?>