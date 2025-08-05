<?php
session_start();
include('dbconnect.php');

// Check if agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header('Location: agent-login.php');
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'edit_user':
                $user_id = intval($_POST['user_id']);
                $fullname = trim($_POST['fullname']);
                $email = trim($_POST['email']);
                $phone = trim($_POST['phone']);
                $address = trim($_POST['address']);
                
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    exit();
                }
                
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?");
                $stmt->bind_param('ssssi', $fullname, $email, $phone, $address, $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating user: Database error']);
                }
                $stmt->close();
                exit();
                break;
                
            case 'delete_user':
                $user_id = intval($_POST['user_id']);
                
                $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
                $stmt->bind_param('i', $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error deleting user: Database error']);
                }
                $stmt->close();
                exit();
                break;
                
            case 'toggle_status':
                $user_id = intval($_POST['user_id']);
                $new_status = trim($_POST['status']);
                
                // Validate status value
                if (!in_array($new_status, ['active', 'inactive'])) {
                    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
                    exit();
                }
                
                $stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
                $stmt->bind_param('si', $new_status, $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'User status updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating status: Database error']);
                }
                $stmt->close();
                exit();
                break;
        }
    }
}

// Fetch all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | QUICK Deliver Agent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
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
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
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
            from { opacity: 0; transform: translateY(-10px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s;
            padding: 32px 16px 16px 16px;
        }
        .sidebar.collapsed ~ .main-content {
            margin-left: 70px;
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
            .main-content {
                margin-left: 0 !important;
                padding-top: 80px;
            }
        }
        .sidebar-backdrop {
            display: none;
        }
        .sidebar.open ~ .sidebar-backdrop {
            display: block;
            position: fixed;
            z-index: 1040;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<?php include('sidebar-agent.php'); ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">User Management</h2>
                    <p class="text-muted">Manage system users - view, edit, and delete user accounts</p>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>All Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                                <tr id="user-row-<?php echo $user['id']; ?>">
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($user['address']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($user['status'] ?? 'active') == 'active' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($user['status'] ?? 'active'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editUser(<?php echo $user['id']; ?>)" title="Edit User">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- <button class="btn btn-sm btn-outline-<?php echo ($user['status'] ?? 'active') == 'active' ? 'warning' : 'success'; ?> me-1" 
                                                onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo ($user['status'] ?? 'active') == 'active' ? 'inactive' : 'active'; ?>')" 
                                                title="<?php echo ($user['status'] ?? 'active') == 'active' ? 'Deactivate' : 'Activate'; ?> User">
                                            <i class="bi bi-<?php echo ($user['status'] ?? 'active') == 'active' ? 'pause' : 'play'; ?>"></i>
                                        </button> -->
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" id="editUserId" name="user_id">
                        <div class="mb-3">
                            <label for="editFullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editFullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="editAddress" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        
        function toggleSidebar() {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('open');
                sidebarBackdrop.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }
        
        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarBackdrop.classList.remove('show');
        });

        // Submenu functionality
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

        // Edit user function
        function editUser(userId) {
            const row = document.getElementById(`user-row-${userId}`);
            const cells = row.getElementsByTagName('td');
            
            document.getElementById('editUserId').value = userId;
            document.getElementById('editFullname').value = cells[1].textContent;
            document.getElementById('editEmail').value = cells[2].textContent;
            document.getElementById('editPhone').value = cells[3].textContent;
            document.getElementById('editAddress').value = cells[4].textContent;
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        // Handle edit user form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'edit_user');
            
            fetch('edit-user-list-agent.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the user.');
            });
        });

        // Toggle user status function
        function toggleUserStatus(userId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus} this user?`)) {
                const formData = new FormData();
                formData.append('action', 'toggle_status');
                formData.append('user_id', userId);
                formData.append('status', newStatus);
                
                fetch('user-list-agent.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User status updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating user status.');
                });
            }
        }

        // Delete user function
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_user');
                formData.append('user_id', userId);
                
                fetch('delete-user-list-agent.php', {
                method: 'POST',
                body: formData
                })
                .then(response => response.json())
                .then(data => {
                if (data.success) {
                alert('User deleted successfully!');
                document.getElementById(`user-row-${userId}`).remove();
                } else {
                alert('Error: ' + data.message);
                }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the user.');
                });
            }
        }

        // Responsive sidebar
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('open');
                sidebarBackdrop.classList.remove('show');
            }
        });
    </script>
</body>
</html>
