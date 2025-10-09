<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Asset.php';
require_once '../../classes/Location.php';

// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
requireLogistician(); // Or requireAdmin() depending on permissions

$page_title = 'Create Asset';

$asset = new Asset();
$location = new Location();
$categories = $asset->getAssetCategories();
$locations = $location->getLocations();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $data = [
        'name' => trim($_POST['name']),
        'asset_code' => trim($_POST['asset_code']),
        'category_id' => (int)trim($_POST['category_id']),
        'description' => trim($_POST['description']),
        'quantity' => (int)trim($_POST['quantity']),
        'unit_of_measure' => trim($_POST['unit_of_measure']),
        'location_id' => (int)trim($_POST['location_id']),
        'status' => trim($_POST['status']),
        'expiration_date' => !empty($_POST['expiration_date']) ? trim($_POST['expiration_date']) : null,
        // Add other fields as necessary, ensure they are null if empty and not required
        'rfid_tag' => null,
        'gps_unit_id' => null,
    ];

    // Basic validation (can be expanded)
    if (!empty($data['name']) && !empty($data['asset_code']) && !empty($data['category_id']) && !empty($data['location_id'])) {
        if ($asset->addAsset($data)) {
            flash('asset_message', 'New asset has been added successfully.');
            redirect('pages/assets/index.php');
        } else {
            flash('asset_message', 'Something went wrong while adding the asset.', 'alert alert-danger');
        }
    } else {
        flash('asset_message', 'Please fill out all required fields.', 'alert alert-danger');
    }
}


include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Create New Asset</h1>
    </div>

    <?php flash('asset_message'); ?>

    <div class="card">
        <div class="card-body">
             <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Asset Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="asset_code" class="form-label">Asset Code</label>
                        <input type="text" class="form-control" id="asset_code" name="asset_code" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="" selected disabled>Select a category...</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            <option value="" selected disabled>Select a location...</option>
                            <?php foreach($locations as $loc): ?>
                                <option value="<?php echo $loc->id; ?>"><?php echo $loc->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="0">
                    </div>
                     <div class="col-md-4 mb-3">
                        <label for="unit_of_measure" class="form-label">Unit of Measure</label>
                        <input type="text" class="form-control" id="unit_of_measure" name="unit_of_measure" placeholder="e.g., units, kg, liters" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available">Available</option>
                            <option value="deployed">Deployed</option>
                            <option value="maintenance">Maintenance</option>
                             <option value="expired">Expired</option>
                        </select>
                    </div>
                </div>
                 <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                 <div class="mb-3">
                    <label for="expiration_date" class="form-label">Expiration Date (Optional)</label>
                    <input type="date" class="form-control date-picker" id="expiration_date" name="expiration_date">
                </div>
                 <button type="submit" class="btn btn-primary">Create Asset</button>
                 <a href="<?php echo URL_ROOT; ?>/pages/assets/index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
