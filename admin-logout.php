<?php
session_start();
// Destroy admin session
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logout | QUICK Deliver</title>
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
        .logout-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            padding: 40px 32px;
            text-align: center;
        }
        .logout-container .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 18px;
        }
        .logout-container h2 {
            font-size: 2rem;
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .logout-container p {
            font-size: 1.08rem;
            margin-bottom: 24px;
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background: #1565c0;
        }
        .logout-footer {
            text-align: center;
            margin-top: 32px;
            color: #888;
            font-size: 0.98rem;
        }
        @media (max-width: 575.98px) {
            .logout-container {
                padding: 24px 8px;
                margin: 32px 4px;
            }
            .logout-container h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container animate__animated animate__fadeInUp">
        <img src="images/logo1.jpg" alt="QUICK Deliver Logo" class="logo rounded-circle shadow">
        <h2>Logged Out</h2>
        <p>You have been successfully logged out.<br>Thank you for using <strong>QUICK Deliver</strong> Admin Panel.</p>
        <a href="admin-login.php" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-left me-1"></i> Back to Login</a>
        <div class="logout-footer mt-4">
            &copy; <?php echo date("Y"); ?> QUICK Deliver. All rights reserved.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
