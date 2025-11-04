@extends('layouts.navigation')

@section('title', 'Turn-by-Turn Navigation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-4">
            <h1 class="text-2xl font-bold">🧭 Turn-by-Turn Navigation</h1>
            <p class="text-blue-100">Hệ thống dẫn đường thông minh sử dụng Mapbox Directions API</p>
        </div>

        <!-- Controls Panel -->
        <div class="p-4 bg-gray-50 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📍 Điểm xuất phát</label>
                    <input type="text" id="start-input" placeholder="Nhập địa chỉ xuất phát" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🎯 Điểm đến cuối</label>
                    <input type="text" id="end-input" placeholder="Nhập địa chỉ đích đến cuối" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Waypoints Section -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">🗺️ Điểm dừng trung gian (tối đa 23 điểm)</label>
                    <button id="add-waypoint" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        ➕ Thêm điểm dừng
                    </button>
                </div>
                <div id="waypoints-container" class="space-y-2">
                    <!-- Waypoints will be added here -->
                </div>
            </div>
            
            <!-- Route Options -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🚗 Phương tiện</label>
                    <select id="profile-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="mapbox/driving-traffic">Ô tô (có giao thông)</option>
                        <option value="mapbox/driving">Ô tô (nhanh nhất)</option>
                        <option value="mapbox/walking">Đi bộ</option>
                        <option value="mapbox/cycling">Xe đạp</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🎯 Tối ưu hóa</label>
                    <select id="optimization-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="false">Theo thứ tự nhập</option>
                        <option value="true">Tự động tối ưu</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="get-directions" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        🚗 Tìm đường
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row">
            <!-- Map Container -->
            <div class="lg:w-2/3">
                <div id="map" class="h-96 lg:h-[600px] bg-gray-200"></div>
            </div>

            <!-- Navigation Panel -->
            <div class="lg:w-1/3 bg-white border-l">
                <!-- Route Info -->
                <div id="route-info" class="p-4 bg-blue-50 border-b hidden">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Tổng quãng đường:</span>
                        <span id="total-distance" class="font-semibold text-blue-600">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Thời gian dự kiến:</span>
                        <span id="total-duration" class="font-semibold text-blue-600">-</span>
                    </div>
                </div>

                <!-- Current Instruction -->
                <div id="current-instruction" class="p-4 bg-yellow-50 border-b hidden">
                    <div class="flex items-center space-x-3">
                        <div id="instruction-icon" class="text-2xl">🚗</div>
                        <div class="flex-1">
                            <div id="instruction-text" class="font-medium text-gray-800">Sẵn sàng bắt đầu</div>
                            <div id="instruction-distance" class="text-sm text-gray-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div id="simulation-controls" class="p-4 border-b hidden">
                    <div class="flex space-x-2">
                        <button id="start-simulation" class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                            ▶️ Bắt đầu
                        </button>
                        <button id="pause-simulation" class="flex-1 bg-yellow-600 text-white px-3 py-2 rounded text-sm hover:bg-yellow-700">
                            ⏸️ Tạm dừng
                        </button>
                        <button id="stop-simulation" class="flex-1 bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                            ⏹️ Dừng
                        </button>
                    </div>
                </div>

                <!-- Instructions List -->
                <div id="instructions-container" class="hidden">
                    <div class="p-4 bg-gray-50 border-b">
                        <h3 class="font-semibold text-gray-800">📋 Hướng dẫn từng bước</h3>
                        <p class="text-sm text-gray-600">Nhập điểm xuất phát và đích đến để bắt đầu</p>
                    </div>
                    <div id="instructions-list" class="max-h-96 overflow-y-auto">
                        <!-- Instructions will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.instruction-item {
    transition: all 0.3s ease;
}
.instruction-item.active {
    background-color: #fef3c7;
    border-left: 4px solid #f59e0b;
}
.instruction-item.completed {
    background-color: #d1fae5;
    border-left: 4px solid #10b981;
    opacity: 0.7;
}

/* Hide Mapbox Directions control */
.mapboxgl-ctrl-directions {
    display: none;
}
</style>
@endsection

@section('scripts')
<script>
// Mapbox Access Token - Sử dụng token demo công khai
mapboxgl.accessToken = 'pk.eyJ1IjoibmhhdG5ndXllbnF2IiwiYSI6ImNtYjZydDNnZDAwY24ybm9qcTdxcTNocG8ifQ.u7X_0DfN7d52xZ8cGFbWyQ';

// Fallback to OpenStreetMap if Mapbox fails
const useOpenStreetMap = true;

class TurnByTurnNavigation {
    constructor() {
        this.map = null;
        this.directions = null;
        this.currentRoute = null;
        this.currentInstructionIndex = 0;
        this.isSimulating = false;
        this.simulationInterval = null;
        this.markers = [];
        this.waypoints = [];
        this.waypointCounter = 0;
        
        this.init();
    }

    init() {
        this.initMap();
        this.bindEvents();
    }

    initMap() {
        try {
            this.map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [105.8342, 21.0285], // Hanoi coordinates
                zoom: 13
            });

            // Add navigation controls
            this.map.addControl(new mapboxgl.NavigationControl());
            
            this.map.on('load', () => {
                console.log('Map loaded successfully!');
            });
            
            console.log('Map initialized successfully');
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    bindEvents() {
        const getDirectionsBtn = document.getElementById('get-directions');
        if (getDirectionsBtn) {
            getDirectionsBtn.addEventListener('click', () => {
                this.getDirections();
            });
        }

        const addWaypointBtn = document.getElementById('add-waypoint');
        if (addWaypointBtn) {
            addWaypointBtn.addEventListener('click', () => {
                this.addWaypoint();
            });
        }

        const startBtn = document.getElementById('start-simulation');
        if (startBtn) {
            startBtn.addEventListener('click', () => {
                this.startSimulation();
            });
        }

        const pauseBtn = document.getElementById('pause-simulation');
        if (pauseBtn) {
            pauseBtn.addEventListener('click', () => {
                this.pauseSimulation();
            });
        }

        const stopBtn = document.getElementById('stop-simulation');
        if (stopBtn) {
            stopBtn.addEventListener('click', () => {
                this.stopSimulation();
            });
        }

        // Enter key support
        ['start-input', 'end-input'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.getDirections();
                    }
                });
            }
        });
    }

    addWaypoint() {
        if (this.waypoints.length >= 23) {
            alert('Tối đa 23 điểm dừng trung gian!');
            return;
        }

        this.waypointCounter++;
        const waypointId = `waypoint-${this.waypointCounter}`;
        
        const waypointDiv = document.createElement('div');
        waypointDiv.className = 'flex gap-2 items-center';
        waypointDiv.innerHTML = `
            <div class="flex-1">
                <input type="text" id="${waypointId}" placeholder="Điểm dừng ${this.waypointCounter}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button onclick="navigation.removeWaypoint('${waypointId}')" 
                    class="text-red-600 hover:text-red-800 px-2 py-2 rounded">
                ❌
            </button>
        `;
        
        document.getElementById('waypoints-container').appendChild(waypointDiv);
        this.waypoints.push(waypointId);
    }

    removeWaypoint(waypointId) {
        const element = document.getElementById(waypointId);
        if (element) {
            element.parentElement.parentElement.remove();
            this.waypoints = this.waypoints.filter(id => id !== waypointId);
        }
    }

    async getDirections() {
        const startInput = document.getElementById('start-input').value;
        const endInput = document.getElementById('end-input').value;
        const profileSelect = document.getElementById('profile-select');
        const optimizationSelect = document.getElementById('optimization-select');

        if (!startInput || !endInput) {
            alert('Vui lòng nhập đầy đủ điểm xuất phát và đích đến!');
            return;
        }

        try {
            // Geocoding để lấy tọa độ
            const startCoords = await this.geocodeAddress(startInput);
            const endCoords = await this.geocodeAddress(endInput);

            if (!startCoords || !endCoords) {
                alert('Không thể tìm thấy địa chỉ. Vui lòng thử lại!');
                return;
            }

            // Collect waypoint coordinates
            const waypointCoords = [];
            for (const waypointId of this.waypoints) {
                const waypointInput = document.getElementById(waypointId);
                if (waypointInput?.value) {
                    const coords = await this.geocodeAddress(waypointInput.value);
                    if (coords) {
                        waypointCoords.push(coords);
                    }
                }
            }

            // Build coordinates string for API
            let coordinates = `${startCoords[0]},${startCoords[1]}`;
            waypointCoords.forEach(coords => {
                coordinates += `;${coords[0]},${coords[1]}`;
            });
            coordinates += `;${endCoords[0]},${endCoords[1]}`;

            const profile = profileSelect?.value || 'mapbox/driving-traffic';
            const optimize = optimizationSelect?.value === 'true';

            let directionsUrl;
            
            if (optimize) {
                if (waypointCoords.length > 0) {
                    // Use Optimization API for route optimization with waypoints
                    directionsUrl = `https://api.mapbox.com/optimized-trips/v1/${profile}/${coordinates}?steps=true&geometries=geojson&source=first&destination=last&access_token=${mapboxgl.accessToken}`;
                } else {
                    // Use Directions API with optimization parameters for start-end only
                    directionsUrl = `https://api.mapbox.com/directions/v5/${profile}/${coordinates}?steps=true&geometries=geojson&alternatives=true&access_token=${mapboxgl.accessToken}`;
                }
            } else {
                // Use regular Directions API
                directionsUrl = `https://api.mapbox.com/directions/v5/${profile}/${coordinates}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`;
            }
            
            const response = await fetch(directionsUrl);
            const data = await response.json();

            if (data.routes && data.routes.length > 0) {
                // If optimization is enabled and we have multiple routes, show route options
                if (optimize && data.routes.length > 1) {
                    this.displayRouteOptions(data.routes);
                } else {
                    this.currentRoute = data.routes[0];
                    this.displaySelectedRoute();
                }
            } else {
                alert('Không thể tìm thấy tuyến đường!');
            }
            
        } catch (error) {
            console.error('Error getting directions:', error);
            alert('Có lỗi xảy ra khi tìm đường. Vui lòng thử lại!');
        }
    }



    async geocodeAddress(address) {
        try {
            const response = await fetch(
                `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${mapboxgl.accessToken}&country=VN&limit=1`
            );
            const data = await response.json();
            
            if (data.features && data.features.length > 0) {
                return data.features[0].center;
            }
            return null;
        } catch (error) {
            console.error('Geocoding error:', error);
            return null;
        }
    }

    handleRouteResponse(route) {
        this.currentRoute = route;
        
        // Hiển thị thông tin route
        this.showRouteInfo(route);
        
        // Hiển thị hướng dẫn từng bước
        this.showInstructions(route.legs[0].steps);
        
        // Hiển thị controls mô phỏng
        this.showSimulationControls();
    }

    showRouteInfo(route) {
        const routeInfo = document.getElementById('route-info');
        const totalDistance = document.getElementById('total-distance');
        const totalDuration = document.getElementById('total-duration');

        if (routeInfo && totalDistance && totalDuration) {
            totalDistance.textContent = `${(route.distance / 1000).toFixed(1)} km`;
            totalDuration.textContent = `${Math.round(route.duration / 60)} phút`;
            routeInfo.classList.remove('hidden');
        }
    }

    showInstructions(steps) {
        const container = document.getElementById('instructions-container');
        const list = document.getElementById('instructions-list');
        
        if (!container || !list) return;

        list.innerHTML = '';
        
        // Collect all steps from all legs
        let allSteps = [];
        if (this.currentRoute && this.currentRoute.legs) {
            this.currentRoute.legs.forEach(leg => {
                allSteps = allSteps.concat(leg.steps);
            });
        } else {
            allSteps = steps;
        }
        
        allSteps.forEach((step, index) => {
            const item = document.createElement('div');
            item.className = 'instruction-item p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50';
            item.setAttribute('data-index', index);
            
            const instruction = step.maneuver.instruction || 'Tiếp tục đi thẳng';
            const distance = step.distance > 0 ? `${Math.round(step.distance)}m` : '';
            
            item.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="text-lg">${this.getInstructionIcon(step.maneuver.type, step.maneuver.modifier)}</div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">${instruction}</div>
                        <div class="text-sm text-gray-600">${distance}</div>
                    </div>
                </div>
            `;
            
            item.addEventListener('click', () => {
                this.currentInstructionIndex = index;
                this.highlightCurrentInstruction(index);
                this.focusOnStep(step);
            });
            
            list.appendChild(item);
        });
        
        container.classList.remove('hidden');
    }

    focusOnStep(step) {
        if (!step.geometry || !step.geometry.coordinates) return;
        
        const coordinates = step.geometry.coordinates;
        if (coordinates.length > 0) {
            const bounds = new mapboxgl.LngLatBounds();
            coordinates.forEach(coord => bounds.extend(coord));
            this.map.fitBounds(bounds, { padding: 50 });
        }
    }

    displayRoute(geometry) {
        // Clear existing markers
        this.clearMarkers();
        
        // Add route to map
        if (this.map.getSource('route')) {
            this.map.removeLayer('route');
            this.map.removeSource('route');
        }
        
        this.map.addSource('route', {
            type: 'geojson',
            data: {
                type: 'Feature',
                properties: {},
                geometry: geometry
            }
        });
        
        this.map.addLayer({
            id: 'route',
            type: 'line',
            source: 'route',
            layout: {
                'line-join': 'round',
                'line-cap': 'round'
            },
            paint: {
                'line-color': '#3b82f6',
                'line-width': 5,
                'line-opacity': 0.75
            }
        });
        
        // Add markers for start, waypoints, and end
        const coordinates = geometry.coordinates;
        const start = coordinates[0];
        const end = coordinates[coordinates.length - 1];

        this.addMarker(start, '🚩', 'Điểm xuất phát');
        
        // Add waypoint markers based on current route legs
        if (this.currentRoute && this.currentRoute.legs) {
            let waypointIndex = 1;
            
            for (let i = 0; i < this.currentRoute.legs.length - 1; i++) {
                // Use the last coordinate of the current leg
                const leg = this.currentRoute.legs[i];
                if (leg.steps && leg.steps.length > 0) {
                    const lastStep = leg.steps[leg.steps.length - 1];
                    if (lastStep.maneuver && lastStep.maneuver.location) {
                        this.addMarker(lastStep.maneuver.location, `${waypointIndex}`, `Điểm dừng ${waypointIndex}`);
                        waypointIndex++;
                    }
                }
            }
        }
        
        this.addMarker(end, '🏁', 'Điểm đến');
        
        // Fit map to route bounds
        const bounds = new mapboxgl.LngLatBounds();
        coordinates.forEach(coord => bounds.extend(coord));
        this.map.fitBounds(bounds, { padding: 50 });
    }

    displayInstructions(steps) {
        const instructionsContainer = document.getElementById('instructions-container');
        const instructionsList = document.getElementById('instructions-list');
        if (!instructionsContainer || !instructionsList || !steps) return;
        
        instructionsList.innerHTML = '';
        this.instructions = [];
        
        steps.forEach((step, index) => {
            const instruction = {
                text: step.maneuver.instruction || step.name || 'Tiếp tục đi thẳng',
                distance: step.distance || 0,
                duration: step.duration || 0,
                coordinates: step.maneuver.location || [0, 0]
            };
            
            this.instructions.push(instruction);
            
            const instructionElement = document.createElement('div');
            instructionElement.className = 'instruction-item p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50';
            instructionElement.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="text-lg">${this.getInstructionIcon(step.maneuver.type, step.maneuver.modifier)}</div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">${instruction.text}</div>
                        <div class="text-sm text-gray-600">${this.formatDistance(instruction.distance)}</div>
                    </div>
                </div>
            `;
            
            instructionElement.addEventListener('click', () => {
                this.highlightInstruction(index);
                this.map.flyTo({
                    center: instruction.coordinates,
                    zoom: 16
                });
            });
            
            instructionsList.appendChild(instructionElement);
        });
        
        instructionsContainer.classList.remove('hidden');
    }

    addMarker(coordinates, emoji, title) {
        const marker = new mapboxgl.Marker({
            element: this.createMarkerElement(emoji)
        })
        .setLngLat(coordinates)
        .setPopup(new mapboxgl.Popup().setHTML(`<div class="font-medium">${title}</div>`))
        .addTo(this.map);
        
        this.markers.push(marker);
    }

    createMarkerElement(emoji) {
        const el = document.createElement('div');
        el.className = 'marker';
        el.style.cssText = `
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        `;
        el.textContent = emoji;
        return el;
    }

    clearMarkers() {
        this.markers.forEach(marker => marker.remove());
        this.markers = [];
    }

    displayRouteSummary(route) {
        let totalDistance = 0;
        let totalDuration = 0;
        
        // Handle Mapbox format
        if (route.legs) {
            route.legs.forEach(leg => {
                totalDistance += leg.distance || 0;
                totalDuration += leg.duration || 0;
            });
        } else if (route.distance && route.duration) {
            // Direct route properties
            totalDistance = route.distance;
            totalDuration = route.duration;
        }
        
        const distance = (totalDistance / 1000).toFixed(1);
        const duration = Math.round(totalDuration / 60);
        
        let summaryHtml = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <h4 class="font-semibold text-blue-800 mb-2">📊 Tóm tắt tuyến đường</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Khoảng cách:</span>
                        <span class="font-medium">${distance} km</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Thời gian:</span>
                        <span class="font-medium">${duration} phút</span>
                    </div>
                </div>
        `;
        
        if (route.legs && route.legs.length > 1) {
            summaryHtml += `
                <div class="mt-2">
                    <span class="text-gray-600">Số điểm dừng:</span>
                    <span class="font-medium">${route.legs.length - 1} điểm</span>
                </div>
            `;
        }
        
        summaryHtml += `</div>`;
        
        const instructionsContainer = document.getElementById('instructions-container');
        if (instructionsContainer) {
            const existingSummary = instructionsContainer.querySelector('.bg-blue-50');
            if (existingSummary) {
                existingSummary.remove();
            }
            instructionsContainer.insertAdjacentHTML('afterbegin', summaryHtml);
        }
    }

    showSimulationControls() {
        const controls = document.getElementById('simulation-controls');
        if (controls) {
            controls.classList.remove('hidden');
        }
    }

    getInstructionIcon(type, modifier) {
        // Mapbox instruction type mapping
        const iconMap = {
            'depart': '🚩',
            'turn': modifier === 'left' ? '⬅️' : modifier === 'right' ? '➡️' : '⬆️',
            'new name': '⬆️',
            'continue': '⬆️',
            'merge': '🔀',
            'on ramp': '↗️',
            'off ramp': '↘️',
            'fork': '🔱',
            'end of road': modifier === 'left' ? '⬅️' : '➡️',
            'use lane': '⬆️',
            'arrive': '🏁',
            'roundabout': '🔄',
            'rotary': '🔄',
            'roundabout turn': '🔄',
            'notification': 'ℹ️',
            'exit roundabout': '↗️',
            'exit rotary': '↗️'
        };
        
        if (type === 'turn' && modifier) {
            if (modifier === 'sharp left') return '↖️';
            if (modifier === 'slight left') return '↖️';
            if (modifier === 'sharp right') return '↗️';
            if (modifier === 'slight right') return '↗️';
            if (modifier === 'uturn') return '🔄';
        }
        
        return iconMap[type] || '➡️';
    }
    
    formatDistance(distance) {
        if (distance < 1000) {
            return `${Math.round(distance)} m`;
        } else {
            return `${(distance / 1000).toFixed(1)} km`;
        }
    }

    startSimulation() {
        if (!this.currentRoute || this.isSimulating) return;
        
        this.isSimulating = true;
        this.currentInstructionIndex = 0;
        
        const steps = this.currentRoute.legs[0].steps;
        
        this.simulationInterval = setInterval(() => {
            if (this.currentInstructionIndex < steps.length) {
                this.updateCurrentInstruction(steps[this.currentInstructionIndex]);
                this.highlightCurrentInstruction(this.currentInstructionIndex);
                this.currentInstructionIndex++;
            } else {
                this.stopSimulation();
                alert('Đã đến đích!');
            }
        }, 3000); // 3 seconds per step
    }

    pauseSimulation() {
        if (this.simulationInterval) {
            clearInterval(this.simulationInterval);
            this.simulationInterval = null;
            this.isSimulating = false;
        }
    }

    stopSimulation() {
        this.pauseSimulation();
        this.currentInstructionIndex = 0;
        
        // Reset UI
        const currentInstruction = document.getElementById('current-instruction');
        if (currentInstruction) {
            currentInstruction.classList.add('hidden');
        }
        
        // Reset instruction highlights
        document.querySelectorAll('.instruction-item').forEach(item => {
            item.classList.remove('active', 'completed');
        });
    }

    updateCurrentInstruction(step) {
        const currentInstruction = document.getElementById('current-instruction');
        const instructionIcon = document.getElementById('instruction-icon');
        const instructionText = document.getElementById('instruction-text');
        const instructionDistance = document.getElementById('instruction-distance');
        
        if (!currentInstruction || !instructionIcon || !instructionText || !instructionDistance) return;

        const instruction = step.maneuver.instruction || 'Tiếp tục đi thẳng';
        const distance = step.distance > 0 ? `Còn ${Math.round(step.distance)}m` : '';

        instructionIcon.textContent = this.getInstructionIcon(step.maneuver.type, step.maneuver.modifier);
        instructionText.textContent = instruction;
        instructionDistance.textContent = distance;

        currentInstruction.classList.remove('hidden');
    }

    highlightInstruction(index) {
        const items = document.querySelectorAll('.instruction-item');
        items.forEach((item, i) => {
            if (i === index) {
                item.classList.add('bg-blue-50', 'border-l-4', 'border-l-blue-500');
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.classList.remove('bg-blue-50', 'border-l-4', 'border-l-blue-500');
            }
        });
    }

    highlightCurrentInstruction(index) {
        this.highlightInstruction(index);
    }

    displayRouteOptions(routes) {
        // Create route options panel
        const routeInfo = document.getElementById('route-info');
        if (!routeInfo) return;

        routeInfo.innerHTML = `
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-4">🛣️ Chọn tuyến đường tối ưu</h3>
                <div class="space-y-3" id="route-options">
                    ${routes.map((route, index) => {
                        const totalDistance = route.distance ? (route.distance / 1000).toFixed(1) : 'N/A';
                        const totalDuration = route.duration ? Math.round(route.duration / 60) : 'N/A';
                        return `
                            <div class="route-option p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors" 
                                 data-route-index="${index}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-medium">Tuyến đường ${index + 1}</span>
                                        <div class="text-sm text-gray-600">
                                            📏 ${totalDistance} km • ⏱️ ${totalDuration} phút
                                        </div>
                                    </div>
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        Chọn
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;

        routeInfo.classList.remove('hidden');

        // Add click handlers for route selection
        document.querySelectorAll('.route-option').forEach((option, index) => {
            option.addEventListener('click', () => {
                this.currentRoute = routes[index];
                this.displaySelectedRoute();
            });
        });

        // Display all routes on map with different colors
        this.displayMultipleRoutes(routes);
    }

    displaySelectedRoute() {
        this.displayRoute(this.currentRoute.geometry);
        
        // Display instructions from legs
        let allSteps = [];
        if (this.currentRoute.legs) {
            this.currentRoute.legs.forEach(leg => {
                if (leg.steps) {
                    allSteps = allSteps.concat(leg.steps);
                }
            });
        }
        this.displayInstructions(allSteps);
        this.currentInstructionIndex = 0;
        
        // Show route summary
        this.displayRouteSummary(this.currentRoute);
        
        // Show simulation controls
        const simulationControls = document.getElementById('simulation-controls');
        if (simulationControls) {
            simulationControls.style.display = 'block';
        }
    }

    displayMultipleRoutes(routes) {
        // Clear existing routes
        if (this.map.getLayer('route')) {
            this.map.removeLayer('route');
        }
        if (this.map.getSource('route')) {
            this.map.removeSource('route');
        }

        // Colors for different routes
        const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6'];

        routes.forEach((route, index) => {
            const sourceId = `route-${index}`;
            const layerId = `route-layer-${index}`;
            
            this.map.addSource(sourceId, {
                type: 'geojson',
                data: {
                    type: 'Feature',
                    properties: {},
                    geometry: route.geometry
                }
            });

            this.map.addLayer({
                id: layerId,
                type: 'line',
                source: sourceId,
                layout: {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                paint: {
                    'line-color': colors[index % colors.length],
                    'line-width': index === 0 ? 6 : 4,
                    'line-opacity': index === 0 ? 0.8 : 0.6
                }
            });
        });

        // Fit map to show all routes
        const allCoordinates = [];
        routes.forEach(route => {
            allCoordinates.push(...route.geometry.coordinates);
        });
        
        const bounds = new mapboxgl.LngLatBounds();
        allCoordinates.forEach(coord => bounds.extend(coord));
        this.map.fitBounds(bounds, { padding: 50 });
    }

    formatDistance(distance) {
        if (distance < 1000) {
            return `${Math.round(distance)}m`;
        } else {
            return `${(distance / 1000).toFixed(1)}km`;
        }
    }
}

// Initialize when page loads
let navigation;
document.addEventListener('DOMContentLoaded', () => {
    navigation = new TurnByTurnNavigation();
});
</script>
@endsection