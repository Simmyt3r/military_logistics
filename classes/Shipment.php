<?php
require_once 'Database.php';

class Shipment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all shipments
    public function getShipments() {
        $this->db->query('SELECT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         ORDER BY s.created_at DESC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get shipment by ID
    public function getShipmentById($id) {
        $this->db->query('SELECT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE s.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Get shipments by status
    public function getShipmentsByStatus($status) {
        $this->db->query('SELECT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE s.status = :status 
                         ORDER BY s.created_at DESC');
        $this->db->bind(':status', $status);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get shipments by origin location
    public function getShipmentsByOrigin($location_id) {
        $this->db->query('SELECT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE s.origin_location_id = :location_id 
                         ORDER BY s.created_at DESC');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get shipments by destination location
    public function getShipmentsByDestination($location_id) {
        $this->db->query('SELECT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE s.destination_location_id = :location_id 
                         ORDER BY s.created_at DESC');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get active shipments (planned or in transit)
    public function getActiveShipments() {
        $this->db->query('SELECT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE s.status IN ("planned", "in_transit") 
                         ORDER BY s.estimated_arrival');
        $results = $this->db->resultSet();

        return $results;
    }

    // Create new shipment
    public function createShipment($data) {
        $this->db->query('INSERT INTO shipments (
                         shipment_code, 
                         origin_location_id, 
                         destination_location_id, 
                         status, 
                         estimated_departure, 
                         estimated_arrival, 
                         transport_mode, 
                         carrier_info) 
                         VALUES (
                         :shipment_code, 
                         :origin_location_id, 
                         :destination_location_id, 
                         :status, 
                         :estimated_departure, 
                         :estimated_arrival, 
                         :transport_mode, 
                         :carrier_info)');
        
        // Bind values
        $this->db->bind(':shipment_code', $data['shipment_code']);
        $this->db->bind(':origin_location_id', $data['origin_location_id']);
        $this->db->bind(':destination_location_id', $data['destination_location_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':estimated_departure', $data['estimated_departure']);
        $this->db->bind(':estimated_arrival', $data['estimated_arrival']);
        $this->db->bind(':transport_mode', $data['transport_mode']);
        $this->db->bind(':carrier_info', $data['carrier_info']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update shipment
    public function updateShipment($data) {
        $this->db->query('UPDATE shipments SET 
                         shipment_code = :shipment_code, 
                         origin_location_id = :origin_location_id, 
                         destination_location_id = :destination_location_id, 
                         status = :status, 
                         estimated_departure = :estimated_departure, 
                         actual_departure = :actual_departure, 
                         estimated_arrival = :estimated_arrival, 
                         actual_arrival = :actual_arrival, 
                         transport_mode = :transport_mode, 
                         carrier_info = :carrier_info 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':shipment_code', $data['shipment_code']);
        $this->db->bind(':origin_location_id', $data['origin_location_id']);
        $this->db->bind(':destination_location_id', $data['destination_location_id']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':estimated_departure', $data['estimated_departure']);
        $this->db->bind(':actual_departure', $data['actual_departure']);
        $this->db->bind(':estimated_arrival', $data['estimated_arrival']);
        $this->db->bind(':actual_arrival', $data['actual_arrival']);
        $this->db->bind(':transport_mode', $data['transport_mode']);
        $this->db->bind(':carrier_info', $data['carrier_info']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete shipment
    public function deleteShipment($id) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Delete shipment items first
            $this->db->query('DELETE FROM shipment_items WHERE shipment_id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // Then delete the shipment
            $this->db->query('DELETE FROM shipments WHERE id = :id');
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

    // Update shipment status
    public function updateShipmentStatus($id, $status) {
        $this->db->query('UPDATE shipments SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Mark shipment as departed
    public function departShipment($id) {
        $this->db->query('UPDATE shipments SET status = "in_transit", actual_departure = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Mark shipment as delivered
    public function deliverShipment($id) {
        $this->db->query('UPDATE shipments SET status = "delivered", actual_arrival = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Generate unique shipment code
    public function generateShipmentCode() {
        $prefix = 'SHP-';
        $year = date('Y');
        $month = date('m');
        
        // Get the last shipment code
        $this->db->query('SELECT shipment_code FROM shipments WHERE shipment_code LIKE :prefix ORDER BY id DESC LIMIT 1');
        $this->db->bind(':prefix', $prefix . $year . $month . '-%');
        $row = $this->db->single();
        
        if($row) {
            // Extract the sequence number and increment
            $parts = explode('-', $row->shipment_code);
            $seq = intval(end($parts)) + 1;
        } else {
            // Start with 1
            $seq = 1;
        }
        
        // Format sequence number with leading zeros
        $seq_formatted = str_pad($seq, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . '-' . $seq_formatted;
    }

    // Get shipment items
    public function getShipmentItems($shipment_id) {
        $this->db->query('SELECT si.*, a.name as asset_name, a.asset_code, a.unit_of_measure 
                         FROM shipment_items si 
                         LEFT JOIN assets a ON si.asset_id = a.id 
                         WHERE si.shipment_id = :shipment_id 
                         ORDER BY si.id');
        $this->db->bind(':shipment_id', $shipment_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Add shipment item
    public function addShipmentItem($data) {
        $this->db->query('INSERT INTO shipment_items (shipment_id, asset_id, quantity) 
                         VALUES (:shipment_id, :asset_id, :quantity)');
        
        // Bind values
        $this->db->bind(':shipment_id', $data['shipment_id']);
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':quantity', $data['quantity']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update shipment item
    public function updateShipmentItem($data) {
        $this->db->query('UPDATE shipment_items SET 
                         asset_id = :asset_id, 
                         quantity = :quantity 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':quantity', $data['quantity']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete shipment item
    public function deleteShipmentItem($id) {
        $this->db->query('DELETE FROM shipment_items WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get shipments for a specific requisition
    public function getShipmentsByRequisition($requisition_id) {
        $this->db->query('SELECT DISTINCT s.*, 
                         o.name as origin_name, 
                         d.name as destination_name 
                         FROM shipments s 
                         JOIN shipment_items si ON s.id = si.shipment_id 
                         JOIN requisition_items ri ON si.asset_id = ri.asset_id 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE ri.requisition_id = :requisition_id 
                         ORDER BY s.created_at DESC');
        $this->db->bind(':requisition_id', $requisition_id);
        $results = $this->db->resultSet();

        return $results;
    }
}
?>
