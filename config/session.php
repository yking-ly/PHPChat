<?php
/**
 * Session Configuration
 * Secure session management settings
 */

// Session security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.gc_maxlifetime', 1800); // 30 minutes timeout

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 * @return string|null
 */
function getCurrentUsername()
{
    return $_SESSION['username'] ?? null;
}

/**
 * Require authentication - redirect to login if not authenticated
 */
function requireAuth()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn()
{
    if (isLoggedIn()) {
        header('Location: chat.php');
        exit();
    }
}

/**
 * Regenerate session ID for security
 */
function regenerateSession()
{
    session_regenerate_id(true);
}

/**
 * Destroy session and logout
 */
function destroySession()
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}
?>