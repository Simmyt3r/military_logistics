<?php
// File: military_logistics/pages/my_unit/index.php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Unit.php';
require_once '../../classes/Shipment.php';
require_once '../../classes/Requisition.php';

// Require user to be logged in and part of a unit
requireLogin();
if (empty($_SESSION['user_unit_id'])) {
    flash('profile_message', 'You must be assigned to a unit to view this page.', 'alert alert-warning');
    redirect('pages/profile.php');
}

$page_title = 'My Unit Dashboard';

// Instantiate models
$unit = new Unit();
$shipment = new Shipment();
$requisition = new Requisition();

// Get Unit Data
$unit_id = $_SESSION['user_unit_id'];
$unit_data = $unit->getUnitById($unit_id);

if (!$unit_data) {
    flash('error', 'Could not retrieve unit data.', 'alert alert-danger');
    redirect('index.php');
}

// Get Unit Inventory, Incoming Shipments, and Requisitions
$unit_inventory = $unit->getUnitInventory($unit_id);
$incoming_shipments = $shipment->getShipmentsByDestination($unit_data->location_id);
$unit_requisitions = $requisition->getRequisitionsByUnit($unit_id);

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Unit: <?php echo htmlspecialchars($unit_data->name); ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo URL_ROOT; ?>/pages/requisitions/create.php" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus-circle me-1"></i>
                New Requisition
            </a>
        </div>
    </div>

    <?php flash('requisition_message'); ?>

    <!-- Unit Overview Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Unit Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Commander:</strong> <?php echo htmlspecialchars($unit_data->commander_name ?? 'N/A'); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($unit_data->type); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($unit_data->location_name); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-truck-loading me-2"></i>Incoming Shipments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Origin</th>
                                    <th>Status</th>
                                    <th>ETA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($incoming_shipments)): ?>
                                    <tr><td colspan="4" class="text-center">No incoming shipments.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($incoming_shipments, 0, 5) as $ship): ?>
                                    <tr>
                                        <td><a href="<?php echo URL_ROOT; ?>/pages/shipments/view.php?id=<?php echo $ship->id; ?>"><?php echo htmlspecialchars($ship->shipment_code); ?></a></td>
                                        <td><?php echo htmlspecialchars($ship->origin_name); ?></td>
                                        <td><span class="badge <?php echo getStatusBadgeClass($ship->status); ?>"><?php echo ucfirst(htmlspecialchars($ship->status)); ?></span></td>
                                        <td><?php echo formatDate($ship->estimated_arrival); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Inventory -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Current Unit Inventory</h5>
            <a href="<?php echo URL_ROOT; ?>/pages/assets/index.php" class="btn btn-sm btn-outline-secondary">View All Assets</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Asset Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unit_inventory)): ?>
                            <tr><td colspan="5" class="text-center">No assets found for this unit's location.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($unit_inventory, 0, 10) as $asset): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($asset->asset_code); ?></td>
                                    <td><?php echo htmlspecialchars($asset->name); ?></td>
                                    <td><?php echo htmlspecialchars($asset->category_name); ?></td>
                                    <td><?php echo htmlspecialchars($asset->quantity) . ' ' . htmlspecialchars($asset->unit_of_measure); ?></td>
                                    <td><span class="badge <?php echo getStatusBadgeClass($asset->status); ?>"><?php echo ucfirst(htmlspecialchars($asset->status)); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<?php include_once '../../components/footer.php'; ?>

