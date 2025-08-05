<?php
session_start();
include('dbconnect.php');
require_once 'rate_limiter.php';

// Session timeout (3 hours)
$session_timeout = 3 * 60 * 60; // 3 hours in seconds

// Check if admin logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Check session timeout
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > $session_timeout) {
    session_destroy();
    header('Location: admin-login.php?timeout=1');
    exit();
}

// Validate session IP (basic session hijacking protection)
if (isset($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== getUserIP()) {
    session_destroy();
    header('Location: admin-login.php?security=1');
    exit();
}

// Update last activity time
$_SESSION['admin_login_time'] = time();
// For dashboard stats:
$total_agents = $conn->query("SELECT COUNT(*) as cnt FROM agents")->fetch_assoc()['cnt'] ?? 0;
$total_packages = $conn->query("SELECT COUNT(*) as cnt FROM packages")->fetch_assoc()['cnt'] ?? 0;
$total_users = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'] ?? 0;

// Fetch recent agent and user logins (last 10)
$recent_logins = [];
$sql = "SELECT la.login_time, la.role, 
    CASE WHEN la.role = 'agent' THEN a.name WHEN la.role = 'user' THEN u.name ELSE '' END as name, 
    CASE WHEN la.role = 'agent' THEN a.email WHEN la.role = 'user' THEN u.email ELSE '' END as email
FROM login_activity la
LEFT JOIN agents a ON la.role = 'agent' AND la.user_id = a.id
LEFT JOIN users u ON la.role = 'user' AND la.user_id = u.id
WHERE la.role IN ('agent', 'user')
ORDER BY la.login_time DESC LIMIT 10";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recent_logins[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .sidebar {
            min-height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
            background: #0d6efd;
            color: #fff;
            transition: width 0.3s, left 0.3s;
            width: 240px;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .nav-link {
            color: #fff;
            font-size: 1.1rem;
            padding: 16px 24px;
            border-radius: 8px;
            margin: 4px 0;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #fff;
            color: #0d6efd;
        }

        .sidebar .nav-icon {
            font-size: 1.5rem;
            margin-right: 16px;
            vertical-align: middle;
            transition: margin 0.3s;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .sidebar .sidebar-header {
            padding: 24px 24px 12px 24px;
            font-size: 1.3rem;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar .sidebar-header img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .sidebar.collapsed .sidebar-header span,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .sidebar-header {
            justify-content: center;
        }

        .sidebar-toggler {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            margin-left: 8px;
            transition: color 0.2s;
        }

        .sidebar-toggler:hover {
            color: #ffc107;
        }

        .sidebar .submenu {
            background: #1565c0;
            border-radius: 6px;
            margin: 0 12px 8px 12px;
            padding: 8px 0 8px 36px;
            display: none;
            animation: fadeIn 0.3s;
        }

        .sidebar .submenu.show {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s;
            padding: 32px 16px 16px 16px;
        }

        .sidebar.collapsed~.main-content {
            margin-left: 70px;
        }

        @media (max-width: 1199.98px) {
            .main-content {
                padding: 24px 8px 8px 8px;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                z-index: 1050;
                left: -240px;
                top: 0;
                height: 100vh;
                width: 240px;
                transition: left 0.3s;
            }

            .sidebar.open {
                left: 0;
            }

            .sidebar.collapsed {
                width: 70px;
            }

            .main-content {
                margin-left: 0 !important;
                padding-top: 80px;
            }
        }

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0 !important;
                padding: 12px 2px 2px 2px;
            }

            .sidebar .sidebar-header {
                padding: 16px 8px 8px 8px;
                font-size: 1.1rem;
            }

            .sidebar .nav-link {
                font-size: 1rem;
                padding: 12px 12px;
            }
        }

        .sidebar-backdrop {
            display: none;
        }

        .sidebar.open~.sidebar-backdrop {
            display: block;
            position: fixed;
            z-index: 1040;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
        }

        .card {
            min-width: 0;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(13, 110, 253, 0.07);
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 24px rgba(13, 110, 253, 0.13);
        }

        .btn-primary {
            background: #0d6efd;
            border: none;
        }

        .btn-primary:hover {
            background: #1565c0;
        }
    </style>
</head>

<body>
<?php include "sidebar-admin.php" ;?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold text-primary mb-2 animate__animated animate__fadeInDown">Admin Dashboard</h2>
                    <p class="lead animate__animated animate__fadeInUp">Welcome back, <span class="fw-semibold">Admin</span>! Here you can manage agents, deliveries, and view statistics.</p>
                </div>
                <div class="col-md-4 text-md-end text-center animate__animated animate__fadeInRight">
                    <div class="d-inline-flex align-items-center gap-2">
                        <img src="images/adminlogo.jpg" alt="Admin" class="rounded-circle" width="70" height="55">
                        <div>
                            <div class="fw-bold">System Admin</div>
                            <span class="badge bg-success">Online</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Quick Stats -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4 animate__animated animate__fadeInUp">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-people fs-2 text-primary mb-2"></i>
                            <h6 class="fw-bold mb-1">Total Agents</h6>
                            <div class="fs-3 fw-bold text-success"><?php echo $total_agents; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 animate__animated animate__fadeInUp" style="animation-delay:0.2s;">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-truck fs-2 text-primary mb-2"></i>
                            <h6 class="fw-bold mb-1">Active Deliveries</h6>
                            <div class="fs-3 fw-bold text-warning"><?php echo $total_packages; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 animate__animated animate__fadeInUp" style="animation-delay:0.4s;">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up-arrow fs-2 text-primary mb-2"></i>
                            <h6 class="fw-bold mb-1">Monthly Reports</h6>
                            <div class="fs-3 fw-bold text-info">7</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Management Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4 animate__animated animate__zoomIn">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-person-lines-fill fs-1 text-primary mb-3"></i>
                            <h5 class="card-title fw-bold">Manage Agents</h5>
                            <p class="card-text">View, add, or edit delivery agents.</p>
                            <a href="manage-agent-list.php" class="btn btn-outline-primary btn-sm mt-auto">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 animate__animated animate__zoomIn" style="animation-delay:0.2s;">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                            <h5 class="card-title fw-bold">Deliveries</h5>
                            <p class="card-text">Monitor and manage all deliveries.</p>
                            <a href="show-delivery-admin.php" class="btn btn-outline-primary btn-sm mt-auto">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 animate__animated animate__zoomIn" style="animation-delay:0.4s;">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                            <h5 class="card-title fw-bold">Statistics</h5>
                            <p class="card-text">View delivery stats and reports.</p>
                            <a href="stats-admin.php" class="btn btn-outline-primary btn-sm mt-auto">Go</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Additional Management Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 animate__animated animate__zoomIn" style="animation-delay:0.6s;">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-chat-dots fs-1 text-primary mb-3"></i>
                            <h5 class="card-title fw-bold">User Feedbacks</h5>
                            <p class="card-text">View and manage user feedback messages.</p>
                            <a href="feedback-admin.php" class="btn btn-outline-primary btn-sm mt-auto">View Feedbacks</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 animate__animated animate__zoomIn" style="animation-delay:0.8s;">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-people fs-1 text-primary mb-3"></i>
                            <h5 class="card-title fw-bold">User Management</h5>
                            <p class="card-text">Manage registered users and accounts.</p>
                            <a href="user-list-admin.php" class="btn btn-outline-primary btn-sm mt-auto">Manage Users</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Agent & User Logins -->
            <div class="row mb-4">
                <div class="col-12 animate__animated animate__fadeInUp">
                    <div class="card shadow border-0">
                        <div class="card-header bg-white fw-bold text-primary">
                            <i class="bi bi-clock-history me-2"></i> Recent Agent & User Logins
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (empty($recent_logins)): ?>
                                    <li class="list-group-item">No recent logins found.</li>
                                <?php else: ?>
                                    <?php foreach ($recent_logins as $login): ?>
                                        <li class="list-group-item">
                                            <?php if ($login['role'] === 'agent'): ?>
                                                <i class="bi bi-person-badge text-success me-2"></i>
                                                <b>Agent:</b> <?php echo htmlspecialchars($login['name']); ?> (<?php echo htmlspecialchars($login['email']); ?>)
                                            <?php elseif ($login['role'] === 'user'): ?>
                                                <i class="bi bi-person-circle text-info me-2"></i>
                                                <b>User:</b> <?php echo htmlspecialchars($login['name']); ?> (<?php echo htmlspecialchars($login['email']); ?>)
                                            <?php endif; ?>
                                            <span class="text-muted float-end"><?php echo date('M d, Y H:i', strtotime($login['login_time'])); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <script>
        // Sidebar toggle for desktop and mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('open');
                sidebarBackdrop.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                document.querySelector('.main-content').classList.toggle('collapsed');
            }
        }
        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarBackdrop.classList.remove('show');
        });
        // Accordion submenu open/close
        document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.dataset.bsTarget);
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                } else {
                    document.querySelectorAll('.sidebar .submenu').forEach(sm => sm.classList.remove('show'));
                    target.classList.add('show');
                }
            });
        });
        // Responsive sidebar on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('open');
                sidebarBackdrop.classList.remove('show');
            }
        });
    </script>
</body>

</html>