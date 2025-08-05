<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout | QUICK Deliver Agent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .logout-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 50px 0;
            margin: 80px auto 40px auto;
            max-width: 450px;
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
    <div class="logout-section text-center animate__animated animate__fadeInDown">
        <img src="images/logo1.jpg" alt="Logo" width="70" height="70" class="rounded-circle mb-3">
        <h2 class="mb-3 text-primary fw-bold"><i class="bi bi-box-arrow-right"></i> Logged Out</h2>
        <p class="mb-4" style="font-size:1.08rem;">
            You have been successfully logged out.<br>
            Thank you for using <span class="fw-bold">QUICK Deliver Agent Panel</span>.
        </p>
        <a href="agent-login.php" class="btn btn-primary btn-lg px-5 shadow-sm"><i class="bi bi-box-arrow-in-right"></i> Login Again</a>
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
</body>
</html>