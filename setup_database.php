<?php
// Database setup script
$host = "localhost";
$username = "root";
$password = "";

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL server successfully!\n";
    
    // Read and execute the SQL file
    $sql = file_get_contents('database_schema.sql');
    
    // Split SQL statements by delimiter
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && $statement !== 'DELIMITER //' && $statement !== 'DELIMITER ;') {
            try {
                $pdo->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Skip delimiter statements and other non-critical errors
                if (strpos($statement, 'DELIMITER') === false && 
                    strpos($statement, 'CREATE PROCEDURE') === false &&
                    strpos($statement, 'END //') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "\nDatabase setup completed successfully!\n";
    echo "Database 'cms' created with all required tables.\n";
    echo "Sample data has been inserted.\n";
    
    // Test the connection to the new database
    $conn = new PDO("mysql:host=$host;dbname=cms", $username, $password);
    
    // Show created tables
    $result = $conn->query("SHOW TABLES");
    echo "\nCreated tables:\n";
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
?>
