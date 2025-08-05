<?php
include('dbconnect.php');

// Total Deliveries
$totalDeliveries = 0;
$sql = "SELECT COUNT(*) as total FROM packages";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalDeliveries = $row['total'];
}

// Active Deliveries (not Delivered or Cancelled)
$activeDeliveries = 0;
$sql = "SELECT COUNT(*) as active FROM packages WHERE status NOT IN ('Delivered', 'Cancelled')";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $activeDeliveries = $row['active'];
}

// Completed Deliveries (Delivered)
$completedDeliveries = 0;
$sql = "SELECT COUNT(*) as completed FROM packages WHERE status = 'Delivered'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $completedDeliveries = $row['completed'];
}

// Total Agents (assuming agents table, fallback to users with role 'agent')
$totalAgents = 0;
if ($conn->query("SHOW TABLES LIKE 'agents'")->num_rows) {
    $sql = "SELECT COUNT(*) as total FROM agents";
} else {
    $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'agent'";
}
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalAgents = $row['total'];
}

// Monthly Growth (deliveries this month vs last month)
$monthlyGrowth = 0;
$thisMonth = date('Y-m');
$lastMonth = date('Y-m', strtotime('-1 month'));
$sql = "SELECT COUNT(*) as cnt FROM packages WHERE DATE_FORMAT(created_at, '%Y-%m') = '$thisMonth'";
$result = $conn->query($sql);
$thisMonthCount = ($result && $row = $result->fetch_assoc()) ? $row['cnt'] : 0;
$sql = "SELECT COUNT(*) as cnt FROM packages WHERE DATE_FORMAT(created_at, '%Y-%m') = '$lastMonth'";
$result = $conn->query($sql);
$lastMonthCount = ($result && $row = $result->fetch_assoc()) ? $row['cnt'] : 0;
if ($lastMonthCount > 0) {
    $monthlyGrowth = round((($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100);
} elseif ($thisMonthCount > 0) {
    $monthlyGrowth = 100;
}

// Top Agent (most deliveries this month)
$topAgent = 'Agent Rana';
$topAgentDeliveries = 0;
$sql = "SELECT assigned_agent_id, COUNT(*) as cnt FROM packages WHERE DATE_FORMAT(created_at, '%Y-%m') = '$thisMonth' AND assigned_agent_id IS NOT NULL GROUP BY assigned_agent_id ORDER BY cnt DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && ($row = $result->fetch_assoc()) && $row['assigned_agent_id']) {
    $agentId = $row['assigned_agent_id'];
    $topAgentDeliveries = $row['cnt'];
    // Get agent name
    $sql2 = $conn->query("SELECT name FROM agents WHERE id = '$agentId'");
    if ($sql2 && $row2 = $sql2->fetch_assoc()) {
        $topAgent = $row2['name'];
    } else {
        $topAgent = 'Agent #' . $agentId;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistics | QUICK Deliver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-content {
            margin-left: 240px;
            padding: 32px 16px 16px 16px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
                padding-top: 80px;
            }
        }
        .stats-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .stats-header .icon {
            font-size: 2.7rem;
            color: #0d6efd;
            background: #e7f1ff;
            border-radius: 50%;
            padding: 0.8rem;
        }
        .stats-header .subtitle {
            color: #6c757d;
            font-size: 1.18rem;
        }
        .stat-card {
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(13,110,253,0.07);
            background: #fff;
            border: none;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .stat-card:hover {
            box-shadow: 0 6px 32px rgba(13,110,253,0.13);
            transform: translateY(-2px) scale(1.02);
        }
        .stat-icon {
            font-size: 2.7rem;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.2rem;
        }
        .stat-label {
            font-size: 1.13rem;
            color: #888;
            letter-spacing: 0.01em;
        }
        .growth-badge {
            font-size: 1rem;
            padding: 0.4em 1em;
            border-radius: 12px;
        }
        .top-agent-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
            margin-right: 1rem;
            box-shadow: 0 2px 8px rgba(13,110,253,0.13);
        }
        .top-agent-section {
            background: linear-gradient(90deg, #e7f1ff 60%, #fff 100%);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(13,110,253,0.07);
        }
        .footer {
            margin-top: 48px;
            padding: 24px 0 0 0;
            color: #6c757d;
            font-size: 1rem;
            text-align: center;
        }
        .divider {
            border-top: 1px solid #e3e8f0;
            margin: 2rem 0 1.5rem 0;
        }
    </style>
</head>
<body>
    <?php include "sidebar-admin.php" ?>
    <div class="main-content">
        <div class="container-fluid animate__animated animate__fadeInDown">
            <div class="stats-header">
                <span class="icon"><i class="bi bi-bar-chart"></i></span>
                <div>
                    <h2 class="mb-1 fw-bold text-primary">Statistics & Reports</h2>
                    <div class="subtitle">Delivery performance and agent analytics</div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card text-center p-4 h-100">
                        <i class="bi bi-truck stat-icon"></i>
                        <div class="stat-value text-primary"><?php echo $totalDeliveries; ?></div>
                        <div class="stat-label">Total Deliveries</div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card text-center p-4 h-100">
                        <i class="bi bi-graph-up-arrow stat-icon"></i>
                        <div class="stat-value text-success"><?php echo $activeDeliveries; ?></div>
                        <div class="stat-label">Active Deliveries</div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card text-center p-4 h-100">
                        <i class="bi bi-check2-circle stat-icon"></i>
                        <div class="stat-value text-info"><?php echo $completedDeliveries; ?></div>
                        <div class="stat-label">Completed Deliveries</div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stat-card text-center p-4 h-100">
                        <i class="bi bi-people stat-icon"></i>
                        <div class="stat-value text-warning"><?php echo $totalAgents; ?></div>
                        <div class="stat-label">Total Agents</div>
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-bar-chart-line me-2"></i> Monthly Growth</h5>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success growth-badge"><i class="bi bi-arrow-up-right me-1"></i> <?php echo $monthlyGrowth; ?>% Growth</span>
                                <span class="ms-3 text-muted">Compared to last month</span>
                            </div>
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $monthlyGrowth; ?>%;" aria-valuenow="<?php echo $monthlyGrowth; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card top-agent-section h-100">
                        <div class="card-body">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-trophy me-2"></i> Top Agent</h5>
                            <div class="d-flex align-items-center mb-2">
                                <img src="images/agent.jpg" alt="Top Agent" class="top-agent-img">
                                <div>
                                    <div class="fw-bold text-dark"><?php echo $topAgent; ?></div>
                                    <div class="text-muted">Deliveries: <?php echo $topAgentDeliveries; ?></div>
                                </div>
                            </div>
                            <span class="badge bg-primary">Top Performer</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                &copy; <?php echo date('Y'); ?> QUICK Deliver. All rights reserved.
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
