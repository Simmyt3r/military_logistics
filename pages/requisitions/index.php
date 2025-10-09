<?php
// Core files are now included in header.php, so we only need the page-specific class
require_once '../../classes/Requisition.php';
// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
$page_title = 'Requisitions';
$requisition = new Requisition();
$requisitions = $requisition->getRequisitions();

include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Requisitions</h1>
         <a href="create.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-plus-circle me-1"></i>
            New Requisition
        </a>
    </div>

     <div class="card">
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Requesting Unit</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Required Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requisitions as $req): ?>
                        <tr>
                           <td><a href="view.php?id=<?php echo $req->id; ?>"><?php echo $req->requisition_code; ?></a></td>
                           <td><?php echo $req->unit_name; ?></td>
                           <td><span class="badge <?php echo getStatusBadgeClass($req->status); ?>"><?php echo ucfirst($req->status); ?></span></td>
                           <td><span class="badge <?php echo getPriorityBadgeClass($req->priority); ?>"><?php echo ucfirst($req->priority); ?></span></td>
                           <td><?php echo formatDate($req->required_date); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
