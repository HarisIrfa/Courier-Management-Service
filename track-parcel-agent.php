<?php
session_start();
include('dbconnect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Parcel | QUICK Deliver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .tracking-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 50px 0;
            margin: 60px auto 40px auto;
            max-width: 700px;
        }
        .tracking-form input {
            font-size: 1.15rem;
            padding: 0.8rem;
        }
        .tracking-result {
            margin-top: 30px;
            padding: 25px;
            background: #e9f7ef;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
<?php include 'sidebar-agent.php'; ?>
<div class="main-content">
<!-- Tracking Section -->
<section class="tracking-section animate__animated animate__fadeInUp">
    <div class="container">
        <h2 class="text-center mb-4 text-primary fw-bold"><i class="bi bi-search"></i> Track Your Parcel</h2>
        <p class="text-center mb-5" style="font-size:1.08rem;">
            Enter your tracking number below to get the latest status and location of your delivery.
        </p>
        <form class="tracking-form row justify-content-center" method="post" action="">
            <div class="col-md-8 mb-3 mb-md-0">
                <input type="text" class="form-control form-control-lg" name="tracking_number" placeholder="Enter Tracking Number" required>
            </div>
            <div class="col-md-2 text-center">
                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-arrow-right-circle"></i> Track</button>
            </div>
        </form>
        <?php
        include('dbconnect.php');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tracking_number'])) {
            $tracking = trim($_POST['tracking_number']);
            
            // Get package details with user information
            $sql = "SELECT p.*, u.name as user_name, u.email as user_email, u.phone as user_phone, 
                           a.name as agent_name, a.phone as agent_phone, a.vehicle_type, a.vehicle_number
                    FROM packages p 
                    LEFT JOIN users u ON p.user_id = u.id 
                    LEFT JOIN agents a ON p.assigned_agent_id = a.id 
                    WHERE p.consignment_no = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $tracking);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // Get tracking history
                $tracking_sql = "SELECT * FROM tracking WHERE package_id = ? ORDER BY event_time DESC";
                $tracking_stmt = $conn->prepare($tracking_sql);
                $tracking_stmt->bind_param('i', $row['id']);
                $tracking_stmt->execute();
                $tracking_result = $tracking_stmt->get_result();
                
                echo '
                <div class="tracking-result animate__animated animate__fadeIn">
                    <h4 class="mb-4 text-success"><i class="bi bi-truck"></i> Package Details</h4>
                    
                    <!-- Package Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-box"></i> Package Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Consignment No:</strong> ' . htmlspecialchars($row['consignment_no']) . '</p>
                                    <p><strong>Type:</strong> ' . htmlspecialchars($row['parcel_type']) . '</p>
                                    <p><strong>Weight:</strong> ' . htmlspecialchars($row['weight'] ?? 'N/A') . ' kg</p>
                                    <p><strong>Dimensions:</strong> ' . htmlspecialchars($row['dimensions'] ?? 'N/A') . '</p>
                                    <p><strong>Value:</strong> $' . htmlspecialchars($row['value'] ?? '0.00') . '</p>
                                    <p><strong>Description:</strong> ' . htmlspecialchars($row['description'] ?? 'N/A') . '</p>
                                    <p><strong>Priority:</strong> <span class="badge bg-warning">' . htmlspecialchars($row['priority']) . '</span></p>
                                    <p><strong>Delivery Type:</strong> <span class="badge bg-info">' . htmlspecialchars($row['delivery_type']) . '</span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Delivery Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>From:</strong> ' . htmlspecialchars($row['from_city']) . '</p>
                                    <p><strong>To:</strong> ' . htmlspecialchars($row['to_city']) . '</p>
                                    <p><strong>Current Status:</strong> <span class="badge bg-primary">' . htmlspecialchars($row['status']) . '</span></p>
                                    <p><strong>Created:</strong> ' . date('M d, Y H:i', strtotime($row['created_at'])) . '</p>
                                    <p><strong>Pickup Date:</strong> ' . ($row['pickup_date'] ? date('M d, Y', strtotime($row['pickup_date'])) : 'Not scheduled') . '</p>
                                    <p><strong>Expected Delivery:</strong> ' . ($row['expected_delivery_date'] ? date('M d, Y', strtotime($row['expected_delivery_date'])) : 'TBD') . '</p>
                                    <p><strong>Shipping Cost:</strong> $' . htmlspecialchars($row['shipping_cost'] ?? '0.00') . '</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sender and Receiver Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bi bi-person-up"></i> Sender Details</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> ' . htmlspecialchars($row['sender_name']) . '</p>
                                    <p><strong>Phone:</strong> ' . htmlspecialchars($row['sender_phone']) . '</p>
                                    <p><strong>Email:</strong> ' . htmlspecialchars($row['sender_email'] ?? 'N/A') . '</p>
                                    <p><strong>Address:</strong> ' . htmlspecialchars($row['sender_address']) . '</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="bi bi-person-down"></i> Receiver Details</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> ' . htmlspecialchars($row['receiver_name']) . '</p>
                                    <p><strong>Phone:</strong> ' . htmlspecialchars($row['receiver_phone']) . '</p>
                                    <p><strong>Email:</strong> ' . htmlspecialchars($row['receiver_email'] ?? 'N/A') . '</p>
                                    <p><strong>Address:</strong> ' . htmlspecialchars($row['receiver_address']) . '</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                    
                // Show agent information if assigned
                if ($row['assigned_agent_id']) {
                    echo '
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="bi bi-person-badge"></i> Assigned Agent</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3"><strong>Name:</strong> ' . htmlspecialchars($row['agent_name']) . '</div>
                                        <div class="col-md-3"><strong>Phone:</strong> ' . htmlspecialchars($row['agent_phone']) . '</div>
                                        <div class="col-md-3"><strong>Vehicle:</strong> ' . htmlspecialchars($row['vehicle_type']) . '</div>
                                        <div class="col-md-3"><strong>Vehicle No:</strong> ' . htmlspecialchars($row['vehicle_number']) . '</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                
                // Show tracking history
                echo '
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Tracking History</h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">';
                                    
                if ($tracking_result->num_rows > 0) {
                    while ($track = $tracking_result->fetch_assoc()) {
                        echo '
                                        <div class="timeline-item mb-3 p-3 border-start border-3 border-primary">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-primary mb-1">' . htmlspecialchars($track['status']) . '</h6>
                                                    <p class="mb-1"><strong>Location:</strong> ' . htmlspecialchars($track['location'] ?? 'N/A') . '</p>
                                                    <p class="mb-1 text-muted">' . htmlspecialchars($track['description'] ?? '') . '</p>
                                                    ' . (!empty($track['remarks']) ? '<p class="mb-0 text-secondary"><small><strong>Remarks:</strong> ' . htmlspecialchars($track['remarks']) . '</small></p>' : '') . '
                                                </div>
                                                <small class="text-muted">' . date('M d, Y H:i', strtotime($track['event_time'])) . '</small>
                                            </div>
                                        </div>';
                    }
                } else {
                    echo '<p class="text-muted">No tracking history available.</p>';
                }
                
                echo '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                
                $tracking_stmt->close();
            } else {
                echo '<div class="tracking-result animate__animated animate__shakeX"><div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <strong>No package found</strong> with tracking number: ' . htmlspecialchars($tracking) . '</div></div>';
            }
            $stmt->close();
        }
        ?>
        <br><br>
    </div>
</section>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
