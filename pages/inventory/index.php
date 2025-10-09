<?php
// Core files are now included in header.php, so we only need the page-specific class
require_once '../../classes/Inventory.php';
// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
requireLogistician();

$page_title = 'Inventory Management';
$inventory = new Inventory();
$transactions = $inventory->getTransactions();

include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Inventory Transactions</h1>
         <a href="transaction.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-plus-circle me-1"></i>
            New Transaction
        </a>
    </div>
    <div class="card">
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Performed By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach($transactions as $txn): ?>
                        <tr>
                            <td><?php echo $txn->asset_name; ?></td>
                            <td><?php echo $txn->location_name; ?></td>
                            <td><?php echo ucfirst($txn->transaction_type); ?></td>
                            <td><?php echo $txn->quantity; ?></td>
                             <td><?php echo $txn->performed_by_name; ?></td>
                            <td><?php echo formatDateTime($txn->created_at); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
