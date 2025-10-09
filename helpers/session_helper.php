<?php
session_start();

// Flash message helper
// EXAMPLE - flash('register_success', 'You are now registered');
// DISPLAY IN VIEW - echo flash('register_success');
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            if(!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }

            if(!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }

            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif(empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

// Check if user is logged in
function isLoggedIn() {
    if(isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

// Check user role
function hasRole($role) {
    if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role) {
        return true;
    } else {
        return false;
    }
}

// Check if user is admin
function isAdmin() {
    return hasRole('admin');
}

// Check if user is logistician
function isLogistician() {
    return hasRole('logistician') || isAdmin();
}

// Check if user is commander
function isCommander() {
    return hasRole('commander') || isAdmin();
}

// Check if user is analyst
function isAnalyst() {
    return hasRole('analyst') || isAdmin() || isLogistician();
}

// Check if user belongs to a unit
function isInUnit($unit_id) {
    if(isset($_SESSION['user_unit_id']) && $_SESSION['user_unit_id'] == $unit_id) {
        return true;
    } else {
        return false;
    }
}

// Redirect if not logged in
function requireLogin() {
    if(!isLoggedIn()) {
        flash('login_message', 'Please log in to access this page', 'alert alert-danger');
        redirect('login.php');
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if(!isAdmin()) {
        flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
        redirect('index.php');
    }
}

// Redirect if not logistician
function requireLogistician() {
    requireLogin();
    if(!isLogistician()) {
        flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
        redirect('index.php');
    }
}

// Redirect if not commander
function requireCommander() {
    requireLogin();
    if(!isCommander()) {
        flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
        redirect('index.php');
    }
}

// Redirect if not analyst
function requireAnalyst() {
    requireLogin();
    if(!isAnalyst()) {
        flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
        redirect('index.php');
    }
}

// Simple redirect function
function redirect($page) {
    header('location: ' . URL_ROOT . '/' . $page);
    exit;
}

// Format date
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Format datetime
function formatDateTime($datetime, $format = 'M d, Y h:i A') {
    return date($format, strtotime($datetime));
}

// Format number
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals);
}

// Get status badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'available':
        case 'open':
        case 'fulfilled':
        case 'delivered':
        case 'approved':
            return 'bg-success';
        case 'in_transit':
        case 'in_progress':
        case 'submitted':
        case 'partially_fulfilled':
            return 'bg-primary';
        case 'maintenance':
        case 'restricted':
        case 'pending':
            return 'bg-warning';
        case 'expired':
        case 'closed':
        case 'cancelled':
        case 'out_of_service':
        case 'delayed':
            return 'bg-danger';
        case 'deployed':
            return 'bg-info';
        default:
            return 'bg-secondary';
    }
}

// Get priority badge class
function getPriorityBadgeClass($priority) {
    switch($priority) {
        case 'low':
            return 'bg-info';
        case 'medium':
            return 'bg-primary';
        case 'high':
            return 'bg-warning';
        case 'critical':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Get readiness status badge class
function getReadinessBadgeClass($status) {
    switch($status) {
        case 'excellent':
            return 'bg-success';
        case 'good':
            return 'bg-primary';
        case 'fair':
            return 'bg-warning';
        case 'poor':
        case 'critical':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Log activity
function logActivity($user_id, $action, $details = '') {
    // This could be implemented to log user activities to a database table
    // For now, just return true
    return true;
}

// Generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Clean input data
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if string starts with substring
function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

// Check if string ends with substring
function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

// Convert bytes to human readable format
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>