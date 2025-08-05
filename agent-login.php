<?php
require_once 'dbconnect.php';
require_once 'rate_limiter.php';
require_once 'csrf_helper.php';

session_start();
$error = '';
$user_ip = getUserIP();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rate_check = checkLoginAttempts($user_ip, 10, 1800); // 10 attempts, 30 min lockout
    if (!$rate_check['allowed']) {
        $error = 'Too many login attempts. Please try again in ' . formatTime($rate_check['lockout_time']) . '.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if ($username && $password) {
            // First check if agent exists and is active
            $sql = "SELECT * FROM agents WHERE email = ? AND status = 'Active' LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $agent_result = $stmt->get_result();
            
            if ($agent_row = $agent_result->fetch_assoc()) {
                // Verify password directly from agents table
                if ($password === $agent_row['password']) {
                    clearLoginAttempts($user_ip);
                    regenerateSession();
                    
                    $_SESSION['agent_logged_in'] = true;
                    $_SESSION['agent_id'] = $agent_row['id'];
                    $_SESSION['agent_name'] = $agent_row['name'];
                    $_SESSION['agent_email'] = $agent_row['email'];
                    $_SESSION['agent_login_time'] = time();
                    $_SESSION['agent_ip'] = $user_ip;
                    
                    header('Location: agent.php');
                    exit();
                } else {
                    recordFailedLogin($user_ip);
                    $error = 'Invalid email or password.';
                }
            } else {
                recordFailedLogin($user_ip);
                $error = 'No active agent found with this email.';
            }
            $stmt->close();
        } else {
            $error = 'Please fill in all fields.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Login | QUICK Deliver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
         body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #0d6efd 60%, #fff 100%);
            color: #333;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .login-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 50px 0;
            margin: 80px auto 40px auto;
            max-width: 420px;
        }
        .footer {
            background: #0d6efd;
            color: #fff;
            padding: 32px 0 0 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="login-section text-center animate__animated animate__fadeInDown">
        <img src="images/logo1.jpg" alt="Logo" width="70" height="70" class="rounded-circle mb-3">
        <h2 class="mb-3 text-primary fw-bold"><i class="bi bi-person-badge"></i> Agent Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger animate__animated animate__shakeX"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" class="px-4">
            <div class="mb-3 text-start">
                <label for="username" class="form-label fw-semibold">Email</label>
                <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Enter username" required autofocus>
            </div>
            <div class="mb-4 text-start">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter password" required>
            </div>
            <!-- FIX: Remove <a> tag from inside the button -->
            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
        <div class="mt-4">
            <div class="mb-2">
                <span>Don't have an account? <a href="agent-register.php" class="text-primary">Register here</a></span>
            </div>
            <!-- <a href="index.php" class="text-decoration-none text-primary"><i class="bi bi-arrow-left"></i> Back to Home</a> -->
        </div>
    </div>

    <footer class="footer animate__animated animate__fadeInUp">
        <div class="container">
            <div class="row align-items-center pb-3">
                <div class="col-md-4 mb-3 mb-md-0 text-center text-md-start">
                    <img src="images/logo1.jpg" alt="Quick Deliver Logo" width="50" height="50" class="rounded-circle mb-2">
                    <div class="fw-bold fs-5">QUICK Deliver</div>
                    <div style="font-size: 0.95rem;">Fast, Reliable & Secure Delivery</div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 text-center">
                    <div class="mb-2 fw-semibold">Quick Links</div>
                    <a href="index.php" class="text-white text-decoration-none me-3">Home</a>
                    <a href="about.php" class="text-white text-decoration-none me-3">About</a>
                    <a href="contact.php" class="text-white text-decoration-none me-3">Contact</a>
                    <a href="tracking.php" class="text-white text-decoration-none">Tracking</a>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div class="mb-2 fw-semibold">Contact</div>
                    <div style="font-size: 0.95rem;">
                        <i class="bi bi-envelope-fill me-1"></i> support@quickdeliver.com<br>
                        <i class="bi bi-telephone-fill me-1"></i> +1 234 567 8901
                    </div>
                    <div class="mt-2">
                        <a href="#" class="text-white me-2"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-2"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram fs-5"></i></a>
                    </div>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.15);">
            <div class="text-center pb-2" style="font-size: 0.97rem;">
                &copy; <?php echo date("Y"); ?> QUICK Deliver. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>