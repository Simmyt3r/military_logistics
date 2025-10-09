<?php
require_once 'Database.php';

class Unit {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all units
    public function getUnits() {
        $this->db->query('SELECT u.*, l.name as location_name, c.name as commander_name 
                         FROM units u 
                         LEFT JOIN locations l ON u.location_id = l.id 
                         LEFT JOIN users c ON u.commander_id = c.id 
                         ORDER BY u.name');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get unit by ID
    public function getUnitById($id) {
        $this->db->query('SELECT u.*, l.name as location_name, c.name as commander_name 
                         FROM units u 
                         LEFT JOIN locations l ON u.location_id = l.id 
                         LEFT JOIN users c ON u.commander_id = c.id 
                         WHERE u.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Get units by type
    public function getUnitsByType($type) {
        $this->db->query('SELECT u.*, l.name as location_name, c.name as commander_name 
                         FROM units u 
                         LEFT JOIN locations l ON u.location_id = l.id 
                         LEFT JOIN users c ON u.commander_id = c.id 
                         WHERE u.type = :type 
                         ORDER BY u.name');
        $this->db->bind(':type', $type);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get units by location
    public function getUnitsByLocation($location_id) {
        $this->db->query('SELECT u.*, l.name as location_name, c.name as commander_name 
                         FROM units u 
                         LEFT JOIN locations l ON u.location_id = l.id 
                         LEFT JOIN users c ON u.commander_id = c.id 
                         WHERE u.location_id = :location_id 
                         ORDER BY u.name');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Add unit
    public function addUnit($data) {
        $this->db->query('INSERT INTO units (name, type, location_id, commander_id) 
                         VALUES (:name, :type, :location_id, :commander_id)');
        
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':commander_id', $data['commander_id']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update unit
    public function updateUnit($data) {
        $this->db->query('UPDATE units SET 
                         name = :name, 
                         type = :type, 
                         location_id = :location_id, 
                         commander_id = :commander_id 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':commander_id', $data['commander_id']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete unit
    public function deleteUnit($id) {
        // Check if unit has users
        $this->db->query('SELECT COUNT(*) as count FROM users WHERE unit_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            return false; // Cannot delete unit with users
        }
        
        // Check if unit has requisitions
        $this->db->query('SELECT COUNT(*) as count FROM requisitions WHERE requesting_unit_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            return false; // Cannot delete unit with requisitions
        }
        
        // Delete the unit
        $this->db->query('DELETE FROM units WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get unit members
    public function getUnitMembers($unit_id) {
        $this->db->query('SELECT * FROM users WHERE unit_id = :unit_id ORDER BY name');
        $this->db->bind(':unit_id', $unit_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get unit requisitions
    public function getUnitRequisitions($unit_id) {
        $this->db->query('SELECT r.*, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         WHERE r.requesting_unit_id = :unit_id 
                         ORDER BY r.created_at DESC');
        $this->db->bind(':unit_id', $unit_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Update unit location
    public function updateUnitLocation($id, $location_id) {
        $this->db->query('UPDATE units SET location_id = :location_id WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':location_id', $location_id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update unit commander
    public function updateUnitCommander($id, $commander_id) {
        $this->db->query('UPDATE units SET commander_id = :commander_id WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':commander_id', $commander_id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get all unit types
    public function getUnitTypes() {
        $this->db->query('SELECT DISTINCT type FROM units ORDER BY type');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get unit asset inventory
    public function getUnitInventory($unit_id) {
        $unit = $this->getUnitById($unit_id);
        
        if($unit && $unit->location_id) {
            $this->db->query('SELECT a.*, c.name as category_name 
                             FROM assets a 
                             LEFT JOIN asset_categories c ON a.category_id = c.id 
                             WHERE a.location_id = :location_id 
                             ORDER BY a.name');
            $this->db->bind(':location_id', $unit->location_id);
            $results = $this->db->resultSet();
            
            return $results;
        }
        
        return [];
    }

    // Get unit readiness status (based on inventory levels)
    public function getUnitReadiness($unit_id) {
        $inventory = $this->getUnitInventory($unit_id);
        $critical_items = 0;
        $low_items = 0;
        $adequate_items = 0;
        
        foreach($inventory as $item) {
            if($item->quantity <= 0) {
                $critical_items++;
            } elseif($item->quantity < 10) {
                $low_items++;
            } else {
                $adequate_items++;
            }
        }
        
        $total_items = count($inventory);
        
        if($total_items == 0) {
            return [
                'status' => 'unknown',
                'percentage' => 0,
                'critical_items' => 0,
                'low_items' => 0,
                'adequate_items' => 0,
                'total_items' => 0
            ];
        }
        
        $readiness_percentage = ($adequate_items / $total_items) * 100;
        
        $status = 'critical';
        if($readiness_percentage >= 90) {
            $status = 'excellent';
        } elseif($readiness_percentage >= 75) {
            $status = 'good';
        } elseif($readiness_percentage >= 50) {
            $status = 'fair';
        } elseif($readiness_percentage >= 25) {
            $status = 'poor';
        }
        
        return [
            'status' => $status,
            'percentage' => $readiness_percentage,
            'critical_items' => $critical_items,
            'low_items' => $low_items,
            'adequate_items' => $adequate_items,
            'total_items' => $total_items
        ];
    }
}
?>
