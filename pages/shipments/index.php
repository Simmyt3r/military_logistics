<?php
// Core files are now included in header.php, so we only need the page-specific class
require_once '../../classes/Shipment.php';
// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
$page_title = 'Shipments';
$shipment = new Shipment();
$shipments = $shipment->getShipments();

include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Shipments</h1>
         <a href="create.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-plus-circle me-1"></i>
            New Shipment
        </a>
    </div>

    <div class="card">
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Estimated Arrival</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($shipments as $ship): ?>
                        <tr>
                           <td><a href="view.php?id=<?php echo $ship->id; ?>"><?php echo $ship->shipment_code; ?></a></td>
                           <td><?php echo $ship->origin_name; ?></td>
                           <td><?php echo $ship->destination_name; ?></td>
                           <td><span class="badge <?php echo getStatusBadgeClass($ship->status); ?>"><?php echo ucfirst($ship->status); ?></span></td>
                           <td><?php echo formatDateTime($ship->estimated_arrival); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
