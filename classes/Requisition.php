<?php
require_once 'Database.php';

class Requisition {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all requisitions
    public function getRequisitions() {
        $this->db->query('SELECT r.*, u.name as unit_name, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN units u ON r.requesting_unit_id = u.id 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         ORDER BY r.created_at DESC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get requisition by ID
    public function getRequisitionById($id) {
        $this->db->query('SELECT r.*, u.name as unit_name, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN units u ON r.requesting_unit_id = u.id 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         WHERE r.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Get requisitions by unit
    public function getRequisitionsByUnit($unit_id) {
        $this->db->query('SELECT r.*, u.name as unit_name, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN units u ON r.requesting_unit_id = u.id 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         WHERE r.requesting_unit_id = :unit_id 
                         ORDER BY r.created_at DESC');
        $this->db->bind(':unit_id', $unit_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get requisitions by status
    public function getRequisitionsByStatus($status) {
        $this->db->query('SELECT r.*, u.name as unit_name, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN units u ON r.requesting_unit_id = u.id 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         WHERE r.status = :status 
                         ORDER BY r.created_at DESC');
        $this->db->bind(':status', $status);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get requisitions by priority
    public function getRequisitionsByPriority($priority) {
        $this->db->query('SELECT r.*, u.name as unit_name, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN units u ON r.requesting_unit_id = u.id 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         WHERE r.priority = :priority 
                         ORDER BY r.created_at DESC');
        $this->db->bind(':priority', $priority);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get pending requisitions
    public function getPendingRequisitions() {
        $this->db->query('SELECT r.*, u.name as unit_name, a.name as approved_by_name 
                         FROM requisitions r 
                         LEFT JOIN units u ON r.requesting_unit_id = u.id 
                         LEFT JOIN users a ON r.approved_by = a.id 
                         WHERE r.status IN ("submitted", "approved", "in_progress") 
                         ORDER BY 
                            CASE r.priority
                                WHEN "critical" THEN 1
                                WHEN "high" THEN 2
                                WHEN "medium" THEN 3
                                WHEN "low" THEN 4
                            END, 
                            r.created_at ASC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Create new requisition
    public function createRequisition($data) {
        $this->db->query('INSERT INTO requisitions (requisition_code, requesting_unit_id, status, priority, required_date, notes) 
                         VALUES (:requisition_code, :requesting_unit_id, :status, :priority, :required_date, :notes)');
        
        // Bind values
        $this->db->bind(':requisition_code', $data['requisition_code']);
        $this->db->bind(':requesting_unit_id', $data['requesting_unit_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':priority', $data['priority']);
        $this->db->bind(':required_date', $data['required_date']);
        $this->db->bind(':notes', $data['notes']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update requisition
    public function updateRequisition($data) {
        $this->db->query('UPDATE requisitions SET 
                         requisition_code = :requisition_code, 
                         requesting_unit_id = :requesting_unit_id, 
                         status = :status, 
                         priority = :priority, 
                         required_date = :required_date, 
                         notes = :notes 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':requisition_code', $data['requisition_code']);
        $this->db->bind(':requesting_unit_id', $data['requesting_unit_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':priority', $data['priority']);
        $this->db->bind(':required_date', $data['required_date']);
        $this->db->bind(':notes', $data['notes']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete requisition
    public function deleteRequisition($id) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Delete requisition items first
            $this->db->query('DELETE FROM requisition_items WHERE requisition_id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // Then delete the requisition
            $this->db->query('DELETE FROM requisitions WHERE id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // Commit transaction
            $this->db->endTransaction();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->cancelTransaction();
            return false;
        }
    }

    // Submit requisition
    public function submitRequisition($id) {
        $this->db->query('UPDATE requisitions SET status = "submitted" WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Approve requisition
    public function approveRequisition($id, $approved_by) {
        $this->db->query('UPDATE requisitions SET status = "approved", approved_by = :approved_by WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':approved_by', $approved_by);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Mark requisition as in progress
    public function startRequisition($id) {
        $this->db->query('UPDATE requisitions SET status = "in_progress" WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Mark requisition as fulfilled
    public function fulfillRequisition($id) {
        $this->db->query('UPDATE requisitions SET status = "fulfilled", fulfillment_date = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Cancel requisition
    public function cancelRequisition($id) {
        $this->db->query('UPDATE requisitions SET status = "cancelled" WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Generate unique requisition code
    public function generateRequisitionCode() {
        $prefix = 'REQ-';
        $year = date('Y');
        $month = date('m');
        
        // Get the last requisition code
        $this->db->query('SELECT requisition_code FROM requisitions WHERE requisition_code LIKE :prefix ORDER BY id DESC LIMIT 1');
        $this->db->bind(':prefix', $prefix . $year . $month . '-%');
        $row = $this->db->single();
        
        if($row) {
            // Extract the sequence number and increment
            $parts = explode('-', $row->requisition_code);
            $seq = intval(end($parts)) + 1;
        } else {
            // Start with 1
            $seq = 1;
        }
        
        // Format sequence number with leading zeros
        $seq_formatted = str_pad($seq, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . '-' . $seq_formatted;
    }

    // Get requisition items
    public function getRequisitionItems($requisition_id) {
        $this->db->query('SELECT ri.*, a.name as asset_name, a.asset_code, a.unit_of_measure 
                         FROM requisition_items ri 
                         LEFT JOIN assets a ON ri.asset_id = a.id 
                         WHERE ri.requisition_id = :requisition_id 
                         ORDER BY ri.id');
        $this->db->bind(':requisition_id', $requisition_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Add requisition item
    public function addRequisitionItem($data) {
        $this->db->query('INSERT INTO requisition_items (requisition_id, asset_id, quantity_requested, status) 
                         VALUES (:requisition_id, :asset_id, :quantity_requested, :status)');
        
        // Bind values
        $this->db->bind(':requisition_id', $data['requisition_id']);
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':quantity_requested', $data['quantity_requested']);
        $this->db->bind(':status', $data['status']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update requisition item
    public function updateRequisitionItem($data) {
        $this->db->query('UPDATE requisition_items SET 
                         asset_id = :asset_id, 
                         quantity_requested = :quantity_requested, 
                         quantity_approved = :quantity_approved, 
                         quantity_fulfilled = :quantity_fulfilled, 
                         status = :status 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':quantity_requested', $data['quantity_requested']);
        $this->db->bind(':quantity_approved', $data['quantity_approved']);
        $this->db->bind(':quantity_fulfilled', $data['quantity_fulfilled']);
        $this->db->bind(':status', $data['status']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete requisition item
    public function deleteRequisitionItem($id) {
        $this->db->query('DELETE FROM requisition_items WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Approve requisition item
    public function approveRequisitionItem($id, $quantity_approved) {
        $this->db->query('UPDATE requisition_items SET status = "approved", quantity_approved = :quantity_approved WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':quantity_approved', $quantity_approved);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update requisition item fulfillment
    public function updateItemFulfillment($id, $quantity_fulfilled) {
        $this->db->query('SELECT quantity_fulfilled, quantity_approved FROM requisition_items WHERE id = :id');
        $this->db->bind(':id', $id);
        $item = $this->db->single();
        
        $new_total = $item->quantity_fulfilled + $quantity_fulfilled;
        $status = ($new_total >= $item->quantity_approved) ? 'fulfilled' : 'partially_fulfilled';
        
        $this->db->query('UPDATE requisition_items SET 
                         quantity_fulfilled = :quantity_fulfilled, 
                         status = :status 
                         WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':quantity_fulfilled', $new_total);
        $this->db->bind(':status', $status);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Check if all items in a requisition are fulfilled
    public function checkAllItemsFulfilled($requisition_id) {
        $this->db->query('SELECT COUNT(*) as total, 
                         SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled 
                         FROM requisition_items 
                         WHERE requisition_id = :requisition_id');
        $this->db->bind(':requisition_id', $requisition_id);
        $result = $this->db->single();
        
        return ($result->total > 0 && $result->total == $result->fulfilled);
    }
}
?>
