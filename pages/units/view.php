<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Unit.php';

requireLogin();

$page_title = 'View Unit';
$unit_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$unit = new Unit();
$unit_data = $unit->getUnitById($unit_id);

if(!$unit_data){
    redirect('pages/units/index.php');
}

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Unit Details: <?php echo $unit_data->name; ?></h1>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Unit details -->
            <p><strong>Commander:</strong> <?php echo $unit_data->commander_name; ?></p>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
