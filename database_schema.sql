-- CMS Database Schema
-- Drop database if exists and create new one
DROP DATABASE IF EXISTS cms;
CREATE DATABASE cms;
USE cms;

-- Users table for customer registration
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
);

-- Admins table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('Super Admin', 'Admin') DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
);

-- Agents table for delivery agents
CREATE TABLE agents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    password VARCHAR(255) NOT NULL,
    employee_id VARCHAR(50) UNIQUE,
    license_number VARCHAR(100),
    vehicle_type VARCHAR(50),
    vehicle_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active'
);

-- Packages table for parcel information
CREATE TABLE packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    consignment_no VARCHAR(100) UNIQUE NOT NULL,
    
    -- Sender Information
    sender_name VARCHAR(100) NOT NULL,
    sender_phone VARCHAR(20) NOT NULL,
    sender_email VARCHAR(100),
    sender_address TEXT NOT NULL,
    
    -- Receiver Information
    receiver_name VARCHAR(100) NOT NULL,
    receiver_phone VARCHAR(20) NOT NULL,
    receiver_email VARCHAR(100),
    receiver_address TEXT NOT NULL,
    
    -- Package Details
    parcel_type VARCHAR(100) NOT NULL,
    weight DECIMAL(10,2),
    dimensions VARCHAR(100),
    description TEXT,
    value DECIMAL(10,2),
    
    -- Delivery Information
    from_city VARCHAR(100) NOT NULL,
    to_city VARCHAR(100) NOT NULL,
    delivery_type ENUM('Standard', 'Express', 'Same Day', 'Next Day') DEFAULT 'Standard',
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    
    -- Status and Assignment
    status ENUM('Pending', 'Confirmed', 'Picked Up', 'In Transit', 'Out for Delivery', 'Delivered', 'Cancelled', 'Returned') DEFAULT 'Pending',
    assigned_agent_id INT NULL,
    
    -- Dates and Costs
    pickup_date DATE,
    expected_delivery_date DATE,
    actual_delivery_date DATE,
    shipping_cost DECIMAL(10,2),
    
    -- System fields
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_agent_id) REFERENCES agents(id) ON DELETE SET NULL,
    INDEX idx_consignment (consignment_no),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_agent (assigned_agent_id)
);

-- Tracking table for parcel tracking history
CREATE TABLE tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT NOT NULL,
    status VARCHAR(100) NOT NULL,
    location VARCHAR(200),
    description TEXT,
    event_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by_type ENUM('Admin', 'Agent', 'System') DEFAULT 'System',
    updated_by_id INT,
    remarks TEXT,
    
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    INDEX idx_package (package_id),
    INDEX idx_event_time (event_time)
);

-- Feedback table for contact form submissions
CREATE TABLE feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    user_id INT NULL,
    
    -- Status tracking
    status ENUM('New', 'In Progress', 'Resolved', 'Closed') DEFAULT 'New',
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    assigned_to_admin_id INT NULL,
    assigned_to_agent_id INT NULL,
    
    -- Response
    admin_response TEXT,
    response_date TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_agent_id) REFERENCES agents(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Package history for audit trail
CREATE TABLE package_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_by_type ENUM('Admin', 'Agent', 'User', 'System') NOT NULL,
    changed_by_id INT,
    changes JSON,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    INDEX idx_package (package_id),
    INDEX idx_created_at (created_at)
);

-- Insert default admin user
INSERT INTO admins (name, email, password, role) VALUES 
('System Admin', 'admin@quickdeliver.com', 'admin123', 'Super Admin'),
('Manager', 'manager@quickdeliver.com', 'manager123', 'Admin');

-- Insert sample agents
INSERT INTO agents (name, email, phone, address, password, employee_id, vehicle_type, vehicle_number) VALUES 
('Agent Haris', 'haris@quickdeliver.com', '+1234567890', '123 Agent Street, City', 'agent123', 'EMP001', 'Motorcycle', 'BIKE001'),
('Agent Sarah', 'sarah@quickdeliver.com', '+1234567891', '456 Delivery Ave, City', 'agent123', 'EMP002', 'Van', 'VAN001'),
('Agent Ahmed', 'ahmed@quickdeliver.com', '+1234567892', '789 Transport Blvd, City', 'agent123', 'EMP003', 'Truck', 'TRUCK001');

-- Insert sample users
INSERT INTO users (name, email, phone, address, password) VALUES 
('John Doe', 'john@example.com', '+1111111111', '100 Customer St, City', 'password123'),
('Jane Smith', 'jane@example.com', '+2222222222', '200 Client Ave, City', 'password123'),
('Bob Wilson', 'bob@example.com', '+3333333333', '300 User Blvd, City', 'password123');

