<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    statistics: Array<{
        station_name: string;
        total_measurements: number;
        avg_temperature: number;
        max_temperature: number;
        min_temperature: number;
        total_precipitation: number;
    }>;
    loading: boolean;
}

const props = defineProps<Props>();

const totalMeasurements = computed(() => {
    return props.statistics.reduce((sum, stat) => sum + stat.total_measurements, 0);
});

const avgTemperature = computed(() => {
    if (props.statistics.length === 0) return 0;
    const sum = props.statistics.reduce((sum, stat) => sum + stat.avg_temperature, 0);
    return (sum / props.statistics.length).toFixed(1);
});

const maxTemperature = computed(() => {
    if (props.statistics.length === 0) return 0;
    return Math.max(...props.statistics.map(s => s.max_temperature)).toFixed(1);
});

const minTemperature = computed(() => {
    if (props.statistics.length === 0) return 0;
    return Math.min(...props.statistics.map(s => s.min_temperature)).toFixed(1);
});

const totalPrecipitation = computed(() => {
    return props.statistics.reduce((sum, stat) => sum + stat.total_precipitation, 0).toFixed(1);
});
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <!-- Total Measurements -->
        <div class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-muted-foreground">
                    Total Measurements
                </h3>
                <svg
                    class="h-4 w-4 text-muted-foreground"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    />
                </svg>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-bold">{{ totalMeasurements.toLocaleString() }}</div>
                <p class="text-xs text-muted-foreground">data points</p>
            </div>
        </div>

        <!-- Average Temperature -->
        <div class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-muted-foreground">
                    Avg Temperature
                </h3>
                <svg
                    class="h-4 w-4 text-muted-foreground"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    />
                </svg>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-bold">{{ avgTemperature }}°C</div>
                <p class="text-xs text-muted-foreground">average</p>
            </div>
        </div>

        <!-- Max Temperature -->
        <div class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-muted-foreground">
                    Max Temperature
                </h3>
                <svg
                    class="h-4 w-4 text-red-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                    />
                </svg>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-bold text-red-500">{{ maxTemperature }}°C</div>
                <p class="text-xs text-muted-foreground">highest</p>
            </div>
        </div>

        <!-- Min Temperature -->
        <div class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-muted-foreground">
                    Min Temperature
                </h3>
                <svg
                    class="h-4 w-4 text-blue-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                    />
                </svg>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-bold text-blue-500">{{ minTemperature }}°C</div>
                <p class="text-xs text-muted-foreground">lowest</p>
            </div>
        </div>

        <!-- Total Precipitation -->
        <div class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-muted-foreground">
                    Total Precipitation
                </h3>
                <svg
                    class="h-4 w-4 text-muted-foreground"
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
            </div>
            <div class="mt-2">
                <div class="text-2xl font-bold">{{ totalPrecipitation }} mm</div>
                <p class="text-xs text-muted-foreground">cumulative</p>
            </div>
        </div>
    </div>
</template>

