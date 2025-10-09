<?php
// File: military_logistics/classes/DemandForecast.php
// This file contains the corrected demand forecasting logic.

require_once 'Database.php';

class DemandForecast {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // --- (getForecasts, getForecastById, etc. are unchanged) ---
    public function getForecasts() {
        $this->db->query('SELECT df.*, a.name as asset_name, a.asset_code, l.name as location_name, u.name as created_by_name 
                         FROM demand_forecasts df 
                         LEFT JOIN assets a ON df.asset_id = a.id 
                         LEFT JOIN locations l ON df.location_id = l.id 
                         LEFT JOIN users u ON df.created_by = u.id 
                         ORDER BY df.start_date DESC');
        return $this->db->resultSet();
    }

    // [MAJOR UPDATE] Generate forecast based on a proper time-series analysis.
    public function generateForecast($asset_id, $location_id, $time_period, $created_by) {
        $look_back_days = 90; // Use a fixed 90-day window for historical analysis.

        // Get the total consumption over the last 90 days.
        $this->db->query('
            SELECT SUM(quantity) as total_consumption
            FROM inventory_transactions
            WHERE asset_id = :asset_id
            AND location_id = :location_id
            AND transaction_type = "issue"
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL :look_back_days DAY)
        ');
        $this->db->bind(':asset_id', $asset_id);
        $this->db->bind(':location_id', $location_id);
        $this->db->bind(':look_back_days', $look_back_days);
        $result = $this->db->single();

        // If there is no consumption data in the look-back period, we cannot generate a forecast.
        if (!$result || $result->total_consumption <= 0) {
            return false; 
        }

        // Calculate the average daily consumption over the entire period.
        $total_consumption = $result->total_consumption;
        $avg_daily_consumption = $total_consumption / $look_back_days;

        // Set forecast parameters based on the selected time period.
        switch ($time_period) {
            case 'weekly':
                $forecast_days = 7;
                break;
            case 'monthly':
                $forecast_days = 30;
                break;
            case 'quarterly':
                $forecast_days = 90;
                break;
            default:
                $forecast_days = 30; // Default to monthly.
        }

        // Calculate the predicted quantity.
        $predicted_quantity = $avg_daily_consumption * $forecast_days;
        
        // Use a simplified confidence interval (e.g., +/- 20% of the prediction).
        $confidence_interval = $predicted_quantity * 0.20;

        // Set the date range for the forecast.
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+{$forecast_days} days"));

        // Prepare the data for insertion.
        $forecast_data = [
            'asset_id' => $asset_id,
            'location_id' => $location_id,
            'time_period' => $time_period,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'predicted_quantity' => round($predicted_quantity, 2),
            'confidence_interval' => round($confidence_interval, 2),
            'model_used' => '90-Day Moving Average',
            'created_by' => $created_by
        ];

        // Create the forecast in the database.
        return $this->createForecast($forecast_data);
    }

    // --- (Helper methods like createForecast, updateForecast, etc. are unchanged) ---
    public function createForecast($data) {
        $this->db->query('INSERT INTO demand_forecasts (
                         asset_id, location_id, time_period, start_date, end_date, 
                         predicted_quantity, confidence_interval, model_used, created_by) 
                         VALUES (
                         :asset_id, :location_id, :time_period, :start_date, :end_date, 
                         :predicted_quantity, :confidence_interval, :model_used, :created_by)');
        
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':time_period', $data['time_period']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':predicted_quantity', $data['predicted_quantity']);
        $this->db->bind(':confidence_interval', $data['confidence_interval']);
        $this->db->bind(':model_used', $data['model_used']);
        $this->db->bind(':created_by', $data['created_by']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    public function getTimePeriods() {
        return ['weekly', 'monthly', 'quarterly'];
    }

    public function getForecastModels() {
        return ['90-Day Moving Average', 'Manual'];
    }
    
    // Other methods from the original file are omitted for brevity...
}
?>

