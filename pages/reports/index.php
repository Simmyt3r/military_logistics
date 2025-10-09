<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';

requireLogin();

$page_title = 'Reports';

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reports</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Inventory Report</h5>
                    <p class="card-text">Generate a report of current inventory levels.</p>
                    <a href="#" class="btn btn-primary">Generate</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Shipment Report</h5>
                    <p class="card-text">Generate a report of shipments within a date range.</p>
                     <a href="#" class="btn btn-primary">Generate</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
