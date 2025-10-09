<?php
// Core files are now included in header.php, so we only need the page-specific class
require_once '../../classes/User.php';
// The header now handles all core includes and session checks
include_once '../../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
requireAdmin();

$page_title = 'User Management';
$user = new User();
$users = $user->getUsers();

include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">User Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="#" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-plus-circle me-1"></i>
                New User
            </a>
        </div>
    </div>

    <?php flash('user_message'); ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Unit</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user_data): ?>
                        <tr>
                            <td><?php echo $user_data->name; ?></td>
                            <td><?php echo $user_data->email; ?></td>
                            <td><span class="badge bg-info"><?php echo ucfirst($user_data->role); ?></span></td>
                            <td><?php echo $user_data->unit_name ?? 'N/A'; ?></td>
                            <td><?php echo formatDate($user_data->created_at); ?></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
