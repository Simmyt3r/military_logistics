// Military Logistics System - Admin JavaScript

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

    // Initialize admin dashboard charts if they exist
    if (document.getElementById('adminUsersChart')) {
        initAdminUsersChart();
    }
    
    if (document.getElementById('adminActivityChart')) {
        initAdminActivityChart();
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

    // Handle user search
    var userSearch = document.querySelector('#userSearch');
    if (userSearch) {
        userSearch.addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var userRows = document.querySelectorAll('.user-row');
            
            userRows.forEach(function(row) {
                var userName = row.querySelector('.user-name').textContent.toLowerCase();
                var userEmail = row.querySelector('.user-email').textContent.toLowerCase();
                var userRole = row.querySelector('.user-role').textContent.toLowerCase();
                
                if (userName.includes(searchValue) || userEmail.includes(searchValue) || userRole.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle log search
    var logSearch = document.querySelector('#logSearch');
    if (logSearch) {
        logSearch.addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var logRows = document.querySelectorAll('.log-row');
            
            logRows.forEach(function(row) {
                var logUser = row.querySelector('.log-user').textContent.toLowerCase();
                var logAction = row.querySelector('.log-action').textContent.toLowerCase();
                var logDate = row.querySelector('.log-date').textContent.toLowerCase();
                
                if (logUser.includes(searchValue) || logAction.includes(searchValue) || logDate.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle settings form
    var settingsForm = document.querySelector('#settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Show loading spinner
            var submitButton = this.querySelector('button[type="submit"]');
            var originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            submitButton.disabled = true;
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(function() {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                // Show success message
                var alertContainer = document.querySelector('.alert-container');
                alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    'Settings saved successfully!' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';
                
                // Auto-hide alert after 5 seconds
                setTimeout(function() {
                    var alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.classList.remove('show');
                        setTimeout(function() {
                            alertContainer.innerHTML = '';
                        }, 500);
                    }
                }, 5000);
            }, 1000);
        });
    }

    // Handle bulk actions
    var bulkActionSelect = document.querySelector('#bulkAction');
    var bulkActionButton = document.querySelector('#applyBulkAction');
    if (bulkActionSelect && bulkActionButton) {
        bulkActionButton.addEventListener('click', function() {
            var selectedAction = bulkActionSelect.value;
            if (!selectedAction) {
                return;
            }
            
            var selectedItems = document.querySelectorAll('input[name="bulkSelect"]:checked');
            if (selectedItems.length === 0) {
                alert('Please select at least one item.');
                return;
            }
            
            var itemIds = [];
            selectedItems.forEach(function(item) {
                itemIds.push(item.value);
            });
            
            if (confirm('Are you sure you want to ' + selectedAction + ' the selected items?')) {
                // Perform the bulk action (replace with actual implementation)
                console.log('Performing ' + selectedAction + ' on items:', itemIds);
                
                // Show success message
                var alertContainer = document.querySelector('.alert-container');
                alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    'Bulk action completed successfully!' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';
                
                // Auto-hide alert after 5 seconds
                setTimeout(function() {
                    var alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.classList.remove('show');
                        setTimeout(function() {
                            alertContainer.innerHTML = '';
                        }, 500);
                    }
                }, 5000);
            }
        });
    }

    // Handle select all checkbox
    var selectAllCheckbox = document.querySelector('#selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[name="bulkSelect"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }
});

// Initialize admin users chart
function initAdminUsersChart() {
    var ctx = document.getElementById('adminUsersChart').getContext('2d');
    
    // Get data from the data attributes
    var roles = JSON.parse(ctx.canvas.getAttribute('data-roles'));
    var counts = JSON.parse(ctx.canvas.getAttribute('data-counts'));
    
    var adminUsersChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: roles,
            datasets: [{
                label: 'Users by Role',
                data: counts,
                backgroundColor: [
                    'rgba(26, 60, 110, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(23, 162, 184, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(26, 60, 110, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
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

// Initialize admin activity chart
function initAdminActivityChart() {
    var ctx = document.getElementById('adminActivityChart').getContext('2d');
    
    // Get data from the data attributes
    var dates = JSON.parse(ctx.canvas.getAttribute('data-dates'));
    var counts = JSON.parse(ctx.canvas.getAttribute('data-counts'));
    
    var adminActivityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Activity by Date',
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

// Function to confirm user deletion
function confirmUserDelete(userId, userName) {
    return confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.');
}

// Function to reset user password
function resetUserPassword(userId, userName) {
    if (confirm('Are you sure you want to reset the password for user "' + userName + '"?')) {
        // Simulate password reset (replace with actual implementation)
        console.log('Resetting password for user:', userId);
        
        // Show success message
        var alertContainer = document.querySelector('.alert-container');
        alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            'Password reset successfully for user "' + userName + '"!' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
        
        // Auto-hide alert after 5 seconds
        setTimeout(function() {
            var alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(function() {
                    alertContainer.innerHTML = '';
                }, 500);
            }
        }, 5000);
    }
}

// Function to export data to CSV
function exportTableToCSV(tableId, filename) {
    var table = document.getElementById(tableId);
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
    link.setAttribute('download', filename + '_' + new Date().toISOString().slice(0, 10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Function to toggle maintenance mode
function toggleMaintenanceMode(isEnabled) {
    if (confirm('Are you sure you want to ' + (isEnabled ? 'enable' : 'disable') + ' maintenance mode?')) {
        // Simulate toggling maintenance mode (replace with actual implementation)
        console.log('Setting maintenance mode to:', isEnabled);
        
        // Show success message
        var alertContainer = document.querySelector('.alert-container');
        alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            'Maintenance mode ' + (isEnabled ? 'enabled' : 'disabled') + ' successfully!' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
        
        // Update the toggle button
        var toggleButton = document.querySelector('#maintenanceModeToggle');
        if (toggleButton) {
            toggleButton.innerHTML = isEnabled ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode';
            toggleButton.classList.toggle('btn-danger', isEnabled);
            toggleButton.classList.toggle('btn-success', !isEnabled);
            toggleButton.setAttribute('onclick', 'toggleMaintenanceMode(' + !isEnabled + ')');
        }
        
        // Auto-hide alert after 5 seconds
        setTimeout(function() {
            var alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(function() {
                    alertContainer.innerHTML = '';
                }, 500);
            }
        }, 5000);
    }
}

// Function to clear system logs
function clearSystemLogs() {
    if (confirm('Are you sure you want to clear all system logs? This action cannot be undone.')) {
        // Simulate clearing logs (replace with actual implementation)
        console.log('Clearing system logs');
        
        // Show success message
        var alertContainer = document.querySelector('.alert-container');
        alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            'System logs cleared successfully!' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
        
        // Clear the logs table
        var logsTable = document.querySelector('#logsTable tbody');
        if (logsTable) {
            logsTable.innerHTML = '<tr><td colspan="5" class="text-center">No logs found</td></tr>';
        }
        
        // Auto-hide alert after 5 seconds
        setTimeout(function() {
            var alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(function() {
                    alertContainer.innerHTML = '';
                }, 500);
            }
        }, 5000);
    }
}