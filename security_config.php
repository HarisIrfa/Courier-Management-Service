<?php
/**
 * Security Configuration
 * Central configuration for security settings
 */

// Security Headers
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (basic)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net;");
}

// Input Sanitization
function sanitizeInput($input, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'url':
            return filter_var(trim($input), FILTER_SANITIZE_URL);
        case 'string':
        default:
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

// Validate Input
function validateInput($input, $type = 'string', $options = []) {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        case 'int':
            $min = $options['min'] ?? null;
            $max = $options['max'] ?? null;
            if ($min !== null || $max !== null) {
                $filter_options = [];
                if ($min !== null) $filter_options['min_range'] = $min;
                if ($max !== null) $filter_options['max_range'] = $max;
                return filter_var($input, FILTER_VALIDATE_INT, [
                    'options' => $filter_options
                ]) !== false;
            }
            return filter_var($input, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
        case 'url':
            return filter_var($input, FILTER_VALIDATE_URL) !== false;
        case 'phone':
            return preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $input);
        case 'name':
            return preg_match('/^[a-zA-Z\s\-\.\']{2,50}$/', $input);
        case 'string':
        default:
            $min_length = $options['min_length'] ?? 1;
            $max_length = $options['max_length'] ?? 255;
            $length = strlen($input);
            return $length >= $min_length && $length <= $max_length;
    }
}

// Security Configuration Constants
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('SESSION_TIMEOUT', 10800); // 3 hours
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Set security headers on every request
setSecurityHeaders();
?>
