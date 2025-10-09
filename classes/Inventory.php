<?php
require_once 'Database.php';

class Inventory {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all inventory transactions
    public function getTransactions() {
        $this->db->query('SELECT it.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as performed_by_name 
                         FROM inventory_transactions it 
                         LEFT JOIN assets a ON it.asset_id = a.id 
                         LEFT JOIN locations l ON it.location_id = l.id 
                         LEFT JOIN users u ON it.performed_by = u.id 
                         ORDER BY it.created_at DESC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get transaction by ID
    public function getTransactionById($id) {
        $this->db->query('SELECT it.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as performed_by_name 
                         FROM inventory_transactions it 
                         LEFT JOIN assets a ON it.asset_id = a.id 
                         LEFT JOIN locations l ON it.location_id = l.id 
                         LEFT JOIN users u ON it.performed_by = u.id 
                         WHERE it.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        return $row;
    }

    // Get transactions by asset
    public function getTransactionsByAsset($asset_id) {
        $this->db->query('SELECT it.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as performed_by_name 
                         FROM inventory_transactions it 
                         LEFT JOIN assets a ON it.asset_id = a.id 
                         LEFT JOIN locations l ON it.location_id = l.id 
                         LEFT JOIN users u ON it.performed_by = u.id 
                         WHERE it.asset_id = :asset_id 
                         ORDER BY it.created_at DESC');
        $this->db->bind(':asset_id', $asset_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get transactions by location
    public function getTransactionsByLocation($location_id) {
        $this->db->query('SELECT it.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as performed_by_name 
                         FROM inventory_transactions it 
                         LEFT JOIN assets a ON it.asset_id = a.id 
                         LEFT JOIN locations l ON it.location_id = l.id 
                         LEFT JOIN users u ON it.performed_by = u.id 
                         WHERE it.location_id = :location_id 
                         ORDER BY it.created_at DESC');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get transactions by type
    public function getTransactionsByType($type) {
        $this->db->query('SELECT it.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as performed_by_name 
                         FROM inventory_transactions it 
                         LEFT JOIN assets a ON it.asset_id = a.id 
                         LEFT JOIN locations l ON it.location_id = l.id 
                         LEFT JOIN users u ON it.performed_by = u.id 
                         WHERE it.transaction_type = :type 
                         ORDER BY it.created_at DESC');
        $this->db->bind(':type', $type);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get transactions by date range
    public function getTransactionsByDateRange($start_date, $end_date) {
        $this->db->query('SELECT it.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as performed_by_name 
                         FROM inventory_transactions it 
                         LEFT JOIN assets a ON it.asset_id = a.id 
                         LEFT JOIN locations l ON it.location_id = l.id 
                         LEFT JOIN users u ON it.performed_by = u.id 
                         WHERE DATE(it.created_at) BETWEEN :start_date AND :end_date 
                         ORDER BY it.created_at DESC');
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        $results = $this->db->resultSet();

        return $results;
    }

    // Create inventory transaction
    public function createTransaction($data) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Create the transaction record
            $this->db->query('INSERT INTO inventory_transactions (
                             asset_id, 
                             location_id, 
                             transaction_type, 
                             quantity, 
                             reference_type, 
                             reference_id, 
                             performed_by, 
                             notes) 
                             VALUES (
                             :asset_id, 
                             :location_id, 
                             :transaction_type, 
                             :quantity, 
                             :reference_type, 
                             :reference_id, 
                             :performed_by, 
                             :notes)');
            
            // Bind values
            $this->db->bind(':asset_id', $data['asset_id']);
            $this->db->bind(':location_id', $data['location_id']);
            $this->db->bind(':transaction_type', $data['transaction_type']);
            $this->db->bind(':quantity', $data['quantity']);
            $this->db->bind(':reference_type', $data['reference_type']);
            $this->db->bind(':reference_id', $data['reference_id']);
            $this->db->bind(':performed_by', $data['performed_by']);
            $this->db->bind(':notes', $data['notes']);
            
            $this->db->execute();
            $transaction_id = $this->db->lastInsertId();
            
            // Update asset quantity based on transaction type
            $this->db->query('SELECT quantity FROM assets WHERE id = :asset_id');
            $this->db->bind(':asset_id', $data['asset_id']);
            $asset = $this->db->single();
            
            $current_quantity = $asset->quantity;
            $new_quantity = $current_quantity;
            
            switch($data['transaction_type']) {
                case 'receipt':
                    $new_quantity = $current_quantity + $data['quantity'];
                    break;
                case 'issue':
                    $new_quantity = $current_quantity - $data['quantity'];
                    break;
                case 'transfer_in':
                    $new_quantity = $current_quantity + $data['quantity'];
                    break;
                case 'transfer_out':
                    $new_quantity = $current_quantity - $data['quantity'];
                    break;
                case 'adjustment':
                    $new_quantity = $current_quantity + $data['quantity']; // Can be positive or negative
                    break;
            }
            
            // Update asset quantity
            $this->db->query('UPDATE assets SET quantity = :quantity WHERE id = :asset_id');
            $this->db->bind(':asset_id', $data['asset_id']);
            $this->db->bind(':quantity', $new_quantity);
            $this->db->execute();
            
            // If this is a transfer, create the corresponding transaction at the other location
            if($data['transaction_type'] == 'transfer_out' && isset($data['destination_location_id'])) {
                $this->db->query('INSERT INTO inventory_transactions (
                                 asset_id, 
                                 location_id, 
                                 transaction_type, 
                                 quantity, 
                                 reference_type, 
                                 reference_id, 
                                 performed_by, 
                                 notes) 
                                 VALUES (
                                 :asset_id, 
                                 :location_id, 
                                 :transaction_type, 
                                 :quantity, 
                                 :reference_type, 
                                 :reference_id, 
                                 :performed_by, 
                                 :notes)');
                
                // Bind values
                $this->db->bind(':asset_id', $data['asset_id']);
                $this->db->bind(':location_id', $data['destination_location_id']);
                $this->db->bind(':transaction_type', 'transfer_in');
                $this->db->bind(':quantity', $data['quantity']);
                $this->db->bind(':reference_type', $data['reference_type']);
                $this->db->bind(':reference_id', $data['reference_id']);
                $this->db->bind(':performed_by', $data['performed_by']);
                $this->db->bind(':notes', 'Transfer from ' . $data['location_id'] . ' - ' . $data['notes']);
                
                $this->db->execute();
            }
            
            // Commit transaction
            $this->db->endTransaction();
            return $transaction_id;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->cancelTransaction();
            return false;
        }
    }

    // Process receipt of assets
    public function receiveAssets($asset_id, $location_id, $quantity, $performed_by, $reference_type = null, $reference_id = null, $notes = '') {
        $data = [
            'asset_id' => $asset_id,
            'location_id' => $location_id,
            'transaction_type' => 'receipt',
            'quantity' => $quantity,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'performed_by' => $performed_by,
            'notes' => $notes
        ];
        
        return $this->createTransaction($data);
    }

    // Process issue of assets
    public function issueAssets($asset_id, $location_id, $quantity, $performed_by, $reference_type = null, $reference_id = null, $notes = '') {
        $data = [
            'asset_id' => $asset_id,
            'location_id' => $location_id,
            'transaction_type' => 'issue',
            'quantity' => $quantity,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'performed_by' => $performed_by,
            'notes' => $notes
        ];
        
        return $this->createTransaction($data);
    }

    // Process transfer of assets between locations
    public function transferAssets($asset_id, $from_location_id, $to_location_id, $quantity, $performed_by, $reference_type = null, $reference_id = null, $notes = '') {
        $data = [
            'asset_id' => $asset_id,
            'location_id' => $from_location_id,
            'destination_location_id' => $to_location_id,
            'transaction_type' => 'transfer_out',
            'quantity' => $quantity,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'performed_by' => $performed_by,
            'notes' => $notes
        ];
        
        return $this->createTransaction($data);
    }

    // Process inventory adjustment
    public function adjustInventory($asset_id, $location_id, $quantity, $performed_by, $notes = '') {
        $data = [
            'asset_id' => $asset_id,
            'location_id' => $location_id,
            'transaction_type' => 'adjustment',
            'quantity' => $quantity, // Can be positive or negative
            'reference_type' => 'adjustment',
            'reference_id' => null,
            'performed_by' => $performed_by,
            'notes' => $notes
        ];
        
        return $this->createTransaction($data);
    }

    // Get inventory levels for all assets at a location
    public function getInventoryByLocation($location_id) {
        $this->db->query('SELECT a.*, c.name as category_name 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         WHERE a.location_id = :location_id 
                         ORDER BY a.name');
        $this->db->bind(':location_id', $location_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get inventory levels for an asset across all locations
    public function getInventoryByAsset($asset_id) {
        $this->db->query('SELECT a.*, l.name as location_name 
                         FROM assets a 
                         LEFT JOIN locations l ON a.location_id = l.id 
                         WHERE a.id = :asset_id 
                         ORDER BY l.name');
        $this->db->bind(':asset_id', $asset_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get inventory summary (total quantities by category)
    public function getInventorySummary() {
        $this->db->query('SELECT c.name as category_name, 
                         SUM(a.quantity) as total_quantity 
                         FROM assets a 
                         LEFT JOIN asset_categories c ON a.category_id = c.id 
                         GROUP BY a.category_id 
                         ORDER BY c.name');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get inventory value (assuming assets have a value field)
    public function getInventoryValue() {
        $this->db->query('SELECT SUM(a.quantity * a.value) as total_value 
                         FROM assets a');
        $row = $this->db->single();

        return $row->total_value ?? 0;
    }

    // Get low stock items
    public function getLowStockItems($threshold = 10) {
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

    // Get consumption rate for an asset
    public function getConsumptionRate($asset_id, $days = 30) {
        $this->db->query('SELECT SUM(quantity) as total_consumed 
                         FROM inventory_transactions 
                         WHERE asset_id = :asset_id 
                         AND transaction_type = "issue" 
                         AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)');
        $this->db->bind(':asset_id', $asset_id);
        $this->db->bind(':days', $days);
        $row = $this->db->single();
        
        $total_consumed = $row->total_consumed ?? 0;
        $daily_rate = $total_consumed / $days;
        
        return [
            'total_consumed' => $total_consumed,
            'daily_rate' => $daily_rate,
            'weekly_rate' => $daily_rate * 7,
            'monthly_rate' => $daily_rate * 30
        ];
    }

    // Calculate days of supply remaining
    public function getDaysOfSupply($asset_id) {
        // Get current quantity
        $this->db->query('SELECT quantity FROM assets WHERE id = :asset_id');
        $this->db->bind(':asset_id', $asset_id);
        $asset = $this->db->single();
        
        $current_quantity = $asset->quantity ?? 0;
        
        // Get consumption rate
        $consumption_rate = $this->getConsumptionRate($asset_id);
        $daily_rate = $consumption_rate['daily_rate'];
        
        // Calculate days of supply
        if($daily_rate > 0) {
            $days_of_supply = $current_quantity / $daily_rate;
        } else {
            $days_of_supply = 999; // No consumption, so effectively infinite
        }
        
        return $days_of_supply;
    }

    // Get all transaction types
    public function getTransactionTypes() {
        return ['receipt', 'issue', 'transfer_in', 'transfer_out', 'adjustment'];
    }
}
?>

