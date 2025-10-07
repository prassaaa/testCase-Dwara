<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { Cloud, TrendingUp, Droplets, Wind } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Weather data
const loading = ref(true);
const weatherStats = ref<any>(null);
const recentData = ref<any[]>([]);

const fetchWeatherSummary = async () => {
    loading.value = true;
    try {
        // Get last 7 days data
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 7);

        const params = new URLSearchParams({
            start_date: startDate.toISOString().split('T')[0],
            end_date: endDate.toISOString().split('T')[0],
            limit: '5',
        });

        const [statsResponse, dataResponse] = await Promise.all([
            axios.get(`/weather/statistics?${params.toString()}`),
            axios.get(`/weather/data?${params.toString()}`),
        ]);

        if (statsResponse.data.success && statsResponse.data.data.length > 0) {
            const stats = statsResponse.data.data;
            weatherStats.value = {
                avgTemp: (stats.reduce((sum: number, s: any) => sum + s.avg_temperature, 0) / stats.length).toFixed(1),
                maxTemp: Math.max(...stats.map((s: any) => s.max_temperature)).toFixed(1),
                minTemp: Math.min(...stats.map((s: any) => s.min_temperature)).toFixed(1),
                totalPrecip: stats.reduce((sum: number, s: any) => sum + s.total_precipitation, 0).toFixed(1),
                stations: stats.length,
            };
        }

        if (dataResponse.data.success) {
            recentData.value = dataResponse.data.data.slice(0, 5);
        }
    } catch (error) {
        console.error('Error fetching weather summary:', error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
    });
};

onMounted(() => {
    fetchWeatherSummary();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Welcome Section -->
            <div class="rounded-xl border bg-gradient-to-r from-blue-500 to-cyan-500 p-6 text-white">
                <h1 class="text-2xl font-bold">Welcome to Weather Dashboard</h1>
                <p class="mt-2 text-blue-50">
                    Monitor global weather patterns with NOAA historical data
                </p>
            </div>

            <!-- Weather Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Average Temperature -->
                <div class="rounded-xl border bg-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Avg Temperature</p>
                            <h3 class="mt-2 text-2xl font-bold">
                                {{ loading ? '...' : weatherStats?.avgTemp || '0' }}°C
                            </h3>
                            <p class="mt-1 text-xs text-muted-foreground">Last 7 days</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                            <Cloud class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <!-- Temperature Range -->
                <div class="rounded-xl border bg-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Temp Range</p>
                            <h3 class="mt-2 text-2xl font-bold">
                                <span class="text-red-500">{{ loading ? '...' : weatherStats?.maxTemp || '0' }}</span>
                                /
                                <span class="text-blue-500">{{ loading ? '...' : weatherStats?.minTemp || '0' }}</span>°C
                            </h3>
                            <p class="mt-1 text-xs text-muted-foreground">Max / Min</p>
                        </div>
                        <div class="rounded-full bg-orange-100 p-3 dark:bg-orange-900">
                            <TrendingUp class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                </div>

                <!-- Precipitation -->
                <div class="rounded-xl border bg-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Precipitation</p>
                            <h3 class="mt-2 text-2xl font-bold">
                                {{ loading ? '...' : weatherStats?.totalPrecip || '0' }} mm
                            </h3>
                            <p class="mt-1 text-xs text-muted-foreground">Total last 7 days</p>
                        </div>
                        <div class="rounded-full bg-cyan-100 p-3 dark:bg-cyan-900">
                            <Droplets class="h-6 w-6 text-cyan-600 dark:text-cyan-400" />
                        </div>
                    </div>
                </div>

                <!-- Active Stations -->
                <div class="rounded-xl border bg-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Active Stations</p>
                            <h3 class="mt-2 text-2xl font-bold">
                                {{ loading ? '...' : weatherStats?.stations || '0' }}
                            </h3>
                            <p class="mt-1 text-xs text-muted-foreground">Monitoring locations</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                            <Wind class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Weather Data & Quick Actions -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Recent Weather Data -->
                <div class="rounded-xl border bg-card">
                    <div class="border-b p-4">
                        <h2 class="text-lg font-semibold">Recent Weather Data</h2>
                        <p class="text-sm text-muted-foreground">Latest measurements from stations</p>
                    </div>
                    <div class="p-4">
                        <div v-if="loading" class="flex items-center justify-center py-8">
                            <svg class="h-8 w-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                        </div>
                        <div v-else-if="recentData.length === 0" class="py-8 text-center text-muted-foreground">
                            No recent data available
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="(item, index) in recentData"
                                :key="index"
                                class="flex items-center justify-between rounded-lg border p-3 hover:bg-muted/50"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">{{ item.station_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ formatDate(item.date) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold">{{ item.temp_avg_celsius?.toFixed(1) }}°C</p>
                                    <p class="text-xs text-muted-foreground">{{ item.weather_type }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link
                                href="/weather"
                                class="inline-flex w-full items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                            >
                                View Full Dashboard
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="rounded-xl border bg-card">
                    <div class="border-b p-4">
                        <h2 class="text-lg font-semibold">Quick Actions</h2>
                        <p class="text-sm text-muted-foreground">Explore weather data</p>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <Link
                                href="/weather"
                                class="flex items-center gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                <div class="rounded-full bg-blue-100 p-2 dark:bg-blue-900">
                                    <Cloud class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium">Weather Dashboard</p>
                                    <p class="text-sm text-muted-foreground">View detailed weather analytics</p>
                                </div>
                            </Link>

                            <a
                                href="https://clickhouse.com/docs/getting-started/example-datasets/noaa"
                                target="_blank"
                                class="flex items-center gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                <div class="rounded-full bg-green-100 p-2 dark:bg-green-900">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium">NOAA Documentation</p>
                                    <p class="text-sm text-muted-foreground">Learn about the dataset</p>
                                </div>
                            </a>

                            <a
                                href="http://localhost:8123/play"
                                target="_blank"
                                class="flex items-center gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                <div class="rounded-full bg-purple-100 p-2 dark:bg-purple-900">
                                    <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium">ClickHouse Playground</p>
                                    <p class="text-sm text-muted-foreground">Run custom SQL queries</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
