<?php
// Test database connection and admin data
include("dbconnect.php");

echo "<h2>Database Connection Test</h2>";

// Test connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>Connected successfully to database: " . $conn->server_info . "</p>";
}

// Test if admins table exists and has data
echo "<h3>Admins Table Test</h3>";
$sql = "SELECT * FROM admins";
$result = $conn->query($sql);

if ($result) {
    echo "<p>Query executed successfully. Number of rows: " . $result->num_rows . "</p>";
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password</th><th>Created At</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['password'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No records found in admins table.</p>";
    }
} else {
    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
}

// Test specific admin lookup
echo "<h3>Specific Admin Lookup Test</h3>";
$test_email = 'admin@demo.com';
$sql = "SELECT * FROM admins WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<p style='color: green;'>Admin found:</p>";
    echo "<ul>";
    echo "<li>ID: " . $row['id'] . "</li>";
    echo "<li>Name: " . $row['name'] . "</li>";
    echo "<li>Email: " . $row['email'] . "</li>";
    echo "<li>Password: " . $row['password'] . "</li>";
    echo "</ul>";
    
    // Test password comparison
    $test_password = 'admin123';
    if ($test_password === $row['password']) {
        echo "<p style='color: green;'>Password comparison: MATCH</p>";
    } else {
        echo "<p style='color: red;'>Password comparison: NO MATCH</p>";
        echo "<p>Expected: '" . $test_password . "'</p>";
        echo "<p>Found: '" . $row['password'] . "'</p>";
    }
} else {
    echo "<p style='color: red;'>No admin found with email: " . $test_email . "</p>";
}

$conn->close();
?>
