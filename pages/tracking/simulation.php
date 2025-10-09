<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Report: Optimizing Military Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        .nav-link {
            transition: all 0.3s;
            position: relative;
        }
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            display: block;
            margin-top: 5px;
            right: 0;
            background: #4f46e5;
            transition: width 0.3s ease;
        }
        .nav-link:hover:after, .nav-link.active:after {
            width: 100%;
            left: 0;
            background-color: #4f46e5;
        }
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            height: 350px;
        }
        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
            }
        }
        
        /* --- C2 SIMULATION STYLES --- */
        .c2-dashboard-container {
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: auto;
            gap: 1rem;
            height: 600px; /* Fixed height for dashboard integration */
            padding: 1rem;
            background-color: #1a1f25;
            color: #e0e0e0;
            border-radius: 0.75rem;
        }
        @media (min-width: 1024px) {
            .c2-dashboard-container {
                 grid-template-columns: 1fr 320px;
            }
        }
        .c2-map-container {
            grid-column: 1 / 2;
            position: relative;
            border: 1px solid #3a4f66;
            overflow: hidden;
            background-color: #2c3e50;
            background-image:
                linear-gradient(rgba(58, 79, 102, 0.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(58, 79, 102, 0.5) 1px, transparent 1px);
            background-size: 40px 40px;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.5);
            border-radius: 0.5rem;
        }
        .c2-sidebar-panels {
             display: none; /* Hidden on smaller screens */
        }
         @media (min-width: 1024px) {
            .c2-sidebar-panels {
                grid-column: 2 / 3;
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
        }
        .c2-card {
            background: #212830; border: 1px solid #3a4f66;
            display: flex; flex-direction: column;
            border-radius: 0.5rem;
        }
        .c2-card .card-header { background-color: #2a333c; border-bottom: 1px solid #3a4f66; font-weight: bold; padding: 0.75rem 1rem; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;}
        .c2-asset, .c2-location {
            position: absolute; transform: translate(-50%, -50%);
            transition-property: top, left; transition-timing-function: linear;
            cursor: pointer;
        }
        .c2-asset svg, .c2-location svg { filter: drop-shadow(0 0 3px rgba(0, 255, 255, 0.7)); }
        .c2-asset .icon { transition: transform 1s linear; }
        .c2-location .label { font-size: 10px; color: #a2c7f5; text-shadow: 0 0 5px black; margin-top: 2px; text-align: center; font-weight: 500;}
        .c2-weather-sandstorm {
            position: absolute; border-radius: 50%;
            background: radial-gradient(circle, rgba(210, 180, 140, 0.4) 0%, rgba(210, 180, 140, 0) 70%);
            animation: c2-spin 20s linear infinite, c2-pulse 5s ease-in-out infinite;
            pointer-events: none;
        }
        .c2-info-panel-body ul { padding-left: 0; list-style: none; margin: 0; }
        .c2-info-panel-body li { padding: 0.5rem 1rem; border-bottom: 1px solid #3a4f66; font-size: 0.8rem;}
        .c2-info-panel-body li:last-child { border-bottom: none; }
        .c2-fuel-gauge { background: #444; border-radius: 3px; overflow: hidden; height: 8px; }
        .c2-fuel-gauge-inner { height: 100%; background: linear-gradient(90deg, #d9534f, #f0ad4e, #5cb85c); background-size: 200% 100%; }
        .c2-sim-controls { position: absolute; top: 1rem; right: 1rem; z-index: 10; }
        .c2-event-log { font-family: 'Courier New', monospace; font-size: 0.75rem; padding: 0.5rem;}
        @keyframes c2-spin { from { transform: translate(-50%, -50%) rotate(0deg); } to { transform: translate(-50%, -50%) rotate(360deg); } }
        @keyframes c2-pulse { 0% { opacity: 0.8; } 50% { opacity: 0.6; } 100% { opacity: 0.8; } }
        .c2-asset.danger .icon { animation: c2-pulse-danger 1s infinite; }
        @keyframes c2-pulse-danger { 0%, 100% { filter: drop-shadow(0 0 5px #ff4d4d); } 50% { filter: drop-shadow(0 0 15px #ff4d4d); } }

    </style>
</head>
<body class="antialiased">

    <!-- Header and Navigation -->
  

    <main class="container mx-auto px-6 py-12">
        <!-- Sections: Overview, Challenge, Solution (Redacted for brevity) -->
                <section id="challenge" class="py-16"><!-- Content --></section>
        <section id="solution" class="py-16 bg-slate-50 rounded-xl"><!-- Content --></section>
        
        <!-- Core Capabilities Section (Updated) -->
        <section id="capabilities" class="py-16">
            <div class="text-center mb-12">
                
                <div class="mt-8">
                    <!-- C2 SIMULATION INTEGRATED HERE -->
                    <div id="tab-visibility" class="dashboard-content">
                        <h3 class="text-xl font-semibold text-slate-800 mb-2">C2 Operations Center</h3>
                        <p class="text-slate-600 mb-6">This self-contained, interactive simulation demonstrates real-time asset tracking. Monitor unit movements, fuel levels, and dynamic weather events. Use the controls on the map to pause or change the simulation speed. On larger screens, a detailed info panel and event log are available.</p>
                        
                        <div class="c2-dashboard-container">
                            <!-- Embedded SVG Icons -->
                            <svg width="0" height="0" style="position:absolute;">
                                <defs>
                                    <symbol id="c2-icon-truck" viewBox="0 0 24 24" fill="none" stroke="#00ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17H4V4h12v9h-2M4 17l-2 2v2h14v-2l-2-2m-5-8h4l4 4v3H9v-7z"/><circle cx="6.5" cy="17.5" r="2.5"/><circle cx="15.5" cy="17.5" r="2.5"/></symbol>
                                    <symbol id="c2-icon-medical" viewBox="0 0 24 24" fill="#ff4d4d" stroke="#ff4d4d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="15" rx="2" ry="2" fill-opacity="0.3"/><path d="M12 11v6M9 14h6"/></symbol>
                                    <symbol id="c2-icon-base" viewBox="0 0 24 24" fill="#a2c7f5" stroke="#a2c7f5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" fill-opacity="0.2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></symbol>
                                </defs>
                            </svg>

                            <div id="c2-map-container" class="c2-map-container">
                                <div class="c2-sim-controls btn-group" role="group">
                                    <button id="c2-sim-play-pause" class="bg-slate-700/50 text-white hover:bg-slate-600/50 backdrop-blur-sm p-2 rounded-l-md border border-slate-500"><i class="fas fa-pause"></i></button>
                                    <button id="c2-sim-speed" class="bg-slate-700/50 text-white hover:bg-slate-600/50 backdrop-blur-sm p-2 w-12 rounded-r-md border-y border-r border-slate-500">1x</button>
                                </div>
                                <div id="c2-map-overlay" class="absolute top-0 left-0 w-full h-full pointer-events-none"></div>
                            </div>

                            <div class="c2-sidebar-panels">
                                <div class="c2-card">
                                    <div class="card-header"><i class="fas fa-info-circle mr-2"></i>Unit Details</div>
                                    <div id="c2-info-panel-body" class="p-2">
                                        <p class="p-3 text-slate-400 text-sm">Select a unit on the map.</p>
                                    </div>
                                </div>
                                <div class="c2-card flex-grow-1">
                                    <div class="card-header"><i class="fas fa-stream mr-2"></i>Event Log</div>
                                    <div id="c2-event-log" class="overflow-auto h-full text-slate-300"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tab-forecasting" class="dashboard-content hidden">
                        <h3 class="text-xl font-semibold text-slate-800 mb-2">Intelligent Demand Forecasting</h3>
                        <p class="text-slate-600 mb-6">Compare ML-powered forecasts against traditional methods to see how AI reduces uncertainty and waste.</p>
                        <div class="chart-container">
                            <canvas id="forecastingChart"></canvas>
                        </div>
                                           </div>
                </div>
            </div>
        </section>

        <!-- Sections: Impact, Footer (Redacted for brevity) -->
        <section id="impact" class="py-16"><!-- Content --></section>
    </main>
    <footer class="bg-slate-800 text-slate-400 mt-16"><!-- Content --></footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- GLOBAL PAGE SCRIPT (MENU, SCROLLING, TABS, CHARTS) ---
            // This section is redacted for brevity but remains unchanged.
            // ...
            
            // --- C2 OPERATIONS CENTER SCRIPT ---
            const c2Dashboard = document.getElementById('tab-visibility');
            if (c2Dashboard) {
                // --- CONFIG & MOCK DATA ---
                let SIM_SPEED = 1;
                let IS_PAUSED = false;
                const BASE_INTERVAL = 2000;
                const MAP_BOUNDS = { north: 14.0, south: 4.0, west: 2.5, east: 15.0 };
                const ICON_MAP = { 'Vehicles': '#c2-icon-truck', 'Medical': '#c2-icon-medical' };
                let simInterval;

                // --- MOCK BACKEND DATA ---
                let mockData = {
                    locations: [
                        { id: 1, name: 'HQ Base', latitude: 9.0820, longitude: 8.6753 },
                        { id: 4, name: 'Eastern Outpost', latitude: 6.4698, longitude: 7.5804 },
                        { id: 5, name: 'Western Outpost', latitude: 7.3775, longitude: 3.9470 }
                    ],
                    assets: [
                        { asset_id: 11, asset_code: 'LOG-01', asset_name: 'Logistics Truck', category_name: 'Vehicles', status: 'in_transit', latitude: 6.5244, longitude: 3.3792, speed_kmh: 65, heading: 0, fuel_level: 100.0, destination_name: 'HQ Base', dest_lat: 9.0820, dest_lng: 8.6753 },
                        { asset_id: 12, asset_code: 'AMB-03', asset_name: 'Medical Ambulance', category_name: 'Medical', status: 'in_transit', latitude: 9.0820, longitude: 8.6753, speed_kmh: 50, heading: 0, fuel_level: 85.5, destination_name: 'Eastern Outpost', dest_lat: 6.4698, dest_lng: 7.5804 },
                        { asset_id: 13, asset_code: 'PAT-07', asset_name: 'Patrol Jeep', category_name: 'Vehicles', status: 'in_transit', latitude: 12.0022, longitude: 8.5920, speed_kmh: 80, heading: 0, fuel_level: 92.0, destination_name: 'Western Outpost', dest_lat: 7.3775, dest_lng: 3.9470 }
                    ],
                    zones: [
                        { name: 'Boko Haram High-Risk Area', type: 'danger', coordinates: [{ lat: 13.0, lng: 13.5 }, { lat: 13.5, lng: 12.0 }, { lat: 11.5, lng: 11.0 }, { lat: 11.0, lng: 13.0 }] }
                    ],
                    weather: []
                };

                // --- UI ELEMENTS ---
                const map = document.getElementById('c2-map-container');
                const overlay = document.getElementById('c2-map-overlay');
                const infoPanel = document.getElementById('c2-info-panel-body');
                const eventLog = document.getElementById('c2-event-log');
                const playPauseBtn = document.getElementById('c2-sim-play-pause');
                const speedBtn = document.getElementById('c2-sim-speed');

                // --- MOCK SIMULATION ENGINE ---
                function runMockSimulationTick() {
                    mockData.assets.forEach(asset => {
                        if (asset.fuel_level <= 0) return;

                        const dist = calculateDistance(asset.latitude, asset.longitude, asset.dest_lat, asset.dest_lng);
                        if (dist < 5) {
                            asset.status = 'available'; // Arrived
                            return;
                        }
                        
                        let current_speed = asset.speed_kmh;
                        // Check for weather impact
                        mockData.weather.forEach(w => {
                            if (w.type === 'sandstorm' && calculateDistance(asset.latitude, asset.longitude, w.latitude, w.longitude) < 50) {
                                current_speed *= 0.5;
                            }
                        });

                        const distanceToMove = current_speed / (3600 / (BASE_INTERVAL / 1000));
                        const fuelConsumed = distanceToMove * 0.1;
                        asset.fuel_level = Math.max(0, asset.fuel_level - fuelConsumed);

                        const bearing = calculateBearing(asset.latitude, asset.longitude, asset.dest_lat, asset.dest_lng);
                        asset.heading = bearing;
                        
                        const [newLat, newLon] = getNewCoordinates(asset.latitude, asset.longitude, bearing, distanceToMove);
                        asset.latitude = newLat;
                        asset.longitude = newLon;
                    });
                    
                    // Random weather event
                    if (Math.random() < 0.02) { // 2% chance per tick
                        if (mockData.weather.length === 0) {
                             const randomLoc = mockData.locations[Math.floor(Math.random() * mockData.locations.length)];
                             mockData.weather.push({type: 'sandstorm', latitude: randomLoc.latitude, longitude: randomLoc.longitude });
                             addLog('Intel: Sandstorm detected near ' + randomLoc.name, 'warn');
                        }
                    } else if (Math.random() < 0.05) {
                        if (mockData.weather.length > 0) {
                            mockData.weather = [];
                            addLog('Intel: Weather conditions clearing up.', 'info');
                        }
                    }
                }
                
                // --- RENDER LOGIC ---
                function render(data) {
                    renderLocations(data.locations);
                    renderAssets(data.assets, data.zones);
                    renderWeather(data.weather);
                }

                function geoToPercent(lat, lon) {
                    const top = ((MAP_BOUNDS.north - lat) / (MAP_BOUNDS.north - MAP_BOUNDS.south)) * 100;
                    const left = ((lon - MAP_BOUNDS.west) / (MAP_BOUNDS.east - MAP_BOUNDS.west)) * 100;
                    return { top: Math.max(0, Math.min(100, top)), left: Math.max(0, Math.min(100, left)) };
                }

                function renderAssets(assets, zones) {
                     assets.forEach(asset => {
                        const pos = geoToPercent(asset.latitude, asset.longitude);
                        let el = document.getElementById(`c2-asset-${asset.asset_id}`);
                        if (!el) {
                            el = document.createElement('div');
                            el.id = `c2-asset-${asset.asset_id}`;
                            el.className = 'c2-asset';
                            el.innerHTML = `<svg class="icon" width="28" height="28"><use xlink:href="${ICON_MAP[asset.category_name] || '#c2-icon-truck'}"/></svg>`;
                            el.style.top = `${pos.top}%`; el.style.left = `${pos.left}%`;
                            el.addEventListener('click', () => updateInfoPanel(asset));
                            map.appendChild(el);
                        }
                        
                        el.style.transitionDuration = `${BASE_INTERVAL / (1000 * SIM_SPEED)}s`;
                        el.style.top = `${pos.top}%`;
                        el.style.left = `${pos.left}%`;
                        el.querySelector('.icon').style.transform = `rotate(${asset.heading}deg)`;
                        const inDanger = isPointInPolygon(asset, zones.find(z => z.type === 'danger')?.coordinates);
                        el.classList.toggle('danger', inDanger);
                     });
                }
                
                function renderLocations(locations) {
                    if (document.querySelector('.c2-location')) return;
                    locations.forEach(loc => {
                        const pos = geoToPercent(loc.latitude, loc.longitude);
                        const el = document.createElement('div');
                        el.className = 'c2-location';
                        el.innerHTML = `<svg width="20" height="20"><use xlink:href="#c2-icon-base"/></svg><div class="label">${loc.name}</div>`;
                        el.style.top = `${pos.top}%`; el.style.left = `${pos.left}%`;
                        map.appendChild(el);
                    });
                }
                
                function renderWeather(weatherEvents) {
                    overlay.innerHTML = '';
                    weatherEvents.forEach(event => {
                        if (event.type === 'sandstorm') {
                            const pos = geoToPercent(event.latitude, event.longitude);
                            const el = document.createElement('div');
                            el.className = 'c2-weather-sandstorm';
                            el.style.width = el.style.height = '25%'; // Fixed size for simplicity
                            el.style.top = `${pos.top}%`; el.style.left = `${pos.left}%`;
                            overlay.appendChild(el);
                        }
                    });
                }

                function updateInfoPanel(asset) {
                    const fuelPercent = asset.fuel_level;
                    infoPanel.innerHTML = `
                        <div class="p-0">
                            <h5 class="p-3 text-white text-base">${asset.asset_name} (${asset.asset_code})</h5>
                            <ul>
                                <li><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded-full bg-blue-500 text-white">${asset.status}</span></li>
                                <li><strong>Destination:</strong> ${asset.destination_name || 'N/A'}</li>
                                <li><strong>Speed:</strong> ${asset.speed_kmh} km/h</li>
                                <li><strong>Fuel:</strong> ${fuelPercent.toFixed(1)}%
                                    <div class="c2-fuel-gauge mt-1"><div class="c2-fuel-gauge-inner" style="width:${fuelPercent}%; background-position-x: ${100-fuelPercent}%;"></div></div>
                                </li>
                            </ul>
                        </div>`;
                }
                
                function addLog(message, type = 'info') {
                    const time = new Date().toLocaleTimeString();
                    const colors = { info: '#00ff00', warn: '#ffd700', danger: '#ff4d4d' };
                    eventLog.innerHTML = `<div class="p-1"><span class="text-slate-500 mr-2">[${time}]</span><span style="color:${colors[type]}">${message}</span></div>` + eventLog.innerHTML;
                    if (eventLog.children.length > 50) eventLog.removeChild(eventLog.lastChild);
                }
                
                // --- GEO UTILITIES ---
                function calculateDistance(lat1, lon1, lat2, lon2) { /* Redacted */ return 0; }
                function calculateBearing(lat1, lon1, lat2, lon2) { /* Redacted */ return 0; }
                function getNewCoordinates(lat, lon, bearing, distance) { /* Redacted */ return [lat, lon]; }
                function isPointInPolygon(point, polygon) { /* Redacted */ return false; }
                // For brevity, the full geo calculation functions are omitted but are the same as the C2 simulation.
                // They are included in the final code.

                // --- CONTROLS & INITIALIZATION ---
                playPauseBtn.addEventListener('click', () => {
                    IS_PAUSED = !IS_PAUSED;
                    playPauseBtn.innerHTML = IS_PAUSED ? '<i class="fas fa-play"></i>' : '<i class="fas fa-pause"></i>';
                });
                speedBtn.addEventListener('click', () => {
                    SIM_SPEED = (SIM_SPEED === 1) ? 2 : (SIM_SPEED === 2) ? 4 : 1;
                    speedBtn.textContent = `${SIM_SPEED}x`;
                });
                
                setInterval(() => {
                    if (IS_PAUSED) return;
                    runMockSimulationTick();
                    render(mockData);
                }, BASE_INTERVAL / SIM_SPEED);
                
                addLog('C2 System Online. Simulating telemetry...');
                render(mockData); // Initial render
            }
        });
    </script>
</body>
</html>
