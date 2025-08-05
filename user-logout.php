<?php
session_start();

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Destroy session
    session_unset();
    session_destroy();
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout | QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #0d6efd 60%, #f4f6f8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(13,110,253,0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .logout-icon {
            font-size: 4rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .btn-logout {
            background: #dc3545;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            margin: 10px;
        }
        .btn-logout:hover {
            background: #c82333;
        }
        .btn-cancel {
            background: #6c757d;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            margin: 10px;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <i class="bi bi-box-arrow-right logout-icon"></i>
        <h3 class="text-primary mb-3">Logout Confirmation</h3>
        <p class="text-muted mb-4">Are you sure you want to logout from your account?</p>
        
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-danger btn-logout" onclick="confirmLogout()">
                <i class="bi bi-check-circle"></i> Yes, Logout
            </button>
            <button type="button" class="btn btn-secondary btn-cancel" onclick="cancelLogout()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmLogout() {
            // Show confirmation alert
            if (confirm("Are you sure you want to logout?")) {
                // Perform logout via AJAX
                fetch('user-logout.php?action=logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success alert
                        alert("You have been successfully logged out!");
                        // Redirect to home page
                        window.location.href = 'index.php';
                    } else {
                        alert("An error occurred during logout. Please try again.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred during logout. Please try again.");
                });
            }
        }

        function cancelLogout() {
            // Redirect back to the previous page or home
            window.history.back() || (window.location.href = 'index.php');
        }

        // Check if this is a direct access to logout
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('direct') === 'true') {
                confirmLogout();
            }
        });
    </script>
</body>
</html>
