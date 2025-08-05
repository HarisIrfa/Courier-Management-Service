<!-- Sidebar Styles and Scripts for Admin Panel -->
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
</style>
<!-- Sidebar HTML -->
<div class="sidebar d-flex flex-column position-fixed h-100" id="sidebar">
    <div class="sidebar-header">
        <span class="d-flex align-items-center">
            <img src="images/logo1.jpg" alt="Logo">
            <span>QUICK DELIVER Admin</span>
        </span>
        <button class="sidebar-toggler" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>
    <nav class="nav flex-column mt-3">
        <a class="nav-link" href="admin.php" data-bs-toggle="collapse" data-bs-target="#dashboardMenu" aria-expanded="false">
            <i class="bi bi-speedometer2 nav-icon"></i>
            <span>Dashboard</span>
        </a>
        <div class="submenu collapse" id="dashboardMenu">
            <a class="nav-link" href="admin.php"><i class="bi bi-house-door me-2"></i>Home</a>
            <!-- <a class="nav-link" href="stats-admin.php"><i class="bi bi-graph-up me-2"></i>Stats</a>
            <a class="nav-link" href="track-parcel-admin.php"><i class="bi bi-search me-2"></i>Track Parcel</a> -->
        </div>
         <a class="nav-link" href="stats-admin.php">
            <i class="bi bi-graph-up me-2"></i>
            <span> Stats</span>
        </a>
        <a class="nav-link" href="track-parcel-admin.php">
            <i class="bi bi-search me-2"></i>
            <span> Track Parcel</span>
        </a>
        <a class="nav-link" href="admin-profile.php">
            <i class="bi bi-person-circle nav-icon"></i>
            <span>My Profile</span>
        </a>
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#usersMenu" aria-expanded="false">
            <i class="bi bi-people nav-icon"></i>
            <span>Users</span>
        </a>
        <div class="submenu collapse" id="usersMenu">
            <a class="nav-link" href="user-list-admin.php"><i class="bi bi-person-lines-fill me-2"></i>User List</a>
        </div>
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#agentsMenu" aria-expanded="false">
            <i class="bi bi-people nav-icon"></i>
            <span>Agents</span>
        </a>
        <div class="submenu collapse" id="agentsMenu">
            <a class="nav-link" href="manage-agent-list.php"><i class="bi bi-person-lines-fill me-2"></i>Agent List</a>
        </div>
        <a class="nav-link" href="show-deliveries.php" data-bs-toggle="collapse" data-bs-target="#deliveriesMenu" aria-expanded="false">
            <i class="bi bi-truck nav-icon"></i>
            <span>Parcel</span>
        </a>
        <div class="submenu collapse" id="deliveriesMenu">
            <a class="nav-link" href="show-delivery-admin.php"><i class="bi bi-truck nav-icon me-2"></i>Show Parcel</a>
        </div>
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#messagesMenu" aria-expanded="false">
            <i class="bi bi-chat-dots nav-icon"></i>
            <span>Message</span>
        </a>
        <div class="submenu collapse" id="messagesMenu">
            <a class="nav-link" href="feedback-admin.php"><i class="bi bi-envelope-open me-2"></i>Feedbacks</a>
        </div>
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#settingsMenu" aria-expanded="false">
            <i class="bi bi-gear nav-icon"></i>
            <span>Settings</span>
        </a>
        <div class="submenu collapse" id="settingsMenu">
            <a class="nav-link" href="security.php"><i class="bi bi-shield-lock me-2"></i>Security</a>
        </div>
        <a class="nav-link mt-auto" href="admin-logout.php">
            <i class="bi bi-box-arrow-right nav-icon"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
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
