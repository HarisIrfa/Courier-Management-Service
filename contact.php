<?php
session_start();
require_once 'dbconnect.php';
require_once 'security_config.php';

$success = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '', 'string');
    $email = sanitizeInput($_POST['email'] ?? '', 'email');
    $phone = sanitizeInput($_POST['phone'] ?? '', 'string');
    $subject = sanitizeInput($_POST['subject'] ?? '', 'string');
    $message = sanitizeInput($_POST['message'] ?? '', 'string');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!validateInput($email, 'email')) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if user is logged in to link feedback
        $user_id = null;
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            $user_id = $_SESSION['user_id'];
        }
        
        // Insert feedback into database
        $sql = "INSERT INTO feedback (name, email, phone, subject, message, user_id, status, priority) VALUES (?, ?, ?, ?, ?, ?, 'New', 'Medium')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssi', $name, $email, $phone, $subject, $message, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Thank you for your message! We will get back to you soon.';
            // Clear form data on success
            $name = $email = $phone = $subject = $message = '';
        } else {
            $error = 'Sorry, there was an error sending your message. Please try again.';
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
    <title>QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .hero-section {
            background: linear-gradient(90deg, #0d6efd 60%, #fff 100%);
            color: #fff;
            padding: 60px 0 40px 0;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero-section p {
            font-size: 1.3rem;
        }
        body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      color: #333;
      margin: 0;
      padding: 0;
    }

    .about-section {
      max-width: 900px;
      margin: 60px auto;
      padding: 40px 30px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    .about-section h1 {
      font-size: 2.5rem;
      color: #1e1e1e;
      margin-bottom: 20px;
    }

    .about-section p {
      font-size: 1.05rem;
      line-height: 1.7;
      margin-bottom: 20px;
    }

    .about-section ul {
      list-style: none;
      padding-left: 0;
      margin-bottom: 20px;
    }

    .about-section li::before {
      content: "⚡";
      margin-right: 10px;
      color: #ff6600;
    }

    .tagline {
      font-weight: bold;
      font-size: 1.2rem;
      color: #1e88e5;
    }

        .footer {
            background: #0d6efd;
            color: #fff;
            padding: 32px 0 0 0;
            margin-top: 40px;
        }

        /* Optional: Smooth fade-in for the whole body */
        body {
            opacity: 0;
            transition: opacity 0.7s ease;
        }
        body.loaded {
            opacity: 1;
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
          <a class="nav-link active fs-5 mx-3" href="contact.php">Contact</a>
        </li>
        <?php
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        ?>
        <li class="nav-item">
          <a class="nav-link fs-5 mx-3" href="submit_parcel.php">Submit Parcel</a>
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


<!-- Contact Section -->
<section class="contact-section animate-on-scroll" id="contact" data-animate="animate__fadeInUp">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0 animate-on-scroll" data-animate="animate__fadeInLeft">
                <div class="p-4 h-100 d-flex flex-column justify-content-center" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.07);">
                    <h2 class="mb-3 text-primary fw-bold" style="font-size:2rem;">Contact Us</h2>
                    <p class="mb-4" style="font-size:1.08rem;">
                        Have a question, need support, or want to partner with us? Reach out and our team will get back to you as soon as possible.
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <span class="fw-bold"><i class="bi bi-envelope-fill text-primary"></i> Email:</span>
                            <a href="mailto:support@quickdeliver.com" class="text-decoration-none text-dark"> support@quickdeliver.com</a>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold"><i class="bi bi-telephone-fill text-primary"></i> Phone:</span>
                            <a href="tel:+12345678901" class="text-decoration-none text-dark"> +1 234 567 8901</a>
                        </li>
                        <li>
                            <span class="fw-bold"><i class="bi bi-geo-alt-fill text-primary"></i> Address:</span>
                            123 Main Street, Your City, Country
                        </li>
                    </ul>
                    <div>
                        <span class="fw-bold text-primary">Follow us:</span>
                        <a href="#" class="ms-2 me-2 text-primary"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="me-2 text-primary"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-primary"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 animate-on-scroll" data-animate="animate__fadeInRight">
                <div class="p-4 h-100" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.07);">
                    <h3 class="mb-4 fw-bold text-secondary">Send Us a Message</h3>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success animate__animated animate__fadeIn mb-3">
                            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX mb-3">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <input type="text" class="form-control form-control-lg" name="name" placeholder="Your Name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control form-control-lg" name="email" placeholder="Your Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control form-control-lg" name="phone" placeholder="Your Phone (Optional)" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" name="subject" placeholder="Subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control form-control-lg" name="message" rows="4" placeholder="Your Message" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm animate-on-scroll" data-animate="animate__pulse">
                            <i class="bi bi-send"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer animate-on-scroll" data-animate="animate__fadeInUp" style="background: #0d6efd; color: #fff; padding: 32px 0 0 0; margin-top: 40px;">
    <div class="container">
        <div class="row align-items-center pb-3">
            <div class="col-md-4 mb-3 mb-md-0 text-center text-md-start animate-on-scroll" data-animate="animate__fadeInLeft">
                <img src="images/logo1.jpg" alt="Quick Deliver Logo" width="50" height="50" class="rounded-circle mb-2">
                <div class="fw-bold fs-5">QUICK Deliver</div>
                <div style="font-size: 0.95rem;">Fast, Reliable & Secure Delivery</div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0 text-center animate-on-scroll" data-animate="animate__fadeInUp">
                <div class="mb-2 fw-semibold">Quick Links</div>
                <a href="index.php" class="text-white text-decoration-none me-3">Home</a>
                <a href="about.php" class="text-white text-decoration-none me-3">About</a>
                <a href="contact.php" class="text-white text-decoration-none me-3">Contact</a>
                <!-- <a href="tracking.php" class="text-white text-decoration-none">Tracking</a> -->
            </div>
            <div class="col-md-4 text-center text-md-end animate-on-scroll" data-animate="animate__fadeInRight">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script>
window.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('loaded');
    // Animate sections on scroll using Intersection Observer
    const animatedSections = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', entry.target.dataset.animate);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });
    animatedSections.forEach(section => {
        observer.observe(section);
    });
});
</script>
</body>
</html>
