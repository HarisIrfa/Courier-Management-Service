<?php
session_start();
include('dbconnect.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
  header('Location: admin-login.php');
  exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  header('Content-Type: application/json');
  $action = $_POST['action'];
  if ($action === 'edit_user') {
    $user_id = intval($_POST['user_id']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
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
  } elseif ($action === 'delete_user') {
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
  } elseif ($action === 'toggle_status') {
    $user_id = intval($_POST['user_id']);
    $new_status = trim($_POST['status']);
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
  <title>User Management | QUICK Deliver Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(120deg, #f1f3f9 60%, #e0e7ff 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      /* min-height: 100vh; */
    }

    .container {
      max-width: 900px;
      margin-left: 240px;
    }

    @media (max-width: 991.98px) {
      .container {
        margin-left: 0 !important;
        max-width: 100vw;
        padding-left: 8px;
        padding-right: 8px;
      }
    }

    .card-box {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 32px rgba(13, 110, 253, 0.08), 0 1.5px 8px rgba(0, 0, 0, 0.04);
      padding: 28px 22px 22px 22px;
      margin-bottom: 18px;
      border: none;
    }

    .table-responsive{
      margin-top: 20px;
      margin-bottom: 20px;
      margin-left: 20px;
    }

    .table {
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 0;
      margin-left: 200px;
      max-width: max-content;
      background: #fff;
    }

    .table th {
      background: linear-gradient(90deg, #0d6efd 80%, #3b82f6 100%);
      color: #fff;
      vertical-align: middle;
      font-weight: 600;
      font-size: 1.05rem;
      border: none;
      letter-spacing: 0.01em;
    }

    .table-hover tbody tr:hover {
      background: #eaf1fb;
      transition: background 0.2s;
    }

    .badge {
      font-size: 0.85rem;
      padding: 0.38em 0.95em;
      border-radius: 12px;
      box-shadow: 0 1px 4px rgba(13, 110, 253, 0.07);
      font-weight: 500;
    }

    .btn-sm {
      font-size: 0.85rem;
      padding: 0.28rem 0.7rem;
      border-radius: 8px;
      box-shadow: 0 1px 4px rgba(13, 110, 253, 0.07);
    }

    .modal-content {
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(13, 110, 253, 0.12);
    }

    .modal-header {
      border-radius: 16px 16px 0 0;
      background: #f1f3f7;
    }

    .modal-title {
      font-weight: 600;
      color: #0d6efd;
    }

  </style>
</head>

<body>
  <?php include 'sidebar-admin.php'; ?>
  <center>
    <div class="container mt-0">
      <h2 class="mb-4 fw-bold text-primary">User Management</h2>
      <!-- <div class="table-responsive card-box"> -->
        <table class="table table-bordered align-middle table-hover">
          <thead>
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
              <tr id="user-row-<?= $user['id'] ?>">
                <td class="fw-semibold text-secondary-emphasis"><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td><?= htmlspecialchars($user['address']) ?></td>
                <td>
                  <span class="badge bg-<?= ($user['status'] ?? 'active') == 'active' ? 'success' : 'danger' ?>">
                    <?= ucfirst($user['status'] ?? 'active') ?>
                  </span>
                </td>
                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" onclick="editUser(<?= $user['id'] ?>)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-warning me-1" onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= ($user['status'] ?? 'active') == 'active' ? 'inactive' : 'active' ?>')"><i class="bi bi-<?= ($user['status'] ?? 'active') == 'active' ? 'pause' : 'play' ?>"></i></button>
                  <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <!-- </div> -->
    </div>
  </center>

  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editUserForm" autocomplete="off">
          <div class="modal-body py-4 px-3">
            <input type="hidden" id="editUserId" name="user_id">
            <div class="mb-3">
              <label for="editFullname" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="editFullname" name="fullname" required maxlength="60">
            </div>
            <div class="mb-3">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required maxlength="80">
            </div>
            <div class="mb-3">
              <label for="editPhone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="editPhone" name="phone" required maxlength="20">
            </div>
            <div class="mb-3">
              <label for="editAddress" class="form-label">Address</label>
              <textarea class="form-control" id="editAddress" name="address" rows="3" required maxlength="120"></textarea>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function editUser(userId) {
      const row = document.getElementById(`user-row-${userId}`);
      if (!row) return;
      const cells = row.getElementsByTagName('td');
      document.getElementById('editUserId').value = userId;
      document.getElementById('editFullname').value = cells[1].textContent.trim();
      document.getElementById('editEmail').value = cells[2].textContent.trim();
      document.getElementById('editPhone').value = cells[3].textContent.trim();
      document.getElementById('editAddress').value = cells[4].textContent.trim();
      var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal'));
      modal.show();
    }

    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
      editUserForm.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'edit_user');
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        fetch('user-list-admin.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
            if (data.success) {
              // Update row in table without reload
              const userId = document.getElementById('editUserId').value;
              const row = document.getElementById(`user-row-${userId}`);
              if (row) {
                const cells = row.getElementsByTagName('td');
                cells[1].textContent = document.getElementById('editFullname').value;
                cells[2].textContent = document.getElementById('editEmail').value;
                cells[3].textContent = document.getElementById('editPhone').value;
                cells[4].textContent = document.getElementById('editAddress').value;
              }
              var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal'));
              modal.hide();
              showToast('User updated successfully!', 'success');
            } else {
              showToast('Error: ' + data.message, 'danger');
            }
          })
          .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
            showToast('An error occurred while updating the user.', 'danger');
          });
      };
    }

    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('action', 'delete_user');
        formData.append('user_id', userId);
        const row = document.getElementById(`user-row-${userId}`);
        if (row) row.style.opacity = '0.5';
        fetch('user-list-admin.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (row) row.style.opacity = '';
            if (data.success) {
              if (row) row.remove();
              showToast('User deleted successfully!', 'success');
            } else {
              showToast('Error: ' + data.message, 'danger');
            }
          })
          .catch(error => {
            if (row) row.style.opacity = '';
            showToast('An error occurred while deleting the user.', 'danger');
          });
      }
    }

    function toggleUserStatus(userId, newStatus) {
      if (confirm(`Are you sure you want to ${newStatus} this user?`)) {
        const formData = new FormData();
        formData.append('action', 'toggle_status');
        formData.append('user_id', userId);
        formData.append('status', newStatus);
        fetch('user-list-admin.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showToast('User status updated successfully!', 'success');
              setTimeout(() => location.reload(), 800);
            } else {
              showToast('Error: ' + data.message, 'danger');
            }
          })
          .catch(error => {
            showToast('An error occurred while updating user status.', 'danger');
          });
      }
    }

    function showToast(message, type = 'success') {
      let toast = document.getElementById('customToast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'customToast';
        toast.className = 'toast align-items-center text-bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0 position-fixed top-0 end-0 m-4';
        toast.style.zIndex = 9999;
        toast.innerHTML = `<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        document.body.appendChild(toast);
      }
      toast.querySelector('.toast-body').textContent = message;
      toast.className = 'toast align-items-center text-bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0 position-fixed top-0 end-0 m-4';
      var bsToast = bootstrap.Toast.getOrCreateInstance(toast, {
        delay: 2500
      });
      bsToast.show();
    }
  </script>
</body>

</html>