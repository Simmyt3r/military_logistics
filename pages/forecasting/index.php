<?php
// File: military_logistics/pages/forecasting/index.php
// This is the user interface for generating and viewing demand forecasts.

require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/DemandForecast.php';
require_once '../../classes/Asset.php';
require_once '../../classes/Location.php';

requireAnalyst();

$page_title = 'Demand Forecasting';

// Instantiate models
$forecast = new DemandForecast();
$asset_model = new Asset();
$location_model = new Location();

// Handle form submission for generating a new forecast
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
        'asset_id' => trim($_POST['asset_id']),
        'location_id' => trim($_POST['location_id']),
        'time_period' => trim($_POST['time_period']),
        'created_by' => $_SESSION['user_id']
    ];

    // Attempt to generate the forecast
    if ($forecast->generateForecast($data['asset_id'], $data['location_id'], $data['time_period'], $data['created_by'])) {
        flash('forecast_message', 'New demand forecast has been successfully generated.');
    } else {
        flash('forecast_message', 'Could not generate forecast. There may not be enough historical consumption data for the selected asset at this location.', 'alert alert-warning');
    }
    
    // Redirect to the same page to show the message and updated list
    redirect('pages/forecasting/index.php');
}

// Fetch data for the page
$forecasts = $forecast->getForecasts();
$assets = $asset_model->getAssets();
$locations = $location_model->getLocations();
$time_periods = $forecast->getTimePeriods();

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Demand Forecasting</h1>
    </div>

    <?php flash('forecast_message'); ?>

    <!-- Generate Forecast Form -->
    <div class="card mb-4">
        <div class="card-header">
            Generate New Forecast
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="asset_id" class="form-label">Select Asset</label>
                        <select name="asset_id" id="asset_id" class="form-select" required>
                            <option value="">Choose...</option>
                            <?php foreach ($assets as $asset_item) : ?>
                                <option value="<?php echo $asset_item->id; ?>"><?php echo $asset_item->name; ?> (<?php echo $asset_item->asset_code; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="location_id" class="form-label">Select Location</label>
                        <select name="location_id" id="location_id" class="form-select" required>
                            <option value="">Choose...</option>
                            <?php foreach ($locations as $location) : ?>
                                <option value="<?php echo $location->id; ?>"><?php echo $location->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="time_period" class="form-label">Time Period</label>
                        <select name="time_period" id="time_period" class="form-select" required>
                            <?php foreach ($time_periods as $period) : ?>
                                <option value="<?php echo $period; ?>"><?php echo ucfirst($period); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary w-100">Generate Forecast</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Forecasts Table -->
    <div class="card">
        <div class="card-header">
            Generated Forecasts
        </div>
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Location</th>
                            <th>Forecast Period</th>
                            <th>Predicted Qty</th>
                            <th>Model Used</th>
                            <th>Generated On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($forecasts)) : ?>
                            <?php foreach($forecasts as $fc): ?>
                            <tr>
                               <td><?php echo $fc->asset_name; ?></td>
                               <td><?php echo $fc->location_name; ?></td>
                               <td><?php echo formatDate($fc->start_date) . ' to ' . formatDate($fc->end_date); ?> (<?php echo ucfirst($fc->time_period);?>)</td>
                               <td><?php echo formatNumber($fc->predicted_quantity, 2); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $fc->model_used; ?></span></td>
                               <td><?php echo formatDateTime($fc->created_at); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No forecasts have been generated yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>

