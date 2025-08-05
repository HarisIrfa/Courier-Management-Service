<?php
/**
 * CSRF Protection Helper
 * Provides functions to generate and validate CSRF tokens
 */

/**
 * Generate a CSRF token and store it in session
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

/**
 * Validate CSRF token
 * @param string $token Token to validate
 * @param int $timeout Token timeout in seconds (default: 3600 = 1 hour)
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token, $timeout = 3600) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Check if token exists in session
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Check if token has expired
    if (time() - $_SESSION['csrf_token_time'] > $timeout) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    // Validate token using hash_equals to prevent timing attacks
    $isValid = hash_equals($_SESSION['csrf_token'], $token);
    
    // Remove token after use (one-time use)
    if ($isValid) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }
    
    return $isValid;
}

/**
 * Get HTML input field for CSRF token
 * @return string HTML input field
 */
function getCSRFTokenField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Regenerate session ID for security
 */
function regenerateSession() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
}
?>
