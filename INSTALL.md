# Military Logistics System - Installation Guide

## System Requirements

- PHP 7.4+ (8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)
- PDO PHP extension
- GD PHP extension
- JSON PHP extension
- mbstring PHP extension

## Installation Steps

### 1. Database Setup

1. Create a new MySQL database:
   ```sql
   CREATE DATABASE military_logistics;
   ```

2. Import the database schema:
   ```bash
   mysql -u username -p military_logistics < database/military_logistics.sql
   ```

### 2. Configuration

1. Open `config/config.php` and update the database connection settings:
   ```php
   define('DB_HOST', 'localhost');     // Your database host
   define('DB_USER', 'root');          // Your database username
   define('DB_PASS', '');              // Your database password
   define('DB_NAME', 'military_logistics'); // Your database name
   ```

2. Update the URL_ROOT constant to match your server configuration:
   ```php
   define('URL_ROOT', 'http://localhost/military_logistics');
   ```

3. For production environments, set DEBUG_MODE to false:
   ```php
   define('DEBUG_MODE', false);
   ```

4. Update the Google Maps API key if you plan to use mapping features:
   ```php
   define('GOOGLE_MAPS_API_KEY', 'your_google_maps_api_key');
   ```

### 3. File Permissions

Ensure the following directories are writable by the web server:
```bash
chmod 755 logs
chmod 755 uploads
chmod 755 assets/images
```

### 4. Web Server Configuration

#### Apache

Create or update your .htaccess file:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /military_logistics/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
</IfModule>
```

#### Nginx

Add this to your server block:
```
location /military_logistics {
    try_files $uri $uri/ /military_logistics/index.php?url=$args;
}
```

### 5. Testing the Installation

1. Navigate to the URL you configured (e.g., http://localhost/military_logistics)
2. You should see the login page
3. Use the default login credentials:
   - Email: admin@military.com
   - Password: admin123

### 6. Security Recommendations

After successful installation:

1. Change the default admin password immediately
2. Update the ENCRYPTION_KEY in config.php:
   ```php
   define('ENCRYPTION_KEY', 'your_secure_random_key_here');
   ```
3. Set appropriate file permissions
4. Configure proper error logging
5. Set DEBUG_MODE to false in production

## Troubleshooting

### Database Connection Issues
- Verify your database credentials in config.php
- Ensure the MySQL server is running
- Check if the database exists and is accessible

### Missing Directory Errors
- Ensure all required directories exist and have proper permissions
- The system will attempt to create missing directories, but may fail if the web server doesn't have sufficient permissions

### Login Issues
- Clear browser cookies and cache
- Verify the users table exists in the database
- Check if the default admin user was created during database import

### File Upload Problems
- Check the permissions on the uploads directory
- Verify the PHP file upload settings in php.ini
- Ensure the maximum upload size is configured correctly

## Support

For additional help, please refer to the project documentation or contact the system administrator.