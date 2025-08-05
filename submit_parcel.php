<?php
session_start();
require_once 'dbconnect.php';
$success = "";
$error = "";
$consignment_no = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $sender_name = trim($_POST['sender_name']);
    $sender_phone = trim($_POST['sender_phone']);
    $sender_email = isset($_POST['sender_email']) ? trim($_POST['sender_email']) : '';
    $receiver_name = trim($_POST['receiver_name']);
    $receiver_phone = trim($_POST['receiver_phone']);
    $receiver_email = isset($_POST['receiver_email']) ? trim($_POST['receiver_email']) : '';
    $from_city = trim($_POST['from_city']);
    $to_city = trim($_POST['to_city']);
    $weight = floatval($_POST['weight']);
    $parcel_type = trim($_POST['parcel_type']);
    $preferred_delivery = $_POST['preferred_delivery'];

    // Generate unique consignment number
    $consignment_no = 'CN' . date('Y') . rand(100000, 999999);

    // Handle image upload with enhanced security
    $image_path = null;
    if (isset($_FILES['parcel_image']) && $_FILES['parcel_image']['error'] == 0) {
        $upload_dir = 'uploads/parcels/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Security validations
        $file_size = $_FILES['parcel_image']['size'];
        $file_tmp = $_FILES['parcel_image']['tmp_name'];
        $file_name = $_FILES['parcel_image']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Define allowed file types and max size
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validate file size
        if ($file_size > $max_size) {
            $error = "File size too large. Maximum allowed size is 5MB.";
        }
        // Validate file extension
        elseif (!in_array($file_extension, $allowed_types)) {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
        // Validate file is actually an image
        elseif (!getimagesize($file_tmp)) {
            $error = "File is not a valid image.";
        } else {
            // Generate secure filename
            $filename = $consignment_no . '_' . uniqid() . '.' . $file_extension;
            $image_path = $upload_dir . $filename;

            if (!move_uploaded_file($file_tmp, $image_path)) {
                $error = "Error uploading image. Please try again.";
            }
        }
    }

    if (empty($error)) {
        // Insert into packages table - need to get user_id from session or set to 1 as default
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to user ID 1 if not logged in

        // For now, use placeholder addresses since form doesn't collect them
        $sender_address = $from_city; // Using city as address placeholder
        $receiver_address = $to_city; // Using city as address placeholder

        $stmt = $conn->prepare("INSERT INTO packages (user_id, consignment_no, sender_name, sender_phone, sender_email, sender_address, receiver_name, receiver_phone, receiver_email, receiver_address, parcel_type, weight, from_city, to_city, status, pickup_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $status = 'Pending';
        $stmt->bind_param('issssssssssssdss', $user_id, $consignment_no, $sender_name, $sender_phone, $sender_email, $sender_address, $receiver_name, $receiver_phone, $receiver_email, $receiver_address, $parcel_type, $weight, $from_city, $to_city, $status, $preferred_delivery);
        if ($stmt->execute()) {
            // Add initial tracking entry
            $package_id = $conn->insert_id;
            $tracking_stmt = $conn->prepare("INSERT INTO tracking (package_id, status, event_time, location, description) VALUES (?, 'Package Received', NOW(), ?, 'Package received and assigned consignment number')");
            if ($tracking_stmt) {
                $tracking_stmt->bind_param('is', $package_id, $from_city);
                if (!$tracking_stmt->execute()) {
                    // Log tracking error but don't fail the whole operation
                    error_log("Tracking insertion failed: " . $tracking_stmt->error);
                }
                $tracking_stmt->close();
            }

            $success = "Parcel information submitted successfully!<br><strong>Your Consignment Number: " . $consignment_no . "</strong><br>Please save this number for tracking your parcel.";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Parcel | QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #0d6efd 60%, #f4f6f8 100%);
            min-height: 100vh;
            /* padding: 0px 0; */
        }

        .navbar {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .form-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(13, 110, 253, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-section {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            background: #f8f9fa;
        }

        .form-section h5 {
            color: #0d6efd;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .image-upload-area {
            border: 2px dashed #0d6efd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s ease;
        }

        .image-upload-area:hover {
            background: #e9ecff;
        }

        .footer {
            background: #0d6efd;
            color: #fff;
            padding: 32px 0;
            margin-top: 40px;
        }

        .btn-primary {
            background: #0d6efd;
            border: none;
            padding: 12px 40px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #1565c0;
        }
    </style>
</head>

<body>
    <!-- navbar start -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logo1.jpg" alt="Logo" width="60" height="60" class="rounded-circle me-2">
                <span class="fw-bold fs-4 text-primary">QUICK Deliver</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link  fs-5 mx-3" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 mx-3" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-5 mx-3" href="contact.php">Contact</a>
                    </li>
                    <?php
                    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
                    ?>
                        <li class="nav-item">
                            <a class="nav-link active fs-5 mx-3" href="submit_parcel.php">Submit Parcel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-5 mx-3" href="tracking.php">Tracking</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary" href="#" onclick="handleLogout(event)">Logout</a>
                        </li>
                    <?php
                    } else {
                    ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary me-2" href="user-login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary" href="signup.php">Sign Up</a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- navbar end -->

    <div class="form-container animate__animated animate__fadeInUp">
        <div class="form-header">
            <h2><i class="bi bi-box-seam"></i> Submit Your Parcel</h2>
            <p class="text-muted">Fill in the details below to ship your parcel with QUICK Deliver</p>
        </div>

        <?php if ($error != ''): ?>
            <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success != ''): ?>
            <div class="alert alert-success animate__animated animate__bounceIn" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                <div class="mt-3">
                    <a href="tracking.php" class="btn btn-outline-success">Track Your Parcel</a>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- Sender Information -->
            <div class="form-section">
                <h5><i class="bi bi-person-circle"></i> Sender Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sender_name" class="form-label">Sender Name *</label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sender_phone" class="form-label">Sender Phone *</label>
                        <input type="number" class="form-control" id="sender_phone" name="sender_phone" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="sender_email" class="form-label">Sender Email *</label>
                        <input type="email" class="form-control" id="sender_email" name="sender_email" required>
                    </div>
                </div>
            </div>

            <!-- Receiver Information -->
            <div class="form-section">
                <h5><i class="bi bi-person-check"></i> Receiver Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="receiver_name" class="form-label">Receiver Name *</label>
                        <input type="text" class="form-control" id="receiver_name" name="receiver_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="receiver_phone" class="form-label">Receiver Phone *</label>
                        <input type="number" class="form-control" id="receiver_phone" name="receiver_phone" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="receiver_email" class="form-label">Receiver Email *</label>
                        <input type="email" class="form-control" id="receiver_email" name="receiver_email" required>
                    </div>
                </div>
            </div>

            <!-- Parcel Details -->
            <div class="form-section">
                <h5><i class="bi bi-box"></i> Parcel Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="from_city" class="form-label">From City *</label>
                        <input type="text" class="form-control" id="from_city" name="from_city" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="to_city" class="form-label">To City *</label>
                        <input type="text" class="form-control" id="to_city" name="to_city" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="weight" class="form-label">Weight (kg) *</label>
                        <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="parcel_type" class="form-label">Parcel Type *</label>
                        <select class="form-select" id="parcel_type" name="parcel_type" required>
                            <option value="">Select Type</option>
                            <option value="Documents">Documents</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Clothing">Clothing</option>
                            <option value="Books">Books</option>
                            <option value="Food Items">Food Items</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="preferred_delivery" class="form-label">Preferred Delivery Date</label>
                        <input type="date" class="form-control" id="preferred_delivery" name="preferred_delivery" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                </div>
            </div>

            <!-- Parcel Image -->
            <div class="form-section">
                <h5><i class="bi bi-camera"></i> Parcel Image (Optional)</h5>
                <div class="image-upload-area">
                    <i class="bi bi-cloud-upload fs-1 text-primary mb-3"></i>
                    <p class="mb-2">Click to upload parcel image</p>
                    <input type="file" class="form-control" id="parcel_image" name="parcel_image" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('parcel_image').click()">
                        <i class="bi bi-upload"></i> Choose Image
                    </button>
                    <small class="text-muted d-block mt-2">Maximum file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg animate__animated animate__pulse">
                    <i class="bi bi-send"></i> Submit Parcel
                </button>
            </div>
        </form>
    </div>

   <!-- Footer -->
<footer class="footer animate-on-scroll" data-animate="animate__fadeInUp">
    <div class="container">
        <div class="row align-items-center pb-3">
            <div class="col-12 col-md-4 mb-3 mb-md-0 text-center text-md-start animate-on-scroll" data-animate="animate__fadeInLeft">
                <img src="images/logo1.jpg" alt="Quick Deliver Logo" width="50" height="50" class="rounded-circle mb-2">
                <div class="fw-bold fs-5">QUICK Deliver</div>
                <div style="font-size: 0.95rem;">Fast, Reliable & Secure Delivery</div>
            </div>
            <div class="col-12 col-md-4 mb-3 mb-md-0 text-center animate-on-scroll" data-animate="animate__fadeInUp">
                <div class="mb-2 fw-semibold">Quick Links</div>
                <a href="index.php" class="text-white text-decoration-none me-3">Home</a>
                <a href="about.php" class="text-white text-decoration-none me-3">About</a>
                <a href="contact.php" class="text-white text-decoration-none me-3">Contact</a>
                <!-- <a href="tracking.php" class="text-white text-decoration-none">Tracking</a> -->
            </div>
            <div class="col-12 col-md-4 text-center text-md-end animate-on-scroll" data-animate="animate__fadeInRight">
                <div class="mb-2 fw-semibold">Contact</div>
                <div style="font-size: 0.95rem;">
                    <i class="bi bi-envelope-fill me-1"></i> support@quickdeliver.com<br>
                    <i class="bi bi-telephone-fill me-1"></i> +1 234 567 8901
                </div>
                <div class="mt-2">
                    <a href="" class="text-white me-2"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="" class="text-white me-2"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="" class="text-white"><i class="bi bi-instagram fs-5"></i></a>
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
    <script>
        // Image upload preview
        document.getElementById('parcel_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const uploadArea = document.querySelector('.image-upload-area');
                uploadArea.innerHTML = `
                    <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                    <p class="mb-2 text-success">Image selected: ${file.name}</p>
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('parcel_image').click()">
                        <i class="bi bi-upload"></i> Change Image
                    </button>
                `;
            }
        });

        // Handle logout with confirmation alerts
        function handleLogout(event) {
            event.preventDefault();

            // Show first confirmation alert
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
    </script>
</body>

</html>