<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users & Agents - QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 30px;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-edit {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
        }
        .btn-delete {
            background: linear-gradient(45deg, #dc3545, #fd7e14);
            border: none;
            color: white;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .page-title {
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .section-title {
            color: #495057;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .status-active {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .status-inactive {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include "sidebar-admin.php" ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fas fa-shipping-fast me-2"></i>QUICK Deliver
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary btn-custom">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="main-container">
            <h1 class="page-title">
                <i class="fas fa-users-cog me-3"></i>Manage Users & Agents
            </h1>

            <?php
            include 'dbconnect.php';
            
            // Simple session simulation - In real application, implement proper authentication
            $user_role = isset($_GET['role']) ? $_GET['role'] : 'admin'; // admin or agent
            
            // Handle edit operations
            if (isset($_POST['action']) && $_POST['action'] == 'edit') {
                $type = $_POST['type'];
                $id = intval($_POST['id']);
                
                if ($type == 'user' && ($user_role == 'admin' || $user_role == 'agent')) {
                    $name = trim($_POST['name']);
                    $email = trim($_POST['email']);
                    $phone = trim($_POST['phone']);
                    $address = trim($_POST['address']);
                    
                    $update_query = "UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param('ssssi', $name, $email, $phone, $address, $id);
                    
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>User updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                } elseif ($type == 'agent' && $user_role == 'admin') {
                    $name = trim($_POST['name']);
                    $email = trim($_POST['email']);
                    $phone = trim($_POST['phone']);
                    $status = $_POST['status'];
                    $branch_city = trim($_POST['branch_city']);
                    
                    $update_query = "UPDATE agents SET name=?, email=?, phone=?, status=?, branch_city=? WHERE id=?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param('sssssi', $name, $email, $phone, $status, $branch_city, $id);
                    
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>Agent updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                }
            }
            
            // Handle delete operations
            if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['type']) && isset($_GET['id'])) {
                $type = $_GET['type'];
                $id = intval($_GET['id']);
                
                if ($type == 'user' && ($user_role == 'admin' || $user_role == 'agent')) {
                    $delete_query = "DELETE FROM users WHERE id = ?";
                    $stmt = $conn->prepare($delete_query);
                    $stmt->bind_param('i', $id);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>User deleted successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                } elseif ($type == 'agent' && $user_role == 'admin') {
                    $delete_query = "DELETE FROM agents WHERE id = ?";
                    $stmt = $conn->prepare($delete_query);
                    $stmt->bind_param('i', $id);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>Agent deleted successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                }
            }
            ?>

            <!-- Role Selection -->
            <div class="text-center mb-4">
                <div class="btn-group" role="group">
                    <a href="?role=admin" class="btn <?php echo $user_role == 'admin' ? 'btn-primary' : 'btn-outline-primary'; ?> btn-custom">
                        <i class="fas fa-user-shield me-1"></i>Admin View
                    </a>
                    <a href="?role=agent" class="btn <?php echo $user_role == 'agent' ? 'btn-primary' : 'btn-outline-primary'; ?> btn-custom">
                        <i class="fas fa-user-tie me-1"></i>Agent View
                    </a>
                </div>
                <p class="text-muted mt-2">Current Role: <strong><?php echo ucfirst($user_role); ?></strong></p>
            </div>

            <!-- Users Management Section -->
            <div class="table-container">
                <h3 class="section-title">
                    <i class="fas fa-users me-2"></i>Users Management
                </h3>
                
                <?php
                $users_query = "SELECT * FROM users ORDER BY created_at DESC";
                $users_result = $conn->query($users_query);
                ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th><i class="fas fa-user me-1"></i>Name</th>
                                <th><i class="fas fa-envelope me-1"></i>Email</th>
                                <th><i class="fas fa-phone me-1"></i>Phone</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i>Address</th>
                                <th><i class="fas fa-calendar me-1"></i>Created</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 14px;">
                                            <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-edit btn-custom" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                                onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['phone'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['address'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <?php if ($user_role == 'admin' || $user_role == 'agent'): ?>
                                        <a href="?role=<?php echo $user_role; ?>&action=delete&type=user&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-delete btn-custom" 
                                           onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agents Management Section (Only for Admin) -->
            <?php if ($user_role == 'admin'): ?>
            <div class="table-container">
                <h3 class="section-title">
                    <i class="fas fa-user-tie me-2"></i>Agents Management
                </h3>
                
                <?php
                $agents_query = "SELECT * FROM agents ORDER BY created_at DESC";
                $agents_result = $conn->query($agents_query);
                ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th><i class="fas fa-user-tie me-1"></i>Name</th>
                                <th><i class="fas fa-envelope me-1"></i>Email</th>
                                <th><i class="fas fa-phone me-1"></i>Phone</th>
                                <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                                <th><i class="fas fa-building me-1"></i>Branch</th>
                                <th><i class="fas fa-calendar me-1"></i>Created</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($agent = $agents_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $agent['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 14px;">
                                            <?php echo strtoupper(substr($agent['name'], 0, 2)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($agent['name']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($agent['email']); ?></td>
                                <td><?php echo htmlspecialchars($agent['phone']); ?></td>
                                <td>
                                    <span class="<?php echo $agent['status'] == 'Active' ? 'status-active' : 'status-inactive'; ?>">
                                        <i class="fas fa-circle me-1"></i><?php echo $agent['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($agent['branch_city']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($agent['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-edit btn-custom" data-bs-toggle="modal" data-bs-target="#editAgentModal" 
                                                onclick="editAgent(<?php echo $agent['id']; ?>, '<?php echo htmlspecialchars($agent['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($agent['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($agent['phone'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($agent['status'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($agent['branch_city'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <a href="?role=admin&action=delete&type=agent&id=<?php echo $agent['id']; ?>" 
                                           class="btn btn-sm btn-delete btn-custom" 
                                           onclick="return confirm('Are you sure you want to delete this agent?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card text-center border-0 shadow">
                        <div class="card-body">
                            <div class="text-primary mb-2">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <?php
                            $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
                            ?>
                            <h3 class="card-title text-primary"><?php echo $total_users; ?></h3>
                            <p class="card-text text-muted">Total Users</p>
                        </div>
                    </div>
                </div>
                <?php if ($user_role == 'admin'): ?>
                <div class="col-md-6">
                    <div class="card text-center border-0 shadow">
                        <div class="card-body">
                            <div class="text-success mb-2">
                                <i class="fas fa-user-tie fa-3x"></i>
                            </div>
                            <?php
                            $total_agents = $conn->query("SELECT COUNT(*) as count FROM agents")->fetch_assoc()['count'];
                            ?>
                            <h3 class="card-title text-success"><?php echo $total_agents; ?></h3>
                            <p class="card-text text-muted">Total Agents</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- User Edit Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" id="editUserId" name="id">
                        <div class="mb-3">
                            <label for="editUserName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editUserName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editUserEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editUserPhone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="editUserAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="editUserAddress" name="address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Agent Edit Modal -->
    <div class="modal fade" id="editAgentModal" tabindex="-1" aria-labelledby="editAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAgentModalLabel">Edit Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="type" value="agent">
                        <input type="hidden" id="editAgentId" name="id">
                        <div class="mb-3">
                            <label for="editAgentName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editAgentName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAgentEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editAgentEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAgentPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editAgentPhone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="editAgentStatus" class="form-label">Status</label>
                            <select class="form-select" id="editAgentStatus" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editAgentBranchCity" class="form-label">Branch City</label>
                            <input type="text" class="form-control" id="editAgentBranchCity" name="branch_city">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(id, name, email, phone, address) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = name;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserPhone').value = phone;
            document.getElementById('editUserAddress').value = address;
        }

        function editAgent(id, name, email, phone, status, branchCity) {
            document.getElementById('editAgentId').value = id;
            document.getElementById('editAgentName').value = name;
            document.getElementById('editAgentEmail').value = email;
            document.getElementById('editAgentPhone').value = phone;
            document.getElementById('editAgentStatus').value = status;
            document.getElementById('editAgentBranchCity').value = branchCity;
        }
    </script>
</body>
</html>
