<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monitoring Tanaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('monitoring') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
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
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Kelembapan Saat Ini</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ $latestData->moisture_percent ?? 0 }}%
                        </p>
                    </div>
                    <i class="fas fa-tint text-4xl text-green-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Status Tanah</p>
                        <p class="text-2xl font-bold 
                            @if($statusIndonesia == 'Basah') text-blue-600
                            @elseif($statusIndonesia == 'Kering') text-red-600
                            @else text-green-600
                            @endif">
                            {{ $statusIndonesia }}
                        </p>
                    </div>
                    <i class="fas fa-info-circle text-4xl text-gray-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Pembacaan</p>
                        <p class="text-3xl font-bold text-indigo-600">
                            {{ number_format($stats['total_readings']) }}
                        </p>
                    </div>
                    <i class="fas fa-database text-4xl text-indigo-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pompa Hidup Hari Ini</p>
                        <p class="text-3xl font-bold text-purple-600">
                            {{ $stats['pump_on_today'] ?? 0 }}x
                        </p>
                    </div>
                    <i class="fas fa-play-circle text-4xl text-purple-200"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Status Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-thermometer-half text-green-600 mr-2"></i>
                    Status Sensor Terkini
                </h2>
                
                @if($latestData)
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b pb-3">
                        <span class="text-gray-600">Device ID</span>
                        <span class="font-semibold">{{ $latestData->device_id }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-3">
                        <span class="text-gray-600">Nilai Raw</span>
                        <span class="font-semibold">{{ $latestData->raw_value }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-3">
                        <span class="text-gray-600">Persentase Kelembapan</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-4 mr-3">
                                <div class="bg-green-600 h-4 rounded-full" style="width: {{ $latestData->moisture_percent }}%"></div>
                            </div>
                            <span class="font-bold text-green-600">{{ $latestData->moisture_percent }}%</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center border-b pb-3">
                        <span class="text-gray-600">Status Pompa</span>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($latestData->pump_status == 'ON') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $latestData->pump_status == 'ON' ? 'Hidup' : 'Mati' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Waktu Update</span>
                        <span class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($latestData->timestamp)->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">Belum ada data sensor</p>
                @endif
            </div>

            <!-- Recent Pump Events -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Riwayat Pompa Terbaru
                </h2>
                
                @if($recentPumpEvents->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($recentPumpEvents as $event)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center">
                            <i class="fas fa-circle text-xs mr-3
                                @if($event->event == 'PUMP_ON') text-green-500
                                @else text-red-500
                                @endif"></i>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    @if($event->event == 'PUMP_ON')
                                        <span class="text-green-600">Pompa Hidup</span>
                                    @else
                                        <span class="text-red-600">Pompa Mati</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $event->device_id }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($event->timestamp)->diffForHumans() }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">Belum ada riwayat pompa</p>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('pompa') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                        Lihat Semua Riwayat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Rata-rata Kelembapan</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['avg_moisture'], 1) }}%</p>
                    </div>
                    <i class="fas fa-chart-line text-4xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Total Kejadian Pompa</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_pump_events']) }}</p>
                    </div>
                    <i class="fas fa-water text-4xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Pompa Mati Hari Ini</p>
                        <p class="text-3xl font-bold">{{ $stats['pump_off_today'] ?? 0 }}x</p>
                    </div>
                    <i class="fas fa-stop-circle text-4xl opacity-30"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">
                © 2024 Sistem Monitoring Tanaman. Update terakhir: <span id="lastUpdate">{{ now()->format('d/m/Y H:i:s') }}</span>
            </p>
        </div>
    </footer>
</body>
</html>