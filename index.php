<?php
session_start();
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
        @media (max-width: 991.98px) {
            .hero-section {
                padding: 40px 0 24px 0;
            }
            .hero-section h1 {
                font-size: 2.2rem;
            }
            .hero-section p {
                font-size: 1.1rem;
            }
        }
        @media (max-width: 767.98px) {
            .hero-section {
                padding: 24px 0 12px 0;
            }
            .hero-section h1 {
                font-size: 1.5rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
        }
        .about-section {
            max-width: 1100px;
            margin: 60px auto;
            padding: 40px 30px;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        }
        .about-section h2 {
            font-size: 2.2rem;
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .about-section p {
            font-size: 1.08rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .about-section ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 20px;
        }
        .about-section li::before {
            content: "🚚";
            margin-right: 10px;
            color: #0d6efd;
        }
        .gallery-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            padding: 40px 0;
            margin-bottom: 40px;
        }
        .gallery-section h2 {
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .gallery-img {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        @media (max-width: 991.98px) {
            .gallery-section {
                padding: 24px 0;
            }
            .gallery-img {
                height: 160px;
            }
        }
        @media (max-width: 767.98px) {
            .gallery-section {
                padding: 12px 0;
            }
            .gallery-img {
                height: 120px;
            }
        }
        .contact-section {
            background: #f8f9fa;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            padding: 50px 0;
            margin-top: 60px;
        }
        @media (max-width: 991.98px) {
            .contact-section {
                padding: 24px 0;
                margin-top: 32px;
            }
        }
        @media (max-width: 767.98px) {
            .contact-section {
                padding: 12px 0;
                margin-top: 16px;
            }
        }
        .footer {
            background: #0d6efd;
            color: #fff;
            padding: 32px 0 0 0;
            margin-top: 40px;
        }
        @media (max-width: 991.98px) {
            .footer .row > div {
                margin-bottom: 18px !important;
            }
        }
        @media (max-width: 767.98px) {
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
            .footer {
                padding: 10px 0 0 0;
            }
        }
        .tagline {
            font-weight: bold;
            font-size: 1.2rem;
            color: #1e88e5;
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
          <a class="nav-link active fs-5 mx-3" aria-current="page" href="index.php">Home</a>
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

<!-- Hero Section -->
<section class="hero-section animate-on-scroll" id="home" data-animate="animate__fadeInDown">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7 animate-on-scroll" data-animate="animate__fadeInLeft">
                <h1>Fast, Reliable & Secure Delivery</h1>
                <p class="mt-3 mb-4">
                    Welcome to <span class="fw-bold">QUICK Deliver</span> – your trusted partner for all your delivery needs. We ensure your packages reach their destination quickly and safely, every time.
                </p>
                <ul class="mb-4">
                    <li>Express same-day and next-day delivery</li>
                    <li>Real-time tracking for every shipment</li>
                    <li>Professional, friendly staff</li>
                    <li>Affordable pricing, no hidden fees</li>
                </ul>
                <a href="user-login.php" class="btn btn-light btn-lg text-primary fw-bold shadow-sm animate-on-scroll" data-animate="animate__pulse">Get Started</a>
            </div>
            <div class="col-md-5 text-center animate-on-scroll" data-animate="animate__zoomIn">
                <img src="images/logo1.jpg" alt="Delivery" class="img-fluid rounded shadow" style="max-height: 260px;">
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="gallery-section animate-on-scroll" id="gallery" data-animate="animate__fadeInUp">
    <div class="container">
        <h2 class="text-center mb-4">Our Delivery Moments</h2>
        <p class="text-center mb-5" style="font-size:1.08rem;">
            Explore some highlights from our fast, reliable, and customer-focused delivery service. Every moment reflects our commitment to excellence and satisfaction.
        </p>
        <div class="row g-4">
            <div class="col-md-3 animate-on-scroll" data-animate="animate__zoomIn">
                <img src="images/team.jpg" alt="Delivery Team" class="gallery-img mb-2">
                <div class="fw-semibold text-center">Our Dedicated Team</div>
            </div>
            <div class="col-md-3 animate-on-scroll" data-animate="animate__zoomIn">
                <img src="images/delivery.jpg" alt="Quick Delivery" class="gallery-img mb-2">
                <div class="fw-semibold text-center">Speedy Dispatch</div>
            </div>
            <div class="col-md-3 animate-on-scroll" data-animate="animate__zoomIn">
                <img src="images/happy1.jpg" alt="Customer Happiness" class="gallery-img mb-2">
                <div class="fw-semibold text-center">Happy Customers</div>
            </div>
            <div class="col-md-3 animate-on-scroll" data-animate="animate__zoomIn">
                <img src="images/track.jpg" alt="Tracking" class="gallery-img mb-2">
                <div class="fw-semibold text-center">Live Tracking</div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-6 animate-on-scroll" data-animate="animate__fadeInLeft">
                <div class="p-4 bg-light rounded shadow-sm h-100">
                    <h4 class="text-primary mb-3"><i class="bi bi-award-fill"></i> Why Choose Us?</h4>
                    <ul>
                        <li>24/7 customer support</li>
                        <li>Eco-friendly delivery vehicles</li>
                        <li>Insurance on every parcel</li>
                        <li>Flexible pickup and drop-off options</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 animate-on-scroll" data-animate="animate__fadeInRight">
                <div class="p-4 bg-light rounded shadow-sm h-100">
                    <h4 class="text-primary mb-3"><i class="bi bi-graph-up-arrow"></i> Our Achievements</h4>
                    <ul>
                        <li>10,000+ successful deliveries</li>
                        <li>98% customer satisfaction rate</li>
                        <li>Partnered with 50+ local businesses</li>
                        <li>Winner of "Best Local Courier" 2025</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="animate-on-scroll" id="testimonials" data-animate="animate__fadeInUp" style="background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,0.06); padding:40px 0; margin-bottom:40px;">
    <div class="container">
        <h2 class="text-center mb-4">What Our Clients Say</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 animate-on-scroll" data-animate="animate__fadeInLeft">
                <div class="p-4 bg-light rounded shadow-sm h-100">
                    <div class="mb-2"><i class="bi bi-person-circle fs-2 text-primary"></i></div>
                    <blockquote class="mb-2">"Quick Deliver is always on time and their staff is super friendly. Highly recommended!"</blockquote>
                    <div class="fw-bold text-primary">- Sarah K.</div>
                </div>
            </div>
            <div class="col-md-4 animate-on-scroll" data-animate="animate__fadeInUp">
                <div class="p-4 bg-light rounded shadow-sm h-100">
                    <div class="mb-2"><i class="bi bi-person-circle fs-2 text-primary"></i></div>
                    <blockquote class="mb-2">"The tracking feature is amazing. I always know where my parcel is!"</blockquote>
                    <div class="fw-bold text-primary">- Ahmed R.</div>
                </div>
            </div>
            <div class="col-md-4 animate-on-scroll" data-animate="animate__fadeInRight">
                <div class="p-4 bg-light rounded shadow-sm h-100">
                    <div class="mb-2"><i class="bi bi-person-circle fs-2 text-primary"></i></div>
                    <blockquote class="mb-2">"Affordable rates and great service. I use Quick Deliver for my business deliveries."</blockquote>
                    <div class="fw-bold text-primary">- Priya S.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="animate-on-scroll" id="faq" data-animate="animate__fadeInUp" style="background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,0.06); padding:40px 0; margin-bottom:40px;">
    <div class="container">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                How fast is QUICK Deliver?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer same-day and next-day delivery options for most locations. Speed is our specialty!
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                Can I track my parcel in real time?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! Every shipment comes with real-time tracking so you always know where your package is.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                What areas do you cover?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We deliver across the city and surrounding regions. Contact us for specific coverage details.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                Is my parcel insured?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, every parcel is insured for your peace of mind.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="animate-on-scroll" id="partners" data-animate="animate__fadeInUp" style="background:#f8f9fa; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,0.06); padding:40px 0; margin-bottom:40px;">
    <div class="container">
        <h2 class="text-center mb-4">Our Trusted Partners</h2>
        <div class="row justify-content-center align-items-center g-4">
            <div class="col-6 col-md-2 text-center">
                <img src="images/partner1.jpg" alt="Partner 1" class="img-fluid mb-2" style="max-height:60px;">
                <div class="fw-semibold">ShopEase</div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <img src="images/partner2.jpg" alt="Partner 2" class="img-fluid mb-2" style="max-height:60px;">
                <div class="fw-semibold">FoodFast</div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <img src="images/partner3.jpg" alt="Partner 3" class="img-fluid mb-2" style="max-height:60px;">
                <div class="fw-semibold">BookNest</div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <img src="images/partner4.jpg" alt="Partner 4" class="img-fluid mb-2" style="max-height:60px;">
                <div class="fw-semibold">TechMart</div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="animate-on-scroll" id="cta" data-animate="animate__fadeInUp" style="background:#0d6efd; color:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,0.06); padding:40px 0; margin-bottom:40px;">
    <div class="container text-center">
        <h2 class="mb-3 fw-bold">Ready to Experience Fast Delivery?</h2>
        <p class="mb-4" style="font-size:1.15rem;">Sign up now and let QUICK Deliver handle your next shipment with speed and care.</p>
        <a href="signup.php" class="btn btn-light btn-lg px-5 fw-bold shadow-sm animate-on-scroll" data-animate="animate__pulse">Create Account</a>
    </div>
</section>

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

// Handle logout with confirmation alerts
function handleLogout(event) {
    event.preventDefault();
    
    // Show first confirmation alert
    if (confirm("Are you sure you want to logout?")) {
        // Perform logout via AJAX
        fetch('logout-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success alert
                alert("You have been successfully logged out!");
                // Redirect to home page
                window.location.href = 'index.php';
            } else {
                alert("An error occurred during logout: " + (data.message || 'Unknown error'));
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

