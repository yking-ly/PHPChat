<?php
/**
 * User Login API
 * Handles user authentication
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/security.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(false, 'Invalid security token. Please refresh the page.');
}

// Get input
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($username) || empty($password)) {
    jsonResponse(false, 'Username and password are required');
}

// Get database connection
$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(false, 'Database connection failed');
}

// Find user
$stmt = $pdo->prepare("SELECT id, username, email, password, avatar_color FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $username]);
$user = $stmt->fetch();

if (!$user || !verifyPassword($password, $user['password'])) {
    jsonResponse(false, 'Invalid username or password');
}

// Regenerate session ID for security
regenerateSession();

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['avatar_color'] = $user['avatar_color'];
$_SESSION['last_activity'] = time();

// Update user status to online
$stmt = $pdo->prepare("UPDATE user_status SET is_online = 1, last_seen = NOW() WHERE user_id = ?");
$stmt->execute([$user['id']]);

jsonResponse(true, 'Login successful', [
    'user_id' => $user['id'],
    'username' => $user['username']
]);
?>