<?php
// File: military_logistics/pages/tracking/index.php
require_once '../../config/config.php';
require_once '../../helpers/session_helper.php';
requireLogin();
$page_title = 'Live Asset Tracking';
include_once '../../components/header.php';
include_once '../../components/sidebar.php';
?>
<style>
    /* Custom styles for the tracking page */
    #map {
        height: calc(100vh - 56px); /* Full viewport height minus navbar */
        width: 100%;
    }
    .tracking-sidebar {
        position: absolute;
        top: 66px;
        left: 10px;
        z-index: 10;
        background: rgba(255, 255, 255, 0.9);
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        max-height: calc(100vh - 86px);
        overflow-y: auto;
        width: 350px;
    }
    .tracking-sidebar h5 {
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    .asset-list-item {
        padding: 8px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s;
        border-bottom: 1px solid #eee;
    }
    .asset-list-item:hover {
        background-color: #f0f0f0;
    }
    .asset-list-item.active {
        background-color: #dbeaff;
    }
    .asset-list-item small {
        color: #666;
    }
    #alerts {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 p-0">
    <div class="tracking-sidebar">
        <h5><i class="fas fa-satellite-dish me-2"></i>Tracked Assets</h5>
        <div id="asset-list" class="list-group">
            <p class="text-center">Loading assets...</p>
        </div>

        <h5 class="mt-4"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Alerts</h5>
        <div id="alerts" class="list-group">
            <div class="list-group-item text-muted">No alerts.</div>
        </div>
    </div>
    <div id="map"></div>
</main>

<!-- This script block must be after the main content -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    let map;
    let assetMarkers = {}; // To store asset markers, keyed by asset_id
    let zonePolygons = {}; // To store zone polygons, keyed by zone_id
    const API_ENDPOINT = '<?php echo URL_ROOT; ?>/api/track.php';
    const FETCH_INTERVAL = 5000; // Fetch new data every 5 seconds

    // Icon paths for different asset types
    const ICONS = {
        'Vehicles': '<?php echo URL_ROOT; ?>/assets/images/truck-icon.png',
        'Medical': '<?php echo URL_ROOT; ?>/assets/images/medical-icon.png',
        'default': 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
    };
    
    // Colors for different zone types
    const ZONE_COLORS = {
        'danger': '#FF0000',
        'operational': '#0000FF',
        'restricted': '#FFA500',
        'staging': '#008000'
    };

    /**
     * Initialize the Google Map
     */
    function initMap() {
        // Centered on Nigeria
        const nigeria = { lat: 9.0820, lng: 8.6753 };
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: nigeria,
            mapTypeId: 'terrain'
        });

        // Initial fetch of data
        fetchTrackingData();
        // Set interval to fetch data periodically
        setInterval(fetchTrackingData, FETCH_INTERVAL);
    }

    /**
     * Fetch tracking data from our API
     */
    async function fetchTrackingData() {
        try {
            const response = await fetch(API_ENDPOINT);
            if (!response.ok) {
                console.error('Failed to fetch tracking data. Status:', response.status);
                return;
            }
            const data = await response.json();
            updateMap(data);
        } catch (error) {
            console.error('Error fetching or parsing tracking data:', error);
        }
    }

    /**
     * Update the map with new data from the API
     */
    function updateMap(data) {
        // Update asset list in the sidebar
        updateAssetList(data.assets);
        
        // Update zones (polygons)
        updateZones(data.zones);

        // Update assets (markers)
        updateAssetMarkers(data.assets);

        // Check for alerts
        checkAlerts(data.assets, data.zones);
    }

    /**
     * Update the list of assets in the sidebar
     */
    function updateAssetList(assets) {
        const assetListEl = document.getElementById('asset-list');
        assetListEl.innerHTML = ''; // Clear current list

        if (assets.length === 0) {
            assetListEl.innerHTML = '<p class="text-center text-muted">No trackable assets found for your unit.</p>';
            return;
        }

        assets.forEach(asset => {
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'asset-list-item list-group-item-action';
            item.dataset.assetId = asset.asset_id;
            item.innerHTML = `
                <strong>${asset.asset_name}</strong> (${asset.asset_code})<br>
                <small>Status: <span class="badge bg-info">${asset.status}</span> | Speed: ${asset.speed_kmh} km/h</small>
            `;
            item.onclick = (e) => {
                e.preventDefault();
                const marker = assetMarkers[asset.asset_id];
                if (marker) {
                    map.panTo(marker.getPosition());
                    map.setZoom(12);
                    marker.get("infoWindow").open(map, marker);
                }
            };
            assetListEl.appendChild(item);
        });
    }

    /**
     * Draw or update zone polygons on the map
     */
    function updateZones(zones) {
        zones.forEach(zone => {
            if (!zonePolygons[zone.id]) {
                // If polygon doesn't exist, create it
                const polygon = new google.maps.Polygon({
                    paths: zone.coordinates,
                    strokeColor: ZONE_COLORS[zone.type] || '#808080',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: ZONE_COLORS[zone.type] || '#808080',
                    fillOpacity: 0.2,
                    map: map
                });
                
                const infoWindow = new google.maps.InfoWindow({
                    content: `<h5>${zone.name}</h5><p>${zone.description}</p>`
                });

                polygon.addListener('click', (event) => {
                    infoWindow.setPosition(event.latLng);
                    infoWindow.open(map);
                });

                zonePolygons[zone.id] = polygon;
            }
        });
    }

    /**
     * Create or update asset markers on the map
     */
    function updateAssetMarkers(assets) {
        const activeAssetIds = new Set(assets.map(a => a.asset_id));

        // Remove markers for assets that are no longer in the data
        for (const assetId in assetMarkers) {
            if (!activeAssetIds.has(parseInt(assetId))) {
                assetMarkers[assetId].setMap(null);
                delete assetMarkers[assetId];
            }
        }

        assets.forEach(asset => {
            const position = new google.maps.LatLng(asset.latitude, asset.longitude);
            const contentString = `
                <h5>${asset.asset_name}</h5>
                <p>
                    <b>Status:</b> ${asset.status}<br>
                    <b>Speed:</b> ${asset.speed_kmh} km/h<br>
                    <b>Last Update:</b> ${asset.last_updated}
                </p>`;
            
            if (assetMarkers[asset.asset_id]) {
                // If marker exists, just update its position and info
                const marker = assetMarkers[asset.asset_id];
                marker.setPosition(position);
                marker.get("infoWindow").setContent(contentString);
            } else {
                // If marker doesn't exist, create it
                const infoWindow = new google.maps.InfoWindow({ content: contentString });
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: asset.asset_name,
                    icon: {
                        url: ICONS[asset.category_name] || ICONS.default,
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });
                marker.set("infoWindow", infoWindow);
                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
                assetMarkers[asset.asset_id] = marker;
            }
        });
    }

     /**
     * Check if any asset is inside a danger zone and update alerts
     */
    function checkAlerts(assets, zones) {
        const alertsEl = document.getElementById('alerts');
        alertsEl.innerHTML = '';
        let hasAlerts = false;

        const dangerZones = zones.filter(z => z.type === 'danger');
        if (dangerZones.length === 0) {
             alertsEl.innerHTML = '<div class="list-group-item text-muted">No danger zones defined.</div>';
             return;
        }

        assets.forEach(asset => {
            const assetPosition = new google.maps.LatLng(asset.latitude, asset.longitude);
            dangerZones.forEach(zone => {
                const polygon = zonePolygons[zone.id];
                if (polygon && google.maps.geometry.poly.containsLocation(assetPosition, polygon)) {
                    hasAlerts = true;
                    const alertItem = document.createElement('div');
                    alertItem.className = 'list-group-item list-group-item-danger';
                    alertItem.innerHTML = `<strong>ALERT:</strong> ${asset.asset_name} has entered the ${zone.name}!`;
                    alertsEl.appendChild(alertItem);
                }
            });
        });

        if (!hasAlerts) {
            alertsEl.innerHTML = '<div class="list-group-item text-muted">No active alerts.</div>';
        }
    }


    // Make initMap globally available for the Google Maps callback
    window.initMap = initMap;
});
</script>
<!-- The Google Maps script needs to be loaded with the API key and a callback -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap&libraries=geometry"></script>

<?php include_once '../../components/footer.php'; ?>
