<?php
// Core files are now included in header.php, so we only need the page-specific class
require_once '../../classes/Location.php';
// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
$page_title = 'Locations';
$location = new Location();
$locations = $location->getLocations();

include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Locations</h1>
    </div>
    <div class="card">
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($locations as $loc): ?>
                        <tr>
                            <td><?php echo $loc->name; ?></td>
                            <td><?php echo $loc->type; ?></td>
                            <td><?php echo $loc->address; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
