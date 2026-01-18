<?php
/**
 * Send Message API
 * Handles sending messages between users
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/security.php';

header('Content-Type: application/json');

// Require authentication
if (!isLoggedIn()) {
    jsonResponse(false, 'Not authenticated');
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Get input
$receiverId = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
$message = trim($_POST['message'] ?? '');

// Validation
if (!$receiverId) {
    jsonResponse(false, 'Invalid receiver');
}

if (empty($message)) {
    jsonResponse(false, 'Message cannot be empty');
}

if (strlen($message) > 5000) {
    jsonResponse(false, 'Message is too long');
}

$senderId = getCurrentUserId();

// Can't send message to yourself
if ($receiverId === $senderId) {
    jsonResponse(false, 'Cannot send message to yourself');
}

$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(false, 'Database connection failed');
}

// Verify receiver exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$receiverId]);
if (!$stmt->fetch()) {
    jsonResponse(false, 'Receiver not found');
}

// Sanitize message for storage (but keep HTML entities for display)
$safeMessage = $message;

// Insert message
$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
$result = $stmt->execute([$senderId, $receiverId, $safeMessage]);

if ($result) {
    $messageId = $pdo->lastInsertId();

    // Get the inserted message with formatted timestamp
    $stmt = $pdo->prepare("SELECT id, sender_id, receiver_id, message, timestamp, is_read FROM messages WHERE id = ?");
    $stmt->execute([$messageId]);
    $newMessage = $stmt->fetch();

    jsonResponse(true, 'Message sent', [
        'message' => [
            'id' => $newMessage['id'],
            'sender_id' => $newMessage['sender_id'],
            'receiver_id' => $newMessage['receiver_id'],
            'message' => sanitizeOutput($newMessage['message']),
            'timestamp' => $newMessage['timestamp'],
            'is_read' => $newMessage['is_read']
        ]
    ]);
} else {
    jsonResponse(false, 'Failed to send message');
}
?>