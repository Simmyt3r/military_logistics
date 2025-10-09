<?php
// File: military_logistics/pages/inventory/transaction.php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Inventory.php';
require_once '../../classes/Asset.php';
require_once '../../classes/Location.php';

requireLogistician();

$page_title = 'New Inventory Transaction';

// Instantiate models
$inventory = new Inventory();
$asset = new Asset();
$location = new Location();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
        'asset_id' => trim($_POST['asset_id']),
        'location_id' => trim($_POST['location_id']),
        'transaction_type' => trim($_POST['transaction_type']),
        'quantity' => trim($_POST['quantity']),
        'notes' => trim($_POST['notes']),
        'performed_by' => $_SESSION['user_id'],
        'reference_type' => 'manual',
        'reference_id' => null
    ];

    // Basic validation
    if (empty($data['asset_id']) || empty($data['location_id']) || empty($data['transaction_type']) || empty($data['quantity'])) {
        flash('transaction_message', 'Please fill in all required fields.', 'alert alert-danger');
        redirect('pages/inventory/transaction.php');
    } else {
        if ($inventory->createTransaction($data)) {
            flash('inventory_message', 'Inventory transaction recorded successfully.');
            redirect('pages/inventory/index.php');
        } else {
            flash('transaction_message', 'Something went wrong. Please try again.', 'alert alert-danger');
            redirect('pages/inventory/transaction.php');
        }
    }
}

// Get data for form dropdowns
$assets = $asset->getAssets();
$locations = $location->getLocations();
$transaction_types = $inventory->getTransactionTypes();

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">New Inventory Transaction</h1>
    </div>

    <?php flash('transaction_message'); ?>

    <div class="card">
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="asset_id" class="form-label">Asset</label>
                        <select class="form-select select2" id="asset_id" name="asset_id" required>
                            <option value="">Select Asset...</option>
                            <?php foreach($assets as $a): ?>
                                <option value="<?php echo $a->id; ?>"><?php echo htmlspecialchars($a->name) . ' (' . htmlspecialchars($a->asset_code) . ')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="form-select select2" id="location_id" name="location_id" required>
                             <option value="">Select Location...</option>
                            <?php foreach($locations as $l): ?>
                                <option value="<?php echo $l->id; ?>"><?php echo htmlspecialchars($l->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="transaction_type" class="form-label">Transaction Type</label>
                        <select class="form-select" id="transaction_type" name="transaction_type" required>
                            <?php foreach($transaction_types as $type): ?>
                                <option value="<?php echo $type; ?>"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes / Justification</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Record Transaction</button>
                <a href="<?php echo URL_ROOT; ?>/pages/inventory/index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
