<?php
// File: military_logistics/pages/requisitions/view.php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Requisition.php';

requireLogin();

$page_title = 'View Requisition';

$req_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$requisition = new Requisition();
$req_data = $requisition->getRequisitionById($req_id);
$req_items = $requisition->getRequisitionItems($req_id);

if(!$req_data){
    flash('requisition_message', 'Requisition not found.', 'alert alert-danger');
    redirect('pages/requisitions/index.php');
}

// Security Check: Ensure non-admin/logistician users can only see their own unit's requisitions
if (!isAdmin() && !isLogistician() && $req_data->requesting_unit_id != $_SESSION['user_unit_id']) {
    flash('requisition_message', 'You do not have permission to view this requisition.', 'alert alert-danger');
    redirect('pages/requisitions/index.php');
}

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Requisition: <?php echo htmlspecialchars($req_data->requisition_code); ?></h1>
        <a href="index.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                   <h5><i class="fas fa-file-alt me-2"></i>Requested Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Qty Requested</th>
                                    <th>Qty Approved</th>
                                    <th>Qty Fulfilled</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($req_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item->asset_name); ?></td>
                                    <td><?php echo htmlspecialchars($item->quantity_requested); ?></td>
                                    <td><?php echo htmlspecialchars($item->quantity_approved ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item->quantity_fulfilled); ?></td>
                                    <td><span class="badge <?php echo getStatusBadgeClass($item->status); ?>"><?php echo ucfirst(htmlspecialchars($item->status)); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                 <div class="card-header">
                   <h5><i class="fas fa-info-circle me-2"></i>Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Requesting Unit:</strong> <?php echo htmlspecialchars($req_data->unit_name); ?></p>
                    <p><strong>Status:</strong> <span class="badge <?php echo getStatusBadgeClass($req_data->status); ?>"><?php echo ucfirst(htmlspecialchars($req_data->status)); ?></span></p>
                    <p><strong>Priority:</strong> <span class="badge <?php echo getPriorityBadgeClass($req_data->priority); ?>"><?php echo ucfirst(htmlspecialchars($req_data->priority)); ?></span></p>
                    <p><strong>Date Submitted:</strong> <?php echo formatDateTime($req_data->requested_date); ?></p>
                    <p><strong>Date Required:</strong> <?php echo formatDate($req_data->required_date); ?></p>
                    <p><strong>Approved By:</strong> <?php echo htmlspecialchars($req_data->approved_by_name ?? 'N/A'); ?></p>
                    <p><strong>Date Fulfilled:</strong> <?php echo isset($req_data->fulfillment_date) ? formatDateTime($req_data->fulfillment_date) : 'N/A'; ?></p>
                    <hr>
                    <h6>Notes:</h6>
                    <p><?php echo !empty($req_data->notes) ? nl2br(htmlspecialchars($req_data->notes)) : 'No notes provided.'; ?></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>

