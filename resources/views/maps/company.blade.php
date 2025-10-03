<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏢 {{ $company->name }} - Company Map</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            color: #2d3748;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;


            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .header-content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .back-link {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 16px;
            border-radius: 25px;
            text-decoration: none;
            color: #667eea;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-50%) translateY(-2px);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            margin: 10px 0 15px 0;
            color: #64748b;
            font-size: 1rem;
            font-weight: 400;
        }

        .company-summary {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .summary-item {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            border-radius: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .summary-item.hq {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .summary-item.warehouses {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .summary-item.dumpsters {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .summary-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 0.85rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .map-container {
            height: calc(100vh - 200px);
            width: 100%;
            position: relative;
            margin: 20px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            background: white;
        }

        #map {
            height: 100%;
            width: 100%;
            border-radius: 20px;
        }

        .legend {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .legend h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #4b5563;
        }

        .legend-icon {
            width: 16px;
            height: 16px;
            border-radius: 3px;
        }

        .controls {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .controls h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            margin: 8px 0;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn.secondary {
            background: linear-gradient(135deg, #64748b, #475569);
            box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
        }

        .btn.secondary:hover {
            box-shadow: 0 8px 25px rgba(100, 116, 139, 0.4);
        }

        .btn.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn.warning:hover {
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #64748b;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .loading p {
            margin-top: 15px;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .spinner {
            border: 4px solid rgba(102, 126, 234, 0.1);
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 1024px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .map-container {
                height: calc(100vh - 220px);
                margin: 10px;
            }
            
            .legend, .controls {
                padding: 15px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }
            
            .back-link {
                position: static;
                transform: none;
                margin: 0 auto 20px auto;
                width: fit-content;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .company-summary {
                flex-direction: column;
                align-items: center;
            }
            
            .legend, .controls {
                position: relative;
                margin: 10px;
                max-width: none;
            }

            .map-container {
                height: calc(100vh - 400px);
                margin: 10px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.5rem;
            }
            
            .summary-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <a href="{{ route('maps') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to All Companies
        </a>
        
        <div class="header-content">
            <h1><i class="fas fa-building"></i> {{ $company->name }}</h1>
            <p>{{ $company->description ?? 'Company Infrastructure Overview' }}</p>
            
            <div class="company-summary">
                <div class="summary-item hq">
                    <div class="summary-number">HQ</div>
                    <div class="summary-label">Headquarters</div>
                </div>
                <div class="summary-item warehouses">
                    <div class="summary-number">{{ $company->warehouses->count() }}</div>
                    <div class="summary-label">Warehouses</div>
                </div>
                <div class="summary-item dumpsters">
                    <div class="summary-number">{{ $company->dumpsters->count() }}</div>
                    <div class="summary-label">Active Dumpsters</div>
                </div>
            </div>
        </div>
    </div>

    <div class="map-container">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Loading {{ $company->name }} map...</p>
        </div>

        <div class="legend">
            <h3><i class="fas fa-map-marked-alt"></i> Legend</h3>
            <div class="legend-item">
                <div class="legend-icon" style="background: linear-gradient(135deg, #10b981, #059669);"></div>
                <span>Company HQ</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);"></div>
                <span>Warehouses</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);"></div>
                <span>Dumpster Locations</span>
            </div>
        </div>

        <div class="controls">
            <h3><i class="fas fa-cogs"></i> Controls</h3>
            <button class="btn" onclick="centerAllLocations()">
                <i class="fas fa-crosshairs"></i>
                Center All
            </button>
            <button class="btn secondary" onclick="showWarehousesOnly()">
                <i class="fas fa-warehouse"></i>
                Show Warehouses
            </button>
            <button class="btn warning" onclick="showDumpstersOnly()">
                <i class="fas fa-dumpster"></i>
                Show Dumpsters
            </button>
            <button class="btn" onclick="showAllMarkers()">
                <i class="fas fa-eye"></i>
                Show All
            </button>
        </div>

        <div id="map"></div>
    </div>

    <script>
        let map;
        let allMarkers = [];
        let companyMarkers = [];
        let warehouseMarkers = [];
        let dumpsterMarkers = [];

        // Company data from Laravel
        const company = @json($company);

        async function initMap() {
            try {
                // Initialize the map centered on company HQ or first warehouse
                let centerLocation;
                
                if (company.address && company.address.latitude && company.address.longitude) {
                    centerLocation = {
                        lat: parseFloat(company.address.latitude),
                        lng: parseFloat(company.address.longitude)
                    };
                } else if (company.warehouses && company.warehouses.length > 0 && company.warehouses[0].address) {
                    centerLocation = {
                        lat: parseFloat(company.warehouses[0].address.latitude),
                        lng: parseFloat(company.warehouses[0].address.longitude)
                    };
                } else {
                    centerLocation = { lat: 40.7128, lng: -74.0060 }; // Default to New York
                }

                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 12,
                    center: centerLocation,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [
                        {
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "poi",
                            elementType: "geometry",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "transit",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "transit",
                            elementType: "geometry",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "administrative",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "road",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        }
                    ],
                    fullscreenControl: false,
                    streetViewControl: false
                });

                // Hide loading indicator
                document.getElementById('loading').style.display = 'none';

                // Add all markers
                addAllMarkers();

            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById('loading').innerHTML =
                    '<div class="spinner"></div><p>Error loading map. Please refresh the page.</p>';
            }
        }

        function addAllMarkers() {
            // Add company HQ marker
            if (company.address && company.address.latitude && company.address.longitude) {
                addCompanyMarker();
            }

            // Add warehouse markers
            company.warehouses.forEach(warehouse => {
                if (warehouse.address && warehouse.address.latitude && warehouse.address.longitude) {
                    addWarehouseMarker(warehouse);
                }
            });

            // Add dumpster markers
            company.dumpsters.forEach(dumpster => {
                if (dumpster.latest_location && dumpster.latest_location.latitude && dumpster.latest_location.longitude) {
                    addDumpsterMarker(dumpster);
                }
            });
        }

        function addCompanyMarker() {
            const position = {
                lat: parseFloat(company.address.latitude),
                lng: parseFloat(company.address.longitude)
            };

            const markerIcon = {
                url: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                        <defs>
                            <linearGradient id="hqGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <circle cx="20" cy="20" r="18" fill="url(#hqGradient)" stroke="#ffffff" stroke-width="3"/>
                        <text x="20" y="26" text-anchor="middle" fill="white" font-family="serif" font-size="16" font-weight="bold">HQ</text>
                    </svg>
                `),
                scaledSize: new google.maps.Size(40, 40),
                anchor: new google.maps.Point(20, 20)
            };

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: company.name + ' - Headquarters',
                icon: markerIcon,
                animation: google.maps.Animation.DROP
            });

            const infoWindow = new google.maps.InfoWindow({
                content: createCompanyInfoContent(company)
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });

            companyMarkers.push(marker);
            allMarkers.push(marker);
        }

        function addWarehouseMarker(warehouse) {
            const position = {
                lat: parseFloat(warehouse.address.latitude),
                lng: parseFloat(warehouse.address.longitude)
            };

            const markerIcon = {
                url: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35">
                        <defs>
                            <linearGradient id="warehouseGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#1d4ed8;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <rect x="5" y="8" width="25" height="25" fill="url(#warehouseGradient)" stroke="#ffffff" stroke-width="2" rx="2"/>
                        <rect x="8" y="12" width="4" height="4" fill="#ffffff" rx="0.5"/>
                        <rect x="14" y="12" width="4" height="4" fill="#ffffff" rx="0.5"/>
                        <rect x="20" y="12" width="4" height="4" fill="#ffffff" rx="0.5"/>
                        <rect x="8" y="18" width="4" height="4" fill="#ffffff" rx="0.5"/>
                        <rect x="14" y="18" width="4" height="4" fill="#ffffff" rx="0.5"/>
                        <rect x="20" y="18" width="4" height="4" fill="#ffffff" rx="0.5"/>
                    </svg>
                `),
                scaledSize: new google.maps.Size(35, 35),
                anchor: new google.maps.Point(17.5, 32)
            };

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: warehouse.name + ' Warehouse',
                icon: markerIcon,
                animation: google.maps.Animation.DROP
            });

            const infoWindow = new google.maps.InfoWindow({
                content: createWarehouseInfoContent(warehouse)
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });

            warehouseMarkers.push(marker);
            allMarkers.push(marker);
        }

        function addDumpsterMarker(dumpster) {
            const position = {
                lat: parseFloat(dumpster.latest_location.latitude),
                lng: parseFloat(dumpster.latest_location.longitude)
            };

            const markerIcon = {
                url: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                        <defs>
                            <linearGradient id="dumpsterGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#d97706;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <rect x="3" y="8" width="24" height="16" fill="url(#dumpsterGradient)" stroke="#ffffff" stroke-width="2" rx="2"/>
                        <rect x="5" y="10" width="3" height="2" fill="#ffffff" rx="0.5"/>
                        <rect x="10" y="10" width="3" height="2" fill="#ffffff" rx="0.5"/>
                        <rect x="15" y="10" width="3" height="2" fill="#ffffff" rx="0.5"/>
                        <rect x="20" y="10" width="3" height="2" fill="#ffffff" rx="0.5"/>
                        <rect x="12" y="3" width="6" height="5" fill="url(#dumpsterGradient)" stroke="#ffffff" stroke-width="1" rx="1"/>
                    </svg>
                `),
                scaledSize: new google.maps.Size(30, 30),
                anchor: new google.maps.Point(15, 27)
            };

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: dumpster.name || 'Dumpster #' + dumpster.serial_number,
                icon: markerIcon,
                animation: google.maps.Animation.DROP
            });

            const infoWindow = new google.maps.InfoWindow({
                content: createDumpsterInfoContent(dumpster)
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });

            dumpsterMarkers.push(marker);
            allMarkers.push(marker);
        }

        function createCompanyInfoContent(company) {
            return `
                <div style="padding: 20px; min-width: 250px; font-family: 'Inter', sans-serif;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">
                        <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%;"></div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 600;">
                            <i class="fas fa-building"></i> ${company.name}
                        </h3>
                    </div>
                    <div style="background: #10b981; color: white; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px;">
                        <strong>HEADQUARTERS</strong>
                    </div>
                    ${company.address ? `
                        <div style="margin-bottom: 15px;">
                            <p style="margin: 0; color: #6b7280; font-weight: 500;">
                                <i class="fas fa-map-marker-alt" style="color: #10b981; margin-right: 8px;"></i>
                                <strong>Address:</strong>
                            </p>
                            <p style="margin: 5px 0 0 20px; color: #374151; line-height: 1.4;">
                                ${[company.address.address_line1, company.address.address_line2, 
                                   \`\${company.address.city || ''} \${company.address.state || ''} \${company.address.postal_code || ''}\`.trim(), 
                                   company.address.country].filter(Boolean).join('<br>')}
                            </p>
                        </div>
                    ` : ''}
                    ${company.phone ? `<div style="margin: 8px 0;"><strong>Phone:</strong> ${company.phone}</div>` : ''}
                    ${company.email ? `<div style="margin: 8px 0;"><strong>Email:</strong> ${company.email}</div>` : ''}
                </div>`;
        }

        function createWarehouseInfoContent(warehouse) {
            return `
                <div style="padding: 20px; min-width: 250px; font-family: 'Inter', sans-serif;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">
                        <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%;"></div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 600;">${warehouse.name}</h3>
                    </div>
                    <div style="background: #3b82f6; color: white; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px;">
                        <strong>WAREHOUSE</strong>
                    </div>
                    ${warehouse.code ? `<p style="margin: 5px 0;"><strong>Code:</strong> ${warehouse.code}</p>` : ''}
                    ${warehouse.type ? `<p style="margin: 5px 0;"><strong>Type:</strong> ${warehouse.type}</p>` : ''}
                    ${warehouse.capacity ? `<p style="margin: 5px 0;"><strong>Capacity:</strong> ${warehouse.capacity}</p>` : ''}
                    ${warehouse.address ? `
                        <p style="margin: 10px 0 5px 0; color: #6b7280; font-weight: 500;">
                            <i class="fas fa-map-marker-alt" style="color: #3b82f6; margin-right: 8px;"></i>Address:
                        </p>
                        <p style="margin-left: 20px; color: #374151;">
                            ${[warehouse.address.address_line1, warehouse.address.address_line2, 
                               \`\${warehouse.address.city || ''} \${warehouse.address.state || ''} \${warehouse.address.postal_code || ''}\`.trim()].filter(Boolean).join('<br>')}
                        </p>
                    ` : ''}
                </div>`;
        }

        function createDumpsterInfoContent(dumpster) {
            const lastUpdate = dumpster.latest_location?.recorded_at ? 
                new Date(dumpster.latest_location.recorded_at).toLocaleString() : 'Unknown';
                
            return `
                <div style="padding: 20px; min-width: 250px; font-family: 'Inter', sans-serif;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">
                        <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%;"></div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 600;">${dumpster.name || 'Dumpster'}</h3>
                    </div>
                    <div style="background: #f59e0b; color: white; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px;">
                        <strong>ACTIVE DUMPSTER</strong>
                    </div>
                    ${dumpster.serial_number ? `<p style="margin: 5px 0;"><strong>Serial:</strong> ${dumpster.serial_number}</p>` : ''}
                    ${dumpster.size ? `<p style="margin: 5px 0;"><strong>Size:</strong> ${dumpster.size.name || dumpster.size.capacity}</p>` : ''}
                    ${dumpster.status ? `<p style="margin: 5px 0;"><strong>Status:</strong> ${dumpster.status}</p>` : ''}
                    <div style="background: #f9fafb; padding: 10px; border-radius: 8px; margin-top: 10px;">
                        <p style="margin: 0; color: #6b7280; font-size: 0.9rem;"><strong>Last Location Update:</strong><br>${lastUpdate}</p>
                    </div>
                </div>`;
        }

        function centerAllLocations() {
            if (allMarkers.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            allMarkers.forEach(marker => bounds.extend(marker.getPosition()));
            map.fitBounds(bounds);
        }

        function showWarehousesOnly() {
            allMarkers.forEach(marker => marker.setMap(null));
            warehouseMarkers.forEach(marker => marker.setMap(map));
            if (warehouseMarkers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                warehouseMarkers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }

        function showDumpstersOnly() {
            allMarkers.forEach(marker => marker.setMap(null));
            dumpsterMarkers.forEach(marker => marker.setMap(map));
            if (dumpsterMarkers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                dumpsterMarkers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }

        function showAllMarkers() {
            allMarkers.forEach(marker => marker.setMap(map));
        }

        // Load Google Maps script
        function loadGoogleMaps() {
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAnviR5bZwRYNdstAiky365nBxvVKswzzQ&callback=initMap&libraries=geometry';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        // Start loading the map when page loads
        document.addEventListener('DOMContentLoaded', loadGoogleMaps);
    </script>
</body>

</html>
