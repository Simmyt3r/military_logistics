<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';

requireAdmin();

$page_title = 'Activity Logs';

// Placeholder for logs data
$logs = [
    (object)['user' => 'Admin User', 'action' => 'login', 'details' => 'User logged in', 'timestamp' => '2025-09-21 12:00:00'],
    (object)['user' => 'Admin User', 'action' => 'update_asset', 'details' => 'Updated asset #123', 'timestamp' => '2025-09-21 12:05:00'],
];

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Activity Logs</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?php echo $log->user; ?></td>
                            <td><?php echo $log->action; ?></td>
                            <td><?php echo $log->details; ?></td>
                            <td><?php echo formatDateTime($log->timestamp); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
