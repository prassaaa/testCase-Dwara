<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeatherDashboardController extends Controller
{
    protected ClickHouseService $clickHouseService;

    public function __construct(ClickHouseService $clickHouseService)
    {
        $this->clickHouseService = $clickHouseService;
    }

    /**
     * Display the weather dashboard
     */
    public function index(): Response
    {
        // Get initial data
        $dateRange = $this->clickHouseService->getDateRange();
        $stations = $this->clickHouseService->getAvailableStations();

        return Inertia::render('WeatherDashboard', [
            'dateRange' => $dateRange,
            'stations' => $stations['data'] ?? [],
        ]);
    }

    /**
     * Get weather data with filters (API endpoint)
     */
    public function getData(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'station_id' => 'nullable|string',
            'region' => 'nullable|string',
            'country' => 'nullable|string|size:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $data = $this->clickHouseService->getWeatherData($validated);

        return response()->json($data);
    }

    /**
     * Get weather statistics (API endpoint)
     */
    public function getStatistics(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'country' => 'nullable|string|size:2',
        ]);

        $statistics = $this->clickHouseService->getWeatherStatistics($validated);

        return response()->json($statistics);
    }

    /**
     * Get available stations (API endpoint)
     */
    public function getStations()
    {
        $stations = $this->clickHouseService->getAvailableStations();

        return response()->json($stations);
    }

    /**
     * Test ClickHouse connection
     */
    public function testConnection()
    {
        $isConnected = $this->clickHouseService->testConnection();

        return response()->json([
            'success' => $isConnected,
            'message' => $isConnected
                ? 'ClickHouse connection successful'
                : 'ClickHouse connection failed',
        ]);
    }
}

