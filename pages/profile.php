<?php
// Core files are now included in header.php
include_once '../components/header.php';

// All logic below here requires the user to be logged in, which header.php enforces.
$page_title = 'User Profile';
// $user object is already created in header.php as $user
$user_data = $user->getUserById($_SESSION['user_id']);

include_once '../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Profile</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Profile Information</h5>
            <p><strong>Name:</strong> <?php echo $user_data->name; ?></p>
            <p><strong>Email:</strong> <?php echo $user_data->email; ?></p>
            <p><strong>Role:</strong> <?php echo ucfirst($user_data->role); ?></p>
            <a href="#" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</main>

<?php include_once '../components/footer.php'; ?>
