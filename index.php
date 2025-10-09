<?php
// File: military_logistics/index.php
require_once 'config/config.php';
require_once 'helpers/session_helper.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Asset.php';
require_once 'classes/Requisition.php';
require_once 'classes/Shipment.php';
require_once 'classes/Unit.php';
require_once 'classes/Inventory.php';

// Check if user is logged in
if(!isLoggedIn()) {
    redirect('login.php');
}

// Redirect field units to their specific dashboard
if(hasRole('field_unit')) {
    redirect('pages/my_unit/index.php');
}

// Set page title
$page_title = 'Dashboard';

// Get user data
$user = new User();
$users = $user->getUsers();
$userCount = count($users);

// Get asset data
$asset = new Asset();
$assets = $asset->getAssets();
$assetCount = count($assets);
$lowStockAssets = $asset->getLowStockAssets();
$lowStockCount = count($lowStockAssets);
$expiringAssets = $asset->getExpiringAssets();
$expiringCount = count($expiringAssets);

// Get requisition data
$requisition = new Requisition();
$pendingRequisitions = $requisition->getPendingRequisitions();
$pendingCount = count($pendingRequisitions);

// Get shipment data
$shipment = new Shipment();
$activeShipments = $shipment->getActiveShipments();
$activeShipmentCount = count($activeShipments);

// Get unit data
$unit = new Unit();
$units = $unit->getUnits();
$unitCount = count($units);

// Get inventory data
$inventory = new Inventory();
$inventorySummary = $inventory->getInventorySummary();

// Prepare data for charts
$assetCategories = [];
$assetQuantities = [];
foreach($inventorySummary as $item) {
    $assetCategories[] = $item->category_name;
    $assetQuantities[] = $item->total_quantity;
}

// Include header
include_once 'components/header.php';
// Include sidebar
include_once 'components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary export-csv-btn" data-table="#dashboardTable">
                    <i class="fas fa-file-csv me-1"></i> Export
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <i class="fas fa-calendar me-1"></i>
                This week
            </button>
        </div>
    </div>

    <?php flash('dashboard_message'); ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Assets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $assetCount; ?></div>
                            <div class="small text-muted mt-2">
                                <span class="text-danger"><?php echo $lowStockCount; ?> low stock</span> | 
                                <span class="text-warning"><?php echo $expiringCount; ?> expiring soon</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300 card-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Requisitions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingCount; ?></div>
                            <div class="small text-muted mt-2">Pending requisitions</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300 card-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Shipments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $activeShipmentCount; ?></div>
                            <div class="small text-muted mt-2">Active shipments</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300 card-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 dashboard-card warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Units</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $unitCount; ?></div>
                            <div class="small text-muted mt-2">Military units</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 card-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Inventory Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="assetsChart" 
                                data-categories='<?php echo json_encode($assetCategories); ?>' 
                                data-quantities='<?php echo json_encode($assetQuantities); ?>'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Low Stock Items</h6>
                </div>
                <div class="card-body">
                    <?php if(count($lowStockAssets) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Asset</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($lowStockAssets as $index => $asset): ?>
                                        <?php if($index < 5): ?>
                                            <tr>
                                                <td><?php echo $asset->name; ?></td>
                                                <td><?php echo $asset->quantity; ?> <?php echo $asset->unit_of_measure; ?></td>
                                                <td>
                                                    <div class="progress" style="height: 5px;">
                                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo min(($asset->quantity / 10) * 100, 100); ?>%" aria-valuenow="<?php echo $asset->quantity; ?>" aria-valuemin="0" aria-valuemax="10"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if(count($lowStockAssets) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo URL_ROOT; ?>/pages/assets/low_stock.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p>No low stock items found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <h2>Recent Activity</h2>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Requisitions</h6>
                    <a href="<?php echo URL_ROOT; ?>/pages/requisitions/index.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if(count($pendingRequisitions) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Unit</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pendingRequisitions as $index => $req): ?>
                                        <?php if($index < 5): ?>
                                            <tr>
                                                <td><a href="<?php echo URL_ROOT; ?>/pages/requisitions/view.php?id=<?php echo $req->id; ?>"><?php echo $req->requisition_code; ?></a></td>
                                                <td><?php echo $req->unit_name; ?></td>
                                                <td><span class="badge <?php echo getPriorityBadgeClass($req->priority); ?>"><?php echo ucfirst($req->priority); ?></span></td>
                                                <td><span class="badge <?php echo getStatusBadgeClass($req->status); ?>"><?php echo ucfirst($req->status); ?></span></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                            <p>No pending requisitions found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Active Shipments</h6>
                    <a href="<?php echo URL_ROOT; ?>/pages/shipments/index.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if(count($activeShipments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Origin</th>
                                        <th>Destination</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($activeShipments as $index => $ship): ?>
                                        <?php if($index < 5): ?>
                                            <tr>
                                                <td><a href="<?php echo URL_ROOT; ?>/pages/shipments/view.php?id=<?php echo $ship->id; ?>"><?php echo $ship->shipment_code; ?></a></td>
                                                <td><?php echo $ship->origin_name; ?></td>
                                                <td><?php echo $ship->destination_name; ?></td>
                                                <td><span class="badge <?php echo getStatusBadgeClass($ship->status); ?>"><?php echo ucfirst($ship->status); ?></span></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-truck-loading fa-3x text-success mb-3"></i>
                            <p>No active shipments found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Readiness -->
    <h2>Unit Readiness</h2>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Unit Readiness Status</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="dashboardTable">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Commander</th>
                                    <th>Readiness</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $unitObj = new Unit();
                                foreach($units as $u): 
                                ?>
                                    <?php 
                                        $readiness = $unitObj->getUnitReadiness($u->id);
                                        $readinessClass = getReadinessBadgeClass($readiness['status']);
                                    ?>
                                    <tr>
                                        <td><a href="<?php echo URL_ROOT; ?>/pages/units/view.php?id=<?php echo $u->id; ?>"><?php echo $u->name; ?></a></td>
                                        <td><?php echo $u->type; ?></td>
                                        <td><?php echo $u->location_name; ?></td>
                                        <td><?php echo $u->commander_name; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2"><?php echo round($readiness['percentage']); ?>%</span>
                                                <div class="progress flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar <?php echo $readinessClass; ?>" role="progressbar" style="width: <?php echo $readiness['percentage']; ?>%" aria-valuenow="<?php echo $readiness['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="ms-2 badge <?php echo $readinessClass; ?>"><?php echo ucfirst($readiness['status']); ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
// Page specific JavaScript
$page_specific_js = '
// Initialize charts when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById("assetsChart")) {
        initAssetsChart();
    }
});
';

include_once 'components/footer.php'; 
?>


