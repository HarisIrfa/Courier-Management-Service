<?php
require_once 'dbconnect.php';
require_once 'security_config.php';
require_once 'error_logger.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name'], 'string');
    $email = sanitizeInput($_POST['email'], 'email');
    $phone = sanitizeInput($_POST['phone'], 'string');
    $branch_city = sanitizeInput($_POST['branch_city'], 'string');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($phone) || empty($branch_city) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!validateInput($name, 'name')) {
        $error = "Please enter a valid name (2-50 characters).";
    } elseif (!validateInput($email, 'email')) {
        $error = "Please enter a valid email address.";
    } elseif (!validateInput($phone, 'phone')) {
        $error = "Please enter a valid phone number.";
    } elseif (!validateInput($password, 'string', ['min_length' => 6, 'max_length' => 255])) {
        $error = "Password must be between 6-255 characters.";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Check if email already exists in users or agents table
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? UNION SELECT id FROM agents WHERE email = ?");
            $stmt->bind_param('ss', $email, $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Email address already in use.");
            }

            // Create user entry with plain text password
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $default_address = $branch_city . " Branch";
            $stmt->bind_param('sssss', $name, $email, $password, $phone, $default_address);
            $stmt->execute();
            
            // Create agent entry
            $stmt = $conn->prepare("INSERT INTO agents (name, email, phone, status) VALUES (?, ?, ?, 'Active')");
            $stmt->bind_param('sss', $name, $email, $phone);
            $stmt->execute();
            
            $success = "Agent account created successfully! <a href='agent-login.php'>Login here</a>.";
        } catch (Exception $e) {
            logError($e->getMessage(), 'AGENT_REGISTRATION_FAILED', ['email' => $email]);
            if ($e->getMessage() === "Email address already in use.") {
                $error = $e->getMessage();
            } else {
                $error = "An unexpected error occurred. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Registration | QUICK Deliver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #0d6efd 60%, #f4f6f8 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .signup-container {
            background: #fff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(13,110,253,0.09);
            max-width: 450px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="signup-container animate__animated animate__fadeInUp">
            <h2 class="text-center text-primary fw-bold mb-4">Agent Registration</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form action="agent-register.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="branch_city" class="form-label">Branch City</label>
                    <input type="text" class="form-control" id="branch_city" name="branch_city" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <span>Already have an account? <a href="agent-login.php">Login</a></span>
            </div>
        </div>
    </div>
</body>
</html>
