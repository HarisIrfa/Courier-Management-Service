<?php
include('dbconnect.php');

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $status = $_POST['status'];
        
        $sql = "UPDATE agents SET name=?, email=?, phone=?, status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $name, $email, $phone, $status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Agent updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating agent']);
        }
        exit;
    }
    
    if ($action === 'delete') {
        $id = intval($_POST['id']);
        
        $sql = "DELETE FROM agents WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Agent deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting agent']);
        }
        exit;
    }
    
    if ($action === 'toggle_status') {
        $id = intval($_POST['id']);
        
        // Get current status
        $sql = "SELECT status FROM agents WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $newStatus = ($row['status'] === 'Active') ? 'Inactive' : 'Active';
            
            $sql = "UPDATE agents SET status=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $newStatus, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully', 'newStatus' => $newStatus]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating status']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Agent not found']);
        }
        exit;
    }
}

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
        .section-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .section-header .icon {
            font-size: 2.5rem;
            color: #0d6efd;
            background: #e7f1ff;
            border-radius: 50%;
            padding: 0.7rem;
        }
        .section-header .subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(13,110,253,0.07);
            background: #fff;
            transition: box-shadow 0.2s, transform 0.2s;
            border: none;
        }
        .card:hover {
            box-shadow: 0 6px 32px rgba(13,110,253,0.13);
            transform: translateY(-2px) scale(1.01);
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
        .action-btn {
            margin-right: 6px;
        }
        .modal-header {
            background: #0d6efd;
            color: #fff;
        }
        .modal-footer .btn {
            min-width: 100px;
        }
        .form-label {
            font-weight: 500;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .footer {
            margin-top: 48px;
            padding: 24px 0 0 0;
            color: #6c757d;
            font-size: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
  <?php include "sidebar-admin.php" ?>
  <div class="main-content">
    <div class="container-fluid animate__animated animate__fadeInDown">
      <div class="section-header">
        <span class="icon"><i class="bi bi-person-lines-fill"></i></span>
        <div>
          <h2 class="mb-1 fw-bold text-primary">Agent List</h2>
          <div class="subtitle">All registered delivery agents</div>
        </div>
      </div>
      <div class="card p-3 mb-4">
        <div class="table-responsive">
          <table class="table table-striped align-middle shadow-sm">
            <thead>
              <tr>
                <th scope="col"><i class="bi bi-person"></i> Name</th>
                <th scope="col"><i class="bi bi-envelope"></i> Email</th>
                <th scope="col"><i class="bi bi-telephone"></i> Phone</th>
                <th scope="col"><i class="bi bi-check-circle"></i> Status</th>
                <th scope="col"><i class="bi bi-gear"></i> Action </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($agents as $agent): ?>
              <tr>
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
                  <button class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#editAgentModal" data-id="<?php echo $agent['id']; ?>" data-name="<?php echo htmlspecialchars($agent['name']); ?>" data-email="<?php echo htmlspecialchars($agent['email']); ?>" data-phone="<?php echo htmlspecialchars($agent['phone']); ?>" data-status="<?php echo $agent['status']; ?>"><i class="bi bi-pencil-square"></i></button>
                  <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteAgent(<?php echo $agent['id']; ?>)"><i class="bi bi-x-circle"></i></button>
                  <button class="btn btn-sm btn-outline-warning action-btn" onclick="toggleStatus(<?php echo $agent['id']; ?>)">
                    <?php if ($agent['status'] === 'Active'): ?>
                      <i class="bi bi-toggle-off"></i>
                    <?php else: ?>
                      <i class="bi bi-toggle-on"></i>
                    <?php endif; ?>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($agents)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted">No agents found.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="footer">
      &copy; <?php echo date('Y'); ?> QUICK Deliver Admin. All rights reserved.
    </div>
  </div>

  <!-- Edit Agent Modal -->
  <div class="modal fade" id="editAgentModal" tabindex="-1" aria-labelledby="editAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAgentModalLabel">Edit Agent</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editAgentForm">
          <div class="modal-body">
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
              <input type="text" class="form-control" id="editAgentPhone" name="phone" required>
            </div>
            <div class="mb-3">
              <label for="editAgentStatus" class="form-label">Status</label>
              <select class="form-select" id="editAgentStatus" name="status">
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
    // Fill modal with agent data
    var editAgentModal = document.getElementById('editAgentModal');
    editAgentModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('editAgentId').value = button.getAttribute('data-id');
      document.getElementById('editAgentName').value = button.getAttribute('data-name');
      document.getElementById('editAgentEmail').value = button.getAttribute('data-email');
      document.getElementById('editAgentPhone').value = button.getAttribute('data-phone');
      document.getElementById('editAgentStatus').value = button.getAttribute('data-status');
    });

    // Handle edit form submit
    document.getElementById('editAgentForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append('action', 'edit');
      fetch('manage-agent-list.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
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

    // Delete agent
    function deleteAgent(id) {
      if (confirm('Are you sure you want to delete this agent?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        fetch('manage-agent-list.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload();
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

    // Toggle status
    function toggleStatus(id) {
      const formData = new FormData();
      formData.append('action', 'toggle_status');
      formData.append('id', id);
      fetch('manage-agent-list.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
      });
    }
  </script>
</body>
</html>
