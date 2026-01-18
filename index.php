<?php
/**
 * Index - Redirect to appropriate page
 */
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: chat.php');
} else {
    header('Location: login.php');
}
exit();
?>