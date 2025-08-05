<?php
session_start();
require_once 'dbconnect.php';
require_once 'rate_limiter.php';
require_once 'csrf_helper.php';
require_once 'error_logger.php';

// Handle form submission
$error = '';
$user_ip = getUserIP();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check rate limiting
    $rate_check = checkLoginAttempts($user_ip);
    if (!$rate_check['allowed']) {
        $error = 'Too many login attempts. Please try again in ' . formatTime($rate_check['lockout_time']) . '.';
    } else {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        // Simple validation
        if ($email == '' || $password == '') {
            $error = "Please enter all fields: Email and Password.";
        } else {
            // Query to check user with proper prepared statement
            $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($password === $row['password']) {
                    // Successful login - clear rate limiting
                    clearLoginAttempts($user_ip);
                    regenerateSession();
                    
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['user_login_time'] = time();
                    $_SESSION['user_ip'] = $user_ip;
                    
                    header("Location: index.php");
                    exit();
                } else {
                    recordFailedLogin($user_ip);
                    $error = "Incorrect email or password.";
                }
            } else {
                recordFailedLogin($user_ip);
                $error = "Incorrect email or password.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
       body {
            background: linear-gradient(120deg, #0d6efd 60%, #f4f6f8 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 350px;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: .5rem;
            color: #1565c0;
        }
        .form-group input {
            width: 100%;
            padding: .7rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        .login-btn {
            width: 100%;
            padding: .8rem;
            background: linear-gradient(90deg, #0d6efd 60%, #1565c0 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1.08rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(13,110,253,0.07);
        }
        .login-btn:hover {
            background: #0056b3;
        }
        .login-footer {
            margin-top: 1rem;
            text-align: center;
            font-size: .95rem;
        }
        .login-footer a {
            color: #007bff;
            text-decoration: none;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #d8000c;
            background: #ffd2d2;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error != ''): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="user-login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="off">
            </div>
            <button type="submit" class="login-btn">Sign In</button>
        </form>
        <div class="login-footer">
            <span>Don't have an account? <a href="signup.php">Sign up</a></span>
        </div>
    </div>
</body>
</html>