-- Sample packages
INSERT INTO packages (
    user_id, consignment_no, sender_name, sender_phone, sender_address, 
    receiver_name, receiver_phone, receiver_address, parcel_type, weight, 
    from_city, to_city, delivery_type, status, shipping_cost
) VALUES 
(1, 'QD2025001', 'John Doe', '+1111111111', '100 Customer St, City A', 
 'Mary Johnson', '+4444444444', '500 Recipient Rd, City B', 'Documents', 0.5, 
 'City A', 'City B', 'Express', 'In Transit', 25.00),
(2, 'QD2025002', 'Jane Smith', '+2222222222', '200 Client Ave, City A', 
 'Tom Brown', '+5555555555', '600 Delivery Dr, City C', 'Electronics', 2.5, 
 'City A', 'City C', 'Standard', 'Picked Up', 45.00),
(3, 'QD2025003', 'Bob Wilson', '+3333333333', '300 User Blvd, City B', 
 'Lisa Davis', '+6666666666', '700 Destination St, City A', 'Clothing', 1.0, 
 'City B', 'City A', 'Same Day', 'Delivered', 35.00);

-- Sample tracking entries
INSERT INTO tracking (package_id, status, location, description) VALUES 
(1, 'Package Confirmed', 'Warehouse A', 'Package received and confirmed'),
(1, 'In Transit', 'Distribution Center', 'Package sorted and dispatched'),
(1, 'In Transit', 'Highway Checkpoint', 'Package in transit to destination'),
(2, 'Package Confirmed', 'Warehouse A', 'Package received and confirmed'),
(2, 'Picked Up', 'Pickup Location', 'Package picked up from sender'),
(3, 'Package Confirmed', 'Warehouse B', 'Package received and confirmed'),
(3, 'Delivered', 'City A', 'Package successfully delivered to recipient');

-- Sample feedback
INSERT INTO feedback (name, email, subject, message, user_id) VALUES 
('John Doe', 'john@example.com', 'Great Service', 'Very satisfied with the delivery service. Fast and reliable!', 1),
('Anonymous User', 'contact@example.com', 'Inquiry about Pricing', 'Could you please provide information about bulk delivery pricing?', NULL),
('Jane Smith', 'jane@example.com', 'Delivery Issue', 'My package was delivered to wrong address. Please help.', 2);

-- Create views for easier data access

-- View for package details with user and agent information
CREATE VIEW package_details AS
SELECT 
    p.*,
    u.name as user_name,
    u.email as user_email,
    u.phone as user_phone,
    a.name as agent_name,
    a.phone as agent_phone,
    a.vehicle_type,
    a.vehicle_number
FROM packages p
LEFT JOIN users u ON p.user_id = u.id
LEFT JOIN agents a ON p.assigned_agent_id = a.id;

-- View for latest tracking status
CREATE VIEW latest_tracking AS
SELECT 
    p.id as package_id,
    p.consignment_no,
    t.status as latest_status,
    t.location as latest_location,
    t.description as latest_description,
    t.event_time as latest_update
FROM packages p
LEFT JOIN tracking t ON p.id = t.package_id
WHERE t.event_time = (
    SELECT MAX(event_time) 
    FROM tracking t2 
    WHERE t2.package_id = p.id
);

-- Create stored procedures for common operations

DELIMITER //

-- Procedure to add tracking entry
CREATE PROCEDURE AddTrackingEntry(
    IN p_package_id INT,
    IN p_status VARCHAR(100),
    IN p_location VARCHAR(200),
    IN p_description TEXT,
    IN p_updated_by_type ENUM('Admin', 'Agent', 'System'),
    IN p_updated_by_id INT
)
BEGIN
    INSERT INTO tracking (package_id, status, location, description, updated_by_type, updated_by_id)
    VALUES (p_package_id, p_status, p_location, p_description, p_updated_by_type, p_updated_by_id);
    
    -- Update package status
    UPDATE packages SET status = p_status, updated_at = CURRENT_TIMESTAMP WHERE id = p_package_id;
END //

-- Procedure to get package tracking history
CREATE PROCEDURE GetPackageTracking(IN p_consignment_no VARCHAR(100))
BEGIN
    SELECT 
        t.*,
        p.consignment_no,
        p.sender_name,
        p.receiver_name,
        p.from_city,
        p.to_city,
        p.parcel_type
    FROM tracking t
    JOIN packages p ON t.package_id = p.id
    WHERE p.consignment_no = p_consignment_no
    ORDER BY t.event_time DESC;
END //

DELIMITER ;

-- Login activity table for tracking user/admin/agent logins
CREATE TABLE login_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
