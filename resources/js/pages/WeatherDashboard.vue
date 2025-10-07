<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import WeatherFilters from '@/components/weather/WeatherFilters.vue';
import WeatherStats from '@/components/weather/WeatherStats.vue';
import WeatherTable from '@/components/weather/WeatherTable.vue';
import WeatherChart from '@/components/weather/WeatherChart.vue';
import axios from 'axios';

interface Props {
    dateRange: {
        success: boolean;
        min_date: string | null;
        max_date: string | null;
    };
    stations: Array<{
        station_id: string;
        station_name: string;
        country_code: string;
        longitude: number;
        latitude: number;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs = [
    { title: 'Weather Dashboard', href: '/weather' },
];

// State
const loading = ref(false);
const weatherData = ref<any[]>([]);
const statistics = ref<any[]>([]);
const filters = ref({
    start_date: '',
    end_date: '',
    station_id: '',
    region: '',
    country: '',
    limit: 100,
});

// Computed
const hasData = computed(() => weatherData.value.length > 0);
const hasStats = computed(() => statistics.value.length > 0);

// Methods
const fetchWeatherData = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        Object.entries(filters.value).forEach(([key, value]) => {
            if (value) params.append(key, String(value));
        });

        const [dataResponse, statsResponse] = await Promise.all([
            axios.get(`/weather/data?${params.toString()}`),
            axios.get(`/weather/statistics?${params.toString()}`),
        ]);

        if (dataResponse.data.success) {
            weatherData.value = dataResponse.data.data;
        }

        if (statsResponse.data.success) {
            statistics.value = statsResponse.data.data;
        }
    } catch (error) {
        console.error('Error fetching weather data:', error);
    } finally {
        loading.value = false;
    }
};

const handleFilterChange = (newFilters: any) => {
    filters.value = { ...filters.value, ...newFilters };
    fetchWeatherData();
};

const handleReset = () => {
    filters.value = {
        start_date: '',
        end_date: '',
        station_id: '',
        region: '',
        country: '',
        limit: 100,
    };
    fetchWeatherData();
};

// Lifecycle
onMounted(() => {
    // Set default date range to last 30 days
    if (props.dateRange.max_date) {
        const endDate = new Date(props.dateRange.max_date);
        const startDate = new Date(endDate);
        startDate.setDate(startDate.getDate() - 30);
        
        filters.value.end_date = endDate.toISOString().split('T')[0];
        filters.value.start_date = startDate.toISOString().split('T')[0];
    }
    
    fetchWeatherData();
});
</script>

<template>
    <Head title="Weather Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">NOAA Weather Dashboard</h1>
                    <p class="text-sm text-muted-foreground">
                        Global Historical Climatology Network Data
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <WeatherFilters
                :stations="stations"
                :date-range="dateRange"
                :filters="filters"
                :loading="loading"
                @filter-change="handleFilterChange"
                @reset="handleReset"
            />

            <!-- Statistics Cards -->
            <WeatherStats
                v-if="hasStats"
                :statistics="statistics"
                :loading="loading"
            />

            <!-- Chart -->
            <WeatherChart
                v-if="hasData"
                :data="weatherData"
                :loading="loading"
            />

            <!-- Data Table -->
            <WeatherTable
                :data="weatherData"
                :loading="loading"
            />

            <!-- Empty State -->
            <div
                v-if="!loading && !hasData"
                class="flex min-h-[400px] flex-col items-center justify-center rounded-xl border border-dashed p-8 text-center"
            >
                <div class="mx-auto flex max-w-[420px] flex-col items-center justify-center text-center">
                    <svg
                        class="h-10 w-10 text-muted-foreground"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"
                        />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold">No weather data found</h3>
                    <p class="mb-4 mt-2 text-sm text-muted-foreground">
                        Try adjusting your filters to see weather data.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

