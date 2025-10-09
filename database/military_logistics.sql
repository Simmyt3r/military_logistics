-- Create database
CREATE DATABASE IF NOT EXISTS military_logistics;
USE military_logistics;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'logistician', 'commander', 'field_unit', 'analyst') DEFAULT 'field_unit',
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    unit_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Units table
CREATE TABLE IF NOT EXISTS units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    location_id INT,
    commander_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Locations table
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Assets table
CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    quantity INT NOT NULL DEFAULT 0,
    unit_of_measure VARCHAR(50) NOT NULL,
    location_id INT,
    status ENUM('available', 'in_transit', 'deployed', 'maintenance', 'expired') DEFAULT 'available',
    rfid_tag VARCHAR(100),
    gps_unit_id VARCHAR(100),
    expiration_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Asset Categories table
CREATE TABLE IF NOT EXISTS asset_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Shipments table
CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_code VARCHAR(50) NOT NULL UNIQUE,
    origin_location_id INT NOT NULL,
    destination_location_id INT NOT NULL,
    status ENUM('planned', 'in_transit', 'delivered', 'delayed', 'cancelled') DEFAULT 'planned',
    estimated_departure DATETIME,
    actual_departure DATETIME,
    estimated_arrival DATETIME,
    actual_arrival DATETIME,
    transport_mode VARCHAR(100),
    carrier_info VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Shipment Items table
CREATE TABLE IF NOT EXISTS shipment_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    asset_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- Requisitions table
CREATE TABLE IF NOT EXISTS requisitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requisition_code VARCHAR(50) NOT NULL UNIQUE,
    requesting_unit_id INT NOT NULL,
    status ENUM('draft', 'submitted', 'approved', 'in_progress', 'fulfilled', 'cancelled') DEFAULT 'draft',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    requested_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    required_date DATETIME,
    fulfillment_date DATETIME,
    approved_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Requisition Items table
CREATE TABLE IF NOT EXISTS requisition_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requisition_id INT NOT NULL,
    asset_id INT NOT NULL,
    quantity_requested INT NOT NULL,
    quantity_approved INT,
    quantity_fulfilled INT DEFAULT 0,
    status ENUM('pending', 'approved', 'partially_fulfilled', 'fulfilled', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requisition_id) REFERENCES requisitions(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- Inventory Transactions table
CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    location_id INT NOT NULL,
    transaction_type ENUM('receipt', 'issue', 'transfer_in', 'transfer_out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50),
    reference_id INT,
    performed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (performed_by) REFERENCES users(id)
);

-- Demand Forecasts table
CREATE TABLE IF NOT EXISTS demand_forecasts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    location_id INT NOT NULL,
    time_period VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    predicted_quantity DECIMAL(10, 2) NOT NULL,
    confidence_interval DECIMAL(5, 2),
    model_used VARCHAR(100),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_code VARCHAR(50) NOT NULL UNIQUE,
    type VARCHAR(100) NOT NULL,
    model VARCHAR(100),
    capacity DECIMAL(10, 2),
    unit_of_capacity VARCHAR(50),
    status ENUM('available', 'in_transit', 'maintenance', 'out_of_service') DEFAULT 'available',
    current_location_id INT,
    gps_unit_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (current_location_id) REFERENCES locations(id)
);

-- Routes table
CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin_id INT NOT NULL,
    destination_id INT NOT NULL,
    distance DECIMAL(10, 2),
    estimated_time INT, -- in minutes
    transport_mode VARCHAR(100),
    risk_level ENUM('low', 'medium', 'high', 'extreme') DEFAULT 'low',
    status ENUM('open', 'restricted', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (origin_id) REFERENCES locations(id),
    FOREIGN KEY (destination_id) REFERENCES locations(id)
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(255) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@military.com', '$2y$10$8x5r8VgQzWBinyGLO.Cj0.Zq9GlQ1fgNmOGJoWJfSQwGbVj5quD0K', 'admin');

-- Insert default settings
INSERT INTO settings (setting_name, setting_value) VALUES 
('site_name', 'Military Logistics System'),
('site_description', 'A comprehensive system for military logistics management'),
('theme_color', '#3498db'),
('maintenance_mode', '0');

-- Insert sample asset categories
INSERT INTO asset_categories (name, description) VALUES
('Weapons', 'All types of weapons and ammunition'),
('Vehicles', 'Military vehicles including tanks, trucks, and jeeps'),
('Communications', 'Communication equipment including radios and satellite phones'),
('Medical', 'Medical supplies and equipment'),
('Food', 'Food rations and supplies'),
('Clothing', 'Uniforms and protective gear'),
('Fuel', 'Fuel for vehicles and equipment');

-- Insert sample locations
INSERT INTO locations (name, type, latitude, longitude, address) VALUES
('HQ Base', 'Headquarters', 9.0820, 8.6753, 'Central Command, Nigeria'),
('Northern Depot', 'Warehouse', 12.0022, 8.5920, 'Northern Region, Nigeria'),
('Southern Depot', 'Warehouse', 6.5244, 3.3792, 'Southern Region, Nigeria'),
('Eastern Outpost', 'Field Base', 6.4698, 7.5804, 'Eastern Region, Nigeria'),
('Western Outpost', 'Field Base', 7.3775, 3.9470, 'Western Region, Nigeria');

-- Insert sample units
INSERT INTO units (name, type, location_id) VALUES
('1st Infantry Division', 'Infantry', 1),
('2nd Armored Division', 'Armored', 2),
('3rd Support Battalion', 'Support', 3),
('4th Medical Corps', 'Medical', 4),
('5th Engineering Corps', 'Engineering', 5);