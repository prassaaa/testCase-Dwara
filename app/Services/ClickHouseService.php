<?php

namespace App\Services;

use ClickHouseDB\Client;
use Illuminate\Support\Facades\Log;

class ClickHouseService
{
    protected Client $client;
    protected string $database;

    public function __construct()
    {
        $this->client = new Client([
            'host' => config('clickhouse.host'),
            'port' => config('clickhouse.port'),
            'username' => config('clickhouse.username'),
            'password' => config('clickhouse.password'),
        ]);

        $this->database = config('clickhouse.database');
        $this->client->database($this->database);
        $this->client->setTimeout(10);
        $this->client->setConnectTimeOut(5);
    }

    /**
     * Test connection to ClickHouse
     */
    public function testConnection(): bool
    {
        try {
            $result = $this->client->select('SELECT 1 as test');
            return $result->rows() > 0;
        } catch (\Exception $e) {
            Log::error('ClickHouse connection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get weather data with filters
     *
     * @param array $filters
     * @return array
     */
    public function getWeatherData(array $filters = []): array
    {
        try {
            $query = "SELECT
                station_id,
                date,
                tempAvg / 10.0 AS temp_avg_celsius,
                tempMax / 10.0 AS temp_max_celsius,
                tempMin / 10.0 AS temp_min_celsius,
                precipitation / 10.0 AS precipitation_mm,
                snowfall AS snowfall_mm,
                snowDepth AS snow_depth_mm,
                percentDailySun AS percent_daily_sun,
                averageWindSpeed / 10.0 AS avg_wind_speed_ms,
                maxWindSpeed / 10.0 AS max_wind_speed_ms,
                weatherType AS weather_type,
                tupleElement(location, 1) AS longitude,
                tupleElement(location, 2) AS latitude,
                elevation,
                name AS station_name
            FROM {$this->database}.noaa";

            $conditions = [];
            $params = [];

            // Filter by date range
            if (!empty($filters['start_date'])) {
                $conditions[] = "date >= :start_date";
                $params['start_date'] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $conditions[] = "date <= :end_date";
                $params['end_date'] = $filters['end_date'];
            }

            // Filter by station/region
            if (!empty($filters['station_id'])) {
                $conditions[] = "station_id = :station_id";
                $params['station_id'] = $filters['station_id'];
            }

            // Filter by station name (region)
            if (!empty($filters['region'])) {
                $conditions[] = "name LIKE :region";
                $params['region'] = '%' . $filters['region'] . '%';
            }

            // Filter by country code (first 2 chars of station_id)
            if (!empty($filters['country'])) {
                $conditions[] = "substring(station_id, 1, 2) = :country";
                $params['country'] = $filters['country'];
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }

            $query .= " ORDER BY date DESC, station_id ASC";

            // Pagination
            $page = $filters['page'] ?? 1;
            $perPage = $filters['per_page'] ?? 50;
            $offset = ($page - 1) * $perPage;

            // Get total count first
            $countQuery = "SELECT count() as total FROM ({$query}) as subquery";
            $countStatement = $this->client->select($countQuery, $params);
            $total = $countStatement->rows()[0]['total'] ?? 0;

            // Add pagination to main query
            $query .= " LIMIT {$perPage} OFFSET {$offset}";

            $statement = $this->client->select($query, $params);

            return [
                'success' => true,
                'data' => $statement->rows(),
                'count' => $statement->count(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('ClickHouse query failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'count' => 0,
            ];
        }
    }

    /**
     * Get aggregated weather statistics
     *
     * @param array $filters
     * @return array
     */
    public function getWeatherStatistics(array $filters = []): array
    {
        try {
            $query = "SELECT
                name AS station_name,
                count() AS total_measurements,
                avg(tempAvg / 10.0) AS avg_temperature,
                max(tempMax / 10.0) AS max_temperature,
                min(tempMin / 10.0) AS min_temperature,
                sum(precipitation / 10.0) AS total_precipitation,
                tupleElement(location, 1) AS longitude,
                tupleElement(location, 2) AS latitude
            FROM {$this->database}.noaa";

            $conditions = [];
            $params = [];

            if (!empty($filters['start_date'])) {
                $conditions[] = "date >= :start_date";
                $params['start_date'] = $filters['start_date'];
            }

            if (!empty($filters['end_date'])) {
                $conditions[] = "date <= :end_date";
                $params['end_date'] = $filters['end_date'];
            }

            if (!empty($filters['country'])) {
                $conditions[] = "substring(station_id, 1, 2) = :country";
                $params['country'] = $filters['country'];
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }

            $query .= " GROUP BY station_name, longitude, latitude";
            $query .= " ORDER BY total_measurements DESC";

            $statement = $this->client->select($query, $params);

            return [
                'success' => true,
                'data' => $statement->rows(),
                'count' => $statement->count(),
            ];
        } catch (\Exception $e) {
            Log::error('ClickHouse statistics query failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'count' => 0,
            ];
        }
    }

    /**
     * Get available stations/regions
     *
     * @return array
     */
    public function getAvailableStations(): array
    {
        try {
            $query = "SELECT DISTINCT
                station_id,
                name AS station_name,
                substring(station_id, 1, 2) AS country_code,
                tupleElement(location, 1) AS longitude,
                tupleElement(location, 2) AS latitude
            FROM {$this->database}.noaa
            ORDER BY name";

            $statement = $this->client->select($query);

            return [
                'success' => true,
                'data' => $statement->rows(),
                'count' => $statement->count(),
            ];
        } catch (\Exception $e) {
            Log::error('ClickHouse stations query failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'count' => 0,
            ];
        }
    }

    /**
     * Get date range of available data
     *
     * @return array
     */
    public function getDateRange(): array
    {
        try {
            $query = "SELECT
                min(date) AS min_date,
                max(date) AS max_date
            FROM {$this->database}.noaa";

            $statement = $this->client->select($query);
            $row = $statement->fetchOne();

            return [
                'success' => true,
                'min_date' => $row['min_date'] ?? null,
                'max_date' => $row['max_date'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('ClickHouse date range query failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'min_date' => null,
                'max_date' => null,
            ];
        }
    }
}

