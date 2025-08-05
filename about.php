<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            opacity: 0;
            transition: opacity 0.7s ease;
        }
        body.loaded {
            opacity: 1;
        }
        .navbar {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .about-section {
            max-width: 900px;
            margin: 60px auto;
            padding: 40px 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }
        .about-section img {
            max-width: 100%;
            height: auto;
        }
        .about-section h1, .about-section h2 {
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
        @media (max-width: 991.98px) {
            .about-section {
                margin: 32px 8px;
                padding: 20px 8px;
            }
            .about-section h1, .about-section h2 {
                font-size: 1.7rem;
            }
            .footer .row > div {
                margin-bottom: 18px !important;
            }
        }
        @media (max-width: 767.98px) {
            .about-section {
                margin: 12px 2px;
                padding: 12px 2px;
            }
            .about-section h1, .about-section h2 {
                font-size: 1.2rem;
            }
            .footer {
                padding: 18px 0 0 0;
            }
            .footer .row {
                flex-direction: column;
                text-align: center;
            }
            .footer .row > div {
                margin-bottom: 12px !important;
            }
        }
        @media (max-width: 575.98px) {
            .about-section {
                margin: 4px 0;
                padding: 8px 0;
            }
            .about-section h1, .about-section h2 {
                font-size: 1rem;
            }
            .footer {
                padding: 10px 0 0 0;
            }
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
          <a class="nav-link active fs-5 mx-3" href="about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fs-5 mx-3" href="contact.php">Contact</a>
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


<!-- About Section -->
<section class="about-section animate-on-scroll" id="about" data-animate="animate__fadeInUp">
    <div class="row align-items-center g-4">
        <div class="col-12 col-sm-10 offset-sm-1 col-md-6 mb-4 mb-md-0 animate-on-scroll" data-animate="animate__fadeInLeft">
            <img src="images/logo1.jpg" alt="About Quick Deliver" class="img-fluid rounded shadow w-100" style="max-height:320px; object-fit:contain;">
        </div>
        <div class="col-12 col-md-6 animate-on-scroll" data-animate="animate__fadeInRight">
            <h2>About QUICK Deliver</h2>
            <p>
                <strong>QUICK Deliver</strong> is a leading delivery service provider committed to offering fast, secure, and affordable delivery solutions for individuals and businesses. With a dedicated team and a robust logistics network, we guarantee timely deliveries and exceptional customer service.
            </p>
            <ul>
                <li>Same-day and next-day delivery options</li>
                <li>Real-time tracking for all shipments</li>
                <li>Professional and courteous delivery staff</li>
                <li>Affordable pricing with no hidden fees</li>
            </ul>
            <p class="tagline mt-3">Let’s move faster, together.</p>
            <p class="fw-bold text-primary">Quick Deliver – When speed matters.</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer animate-on-scroll" data-animate="animate__fadeInUp" style="background: #0d6efd; color: #fff; padding: 32px 0 0 0; margin-top: 40px;">
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
