<?php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';

requireAdmin();

$page_title = 'Settings';

// Placeholder for settings logic
$settings = [
    'site_name' => SITE_NAME,
    'maintenance_mode' => '0'
];


include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Settings</h1>
    </div>

    <?php flash('settings_message'); ?>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">System Settings</h5>
            <form action="#" method="POST">
                <div class="mb-3">
                    <label for="site_name" class="form-label">Site Name</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo $settings['site_name']; ?>">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode" <?php echo $settings['maintenance_mode'] == '1' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</main>

<?php include_once '../../components/footer.php'; ?>
