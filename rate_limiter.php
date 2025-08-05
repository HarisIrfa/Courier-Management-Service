<?php
/**
 * Rate Limiter Helper
 * Prevents brute force attacks by limiting login attempts
 */

/**
 * Check if IP address has exceeded maximum login attempts
 * @param string $ip IP address to check
 * @param int $max_attempts Maximum allowed attempts (default: 5)
 * @param int $lockout_time Lockout duration in seconds (default: 900 = 15 minutes)
 * @return array ['allowed' => bool, 'remaining_attempts' => int, 'lockout_time' => int]
 */
function checkLoginAttempts($ip, $max_attempts = 5, $lockout_time = 900) {
    $attempts_file = 'login_attempts.json';
    $current_time = time();
    
    // Read existing attempts
    $attempts_data = [];
    if (file_exists($attempts_file)) {
        $json_data = file_get_contents($attempts_file);
        $attempts_data = json_decode($json_data, true) ?? [];
    }
    
    // Clean old attempts for all IPs (older than lockout time)
    foreach ($attempts_data as $ip_key => $ip_attempts) {
        $attempts_data[$ip_key] = array_filter($ip_attempts, function($attempt_time) use ($current_time, $lockout_time) {
            return ($current_time - $attempt_time) < $lockout_time;
        });
        
        // Remove IP entries that have no attempts left
        if (empty($attempts_data[$ip_key])) {
            unset($attempts_data[$ip_key]);
        }
    }
    
    // Check current IP attempts
    $ip_attempts = isset($attempts_data[$ip]) ? $attempts_data[$ip] : [];
    $attempt_count = count($ip_attempts);
    
    // Clean old attempts for this IP
    $ip_attempts = array_filter($ip_attempts, function($attempt_time) use ($current_time, $lockout_time) {
        return ($current_time - $attempt_time) < $lockout_time;
    });
    
    $attempt_count = count($ip_attempts);
    $remaining_attempts = max(0, $max_attempts - $attempt_count);
    
    // If maximum attempts reached, calculate remaining lockout time
    if ($attempt_count >= $max_attempts) {
        $oldest_attempt = min($ip_attempts);
        $remaining_lockout = $lockout_time - ($current_time - $oldest_attempt);
        return [
            'allowed' => false,
            'remaining_attempts' => 0,
            'lockout_time' => max(0, $remaining_lockout)
        ];
    }
    
    return [
        'allowed' => true,
        'remaining_attempts' => $remaining_attempts,
        'lockout_time' => 0
    ];
}

/**
 * Record a failed login attempt
 * @param string $ip IP address
 */
function recordFailedLogin($ip) {
    $attempts_file = 'login_attempts.json';
    $current_time = time();
    
    // Read existing attempts
    $attempts_data = [];
    if (file_exists($attempts_file)) {
        $json_data = file_get_contents($attempts_file);
        $attempts_data = json_decode($json_data, true) ?? [];
    }
    
    // Add current attempt
    if (!isset($attempts_data[$ip])) {
        $attempts_data[$ip] = [];
    }
    $attempts_data[$ip][] = $current_time;
    
    // Save attempts
    file_put_contents($attempts_file, json_encode($attempts_data), LOCK_EX);
}

/**
 * Clear login attempts for an IP (successful login)
 * @param string $ip IP address
 */
function clearLoginAttempts($ip) {
    $attempts_file = 'login_attempts.json';
    
    if (file_exists($attempts_file)) {
        $json_data = file_get_contents($attempts_file);
        $attempts_data = json_decode($json_data, true) ?? [];
        
        // Remove attempts for this IP
        unset($attempts_data[$ip]);
        
        // Save updated data
        file_put_contents($attempts_file, json_encode($attempts_data), LOCK_EX);
    }
}

/**
 * Get user's real IP address
 * @return string IP address
 */
function getUserIP() {
    // Check for IP from various headers
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
    
    // Handle comma-separated IPs (take first one)
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }
    
    // Validate IP address
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '0.0.0.0';
    }
    
    return $ip;
}

/**
 * Format time duration in human readable format
 * @param int $seconds Time in seconds
 * @return string Formatted time
 */
function formatTime($seconds) {
    if ($seconds < 60) {
        return $seconds . ' seconds';
    } elseif ($seconds < 3600) {
        return ceil($seconds / 60) . ' minutes';
    } else {
        return ceil($seconds / 3600) . ' hours';
    }
}
?>
