<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TanamController extends Controller
{
    /**
     * Halaman monitoring sensor kelembapan
     */
    public function monitoring()
    {
        $latestData = DB::table('sensor_data')
            ->orderBy('timestamp', 'desc')
            ->first();

        // Ambil riwayat untuk tampilan awal
        $sensorHistory = DB::table('sensor_data')
            ->orderBy('timestamp', 'desc')
            ->limit(20)
            ->get();

        // Konversi status ke Bahasa Indonesia
        $statusIndonesia = $this->translateStatus($latestData->status ?? 'OK');
        
        return view('monitoring', compact('latestData', 'statusIndonesia', 'sensorHistory'));
    }

    /**
     * Halaman riwayat pompa
     */
    public function pompa()
    {
        $pumpHistory = DB::table('pump_events')
            ->orderBy('timestamp', 'desc')
            ->paginate(20);

        // Hitung statistik
        $stats = [
            'total' => DB::table('pump_events')->count(),
            'pump_on' => DB::table('pump_events')->where('event', 'PUMP_ON')->count(),
            'pump_off' => DB::table('pump_events')->where('event', 'PUMP_OFF')->count(),
            'today' => DB::table('pump_events')
                ->whereDate('timestamp', Carbon::today())
                ->count(),
        ];

        return view('pompa', compact('pumpHistory', 'stats'));
    }

    /**
     * API untuk mendapatkan data sensor terbaru (AJAX)
     */
    public function getLatestData()
    {
        $latestData = DB::table('sensor_data')
            ->orderBy('timestamp', 'desc')
            ->first();

        if ($latestData) {
            $latestData->status_indonesia = $this->translateStatus($latestData->status);
            $latestData->pump_status_indonesia = $this->translatePumpStatus($latestData->pump_status);
        }

        return response()->json($latestData);
    }

    /**
     * API untuk mendapatkan riwayat pompa terbaru (AJAX)
     */
    public function getLatestPumpEvents()
    {
        // Ambil 20 event terbaru
        $pumpEvents = DB::table('pump_events')
            ->orderBy('timestamp', 'desc')
            ->limit(20)
            ->get();

        // Hitung statistik terbaru
        $stats = [
            'total' => DB::table('pump_events')->count(),
            'pump_on' => DB::table('pump_events')->where('event', 'PUMP_ON')->count(),
            'pump_off' => DB::table('pump_events')->where('event', 'PUMP_OFF')->count(),
            'today' => DB::table('pump_events')
                ->whereDate('timestamp', Carbon::today())
                ->count(),
        ];

        return response()->json([
            'events' => $pumpEvents,
            'stats' => $stats
        ]);
    }

    /**
     * Dashboard utama
     */
    public function dashboard()
    {
        $latestData = DB::table('sensor_data')
            ->orderBy('timestamp', 'desc')
            ->first();

        $recentPumpEvents = DB::table('pump_events')
            ->orderBy('timestamp', 'desc')
            ->limit(5)
            ->get();

        $statusIndonesia = $this->translateStatus($latestData->status ?? 'OK');

        // Statistik
        $stats = [
            'total_readings' => DB::table('sensor_data')->count(),
            'total_pump_events' => DB::table('pump_events')->count(),
            'avg_moisture' => DB::table('sensor_data')->avg('moisture_percent'),
            'last_pump_event' => DB::table('pump_events')->orderBy('timestamp', 'desc')->first(),
            'pump_on_today' => DB::table('pump_events')
                ->where('event', 'PUMP_ON')
                ->whereDate('timestamp', Carbon::today())
                ->count(),
            'pump_off_today' => DB::table('pump_events')
                ->where('event', 'PUMP_OFF')
                ->whereDate('timestamp', Carbon::today())
                ->count(),
        ];

        return view('dashboard', compact('latestData', 'statusIndonesia', 'recentPumpEvents', 'stats'));
    }

    /**
     * Translate status ke Bahasa Indonesia
     */
    private function translateStatus($status)
    {
        $translations = [
            'WET' => 'Basah',
            'DRY' => 'Kering',
            'OK' => 'Normal',
        ];

        return $translations[strtoupper($status)] ?? 'Normal';
    }

    /**
     * Translate pump status ke Bahasa Indonesia
     */
    private function translatePumpStatus($status)
    {
        $translations = [
            'ON' => 'Hidup',
            'OFF' => 'Mati',
        ];

        return $translations[strtoupper($status)] ?? 'Mati';
    }

    /**
     * API untuk mendapatkan riwayat sensor (AJAX)
     */
    public function getSensorHistory(Request $request)
    {
        $limit = $request->get('limit', 20);
        
        $sensorHistory = DB::table('sensor_data')
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($sensorHistory);
    }

    /**
     * API untuk mendapatkan statistik pompa per hari
     */
    public function getPumpStatsByDate(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(7));
        $endDate = $request->get('end_date', Carbon::now());

        $stats = DB::table('pump_events')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('SUM(CASE WHEN event = "PUMP_ON" THEN 1 ELSE 0 END) as pump_on_count'),
                DB::raw('SUM(CASE WHEN event = "PUMP_OFF" THEN 1 ELSE 0 END) as pump_off_count'),
                DB::raw('COUNT(*) as total_events')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(timestamp)'))
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($stats);
    }
}