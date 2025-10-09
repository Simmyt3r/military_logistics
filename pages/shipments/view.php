<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Shipment.php';

requireLogin();

$page_title = 'View Shipment';
$ship_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$shipment = new Shipment();
$ship_data = $shipment->getShipmentById($ship_id);

if(!$ship_data){
     redirect('pages/shipments/index.php');
}


include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Shipment Details: <?php echo $ship_data->shipment_code; ?></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Shipment details -->
            <p><strong>Status:</strong> <span class="badge <?php echo getStatusBadgeClass($ship_data->status); ?>"><?php echo ucfirst($ship_data->status); ?></span></p>
             <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
