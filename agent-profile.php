<?php
session_start();
require_once 'dbconnect.php';
require_once 'security_config.php';
require_once 'error_logger.php';

// Check if agent is logged in
if (!isset($_SESSION['agent_logged_in']) || !isset($_SESSION['agent_id'])) {
    header('Location: agent-login.php');
    exit();
}

$agent = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'status' => '',
    'branch_city' => 'Not Available',
    'joined' => '',
    'avatar' => 'images/agentlogo.jpg',
    'total_deliveries' => 0
];

$agent_id = $_SESSION['agent_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $new_name = trim($_POST['edit_name']);
    $new_email = trim($_POST['edit_email']);
    $new_phone = trim($_POST['edit_phone']);
    $new_branch = trim($_POST['edit_branch']);
    $update_sql = "UPDATE agents SET name=?, email=?, phone=?, branch_city=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt) {
        $update_stmt->bind_param('ssssi', $new_name, $new_email, $new_phone, $new_branch, $agent_id);
        $update_stmt->execute();
        $update_stmt->close();
        // Refresh to show updated info
        header('Location: agent-profile.php?updated=1');
        exit();
    }
}

$sql = "SELECT * FROM agents WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    logError('Prepare failed: ' . $conn->error, 'AGENT_PROFILE_ERROR', ['agent_id' => $agent_id]);
    die('Database error.');
}
$stmt->bind_param('i', $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $agent['name'] = htmlspecialchars($row['name']);
    $agent['email'] = htmlspecialchars($row['email']);
    $agent['phone'] = htmlspecialchars($row['phone']);
    $agent['status'] = htmlspecialchars($row['status']);
    $agent['avatar'] = !empty($row['avatar']) ? htmlspecialchars($row['avatar']) : 'images/agentlogo.jpg';
    $agent['joined'] = !empty($row['joined']) ? $row['joined'] : date('Y-m-d');
    if (isset($row['branch_city'])) {
        $agent['branch_city'] = htmlspecialchars($row['branch_city']);
    }
    // Check if parcels table exists before querying
    $check_table_sql = "SHOW TABLES LIKE 'parcels'";
    $check_table_result = $conn->query($check_table_sql);
    if ($check_table_result && $check_table_result->num_rows > 0) {
        $delivery_sql = "SELECT COUNT(*) as total FROM parcels WHERE agent_id = ?";
        $delivery_stmt = $conn->prepare($delivery_sql);
        if ($delivery_stmt) {
            $delivery_stmt->bind_param('i', $agent_id);
            $delivery_stmt->execute();
            $delivery_result = $delivery_stmt->get_result();
            if ($delivery_row = $delivery_result->fetch_assoc()) {
                $agent['total_deliveries'] = $delivery_row['total'];
            }
            $delivery_stmt->close();
        }
    } else {
        $agent['total_deliveries'] = 0; // Table does not exist
    }
} else {
    logError('Agent profile not found', 'AGENT_PROFILE_ERROR', ['agent_id' => $agent_id]);
    header('Location: agent-login.php');
    exit();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Profile | QUICK Deliver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-header {
            background: #0d6efd;
            color: #fff;
            padding: 32px 0 24px 0;
            text-align: center;
            border-radius: 0 0 32px 32px;
            box-shadow: 0 4px 24px rgba(13,110,253,0.08);
            margin-bottom: 0;
        }
        .main-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .main-header p {
            font-size: 1.1rem;
            opacity: 0.92;
        }
        .profile-section {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.09);
            padding: 20px 32px 36px 32px;
            margin: 0 auto 40px auto;
            max-width: 500px;
            position: relative;
            transition: box-shadow 0.2s;
        }
        .profile-section:hover {
            box-shadow: 0 8px 40px rgba(0,0,0,0.13);
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #0d6efd;
            box-shadow: 0 2px 12px rgba(13,110,253,0.13);
            margin-bottom: 18px;
            background: #f1f5f9;
            transition: transform 0.2s;
        }
        .profile-avatar:hover {
            transform: scale(1.04);
        }
        .status-dot {
            display: inline-block;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            margin-right: 7px;
            vertical-align: middle;
        }
        .status-dot.active {
            background: #28a745;
        }
        .status-dot.inactive {
            background: #adb5bd;
        }
        .profile-info-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            margin-bottom: 18px;
            font-size: 1.08rem;
        }
        .profile-info-row i {
            font-size: 1.25rem;
            color: #0d6efd;
        }
        .profile-actions {
            margin-top: 32px;
            display: flex;
            justify-content: center;
            gap: 18px;
        }
        .edit-btn {
            background: #f1f5f9;
            color: #0d6efd;
            border: 1px solid #0d6efd;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }
        .edit-btn:hover, .edit-btn:focus {
            background: #0d6efd;
            color: #fff;
        }
        .logout-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .logout-btn:hover, .logout-btn:focus {
            background: #b52a37;
            color: #fff;
        }
        .panel-btn {
            background: #fff;
            color: #0d6efd;
            border: 1px solid #0d6efd;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }
        .panel-btn:hover, .panel-btn:focus {
            background: #0d6efd;
            color: #fff;
        }
        .profile-label {
            color: #6c757d;
            font-size: 0.98rem;
            min-width: 90px;
        }
        @media (max-width: 600px) {
            .main-header {
                padding: 24px 0 16px 0;
                border-radius: 0 0 18px 18px;
            }
            .profile-section {
                padding: 32px 10px 24px 10px;
                max-width: 98vw;
            }
            .profile-avatar {
                width: 90px;
                height: 90px;
            }
        }
    </style>
</head>
<body>
<?php include "sidebar-agent.php"; ?>



<main aria-label="Agent Profile" class="d-flex justify-content-center align-items-center flex-column">
    <?php if (isset($_GET['updated'])): ?>
      <div class="alert alert-success alert-dismissible fade show mt-4 w-100 text-center" role="alert" style="max-width:500px; margin:0 auto;">
        <i class="bi bi-check-circle-fill me-2"></i>Profile updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <section class="profile-section text-center animate__animated animate__fadeInUp mt-5" tabindex="0">
        <img src="<?php echo $agent['avatar']; ?>" alt="Agent Avatar for <?php echo $agent['name']; ?>" class="profile-avatar shadow" loading="lazy">
        <h2 class="fw-bold text-primary mb-2" aria-label="Agent Name"><?php echo $agent['name']; ?></h2>
        <div class="mb-3" aria-label="Status">
            <span class="status-dot <?php echo ($agent['status'] === 'Active') ? 'active' : 'inactive'; ?>" aria-hidden="true"></span>
            <span class="fw-semibold text-<?php echo ($agent['status'] === 'Active') ? 'success' : 'secondary'; ?>">
                <?php echo ($agent['status'] === 'Active') ? 'Active' : 'Inactive'; ?>
            </span>
        </div>
        <div class="profile-info-row" aria-label="Email">
            <span class="profile-label"><i class="bi bi-envelope-fill me-1"></i>Email:</span>
            <span class="text-break"><?php echo $agent['email']; ?></span>
        </div>
        <div class="profile-info-row" aria-label="Phone">
            <span class="profile-label"><i class="bi bi-telephone-fill me-1"></i>Phone:</span>
            <span><?php echo $agent['phone']; ?></span>
        </div>
        <div class="profile-info-row" aria-label="Branch">
            <span class="profile-label"><i class="bi bi-geo-alt-fill me-1"></i>Branch:</span>
            <span><?php echo $agent['branch_city']; ?></span>
        </div>
        <div class="profile-info-row" aria-label="Total Deliveries">
            <span class="profile-label"><i class="bi bi-truck me-1"></i>Deliveries:</span>
            <span><?php echo $agent['total_deliveries']; ?></span>
        </div>
        <div class="profile-info-row" aria-label="Joined Date">
            <span class="profile-label"><i class="bi bi-calendar-check me-1"></i>Joined:</span>
            <span><?php echo date('F j, Y', strtotime($agent['joined'])); ?></span>
        </div>
        <div class="profile-actions">
            <button type="button" class="btn edit-btn px-4" id="openEditProfile"><i class="bi bi-pencil"></i> Edit</button>
            <a href="agent.php" class="btn panel-btn px-4" title="Back to Panel"><i class="bi bi-arrow-left"></i> Panel</a>
            <a href="agent-logout.php" class="btn logout-btn px-4" title="Logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>

        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="post" action="agent-profile.php">
                <div class="modal-header">
                  <h5 class="modal-title" id="editProfileModalLabel"><i class="bi bi-pencil"></i> Edit Profile</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="edit_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="edit_name" name="edit_name" value="<?php echo $agent['name']; ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="edit_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="edit_email" name="edit_email" value="<?php echo $agent['email']; ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="edit_phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="edit_phone" name="edit_phone" value="<?php echo $agent['phone']; ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="edit_branch" class="form-label">Branch City</label>
                    <input type="text" class="form-control" id="edit_branch" name="edit_branch" value="<?php echo $agent['branch_city']; ?>" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" name="edit_profile">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </section>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var editBtn = document.getElementById('openEditProfile');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      document.getElementById('edit_name').value = <?php echo json_encode($agent['name']); ?>;
      document.getElementById('edit_email').value = <?php echo json_encode($agent['email']); ?>;
      document.getElementById('edit_phone').value = <?php echo json_encode($agent['phone']); ?>;
      document.getElementById('edit_branch').value = <?php echo json_encode($agent['branch_city']); ?>;
      var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editProfileModal'));
      modal.show();
    });
  }
});
</script>
</body>
</html>
