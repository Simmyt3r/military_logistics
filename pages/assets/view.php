<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Asset.php';

requireLogin();

$page_title = 'View Asset';

$asset_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$asset = new Asset();
$asset_data = $asset->getAssetById($asset_id);

if(!$asset_data){
    redirect('pages/assets/index.php');
}

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Asset Details: <?php echo $asset_data->name; ?></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Asset details would be displayed here -->
            <p><strong>Asset Code:</strong> <?php echo $asset_data->asset_code; ?></p>
            <p><strong>Category:</strong> <?php echo $asset_data->category_name; ?></p>
            <p><strong>Location:</strong> <?php echo $asset_data->location_name; ?></p>
            <p><strong>Quantity:</strong> <?php echo $asset_data->quantity . ' ' . $asset_data->unit_of_measure; ?></p>
            <p><strong>Status:</strong> <span class="badge <?php echo getStatusBadgeClass($asset_data->status); ?>"><?php echo ucfirst($asset_data->status); ?></span></p>
             <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
