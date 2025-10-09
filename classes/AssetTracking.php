<?php
// File: military_logistics/classes/AssetTracking.php
require_once 'Database.php';

class AssetTracking {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Get all defined map zones from the database.
     */
    public function getMapZones() {
        $this->db->query('SELECT * FROM map_zones');
        $results = $this->db->resultSet();
        // Decode the JSON coordinates string into an array
        foreach ($results as $result) {
            $result->coordinates = json_decode($result->coordinates);
        }
        return $results;
    }

    /**
     * Get the current locations of all trackable assets.
     * This is the core of the simulation.
     */
    public function getLiveAssetLocations($user_role = 'admin', $user_unit_id = null) {
        // Step 1: Simulate movement for assets that are 'in_transit'
        $this->simulateMovement();

        // Step 2: Fetch the latest positions of all trackable assets
        $sql = 'SELECT 
                    alt.asset_id, 
                    alt.latitude, 
                    alt.longitude, 
                    alt.speed_kmh, 
                    alt.heading,
                    alt.last_updated,
                    a.name as asset_name,
                    a.asset_code,
                    a.status,
                    a.gps_unit_id,
                    ac.name as category_name,
                    u.id as unit_id,
                    u.name as unit_name
                FROM asset_live_tracking alt
                JOIN assets a ON alt.asset_id = a.id
                JOIN asset_categories ac ON a.category_id = ac.id
                LEFT JOIN units u ON a.location_id = u.location_id'; // A simple way to associate assets with units at their base

        // Role-based filtering
        if ($user_role == 'field_unit' && $user_unit_id) {
             // Soldiers see assets associated with their unit's location
            $sql .= ' WHERE u.id = :unit_id';
            $this->db->query($sql);
            $this->db->bind(':unit_id', $user_unit_id);
        } else {
            $this->db->query($sql);
        }

        return $this->db->resultSet();
    }

    /**
     * This function simulates the movement of assets marked as 'in_transit'.
     * In a real system, this would be replaced by data from actual GPS units.
     */
    private function simulateMovement() {
        // Get all active shipments with trackable assets
        $this->db->query('SELECT 
                            s.id as shipment_id,
                            si.asset_id,
                            orig.latitude as origin_lat,
                            orig.longitude as origin_lon,
                            dest.latitude as dest_lat,
                            dest.longitude as dest_lon,
                            alt.latitude as current_lat,
                            alt.longitude as current_lon
                        FROM shipments s
                        JOIN shipment_items si ON s.id = si.shipment_id
                        JOIN assets a ON si.asset_id = a.id
                        JOIN locations orig ON s.origin_location_id = orig.id
                        JOIN locations dest ON s.destination_location_id = dest.id
                        JOIN asset_live_tracking alt ON a.id = alt.asset_id
                        WHERE s.status = "in_transit" AND a.gps_unit_id IS NOT NULL');
        
        $moving_assets = $this->db->resultSet();

        foreach ($moving_assets as $asset) {
            $current_lat = $asset->current_lat;
            $current_lon = $asset->current_lon;
            $dest_lat = $asset->dest_lat;
            $dest_lon = $asset->dest_lon;

            // Simple linear interpolation for movement
            // Move 0.5% of the remaining distance each time the API is called
            $step = 0.005; 
            $new_lat = $current_lat + ($dest_lat - $current_lat) * $step;
            $new_lon = $current_lon + ($dest_lon - $current_lon) * $step;

            // Calculate distance to destination
            $dist_to_dest = $this->calculateDistance($new_lat, $new_lon, $dest_lat, $dest_lon);

            // If asset is very close to destination, mark shipment as delivered
            if ($dist_to_dest < 1) { // less than 1km
                $this->db->query('UPDATE shipments SET status = "delivered", actual_arrival = NOW() WHERE id = :shipment_id');
                $this->db->bind(':shipment_id', $asset->shipment_id);
                $this->db->execute();
                
                $this->db->query('UPDATE assets SET status = "available" WHERE id = :asset_id');
                $this->db->bind(':asset_id', $asset->asset_id);
                $this->db->execute();
            } else {
                 // Update the asset's live position in the database
                $this->db->query('UPDATE asset_live_tracking SET latitude = :lat, longitude = :lon, speed_kmh = 60 WHERE asset_id = :asset_id');
                $this->db->bind(':lat', $new_lat);
                $this->db->bind(':lon', $new_lon);
                $this->db->bind(':asset_id', $asset->asset_id);
                $this->db->execute();
            }
        }
    }
    
    // Haversine formula to calculate distance between two points in km
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}
