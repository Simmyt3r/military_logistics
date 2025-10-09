<?php
// File: military_logistics/pages/requisitions/create.php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
require_once '../../classes/Requisition.php';
require_once '../../classes/Asset.php';

requireLogin();
if (empty($_SESSION['user_unit_id'])) {
    flash('profile_message', 'You must be assigned to a unit to create a requisition.', 'alert alert-warning');
    redirect('pages/profile.php');
}

$requisition = new Requisition();
$asset = new Asset();
$page_title = 'Create Requisition';

// Get available assets for the dropdown
$available_assets = $asset->getAssets(); 

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
        'requesting_unit_id' => $_SESSION['user_unit_id'],
        'priority' => trim($_POST['priority']),
        'required_date' => trim($_POST['required_date']),
        'notes' => trim($_POST['notes']),
        'items' => isset($_POST['items']) ? $_POST['items'] : [],
        'error' => ''
    ];

    // Validate form data
    if (empty($data['priority']) || empty($data['required_date'])) {
        $data['error'] = 'Please fill in all required fields.';
    }
    if (empty($data['items']) || !is_array($data['items'])) {
        $data['error'] = 'Please add at least one item to the requisition.';
    }

    // Check items for valid asset_id and quantity
    foreach ($data['items'] as $item) {
        if (empty($item['asset_id']) || !is_numeric($item['asset_id']) || empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
            $data['error'] = 'Invalid data for one or more items. Please check asset and quantity.';
            break;
        }
    }

    if (empty($data['error'])) {
        // Attempt to create the requisition
        if ($requisition->createRequisitionWithItems($data)) {
            flash('requisition_message', 'Requisition submitted successfully.');
            redirect('pages/requisitions/index.php');
        } else {
            flash('requisition_message', 'Something went wrong. Please try again.', 'alert alert-danger');
            redirect('pages/requisitions/create.php');
        }
    } else {
        flash('requisition_message', $data['error'], 'alert alert-danger');
    }
}

include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Create New Requisition</h1>
    </div>

    <?php flash('requisition_message'); ?>

    <div class="card">
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="requisitionForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="required_date" class="form-label">Required Date</label>
                        <input type="text" class="form-control date-picker" id="required_date" name="required_date" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes / Justification</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Requested Items</h5>
                <div id="items-container">
                    <!-- Dynamic items will be added here -->
                </div>
                
                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-item-btn">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Submit Requisition</button>
                    <a href="<?php echo URL_ROOT; ?>/pages/requisitions/index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Item Row Template -->
<template id="item-template">
    <div class="row item-row mb-3 align-items-center">
        <div class="col-md-6">
            <label class="form-label">Asset</label>
            <select class="form-select item-asset" name="items[][asset_id]" required>
                <option value="">Select Asset...</option>
                <?php foreach($available_assets as $asset_item): ?>
                    <option value="<?php echo $asset_item->id; ?>"><?php echo htmlspecialchars($asset_item->name) . ' (' . htmlspecialchars($asset_item->asset_code) . ')'; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Quantity</label>
            <input type="number" class="form-control item-quantity" name="items[][quantity]" min="1" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger remove-item-btn w-100">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<?php 
$page_specific_js = '
document.addEventListener("DOMContentLoaded", function() {
    const itemsContainer = document.getElementById("items-container");
    const addItemBtn = document.getElementById("add-item-btn");
    const itemTemplate = document.getElementById("item-template");

    let itemIndex = 0;

    function addNewItem() {
        const templateContent = itemTemplate.content.cloneNode(true);
        const itemRow = templateContent.querySelector(".item-row");
        
        // Update names to be unique for form submission
        itemRow.querySelector(".item-asset").name = `items[${itemIndex}][asset_id]`;
        itemRow.querySelector(".item-quantity").name = `items[${itemIndex}][quantity]`;

        itemsContainer.appendChild(templateContent);

        // Initialize select2 on the new select element
        if (typeof $.fn.select2 !== "undefined") {
            $(itemRow).find(".item-asset").select2({
                theme: "bootstrap-5",
                placeholder: "Select an asset"
            });
        }
        
        itemIndex++;
    }

    addItemBtn.addEventListener("click", addNewItem);

    itemsContainer.addEventListener("click", function(e) {
        if (e.target.classList.contains("remove-item-btn") || e.target.closest(".remove-item-btn")) {
            e.target.closest(".item-row").remove();
        }
    });

    // Add one item row by default
    addNewItem();
});
';
include_once '../../components/footer.php'; 
?>

