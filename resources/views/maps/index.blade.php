<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🗺️ Company Locations Map</title>
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
            padding: 30px;
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
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header p {
            margin: 15px 0 20px 0;
            color: #64748b;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .company-count {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            margin-top: 10px;
        }

        .company-count i {
            font-size: 1.1rem;
        }

        .map-container {
            height: calc(100vh - 140px);
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

        .control-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 320px;
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .control-panel h3 {
            margin: 0 0 20px 0;
            color: #2d3748;
            font-size: 1.4rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .control-panel h3 i {
            color: #667eea;
        }

        .control-panel p {
            margin: 0 0 20px 0;
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            margin: 8px 0;
            font-size: 0.9rem;
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

        .btn:active {
            transform: translateY(0);
        }

        .btn.secondary {
            background: linear-gradient(135deg, #64748b, #475569);
            box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
        }

        .btn.secondary:hover {
            box-shadow: 0 8px 25px rgba(100, 116, 139, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
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

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-item {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(203, 213, 225, 0.3);
        }

        .stat-item .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-item .label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .floating-action {
            position: absolute;
            bottom: 30px;
            left: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 20px;
            border-radius: 50px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
        }

        .quick-controls {
            display: flex;
            gap: 10px;
        }

        .quick-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .quick-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 1024px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .map-container {
                margin: 10px;
                height: calc(100vh - 180px);
            }
            
            .control-panel {
                max-width: 280px;
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .control-panel {
                position: relative;
                top: auto;
                right: auto;
                margin: 20px;
                max-width: none;
            }

            .map-container {
                height: calc(100vh - 300px);
                margin: 10px;
            }

            .floating-action {
                position: relative;
                bottom: auto;
                left: auto;
                margin: 20px;
                border-radius: 20px;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .company-count {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-map-marked-alt"></i> Company Locations Map</h1>
            <p>Discover and explore all company locations across the interactive map</p>
            <div class="company-count">
                <i class="fas fa-building"></i>
                {{ $companies->count() }} Companies Found
            </div>
        </div>
    </div>

    <div class="map-container">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Loading interactive map...</p>
        </div>

        <div class="control-panel">
            <h3><i class="fas fa-info-circle"></i> Map Information</h3>
            <p>Click on any marker to view detailed company information</p>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number">{{ $companies->count() }}</div>
                    <div class="label">Total Companies</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ $companies->count() }}</div>
                    <div class="label">With Locations</div>
                </div>
            </div>

            <button class="btn btn-success" onclick="centerMap()">
                <i class="fas fa-crosshairs"></i>
                Center All Locations
            </button>
            <button class="btn" onclick="toggleClustering()">
                <i class="fas fa-layer-group"></i>
                Toggle Clustering
            </button>
            <button class="btn secondary" onclick="zoomToDefault()">
                <i class="fas fa-home"></i>
                Reset View
            </button>
        </div>

        <div class="floating-action">
            <div class="quick-card" style="padding: 15px; text-align: center; background: rgba(255,255,255,0.9); border-radius: 15px; backdrop-filter: blur(10px);">
                <div style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                    <i class="fas fa-map-pin"></i> Quick Actions
                </div>
                <div class="quick-controls">
                    <button class="quick-btn" onclick="centerMap()" title="Center Map">
                        <i class="fas fa-crosshairs"></i>
                    </button>
                    <button class="quick-btn" onclick="toggleClustering()" title="Toggle Clustering">
                        <i class="fas fa-layer-group"></i>
                    </button>
                    <button class="quick-btn" onclick="zoomToDefault()" title="Reset View">
                        <i class="fas fa-home"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="map"></div>
    </div>

    <script>
        let map;
        let markers = [];
        let markerClusterer;
        let clusteringEnabled = true;

        // Company data from Laravel
        const companies = @json($companies);

        async function initMap() {
            try {
                // Initialize the map centered on the first company or default location
                const defaultLocation = companies.length > 0 && companies[0].address ?
                    {
                        lat: parseFloat(companies[0].address.latitude),
                        lng: parseFloat(companies[0].address.longitude)
                    } :
                    {
                        lat: 40.7128,
                        lng: -74.0060
                    }; // Default to New York

                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 10,
                    center: defaultLocation,
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
                            featureType: "administrative.locality",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "administrative.neighborhood",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "administrative.province",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "administrative.country",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "administrative.land_parcel",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "landscape",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "water",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "road",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "road",
                            elementType: "labels.text",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "road",
                            elementType: "labels.icon",
                            stylers: [{ visibility: "off" }]
                        }
                    ],
                    fullscreenControl: false,
                    streetViewControl: false,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.BOTTOM_CENTER
                    }
                });

                // Hide loading indicator
                document.getElementById('loading').style.display = 'none';

                // Add markers for each company
                addCompanyMarkers();

            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById('loading').innerHTML =
                    '<div class="spinner"></div><p>Error loading map. Please refresh the page.</p>';
            }
        }

        function addCompanyMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];

            companies.forEach(company => {
                if (company.address && company.address.latitude && company.address.longitude) {
                    const position = {
                        lat: parseFloat(company.address.latitude),
                        lng: parseFloat(company.address.longitude)
                    };

                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: company.name,
                        animation: google.maps.Animation.DROP
                    });

                    // Add a subtle animation on hover
                    marker.addListener('mouseover', () => {
                        marker.setAnimation(google.maps.Animation.BOUNCE);
                        setTimeout(() => {
                            marker.setAnimation(null);
                        }, 750);
                    });

                    // Create info window with enhanced styling
                    const infoWindow = new google.maps.InfoWindow({
                        content: createInfoWindowContent(company),
                        pixelOffset: new google.maps.Size(0, -10)
                    });

                    // Add click listener
                    marker.addListener('click', () => {
                        // Close any other open info windows
                        markers.forEach(m => {
                            if (m.infoWindow && m.infoWindow !== infoWindow) {
                                m.infoWindow.close();
                            }
                        });
                        marker.infoWindow = infoWindow;
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });

            // Initialize marker clustering
            if (clusteringEnabled) {
                markerClusterer = new MarkerClusterer(map, markers, {
                    maxZoom: 15,
                    gridSize: 50,
                    minimumClusterSize: 2
                });
            }
        }

        function createInfoWindowContent(company) {
            const address = company.address;
            return `
                <div style="padding: 20px; min-width: 250px; font-family: 'Inter', sans-serif;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">
                            <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%;"></div>
                            <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 600;">${company.name}</h3>
                        </div>
                        <div style="text-align: center; margin-bottom: 15px;">
                            <a href="/maps/company/${company.id}" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                                <i class="fas fa-map"></i> View Company Map
                            </a>
                        </div>
                    
                    ${address ? `
                        <div style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 8px; border-left: 4px solid #667eea;">
                            <p style="margin: 0; color: #374151; font-weight: 500; font-size: 0.9rem;">
                                <i class="fas fa-map-marker-alt" style="color: #667eea; margin-right: 8px;"></i>
                                <strong>Address:</strong>
                            </p>
                            <p style="margin: 5px 0 0 20px; color: #6b7280; line-height: 1.4;">
                                ${[address.address_line1, address.address_line2, `${address.city || ''} ${address.state || ''} ${address.postal_code || ''}`.trim(), address.country].filter(Boolean).join('<br>')}
                            </p>
                        </div>
                    ` : '<p style="color: #9ca3af;">No address information available</p>'}
                    
                    ${company.phone ? `<div style="margin: 8px 0; display: flex; align-items: center; gap: 8px;"><i class="fas fa-phone" style="color: #10b981;"></i><span style="color: #374151;">${company.phone}</span></div>` : ''}
                    ${company.email ? `<div style="margin: 8px 0; display: flex; align-items: center; gap: 8px;"><i class="fas fa-envelope" style="color: #3b82f6;"></i><span style="color: #374151;">${company.email}</span></div>` : ''}
                    ${company.website ? `<div style="margin: 8px 0; display: flex; align-items: center; gap: 8px;"><i class="fas fa-globe" style="color: #8b5cf6;"></i><a href="${company.website}" target="_blank" style="color: #667eea; text-decoration: none;">Visit Website</a></div>` : ''}
                    
                    ${company.description ? `
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-style: italic;">${company.description}</p>
                        </div>
                    ` : ''}
                </div>`;
        }

        function centerMap() {
            if (markers.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            markers.forEach(marker => bounds.extend(marker.getPosition()));
            map.fitBounds(bounds);
            
            // Add a slight padding
            setTimeout(() => {
                map.setZoom(Math.min(map.getZoom(), 12));
            }, 300);
        }

        function showAllCompanies() {
            centerMap();
        }

        function zoomToDefault() {
            const defaultLocation = companies.length > 0 && companies[0].address ?
                {
                    lat: parseFloat(companies[0].address.latitude),
                    lng: parseFloat(companies[0].address.longitude)
                } :
                {
                    lat: 40.7128,
                    lng: -74.0060
                };

            map.setCenter(defaultLocation);
            map.setZoom(10);
        }

        function toggleClustering() {
            clusteringEnabled = !clusteringEnabled;

            if (clusteringEnabled) {
                markerClusterer = new MarkerClusterer(map, markers, {
                    maxZoom: 15,
                    gridSize: 50,
                    minimumClusterSize: 2
                });
            } else {
                if (markerClusterer) {
                    markerClusterer.clearMarkers();
                }
                // Re-add individual markers to map
                markers.forEach(marker => marker.setMap(map));
            }
        }

        // Load Google Maps script
        function loadGoogleMaps() {
            const script = document.createElement('script');
            script.src =
                'https://maps.googleapis.com/maps/api/js?key=AIzaSyAnviR5bZwRYNdstAiky365nBxvVKswzzQ&callback=initMap&libraries=geometry';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        // Start loading the map when page loads
        document.addEventListener('DOMContentLoaded', loadGoogleMaps);
    </script>

    <script src="https://unpkg.com/@google/markerclustererplus/dist/markerclustererplus.min.js"></script>
</body>

</html>