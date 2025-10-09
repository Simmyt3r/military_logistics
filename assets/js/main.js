// Military Logistics System - Main JavaScript

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide flash messages after 5 seconds
    var flashMessage = document.getElementById('msg-flash');
    if (flashMessage) {
        setTimeout(function() {
            flashMessage.style.opacity = '0';
            setTimeout(function() {
                flashMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }

    // Initialize dashboard charts if they exist
    if (document.getElementById('assetsChart')) {
        initAssetsChart();
    }
    
    if (document.getElementById('requisitionsChart')) {
        initRequisitionsChart();
    }

    if (document.getElementById('shipmentsChart')) {
        initShipmentsChart();
    }

    if (document.getElementById('readinessChart')) {
        initReadinessChart();
    }

    // Handle sidebar toggle on mobile
    var sidebarToggle = document.querySelector('[data-bs-toggle="collapse"]');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Handle form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Handle password visibility toggle
    var togglePassword = document.querySelector('.toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            var passwordInput = document.querySelector(this.getAttribute('toggle'));
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    }

    // Handle file input preview
    var fileInput = document.querySelector('.custom-file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var fileName = this.files[0].name;
            var nextSibling = this.nextElementSibling;
            nextSibling.innerText = fileName;
            
            // Image preview
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.querySelector('.img-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Handle date range pickers
    var dateRangePicker = document.querySelector('.date-range-picker');
    if (dateRangePicker) {
        flatpickr(dateRangePicker, {
            mode: "range",
            dateFormat: "Y-m-d"
        });
    }

    // Handle single date pickers
    var datePickers = document.querySelectorAll('.date-picker');
    if (datePickers.length > 0) {
        datePickers.forEach(function(picker) {
            flatpickr(picker, {
                dateFormat: "Y-m-d"
            });
        });
    }

    // Handle datetime pickers
    var datetimePickers = document.querySelectorAll('.datetime-picker');
    if (datetimePickers.length > 0) {
        datetimePickers.forEach(function(picker) {
            flatpickr(picker, {
                enableTime: true,
                dateFormat: "Y-m-d H:i"
            });
        });
    }

    // Handle select2 dropdowns
    var select2Inputs = document.querySelectorAll('.select2');
    if (select2Inputs.length > 0 && typeof $.fn.select2 !== 'undefined') {
        $(select2Inputs).select2({
            theme: 'bootstrap-5'
        });
    }

    // Handle dynamic form fields
    var addItemButton = document.querySelector('.add-item-btn');
    if (addItemButton) {
        addItemButton.addEventListener('click', function() {
            var itemsContainer = document.querySelector('.items-container');
            var itemTemplate = document.querySelector('.item-template');
            var newItem = itemTemplate.cloneNode(true);
            
            newItem.classList.remove('item-template');
            newItem.classList.remove('d-none');
            
            // Update IDs and names to make them unique
            var itemCount = document.querySelectorAll('.item-row').length;
            var inputs = newItem.querySelectorAll('input, select');
            inputs.forEach(function(input) {
                var name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace('[]', '[' + itemCount + ']'));
                }
                input.value = '';
            });
            
            // Add remove button functionality
            var removeButton = newItem.querySelector('.remove-item-btn');
            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    newItem.remove();
                });
            }
            
            itemsContainer.appendChild(newItem);
            
            // Reinitialize select2 for new dropdowns
            if (typeof $.fn.select2 !== 'undefined') {
                $(newItem).find('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }
        });
    }

    // Handle asset search
    var assetSearch = document.querySelector('#assetSearch');
    if (assetSearch) {
        assetSearch.addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var assetRows = document.querySelectorAll('.asset-row');
            
            assetRows.forEach(function(row) {
                var assetName = row.querySelector('.asset-name').textContent.toLowerCase();
                var assetCode = row.querySelector('.asset-code').textContent.toLowerCase();
                var assetCategory = row.querySelector('.asset-category').textContent.toLowerCase();
                
                if (assetName.includes(searchValue) || assetCode.includes(searchValue) || assetCategory.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle requisition search
    var requisitionSearch = document.querySelector('#requisitionSearch');
    if (requisitionSearch) {
        requisitionSearch.addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var requisitionRows = document.querySelectorAll('.requisition-row');
            
            requisitionRows.forEach(function(row) {
                var requisitionCode = row.querySelector('.requisition-code').textContent.toLowerCase();
                var unitName = row.querySelector('.unit-name').textContent.toLowerCase();
                var status = row.querySelector('.status').textContent.toLowerCase();
                
                if (requisitionCode.includes(searchValue) || unitName.includes(searchValue) || status.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle shipment search
    var shipmentSearch = document.querySelector('#shipmentSearch');
    if (shipmentSearch) {
        shipmentSearch.addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var shipmentRows = document.querySelectorAll('.shipment-row');
            
            shipmentRows.forEach(function(row) {
                var shipmentCode = row.querySelector('.shipment-code').textContent.toLowerCase();
                var origin = row.querySelector('.origin').textContent.toLowerCase();
                var destination = row.querySelector('.destination').textContent.toLowerCase();
                var status = row.querySelector('.status').textContent.toLowerCase();
                
                if (shipmentCode.includes(searchValue) || origin.includes(searchValue) || 
                    destination.includes(searchValue) || status.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle print button
    var printButton = document.querySelector('.print-btn');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }

    // Handle export to CSV button
    var exportCsvButton = document.querySelector('.export-csv-btn');
    if (exportCsvButton) {
        exportCsvButton.addEventListener('click', function() {
            var table = document.querySelector(this.getAttribute('data-table'));
            if (!table) return;
            
            var rows = table.querySelectorAll('tr');
            var csv = [];
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (var j = 0; j < cols.length; j++) {
                    // Clean the text content (remove extra spaces, quotes, etc.)
                    var text = cols[j].textContent.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ').trim();
                    // Escape double quotes
                    text = text.replace(/"/g, '""');
                    // Add the text wrapped in quotes
                    row.push('"' + text + '"');
                }
                
                csv.push(row.join(','));
            }
            
            var csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'export_' + new Date().toISOString().slice(0, 10) + '.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    // Handle map initialization if Google Maps API is loaded
    var mapContainer = document.getElementById('map');
    if (mapContainer && typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
        initMap(mapContainer);
    }
});

// Initialize assets chart
function initAssetsChart() {
    var ctx = document.getElementById('assetsChart').getContext('2d');
    
    // Get data from the data attributes
    var categories = JSON.parse(ctx.canvas.getAttribute('data-categories'));
    var quantities = JSON.parse(ctx.canvas.getAttribute('data-quantities'));
    
    var assetsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Asset Quantities by Category',
                data: quantities,
                backgroundColor: [
                    'rgba(26, 60, 110, 0.7)',
                    'rgba(44, 95, 155, 0.7)',
                    'rgba(77, 126, 184, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(23, 162, 184, 0.7)'
                ],
                borderColor: [
                    'rgba(26, 60, 110, 1)',
                    'rgba(44, 95, 155, 1)',
                    'rgba(77, 126, 184, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(23, 162, 184, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Initialize requisitions chart
function initRequisitionsChart() {
    var ctx = document.getElementById('requisitionsChart').getContext('2d');
    
    // Get data from the data attributes
    var months = JSON.parse(ctx.canvas.getAttribute('data-months'));
    var counts = JSON.parse(ctx.canvas.getAttribute('data-counts'));
    
    var requisitionsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Requisitions by Month',
                data: counts,
                fill: false,
                borderColor: 'rgba(26, 60, 110, 1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

// Initialize shipments chart
function initShipmentsChart() {
    var ctx = document.getElementById('shipmentsChart').getContext('2d');
    
    // Get data from the data attributes
    var statuses = JSON.parse(ctx.canvas.getAttribute('data-statuses'));
    var counts = JSON.parse(ctx.canvas.getAttribute('data-counts'));
    
    var shipmentsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statuses,
            datasets: [{
                label: 'Shipments by Status',
                data: counts,
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(23, 162, 184, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Initialize readiness chart
function initReadinessChart() {
    var ctx = document.getElementById('readinessChart').getContext('2d');
    
    // Get data from the data attributes
    var units = JSON.parse(ctx.canvas.getAttribute('data-units'));
    var percentages = JSON.parse(ctx.canvas.getAttribute('data-percentages'));
    
    var readinessChart = new Chart(ctx, {
        type: 'bar',
           indexAxis: 'y', // This replaces the deprecated 'horizontalBar' type
        data: {
            labels: units,
            datasets: [{
                label: 'Unit Readiness (%)',
                data: percentages,
                backgroundColor: percentages.map(function(value) {
                    if (value >= 90) return 'rgba(40, 167, 69, 0.7)'; // Excellent
                    if (value >= 75) return 'rgba(23, 162, 184, 0.7)'; // Good
                    if (value >= 50) return 'rgba(255, 193, 7, 0.7)'; // Fair
                    if (value >= 25) return 'rgba(255, 128, 0, 0.7)'; // Poor
                    return 'rgba(220, 53, 69, 0.7)'; // Critical
                }),
                borderColor: percentages.map(function(value) {
                    if (value >= 90) return 'rgba(40, 167, 69, 1)'; // Excellent
                    if (value >= 75) return 'rgba(23, 162, 184, 1)'; // Good
                    if (value >= 50) return 'rgba(255, 193, 7, 1)'; // Fair
                    if (value >= 25) return 'rgba(255, 128, 0, 1)'; // Poor
                    return 'rgba(220, 53, 69, 1)'; // Critical
                }),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

// Initialize Google Map
function initMap(mapContainer) {
    // Get center coordinates from data attributes
    var lat = parseFloat(mapContainer.getAttribute('data-lat')) || 9.0820;
    var lng = parseFloat(mapContainer.getAttribute('data-lng')) || 8.6753;
    
    var map = new google.maps.Map(mapContainer, {
        center: { lat: lat, lng: lng },
        zoom: 6
    });
    
    // If there are locations to display
    if (mapContainer.hasAttribute('data-locations')) {
        var locations = JSON.parse(mapContainer.getAttribute('data-locations'));
        
        locations.forEach(function(location) {
            var marker = new google.maps.Marker({
                position: { lat: parseFloat(location.lat), lng: parseFloat(location.lng) },
                map: map,
                title: location.name,
                icon: location.icon || null
            });
            
            if (location.info) {
                var infoWindow = new google.maps.InfoWindow({
                    content: location.info
                });
                
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });
            }
        });
    }
    
    // If there are routes to display
    if (mapContainer.hasAttribute('data-routes')) {
        var routes = JSON.parse(mapContainer.getAttribute('data-routes'));
        
        routes.forEach(function(route) {
            var path = new google.maps.Polyline({
                path: [
                    { lat: parseFloat(route.start_lat), lng: parseFloat(route.start_lng) },
                    { lat: parseFloat(route.end_lat), lng: parseFloat(route.end_lng) }
                ],
                geodesic: true,
                strokeColor: route.color || '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            
            path.setMap(map);
        });
    }
}

// Handle dark mode toggle
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    
    // Save preference to localStorage
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
    } else {
        localStorage.setItem('darkMode', 'disabled');
    }
}

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'enabled') {
    document.body.classList.add('dark-mode');
}

// Function to confirm deletion
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item? This action cannot be undone.');
}

// Function to confirm action
function confirmAction(message) {
    return confirm(message || 'Are you sure you want to perform this action?');
}

// Function to format date
function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString();
}

// Function to format datetime
function formatDateTime(dateTimeString) {
    var date = new Date(dateTimeString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Function to format number
function formatNumber(number, decimals = 0) {
    return parseFloat(number).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}