<?php
// Use __DIR__ to create absolute paths relative to the current file
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/session_helper.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Unit.php';

// Initialize database connection
$database = new Database();

// Check if user is logged in
if(!isLoggedIn()) {
    // Adjust redirect path based on directory depth
    $path_to_login = file_exists('login.php') ? 'login.php' : (file_exists('../login.php') ? '../login.php' : '../../login.php');
    redirect($path_to_login);
}

// Get current user info
$user = new User();
$currentUser = $user->getUserById($_SESSION['user_id']);

// Get user's unit info if applicable
$unitInfo = null;
if($currentUser->unit_id) {
    $unit = new Unit();
    $unitInfo = $unit->getUnitById($currentUser->unit_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr for date picking -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Select2 for enhanced dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/style.css">
</head>
<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="<?php echo URL_ROOT; ?>/index.php">
            <i class="fas fa-shield-alt me-2"></i>
            <?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="<?php echo URL_ROOT; ?>/pages/profile.php">
                    <img src="<?php echo URL_ROOT; ?>/assets/images/<?php echo $currentUser->profile_image; ?>" alt="Profile" class="rounded-circle me-2" width="24" height="24" onerror="this.src='<?php echo URL_ROOT; ?>/assets/images/default.jpg'">
                    <?php echo $currentUser->name; ?>
                    <?php if($unitInfo): ?>
                        <span class="badge bg-secondary"><?php echo $unitInfo->name; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="<?php echo URL_ROOT; ?>/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Sign out
                </a>
            </div>
        </div>
    </header>
