<?php
require_once 'dbconnect.php';
require_once 'security_config.php';
require_once 'error_logger.php';

// Set variables
$success = "";
$error = "";

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name'], 'string');
    $email = sanitizeInput($_POST['email'], 'email');
    $phone = sanitizeInput($_POST['phone'], 'string');
    $address = sanitizeInput($_POST['address'], 'string');
    $password = $_POST['password']; // Don't sanitize passwords
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!validateInput($name, 'name')) {
        $error = "Please enter a valid name (2-50 characters, letters only).";
    } elseif (!validateInput($email, 'email')) {
        $error = "Please enter a valid email address.";
    } elseif (!validateInput($phone, 'phone')) {
        $error = "Please enter a valid phone number.";
    } elseif (!validateInput($password, 'string', ['min_length' => 6, 'max_length' => 255])) {
        $error = "Password must be between 6-255 characters.";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            // Insert user with plain text password
            $sql = "INSERT INTO users (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssss', $name, $email, $phone, $address, $password);
            if ($stmt->execute()) {
                $success = "Account created! <a href='user-login.php'>Login here</a>.";
            } else {
                $error = "Error: " . $stmt->error;
            }
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
    <title>User Signup</title>
    <style>
        body {
            background: linear-gradient(120deg, #0d6efd 60%, #f4f6f8 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .signup-container {
            background: #fff;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(13,110,253,0.09);
            width: 100%;
            max-width: 420px;
        }
        .signup-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #0d6efd;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: .5rem;
            color: #1565c0;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: .7rem;
            border: 1px solid #bcd0fa;
            border-radius: 6px;
            font-size: 1rem;
            background: #f7faff;
        }
        .signup-btn {
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
        .signup-btn:hover {
           background: #0056b3;
        }
        .signup-footer {
            margin-top: 1rem;
            text-align: center;
            font-size: .97rem;
        }
        .signup-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        .signup-footer a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #d8000c;
            background: #ffd2d2;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .success-message {
            color: #155724;
            background: #d4edda;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        <?php if ($error != ''): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success != ''): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required autocomplete="name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" required autocomplete="tel">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" autocomplete="address-line1">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="signup-btn" name="signup">Sign Up</button>
        </form>
        <div class="signup-footer">
            <span>Already have an account? <a href="user-login.php">Login</a></span>
        </div>
    </div>
</body>
</html>