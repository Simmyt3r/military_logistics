<?php
// File: military_logistics/classes/Asset.php
require_once 'Database.php';
class Asset {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all assets
    public function getAssets() {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         ORDER BY a.name ASC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get asset by ID
    public function getAssetById($id) {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Get assets by category
    public function getAssetsByCategory($category_id) {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.category_id = :category_id 
                         ORDER BY a.name');
        $this->db->bind(':category_id', $category_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get assets by location
    public function getAssetsByLocation($location_id) {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.location_id = :location_id 
                         ORDER BY a.name');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get assets by status
    public function getAssetsByStatus($status) {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.status = :status 
                         ORDER BY a.name');
        $this->db->bind(':status', $status);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get assets that are expiring soon (within 30 days)
    public function getExpiringAssets($days = 30) {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.expiration_date IS NOT NULL 
                         AND a.expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY) 
                         ORDER BY a.expiration_date');
        $this->db->bind(':days', $days);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get low stock assets (below threshold)
    public function getLowStockAssets($threshold = 10) {
        $this->db->query('SELECT a.*, c.name as category_name, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.quantity <= :threshold 
                         ORDER BY a.quantity');
        $this->db->bind(':threshold', $threshold);
        $results = $this->db->resultSet();

        return $results;
    }

    // Add new asset
    public function addAsset($data) {
        $this->db->query('INSERT INTO assets (asset_code, name, category_id, description, quantity, unit_of_measure, location_id, status, rfid_tag, gps_unit_id, expiration_date) 
                         VALUES (:asset_code, :name, :category_id, :description, :quantity, :unit_of_measure, :location_id, :status, :rfid_tag, :gps_unit_id, :expiration_date)');
        
        // Bind values
        $this->db->bind(':asset_code', $data['asset_code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':unit_of_measure', $data['unit_of_measure']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':rfid_tag', $data['rfid_tag']);
        $this->db->bind(':gps_unit_id', $data['gps_unit_id']);
        $this->db->bind(':expiration_date', $data['expiration_date']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update asset
    public function updateAsset($data) {
        $this->db->query('UPDATE assets SET 
                         asset_code = :asset_code, 
                         name = :name, 
                         category_id = :category_id, 
                         description = :description, 
                         quantity = :quantity, 
                         unit_of_measure = :unit_of_measure, 
                         location_id = :location_id, 
                         status = :status, 
                         rfid_tag = :rfid_tag, 
                         gps_unit_id = :gps_unit_id, 
                         expiration_date = :expiration_date 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':asset_code', $data['asset_code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':unit_of_measure', $data['unit_of_measure']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':rfid_tag', $data['rfid_tag']);
        $this->db->bind(':gps_unit_id', $data['gps_unit_id']);
        $this->db->bind(':expiration_date', $data['expiration_date']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete asset
    public function deleteAsset($id) {
        $this->db->query('DELETE FROM assets WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update asset quantity
    public function updateAssetQuantity($id, $quantity) {
        $this->db->query('UPDATE assets SET quantity = :quantity WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':quantity', $quantity);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update asset location
    public function updateAssetLocation($id, $location_id) {
        $this->db->query('UPDATE assets SET location_id = :location_id WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':location_id', $location_id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update asset status
    public function updateAssetStatus($id, $status) {
        $this->db->query('UPDATE assets SET status = :status WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Check if asset code exists
    public function assetCodeExists($asset_code) {
        $this->db->query('SELECT * FROM assets WHERE asset_code = :asset_code');
        $this->db->bind(':asset_code', $asset_code);
        $row = $this->db->single();

        return $this->db->rowCount() > 0;
    }

    // Get all asset categories
    public function getAssetCategories() {
        $this->db->query('SELECT * FROM asset_categories ORDER BY name');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get asset category by ID
    public function getAssetCategoryById($id) {
        $this->db->query('SELECT * FROM asset_categories WHERE id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Add asset category
    public function addAssetCategory($data) {
        $this->db->query('INSERT INTO asset_categories (name, description) VALUES (:name, :description)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update asset category
    public function updateAssetCategory($data) {
        $this->db->query('UPDATE asset_categories SET name = :name, description = :description WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete asset category
    public function deleteAssetCategory($id) {
        // First check if there are assets using this category
        $this->db->query('SELECT COUNT(*) as count FROM assets WHERE category_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            return false; // Cannot delete category with assets
        }
        
        $this->db->query('DELETE FROM asset_categories WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>

