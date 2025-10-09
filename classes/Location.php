<?php
require_once 'Database.php';

class Location {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all locations
    public function getLocations() {
        $this->db->query('SELECT * FROM locations ORDER BY name');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get location by ID
    public function getLocationById($id) {
        $this->db->query('SELECT * FROM locations WHERE id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Get locations by type
    public function getLocationsByType($type) {
        $this->db->query('SELECT * FROM locations WHERE type = :type ORDER BY name');
        $this->db->bind(':type', $type);
        $results = $this->db->resultSet();

        return $results;
    }

    // Add location
    public function addLocation($data) {
        $this->db->query('INSERT INTO locations (name, type, latitude, longitude, address) 
                         VALUES (:name, :type, :latitude, :longitude, :address)');
        
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':address', $data['address']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Update location
    public function updateLocation($data) {
        $this->db->query('UPDATE locations SET 
                         name = :name, 
                         type = :type, 
                         latitude = :latitude, 
                         longitude = :longitude, 
                         address = :address 
                         WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':latitude', $data['latitude']);
        $this->db->bind(':longitude', $data['longitude']);
        $this->db->bind(':address', $data['address']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete location
    public function deleteLocation($id) {
        // Check if location is used in assets
        $this->db->query('SELECT COUNT(*) as count FROM assets WHERE location_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            return false; // Cannot delete location with assets
        }
        
        // Check if location is used in shipments
        $this->db->query('SELECT COUNT(*) as count FROM shipments WHERE origin_location_id = :id OR destination_location_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            return false; // Cannot delete location with shipments
        }
        
        // Check if location is used in units
        $this->db->query('SELECT COUNT(*) as count FROM units WHERE location_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            return false; // Cannot delete location with units
        }
        
        // Delete the location
        $this->db->query('DELETE FROM locations WHERE id = :id');
        $this->db->bind(':id', $id);
        
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get assets at location
    public function getAssetsAtLocation($location_id) {
        $this->db->query('SELECT a.*, c.name as category_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         WHERE a.location_id = :location_id 
                         ORDER BY a.name');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get units at location
    public function getUnitsAtLocation($location_id) {
        $this->db->query('SELECT * FROM units WHERE location_id = :location_id ORDER BY name');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get incoming shipments to location
    public function getIncomingShipments($location_id) {
        $this->db->query('SELECT s.*, o.name as origin_name 
                         FROM shipments s 
                         LEFT JOIN locations o ON s.origin_location_id = o.id 
                         WHERE s.destination_location_id = :location_id 
                         AND s.status IN ("planned", "in_transit") 
                         ORDER BY s.estimated_arrival');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get outgoing shipments from location
    public function getOutgoingShipments($location_id) {
        $this->db->query('SELECT s.*, d.name as destination_name 
                         FROM shipments s 
                         LEFT JOIN locations d ON s.destination_location_id = d.id 
                         WHERE s.origin_location_id = :location_id 
                         AND s.status IN ("planned", "in_transit") 
                         ORDER BY s.estimated_departure');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Calculate distance between two locations (using Haversine formula)
    public function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius of the earth in km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c; // Distance in km
        
        return $distance;
    }

    // Get distance between two locations
    public function getDistanceBetweenLocations($location1_id, $location2_id) {
        $location1 = $this->getLocationById($location1_id);
        $location2 = $this->getLocationById($location2_id);
        
        if($location1 && $location2) {
            return $this->calculateDistance(
                $location1->latitude, 
                $location1->longitude, 
                $location2->latitude, 
                $location2->longitude
            );
        }
        
        return false;
    }

    // Get all location types
    public function getLocationTypes() {
        $this->db->query('SELECT DISTINCT type FROM locations ORDER BY type');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get routes from a location
    public function getRoutesFromLocation($location_id) {
        $this->db->query('SELECT r.*, d.name as destination_name 
                         FROM routes r 
                         LEFT JOIN locations d ON r.destination_id = d.id 
                         WHERE r.origin_id = :location_id 
                         ORDER BY r.distance');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get routes to a location
    public function getRoutesToLocation($location_id) {
        $this->db->query('SELECT r.*, o.name as origin_name 
                         FROM routes r 
                         LEFT JOIN locations o ON r.origin_id = o.id 
                         WHERE r.destination_id = :location_id 
                         ORDER BY r.distance');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }
}
?>
