<?php
// Core files are now included in header.php, so we only need the page-specific class
require_once '../../classes/Asset.php';
// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
$page_title = 'Asset Management';
$asset = new Asset();
$assets = $asset->getAssets();

include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Asset Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="create.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-plus-circle me-1"></i>
                New Asset
            </a>
        </div>
    </div>

    <?php flash('asset_message'); ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($assets as $asset_data): ?>
                        <tr>
                            <td><?php echo $asset_data->asset_code; ?></td>
                            <td><?php echo $asset_data->name; ?></td>
                            <td><?php echo $asset_data->category_name; ?></td>
                            <td><?php echo $asset_data->location_name; ?></td>
                            <td><?php echo $asset_data->quantity . ' ' . $asset_data->unit_of_measure; ?></td>
                            <td><span class="badge <?php echo getStatusBadgeClass($asset_data->status); ?>"><?php echo ucfirst($asset_data->status); ?></span></td>
                            <td>
                                <a href="view.php?id=<?php echo $asset_data->id; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                <a href="edit.php?id=<?php echo $asset_data->id; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
