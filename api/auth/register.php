<?php
/**
 * User Registration API
 * Handles new user registration with validation
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

// Get and validate input
$username = sanitizeInput($_POST['username'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validation
$errors = [];

if (empty($username)) {
    $errors[] = 'Username is required';
} elseif (!validateUsername($username)) {
    $errors[] = 'Username must be 3-20 characters (letters, numbers, underscores only)';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (!validatePassword($password)) {
    $errors[] = 'Password must be at least 8 characters';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match';
}

if (!empty($errors)) {
    jsonResponse(false, implode(', ', $errors));
}

// Check if username or email already exists
$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(false, 'Database connection failed');
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);

if ($stmt->fetch()) {
    jsonResponse(false, 'Username or email already exists');
}

// Generate random avatar color
$colors = ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#3b82f6'];
$avatarColor = $colors[array_rand($colors)];

// Create user
$hashedPassword = hashPassword($password);

$stmt = $pdo->prepare("INSERT INTO users (username, email, password, avatar_color) VALUES (?, ?, ?, ?)");
$result = $stmt->execute([$username, $email, $hashedPassword, $avatarColor]);

if ($result) {
    jsonResponse(true, 'Registration successful! You can now login.');
} else {
    jsonResponse(false, 'Registration failed. Please try again.');
}
?>