<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pompa - Monitoring Tanaman</title>
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
                    <a href="{{ route('monitoring') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-chart-line mr-2"></i>Monitoring
                    </a>
                    <a href="{{ route('pompa') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">
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
                        <i class="fas fa-water text-blue-600 mr-2"></i>
                        Riwayat Kejadian Pompa
                    </h1>
                    <p class="text-gray-600">Histori lengkap aktivitas pompa penyiraman otomatis</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-semibold text-blue-600">AUTO REFRESH</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Update setiap 5 detik</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Total Kejadian</p>
                        <p id="totalEvents" class="text-4xl font-bold">{{ $stats['total'] }}</p>
                    </div>
                    <i class="fas fa-list text-5xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Pompa Hidup</p>
                        <p id="startEvents" class="text-4xl font-bold">{{ $stats['pump_on'] }}</p>
                    </div>
                    <i class="fas fa-play-circle text-5xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Pompa Mati</p>
                        <p id="stopEvents" class="text-4xl font-bold">{{ $stats['pump_off'] }}</p>
                    </div>
                    <i class="fas fa-stop-circle text-5xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Hari Ini</p>
                        <p id="todayEvents" class="text-4xl font-bold">{{ $stats['today'] }}</p>
                    </div>
                    <i class="fas fa-calendar-day text-5xl opacity-30"></i>
                </div>
            </div>
        </div>

        <!-- Events Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Daftar Kejadian Pompa
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Device ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Kejadian
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Waktu
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Relatif
                            </th>
                        </tr>
                    </thead>
                    <tbody id="pumpEventsTable" class="bg-white divide-y divide-gray-200">
                        @forelse($pumpHistory as $index => $event)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pumpHistory->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-microchip text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $event->device_id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($event->event == 'PUMP_ON')
                                    <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-play mr-1"></i>
                                        Pompa Hidup
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-stop mr-1"></i>
                                        Pompa Mati
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="far fa-clock text-gray-400 mr-2"></i>
                                    {{ \Carbon\Carbon::parse($event->timestamp)->format('d/m/Y H:i:s') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($event->timestamp)->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500">Belum ada riwayat kejadian pompa</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pumpHistory->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-semibold">{{ $pumpHistory->firstItem() }}</span> 
                        sampai <span class="font-semibold">{{ $pumpHistory->lastItem() }}</span> 
                        dari <span class="font-semibold">{{ $pumpHistory->total() }}</span> kejadian
                    </div>
                    <div class="flex space-x-2">
                        {{ $pumpHistory->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">
                © 2024 Sistem Monitoring Tanaman - Riwayat diperbarui otomatis setiap 5 detik
            </p>
        </div>
    </footer>

    <script>
        // Auto refresh data setiap 5 detik
        setInterval(fetchLatestPumpEvents, 5000);

        function fetchLatestPumpEvents() {
            fetch('{{ route("api.pompa.latest") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.events && data.events.length > 0) {
                        updatePumpTable(data.events);
                        updateStatistics(data.stats);
                    }
                })
                .catch(error => console.error('Error fetching pump events:', error));
        }

        function updatePumpTable(events) {
            const tbody = document.getElementById('pumpEventsTable');
            
            let html = '';
            events.forEach((event, index) => {
                const isOn = event.event === 'PUMP_ON';
                const statusClass = isOn ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const statusIcon = isOn ? 'fa-play' : 'fa-stop';
                const statusText = isOn ? 'Pompa Hidup' : 'Pompa Mati';
                
                const eventTime = new Date(event.timestamp);
                const formattedTime = eventTime.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                
                const timeAgo = getTimeAgo(eventTime);
                
                html += `
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-microchip text-gray-400 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900">${event.device_id}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                <i class="fas ${statusIcon} mr-1"></i>
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="far fa-clock text-gray-400 mr-2"></i>
                                ${formattedTime}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${timeAgo}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html || `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">Belum ada riwayat kejadian pompa</p>
                    </td>
                </tr>
            `;
        }

        function updateStatistics(stats) {
            document.getElementById('totalEvents').textContent = stats.total;
            document.getElementById('startEvents').textContent = stats.pump_on;
            document.getElementById('stopEvents').textContent = stats.pump_off;
            document.getElementById('todayEvents').textContent = stats.today;
        }

        function getTimeAgo(date) {
            const now = new Date();
            const diffSeconds = Math.floor((now - date) / 1000);
            
            if (diffSeconds < 60) {
                return diffSeconds + ' detik yang lalu';
            } else if (diffSeconds < 3600) {
                return Math.floor(diffSeconds / 60) + ' menit yang lalu';
            } else if (diffSeconds < 86400) {
                return Math.floor(diffSeconds / 3600) + ' jam yang lalu';
            } else {
                return Math.floor(diffSeconds / 86400) + ' hari yang lalu';
            }
        }
    </script>
</body>
</html>