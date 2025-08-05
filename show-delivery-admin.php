<?php
include('dbconnect.php');

// Handle Delete
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM packages WHERE consignment_no = ?");
    $stmt->bind_param('s', $delete_id);
    if ($stmt->execute()) {
        $feedback = '<div class="alert alert-success alert-dismissible fade show" role="alert">Delivery deleted successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $feedback = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to delete delivery.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    $stmt->close();
    echo '<script>setTimeout(function(){ window.location.href=window.location.href; }, 1000);</script>';
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $edit_customer = $_POST['edit_customer'];
    $edit_phone = $_POST['edit_phone'];
    $edit_email = $_POST['edit_email'];
    $edit_raddress = $_POST['edit_raddress'];
    $edit_address = $_POST['edit_address'];
    $edit_status = $_POST['edit_status'];
    $stmt = $conn->prepare("UPDATE packages SET receiver_name = ?, receiver_phone = ?, receiver_email = ?, receiver_address = ?, to_city = ?, status = ? WHERE consignment_no = ?");
    $stmt->bind_param('sssssss', $edit_customer, $edit_phone, $edit_email, $edit_raddress, $edit_address, $edit_status, $edit_id);
    if ($stmt->execute()) {
        $feedback = '<div class="alert alert-success alert-dismissible fade show" role="alert">Delivery updated successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $feedback = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to update delivery.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    $stmt->close();
    echo '<script>setTimeout(function(){ window.location.href=window.location.href; }, 1000);</script>';
}

$deliveries = [];
$sql = "SELECT p.consignment_no, p.sender_name, p.receiver_name, p.receiver_phone, p.receiver_email, p.receiver_address, p.to_city, p.status, p.created_at as booking_date FROM packages p ORDER BY p.created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deliveries[] = [
            'id' => $row['consignment_no'],
            'customer' => $row['receiver_name'] ?: 'N/A',
            'receiver_phone' => $row['receiver_phone'] ?: 'N/A',
            'receiver_email' => $row['receiver_email'] ?: 'N/A',
            'receiver_address' => $row['receiver_address'] ?: 'N/A',
            'address' => $row['to_city'] ?: 'N/A',
            'status' => $row['status'],
            'date' => $row['booking_date']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Show Deliveries | QUICK Deliver</title>
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
        .section-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .section-header .icon {
            font-size: 2.7rem;
            color: #0d6efd;
            background: #e7f1ff;
            border-radius: 50%;
            padding: 0.8rem;
        }
        .section-header .subtitle {
            color: #6c757d;
            font-size: 1.18rem;
        }
        .card {
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(13,110,253,0.07);
            background: #fff;
            border: none;
        }
        .table {
            margin-bottom: 0;
            font-size: 1rem;
        }
        .table thead th {
            background: #0d6efd;
            color: #fff;
            border: none;
            font-weight: 600;
            vertical-align: middle;
            font-size: 1rem;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f3f8fd;
        }
        .table-striped > tbody > tr:hover {
            background-color: #e7f1ff;
            transition: background 0.2s;
        }
        .table td, .table th {
            vertical-align: middle;
            padding: 0.7rem 0.6rem;
        }
        .status-badge {
            font-size: 0.98rem;
            padding: 0.28em 0.8em;
            border-radius: 12px;
        }
        .btn-sm {
            padding: 0.22rem 0.8rem;
            font-size: 0.98rem;
        }
        .footer {
            margin-top: 48px;
            padding: 24px 0 0 0;
            color: #6c757d;
            font-size: 1rem;
            text-align: center;
        }
        .alert {
            margin-bottom: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            font-size: 1rem;
        }
        .modal-content {
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(13,110,253,0.09);
        }
        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e3e8f0;
        }
        .modal-title {
            font-weight: 600;
            color: #0d6efd;
            font-size: 1.08rem;
        }
        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #e3e8f0;
        }
        .form-label {
            font-weight: 500;
            color: #0d6efd;
            font-size: 1rem;
        }
        .form-control, .form-select {
            font-size: 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.13rem rgba(13,110,253,0.11);
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .action-btns .btn {
            margin-right: 0.3rem;
        }
        .action-btns .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <?php include "sidebar-admin.php" ?>
    <div class="main-content">
        <div class="container-fluid animate__animated animate__fadeInDown">
            <div class="section-header">
                <span class="icon"><i class="bi bi-truck"></i></span>
                <div>
                    <h2 class="mb-1 fw-bold text-primary">Parcel</h2>
                    <div class="subtitle">Recent and ongoing Parcel</div>
                </div>
            </div>
            <?php if (!empty($feedback)) echo $feedback; ?>
            <div class="card p-3 mb-4">
                <div class="table-responsive">
                    <table class="table table-striped align-middle shadow-sm">
                        <thead>
                            <tr>
                                <th scope="col"><i class="bi bi-hash"></i> Delivery ID</th>
                                <th scope="col"><i class="bi bi-person"></i> Customer</th>
                                <th scope="col"><i class="bi bi-telephone"></i> Phone</th>
                                <th scope="col"><i class="bi bi-envelope"></i> Email</th>
                                <th scope="col"><i class="bi bi-geo"></i> Address</th>
                                <th scope="col"><i class="bi bi-geo-alt"></i> City</th>
                                <th scope="col"><i class="bi bi-calendar"></i> Date</th>
                                <th scope="col"><i class="bi bi-truck"></i> Status</th>
                                <th scope="col"><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deliveries as $delivery): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($delivery['id']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['customer']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['receiver_phone']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['receiver_email']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['receiver_address']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['address']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['date']); ?></td>
                                <td>
                                    <?php if ($delivery['status'] === 'Delivered'): ?>
                                        <span class="badge bg-success status-badge">Delivered</span>
                                    <?php elseif ($delivery['status'] === 'In Transit'): ?>
                                        <span class="badge bg-warning text-dark status-badge">In Transit</span>
                                    <?php elseif ($delivery['status'] === 'Out for Delivery'): ?>
                                        <span class="badge bg-info text-white status-badge">Out for Delivery</span>
                                    <?php elseif ($delivery['status'] === 'Cancelled'): ?>
                                        <span class="badge bg-danger status-badge">Cancelled</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary status-badge"><?php echo htmlspecialchars($delivery['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-btns">
                                    <button class="btn btn-sm btn-primary" title="Edit Delivery" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo htmlspecialchars($delivery['id']); ?>" data-customer="<?php echo htmlspecialchars($delivery['customer']); ?>" data-phone="<?php echo htmlspecialchars($delivery['receiver_phone']); ?>" data-email="<?php echo htmlspecialchars($delivery['receiver_email']); ?>" data-raddress="<?php echo htmlspecialchars($delivery['receiver_address']); ?>" data-address="<?php echo htmlspecialchars($delivery['address']); ?>" data-status="<?php echo htmlspecialchars($delivery['status']); ?>"><i class="bi bi-pencil"></i></button>
                                    <form method="post" action="" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this delivery?');">
                                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($delivery['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Delivery"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($deliveries)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">No deliveries found.</td>
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
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Delivery</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="mb-3">
            <label for="edit_customer" class="form-label">Customer</label>
            <input type="text" class="form-control" id="edit_customer" name="edit_customer" required>
          </div>
          <div class="mb-3">
            <label for="edit_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="edit_phone" name="edit_phone" required>
          </div>
          <div class="mb-3">
            <label for="edit_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="edit_email" name="edit_email" required>
          </div>
          <div class="mb-3">
            <label for="edit_raddress" class="form-label">Receiver Address</label>
            <input type="text" class="form-control" id="edit_raddress" name="edit_raddress" required>
          </div>
          <div class="mb-3">
            <label for="edit_address" class="form-label">City</label>
            <input type="text" class="form-control" id="edit_address" name="edit_address" required>
          </div>
          <div class="mb-3">
            <label for="edit_status" class="form-label">Status</label>
            <select class="form-select" id="edit_status" name="edit_status" required>
              <option value="Delivered">Delivered</option>
              <option value="In Transit">In Transit</option>
              <option value="Out for Delivery">Out for Delivery</option>
              <option value="Cancelled">Cancelled</option>
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
var editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var id = button.getAttribute('data-id');
  var customer = button.getAttribute('data-customer');
  var phone = button.getAttribute('data-phone');
  var email = button.getAttribute('data-email');
  var raddress = button.getAttribute('data-raddress');
  var address = button.getAttribute('data-address');
  var status = button.getAttribute('data-status');
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_customer').value = customer;
  document.getElementById('edit_phone').value = phone;
  document.getElementById('edit_email').value = email;
  document.getElementById('edit_raddress').value = raddress;
  document.getElementById('edit_address').value = address;
  document.getElementById('edit_status').value = status;
});

// Enable Bootstrap tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>
</body>
</html>
