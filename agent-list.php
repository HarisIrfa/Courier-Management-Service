<?php
include('dbconnect.php');
$agents = [];
$sql = "SELECT * FROM agents ORDER BY id ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $agents[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent List | QUICK Deliver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .agent-list-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 40px 0 30px 0;
            margin: 60px auto 40px auto;
            max-width: 1000px;
        }
        .table thead th {
            background: #0d6efd;
            color: #fff;
            border: none;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f3f8fd;
        }
        .status-badge {
            font-size: 0.95rem;
            padding: 0.35em 0.9em;
            border-radius: 12px;
        }
        .footer {
            background: #0d6efd;
            color: #fff;
            padding: 32px 0 0 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <?php include "sidebar-admin.php" ?>
    
    <div class="agent-list-section animate__animated animate__fadeInDown">
        <div class="container">
            <div class="d-flex align-items-center mb-4">
                <img src="images/logo1.jpg" alt="Logo" width="48" height="48" class="rounded-circle me-3">
                <div>
                    <h2 class="mb-0 text-primary fw-bold">Agent List</h2>
                    <div class="text-muted" style="font-size:1.02rem;">All registered delivery agents</div>
                </div>
                <a href="agent.php" class="btn btn-primary ms-auto me-2"><i class="bi bi-arrow-left"></i> Back To Agent Pannel</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle shadow-sm">
                    <thead>
                        <tr>
                            <th scope="col"><i class="bi bi-person"></i> Name</th>
                            <th scope="col"><i class="bi bi-envelope"></i> Email</th>
                            <th scope="col"><i class="bi bi-telephone"></i> Phone</th>
                            <th scope="col"><i class="bi bi-check-circle"></i> Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agents as $agent): ?>
                        <tr id="agent-row-<?php echo $agent['id']; ?>">
                            <td><?php echo htmlspecialchars($agent['name']); ?></td>
                            <td><?php echo htmlspecialchars($agent['email']); ?></td>
                            <td><?php echo htmlspecialchars($agent['phone']); ?></td>
                            <td>
                                <?php if ($agent['status'] === 'Active'): ?>
                                    <span class="badge bg-success status-badge">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary status-badge">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editAgent(<?php echo $agent['id']; ?>)" title="Edit Agent">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteAgent(<?php echo $agent['id']; ?>)" title="Delete Agent">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($agents)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No agents found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Agent Modal -->
    <div class="modal fade" id="editAgentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAgentForm">
                    <div class="modal-body">
                        <input type="hidden" id="editAgentId" name="agent_id">
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
                            <input type="text" class="form-control" id="editAgentPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAgentStatus" class="form-label">Status</label>
                            <select class="form-control" id="editAgentStatus" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
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
        // Edit agent function
        function editAgent(agentId) {
            const row = document.getElementById(`agent-row-${agentId}`);
            const cells = row.getElementsByTagName('td');
            document.getElementById('editAgentId').value = agentId;
            document.getElementById('editAgentName').value = cells[0].textContent.trim();
            document.getElementById('editAgentEmail').value = cells[1].textContent.trim();
            document.getElementById('editAgentPhone').value = cells[2].textContent.trim();
            document.getElementById('editAgentStatus').value = cells[3].textContent.trim() === 'Active' ? 'Active' : 'Inactive';
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editAgentModal'));
            modal.show();
        }

        // Handle edit agent form submission
        document.getElementById('editAgentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('agent-list.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Agent updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the agent.');
            });
        });

        // Delete agent function
        function deleteAgent(agentId) {
            if (confirm('Are you sure you want to delete this agent? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('agent_id', agentId);
                fetch('agent-list.php', { // <-- FIXED: use agent-list.php
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => { // <-- FIXED: correct syntax
                    if (data.success) {
                        alert('Agent deleted successfully!');
                        document.getElementById(`agent-row-${agentId}`).remove();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the agent.');
                });
            }
        }
    </script>
    <footer class="footer animate__animated animate__fadeInUp">
        <div class="container">
            <div class="row align-items-center pb-3">
                <div class="col-md-4 mb-3 mb-md-0 text-center text-md-start">
                    <img src="images/logo1.jpg" alt="Quick Deliver Logo" width="50" height="50" class="rounded-circle mb-2">
                    <div class="fw-bold fs-5">QUICK Deliver</div>
                    <div style="font-size: 0.95rem;">Fast, Reliable & Secure Delivery</div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 text-center">
                    <div class="mb-2 fw-semibold">Quick Links</div>
                    <a href="index.php" class="text-white text-decoration-none me-3">Home</a>
                    <a href="about.php" class="text-white text-decoration-none me-3">About</a>
                    <a href="contact.php" class="text-white text-decoration-none me-3">Contact</a>
                    <a href="tracking.php" class="text-white text-decoration-none">Tracking</a>
                </div>
                <div class="col-md-4 text-center text-md-end">
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
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (isset($_POST['agent_id'])) {
        $agent_id = intval($_POST['agent_id']);
        // Edit agent
        if (isset($_POST['name'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $status = trim($_POST['status']);
            $stmt = $conn->prepare("UPDATE agents SET name=?, email=?, phone=?, status=? WHERE id=?");
            $stmt->bind_param('ssssi', $name, $email, $phone, $status, $agent_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating agent']);
            }
            exit;
        }
        // Delete agent
        if (!isset($_POST['name'])) {
            $stmt = $conn->prepare("DELETE FROM agents WHERE id=?");
            $stmt->bind_param('i', $agent_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting agent']);
            }
            exit;
        }
    }
}
