<!-- File: military_logistics/components/sidebar.php -->
<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <?php if (isLoggedIn() && hasRole('field_unit')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/my_unit/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/my_unit/index.php">
                                <i class="fas fa-home me-2"></i>
                                My Unit
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- [UPDATED] Live Simulation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/tracking/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/tracking/simulation.php">
                            <i class="fas fa-crosshairs me-2"></i>
                            Live Simulation
                        </a>
                    </li>
                    
                    <!-- Assets Management -->
                    <?php if (!hasRole('field_unit')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/assets/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/assets/index.php">
                            <i class="fas fa-boxes me-2"></i>
                            Assets
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Requisitions Management -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/requisitions/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/requisitions/index.php">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Requisitions
                        </a>
                    </li>
                    
                    <!-- Shipments Management -->
                     <?php if (!hasRole('field_unit')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/shipments/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/shipments/index.php">
                            <i class="fas fa-truck me-2"></i>
                            Shipments
                        </a>
                    </li>
                    
                    <!-- Inventory Management -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/inventory/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/inventory/index.php">
                            <i class="fas fa-warehouse me-2"></i>
                            Inventory
                        </a>
                    </li>
                    
                    <!-- Locations Management -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/locations/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/locations/index.php">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Locations
                        </a>
                    </li>
                    
                    <!-- Units Management -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/units/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/units/index.php">
                            <i class="fas fa-users me-2"></i>
                            Units
                        </a>
                    </li>
                    
                    <!-- Forecasting -->
                    <?php if(isAnalyst() || isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/forecasting/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/forecasting/index.php">
                            <i class="fas fa-chart-line me-2"></i>
                            Forecasting
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Reports -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pages/reports/') !== false ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/reports/index.php">
                            <i class="fas fa-file-alt me-2"></i>
                            Reports
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- User Profile -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/profile.php">
                            <i class="fas fa-user me-2"></i>
                            Profile
                        </a>
                    </li>
                </ul>

                <?php if(isAdmin()): ?>
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Administration</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/admin/users.php">
                            <i class="fas fa-user-cog me-2"></i>
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/admin/settings.php">
                            <i class="fas fa-cog me-2"></i>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/pages/admin/logs.php">
                            <i class="fas fa-history me-2"></i>
                            Activity Logs
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </nav>

