<?php
/**
 * Security Functions
 * CSRF protection, input sanitization, and validation
 */

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool
 */
function verifyCSRFToken($token)
{
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input to prevent XSS
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Sanitize output for display
 * @param string $output Output to sanitize
 * @return string Sanitized output
 */
function sanitizeOutput($output)
{
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 * @param string $email Email to validate
 * @return bool
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate username format (alphanumeric, 3-20 chars)
 * @param string $username Username to validate
 * @return bool
 */
function validateUsername($username)
{
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Validate password strength (min 8 chars)
 * @param string $password Password to validate
 * @return bool
 */
function validatePassword($password)
{
    return strlen($password) >= 8;
}

/**
 * Hash password securely
 * @param string $password Password to hash
 * @return string Hashed password
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 * @param string $password Password to verify
 * @param string $hash Hash to verify against
 * @return bool
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Send JSON response
 * @param bool $success Success status
 * @param string $message Message
 * @param array $data Additional data
 */
function jsonResponse($success, $message = '', $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>