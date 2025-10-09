<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';

requireLogin();

$page_title = 'Create Shipment';

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Create New Shipment</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="#" method="POST">
                <!-- Form fields for creating a shipment -->
                <button type="submit" class="btn btn-primary">Create Shipment</button>
            </form>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
