<?php
/**
 * Error Logging Helper
 * Provides secure error logging functionality
 */

/**
 * Log an error to file with context information
 * @param string $error_message Error message
 * @param string $error_type Type of error (e.g., 'LOGIN_FAILED', 'SQL_ERROR', 'VALIDATION_ERROR')
 * @param array $context Additional context information
 */
function logError($error_message, $error_type = 'GENERAL', $context = []) {
    $log_file = 'logs/error.log';
    $log_dir = dirname($log_file);
    
    // Create logs directory if it doesn't exist
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    // Prepare log entry
    $timestamp = date('Y-m-d H:i:s');
    $user_ip = getUserIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $request_uri = $_SERVER['REQUEST_URI'] ?? 'Unknown';
    $request_method = $_SERVER['REQUEST_METHOD'] ?? 'Unknown';
    
    // Build log entry
    $log_entry = [
        'timestamp' => $timestamp,
        'type' => $error_type,
        'message' => $error_message,
        'ip' => $user_ip,
        'user_agent' => $user_agent,
        'request_uri' => $request_uri,
        'request_method' => $request_method,
        'context' => $context
    ];
    
    // Format log entry
    $log_line = json_encode($log_entry) . "\n";
    
    // Write to log file
    file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

/**
 * Log security events
 * @param string $event_type Type of security event
 * @param string $description Description of the event
 * @param array $context Additional context
 */
function logSecurityEvent($event_type, $description, $context = []) {
    logError($description, 'SECURITY_' . strtoupper($event_type), $context);
}

/**
 * Get user IP address (same as in rate_limiter.php but included for completeness)
 * @return string IP address
 */
if (!function_exists('getUserIP')) {
    function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        return $ip;
    }
}

/**
 * Create .htaccess file to protect logs directory
 */
function protectLogsDirectory() {
    $logs_dir = 'logs';
    $htaccess_file = $logs_dir . '/.htaccess';
    $htaccess_content = "Order Deny,Allow\nDeny from all";
    
    // Create logs directory if it doesn't exist
    if (!file_exists($logs_dir)) {
        mkdir($logs_dir, 0755, true);
    }
    
    // Create .htaccess file if it doesn't exist
    if (!file_exists($htaccess_file)) {
        file_put_contents($htaccess_file, $htaccess_content);
    }
}

/**
 * Clean old log files (older than specified days)
 * @param int $days_to_keep Number of days to keep logs (default: 30)
 */
function cleanOldLogs($days_to_keep = 30) {
    $log_file = 'logs/error.log';
    
    if (!file_exists($log_file)) {
        return;
    }
    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $cutoff_time = time() - ($days_to_keep * 24 * 60 * 60);
    $filtered_lines = [];
    
    foreach ($lines as $line) {
        $log_entry = json_decode($line, true);
        if ($log_entry && isset($log_entry['timestamp'])) {
            $log_time = strtotime($log_entry['timestamp']);
            if ($log_time >= $cutoff_time) {
                $filtered_lines[] = $line;
            }
        }
    }
    
    // Write filtered logs back to file
    file_put_contents($log_file, implode("\n", $filtered_lines) . "\n");
}

// Initialize logging protection
protectLogsDirectory();
?>
