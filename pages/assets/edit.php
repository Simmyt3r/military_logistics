<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Asset.php';

requireLogin();

$page_title = 'Edit Asset';

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
        <h1 class="h2">Edit Asset: <?php echo $asset_data->name; ?></h1>
    </div>

    <div class="card">
        <div class="card-body">
             <form action="#" method="POST">
                <!-- Form fields for editing an asset, pre-filled with $asset_data -->
                <div class="mb-3">
                    <label for="name" class="form-label">Asset Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $asset_data->name; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Asset</button>
            </form>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
