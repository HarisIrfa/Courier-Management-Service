<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #0d6efd 60%, #fff 100%);
            color: #333;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            padding: 40px 32px;
        }
        .login-container .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 18px;
        }
        .login-container h2 {
            font-size: 2rem;
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background: #1565c0;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,.15);
        }
        .login-footer {
            text-align: center;
            margin-top: 32px;
            color: #888;
            font-size: 0.98rem;
        }
        @media (max-width: 575.98px) {
            .login-container {
                padding: 24px 8px;
                margin: 32px 4px;
            }
            .login-container h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<?php
    session_start();
    include ("dbconnect.php");
    require_once 'rate_limiter.php';
    require_once 'csrf_helper.php';
    
    $error = '';
    $user_ip = getUserIP();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check rate limiting
        $rate_check = checkLoginAttempts($user_ip);
        if (!$rate_check['allowed']) {
            $error = 'Too many login attempts. Please try again in ' . formatTime($rate_check['lockout_time']) . '.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            if ($username && $password) {
                $sql = "SELECT * FROM admins WHERE email = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $error = 'Database error occurred. Please try again later.';
                } else {
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($row = $result->fetch_assoc()) {
                        if ($password === $row['password']) {
                            // Successful login - clear rate limiting and regenerate session
                            clearLoginAttempts($user_ip);
                            regenerateSession();
                            
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['admin_id'] = $row['id'];
                            $_SESSION['admin_name'] = $row['name'];
                            $_SESSION['admin_login_time'] = time();
                            $_SESSION['admin_ip'] = $user_ip;
                            
                            header('Location: admin.php');
                            exit();
                        } else {
                            recordFailedLogin($user_ip);
                            $error = 'Invalid email or password.';
                        }
                    } else {
                        recordFailedLogin($user_ip);
                        $error = 'Invalid email or password.';
                    }
                    $stmt->close();
                }
            } else {
                $error = 'Please fill in all fields.';
            }
        }
    }
?>
    <div class="login-container animate__animated animate__fadeInUp">
        <div class="text-center">
            <img src="images/logo1.jpg" alt="QUICK Deliver Logo" class="logo rounded-circle shadow">
            <h2>Admin Login</h2>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 mb-3" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($debug) && isset($_GET['debug'])): ?>
            <div class="alert alert-info py-2 mb-3" role="alert">
                <h6>Debug Information:</h6>
                <?php foreach ($debug as $d): ?>
                    <small><?php echo htmlspecialchars($d); ?></small><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="admin-login.php" method="post" autocomplete="off">
            <div class="mb-3">
                <label for="username" class="form-label">Email</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Login <i class="bi bi-box-arrow-in-right ms-1"></i></button>
        </form>
        <div class="login-footer mt-4">
            &copy; <?php echo date("Y"); ?> QUICK Deliver. All rights reserved.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
