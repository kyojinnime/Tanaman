<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Sensor - Monitoring Tanaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-seedling text-green-600 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">Monitoring Tanaman</span>
                </div>
                <div class="flex space-x-4 items-center">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('monitoring') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">
                        <i class="fas fa-chart-line mr-2"></i>Monitoring
                    </a>
                    <a href="{{ route('pompa') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-water mr-2"></i>Pompa
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-chart-line text-green-600 mr-2"></i>
                        Monitoring Sensor Real-time
                    </h1>
                    <p class="text-gray-600">Data kelembapan tanaman diperbarui otomatis setiap 3 detik</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-semibold text-green-600">LIVE</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Update terakhir: <span id="lastUpdateTime">-</span></p>
                </div>
            </div>
        </div>

        <!-- Main Sensor Display -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Moisture Percentage Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-xl p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold opacity-90">Kelembapan</h3>
                    <i class="fas fa-tint text-3xl opacity-50"></i>
                </div>
                <div class="text-center">
                    <p id="moisturePercent" class="text-6xl font-bold mb-2">{{ $latestData->moisture_percent ?? 0 }}%</p>
                    <div class="w-full bg-white bg-opacity-30 rounded-full h-3 mb-2">
                        <div id="moistureBar" class="bg-white h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $latestData->moisture_percent ?? 0 }}%"></div>
                    </div>
                    <p class="text-sm opacity-75">Persentase Kelembapan Tanah</p>
                </div>
            </div>

            <!-- Status Card -->
            <div id="statusCard" class="rounded-xl shadow-xl p-8 text-white
                @if($statusIndonesia == 'Basah') bg-gradient-to-br from-blue-600 to-blue-700
                @elseif($statusIndonesia == 'Kering') bg-gradient-to-br from-red-500 to-red-600
                @else bg-gradient-to-br from-green-500 to-green-600
                @endif">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold opacity-90">Status Tanah</h3>
                    <i id="statusIcon" class="text-3xl opacity-50
                        @if($statusIndonesia == 'Basah') fas fa-cloud-rain
                        @elseif($statusIndonesia == 'Kering') fas fa-sun
                        @else fas fa-check-circle
                        @endif"></i>
                </div>
                <div class="text-center">
                    <p id="statusText" class="text-5xl font-bold mb-4">{{ $statusIndonesia }}</p>
                    <p id="statusDescription" class="text-sm opacity-75">
                        @if($statusIndonesia == 'Basah')
                            Tanah dalam kondisi basah, tidak perlu penyiraman
                        @elseif($statusIndonesia == 'Kering')
                            Tanah kering, memerlukan penyiraman segera
                        @else
                            Kelembapan tanah dalam kondisi optimal
                        @endif
                    </p>
                </div>
            </div>

            <!-- Pump Status Card -->
            <div id="pumpCard" class="rounded-xl shadow-xl p-8 text-white
                @if($latestData && $latestData->pump_status == 'ON') bg-gradient-to-br from-green-500 to-green-600
                @else bg-gradient-to-br from-gray-500 to-gray-600
                @endif">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold opacity-90">Status Pompa</h3>
                    <i class="fas fa-pump-medical text-3xl opacity-50"></i>
                </div>
                <div class="text-center">
                    <p id="pumpStatus" class="text-5xl font-bold mb-4">
                        {{ $latestData && $latestData->pump_status == 'ON' ? 'HIDUP' : 'MATI' }}
                    </p>
                    <div class="flex items-center justify-center space-x-2">
                        <div id="pumpIndicator" class="w-4 h-4 rounded-full 
                            {{ $latestData && $latestData->pump_status == 'ON' ? 'bg-white animate-pulse' : 'bg-gray-300' }}"></div>
                        <p class="text-sm opacity-75">
                            {{ $latestData && $latestData->pump_status == 'ON' ? 'Pompa sedang menyiram' : 'Pompa dalam kondisi mati' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Informasi Detail Sensor
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Device ID</span>
                        <span id="deviceId" class="font-bold text-gray-800">{{ $latestData->device_id ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Nilai Raw Sensor</span>
                        <span id="rawValue" class="font-bold text-gray-800">{{ $latestData->raw_value ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Waktu Pembacaan</span>
                        <span id="timestamp" class="font-bold text-gray-800">
                            {{ $latestData ? \Carbon\Carbon::parse($latestData->timestamp)->format('d/m/Y H:i:s') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 font-medium">Waktu Relatif</span>
                        <span id="timeAgo" class="font-bold text-gray-800">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Pembacaan Sensor -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-history text-purple-600 mr-2"></i>
                        Riwayat Pembacaan Sensor
                    </h2>
                    <div class="flex items-center space-x-2">
                        <label for="recordLimit" class="text-sm text-gray-600">Tampilkan:</label>
                        <select id="recordLimit" class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="10">10 Data</option>
                            <option value="20" selected>20 Data</option>
                            <option value="50">50 Data</option>
                            <option value="100">100 Data</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelembapan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Raw</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pompa</th>
                        </tr>
                    </thead>
                    <tbody id="sensorTableBody" class="bg-white divide-y divide-gray-200">
                        @foreach($sensorHistory as $index => $data)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($data->timestamp)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->device_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data->moisture_percent }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $data->moisture_percent }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $data->raw_value }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($data->status == 'WET') bg-blue-100 text-blue-800
                                    @elseif($data->status == 'DRY') bg-red-100 text-red-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @if($data->status == 'WET') Basah
                                    @elseif($data->status == 'DRY') Kering
                                    @else Normal
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $data->pump_status == 'ON' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $data->pump_status == 'ON' ? 'Hidup' : 'Mati' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Loading State -->
            <div id="tableLoading" class="hidden p-8 text-center">
                <i class="fas fa-spinner fa-spin text-3xl text-green-600 mb-2"></i>
                <p class="text-gray-600">Memuat data...</p>
            </div>

            <!-- Empty State -->
            <div id="tableEmpty" class="hidden p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-600">Belum ada data pembacaan sensor</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">
                © 2024 Sistem Monitoring Tanaman - Auto Refresh setiap 3 detik
            </p>
        </div>
    </footer>

    <script>
        // Auto refresh data setiap 3 detik
        setInterval(() => {
            fetchLatestData();
            fetchSensorHistory();
        }, 10000);

        // Fetch saat halaman dimuat
        fetchLatestData();
        fetchSensorHistory();

        // Event listener untuk perubahan limit
        document.getElementById('recordLimit').addEventListener('change', function() {
            fetchSensorHistory();
        });

        function fetchLatestData() {
            fetch('{{ route("api.sensor.latest") }}')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        updateUI(data);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        function fetchSensorHistory() {
            const limit = document.getElementById('recordLimit').value;
            const tableBody = document.getElementById('sensorTableBody');
            const tableLoading = document.getElementById('tableLoading');
            const tableEmpty = document.getElementById('tableEmpty');

            // Show loading
            tableBody.classList.add('hidden');
            tableLoading.classList.remove('hidden');
            tableEmpty.classList.add('hidden');

            fetch(`{{ route("api.sensor.history") }}?limit=${limit}`)
                .then(response => response.json())
                .then(data => {
                    tableLoading.classList.add('hidden');
                    
                    if (data.length === 0) {
                        tableEmpty.classList.remove('hidden');
                        return;
                    }

                    tableBody.classList.remove('hidden');
                    updateTable(data);
                })
                .catch(error => {
                    console.error('Error fetching history:', error);
                    tableLoading.classList.add('hidden');
                    tableEmpty.classList.remove('hidden');
                });
        }

        function updateTable(data) {
            const tableBody = document.getElementById('sensorTableBody');
            tableBody.innerHTML = '';

            data.forEach((item, index) => {
                const statusClass = item.status === 'WET' ? 'bg-blue-100 text-blue-800' :
                                   item.status === 'DRY' ? 'bg-red-100 text-red-800' :
                                   'bg-green-100 text-green-800';
                
                const statusText = item.status === 'WET' ? 'Basah' :
                                  item.status === 'DRY' ? 'Kering' : 'Normal';

                const pumpClass = item.pump_status === 'ON' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                const pumpText = item.pump_status === 'ON' ? 'Hidup' : 'Mati';

                const timestamp = new Date(item.timestamp).toLocaleString('id-ID');

                const row = `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${timestamp}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.device_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: ${item.moisture_percent}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">${item.moisture_percent}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${item.raw_value}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${pumpClass}">
                                ${pumpText}
                            </span>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }

        function updateUI(data) {
            // Update moisture percentage
            document.getElementById('moisturePercent').textContent = data.moisture_percent + '%';
            document.getElementById('moistureBar').style.width = data.moisture_percent + '%';

            // Update status
            const statusCard = document.getElementById('statusCard');
            const statusText = document.getElementById('statusText');
            const statusIcon = document.getElementById('statusIcon');
            const statusDescription = document.getElementById('statusDescription');

            statusText.textContent = data.status_indonesia;
            
            // Update status card styling
            statusCard.className = 'rounded-xl shadow-xl p-8 text-white transition-all duration-500 ';
            statusIcon.className = 'text-3xl opacity-50 ';
            
            if (data.status_indonesia === 'Basah') {
                statusCard.className += 'bg-gradient-to-br from-blue-600 to-blue-700';
                statusIcon.className += 'fas fa-cloud-rain';
                statusDescription.textContent = 'Tanah dalam kondisi basah, tidak perlu penyiraman';
            } else if (data.status_indonesia === 'Kering') {
                statusCard.className += 'bg-gradient-to-br from-red-500 to-red-600';
                statusIcon.className += 'fas fa-sun';
                statusDescription.textContent = 'Tanah kering, memerlukan penyiraman segera';
            } else {
                statusCard.className += 'bg-gradient-to-br from-green-500 to-green-600';
                statusIcon.className += 'fas fa-check-circle';
                statusDescription.textContent = 'Kelembapan tanah dalam kondisi optimal';
            }

            // Update pump status
            const pumpCard = document.getElementById('pumpCard');
            const pumpStatus = document.getElementById('pumpStatus');
            const pumpIndicator = document.getElementById('pumpIndicator');

            pumpStatus.textContent = data.pump_status_indonesia.toUpperCase();
            pumpCard.className = 'rounded-xl shadow-xl p-8 text-white transition-all duration-500 ';
            
            if (data.pump_status === 'ON') {
                pumpCard.className += 'bg-gradient-to-br from-green-500 to-green-600';
                pumpIndicator.className = 'w-4 h-4 rounded-full bg-white animate-pulse';
            } else {
                pumpCard.className += 'bg-gradient-to-br from-gray-500 to-gray-600';
                pumpIndicator.className = 'w-4 h-4 rounded-full bg-gray-300';
            }

            // Update detailed info
            document.getElementById('deviceId').textContent = data.device_id;
            document.getElementById('rawValue').textContent = data.raw_value;
            document.getElementById('timestamp').textContent = new Date(data.timestamp).toLocaleString('id-ID');
            
            // Update last update time
            const now = new Date();
            const updateTime = now.toLocaleTimeString('id-ID');
            document.getElementById('lastUpdateTime').textContent = updateTime;
            
            // Calculate time ago
            const dataTime = new Date(data.timestamp);
            const diffSeconds = Math.floor((now - dataTime) / 1000);
            let timeAgo = '';
            
            if (diffSeconds < 60) {
                timeAgo = diffSeconds + ' detik yang lalu';
            } else if (diffSeconds < 3600) {
                timeAgo = Math.floor(diffSeconds / 60) + ' menit yang lalu';
            } else {
                timeAgo = Math.floor(diffSeconds / 3600) + ' jam yang lalu';
            }
            
            document.getElementById('timeAgo').textContent = timeAgo;
        }
    </script>
</body>
</html>