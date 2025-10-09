# Military Logistics System

A comprehensive PHP-based system for managing military logistics, supply chain efficiency, and troop resource delivery.

## Features

- **End-to-End Supply Chain Visibility**: Track assets throughout the entire supply chain
- **Asset Tracking**: Monitor the location and status of all military assets
- **Demand Forecasting**: Predict future resource requirements using historical data
- **Just-in-Time Resupply**: Optimize delivery timing to reduce waste and ensure availability
- **Inventory Management**: Track stock levels, transactions, and asset lifecycle
- **Requisition Management**: Process and fulfill resource requests from military units
- **Shipment Tracking**: Monitor the movement of supplies between locations
- **Unit Management**: Track military units, their locations, and readiness status
- **Location Management**: Manage bases, depots, and field locations
- **User Management**: Role-based access control for different personnel
- **Reporting and Analytics**: Generate insights and reports on logistics operations

## Project Structure

```
military_logistics/
├── admin/
│   ├── assets/
│   ├── classes/
│   ├── components/
│   ├── config/
│   ├── includes/
│   └── pages/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── classes/
│   ├── Asset.php
│   ├── Database.php
│   ├── DemandForecast.php
│   ├── Inventory.php
│   ├── Location.php
│   ├── Requisition.php
│   ├── Shipment.php
│   ├── Unit.php
│   └── User.php
├── components/
│   ├── footer.php
│   ├── header.php
│   └── sidebar.php
├── config/
│   └── config.php
├── database/
│   └── military_logistics.sql
├── helpers/
│   └── session_helper.php
├── pages/
│   ├── admin/
│   ├── assets/
│   ├── forecasting/
│   ├── inventory/
│   ├── locations/
│   ├── profile.php
│   ├── reports/
│   ├── requisitions/
│   ├── shipments/
│   └── units/
├── index.php
├── login.php
├── logout.php
└── README.md
```

## Installation

1. Install Xampp (or any other PHP development environment)
2. Clone this repository to your htdocs folder
3. Import the database schema from `database/military_logistics.sql`
4. Configure the database connection in `config/config.php`
5. Access the application through your browser at `http://localhost/military_logistics/`

## Default Login Credentials

- Email: admin@military.com
- Password: admin123

## User Roles

The system supports multiple user roles with different permissions:

1. **Admin**: Full access to all features and settings
2. **Logistician**: Manages inventory, shipments, and requisitions
3. **Commander**: Oversees unit operations and approves requisitions
4. **Field Unit**: Submits requisitions and tracks shipments
5. **Analyst**: Generates reports and forecasts

## Core Modules

### Asset Management

The Asset Management module allows tracking of all military assets including:

- Weapons and ammunition
- Vehicles and equipment
- Medical supplies
- Food and water
- Clothing and gear
- Fuel and other consumables

### Requisition Management

The Requisition Management module handles the request and approval process for resources:

- Create and submit requisitions
- Review and approve/reject requisitions
- Track requisition status
- Fulfill requisitions through inventory transactions

### Shipment Management

The Shipment Management module tracks the movement of supplies between locations:

- Create and plan shipments
- Track shipment status and location
- Record departures and arrivals
- Manage shipment items and quantities

### Inventory Management

The Inventory Management module maintains accurate stock levels:

- Record inventory transactions (receipts, issues, transfers)
- Track inventory levels by location
- Monitor low stock items
- Handle expiring items

### Location Management

The Location Management module manages all physical locations:

- Headquarters and bases
- Warehouses and depots
- Field locations
- Geographic coordinates and mapping

### Unit Management

The Unit Management module tracks military units:

- Unit information and personnel
- Unit location and movements
- Unit readiness status
- Resource requirements

### Forecasting

The Forecasting module predicts future resource requirements:

- Analyze historical consumption patterns
- Generate demand forecasts
- Calculate reorder points
- Optimize inventory levels

### Reporting

The Reporting module provides insights and analytics:

- Inventory reports
- Requisition status reports
- Shipment tracking reports
- Unit readiness reports
- Custom report generation

## Technical Details

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Libraries**: Chart.js, Flatpickr, Select2
- **Authentication**: Session-based with role-based access control
- **Security**: Password hashing, input sanitization, prepared statements

## Development

This system was developed using a hybrid methodology combining traditional SDLC with Agile principles, focusing on:

- Modular design for easy maintenance and extension
- Responsive UI for use on various devices
- Secure coding practices
- Performance optimization
- User-centered design

## License

This project is licensed under the MIT License.

## Credits

Developed by the NinjaTech AI team.