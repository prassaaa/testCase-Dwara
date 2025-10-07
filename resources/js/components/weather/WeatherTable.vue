<script setup lang="ts">
interface Props {
    data: Array<{
        station_id: string;
        date: string;
        temp_avg_celsius: number;
        temp_max_celsius: number;
        temp_min_celsius: number;
        precipitation_mm: number;
        weather_type: string;
        station_name: string;
    }>;
    loading: boolean;
}

defineProps<Props>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getWeatherIcon = (weatherType: string) => {
    const icons: Record<string, string> = {
        'Normal': '☀️',
        'Rain': '🌧️',
        'Snow': '❄️',
        'Fog': '🌫️',
        'Thunder': '⛈️',
        'Drizzle': '🌦️',
    };
    return icons[weatherType] || '🌤️';
};
</script>

<template>
    <div class="rounded-xl border bg-card">
        <div class="p-4">
            <h2 class="text-lg font-semibold">Weather Data</h2>
            <p class="text-sm text-muted-foreground">
                Detailed weather measurements
            </p>
        </div>
        
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-t bg-muted/50 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-left font-medium">Station</th>
                        <th class="px-4 py-3 text-left font-medium">Weather</th>
                        <th class="px-4 py-3 text-right font-medium">Avg Temp</th>
                        <th class="px-4 py-3 text-right font-medium">Max Temp</th>
                        <th class="px-4 py-3 text-right font-medium">Min Temp</th>
                        <th class="px-4 py-3 text-right font-medium">Precipitation</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-if="loading">
                        <td colspan="7" class="px-4 py-8 text-center">
                            <div class="flex items-center justify-center">
                                <svg
                                    class="h-8 w-8 animate-spin text-primary"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    />
                                </svg>
                                <span class="ml-2">Loading data...</span>
                            </div>
                        </td>
                    </tr>
                    
                    <tr v-else-if="data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                            No data available
                        </td>
                    </tr>
                    
                    <tr
                        v-for="(row, index) in data"
                        v-else
                        :key="index"
                        class="hover:bg-muted/50"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ formatDate(row.date) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="max-w-[200px] truncate" :title="row.station_name">
                                {{ row.station_name }}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ row.station_id }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1">
                                <span>{{ getWeatherIcon(row.weather_type) }}</span>
                                <span>{{ row.weather_type }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ row.temp_avg_celsius?.toFixed(1) }}°C
                        </td>
                        <td class="px-4 py-3 text-right text-red-500">
                            {{ row.temp_max_celsius?.toFixed(1) }}°C
                        </td>
                        <td class="px-4 py-3 text-right text-blue-500">
                            {{ row.temp_min_celsius?.toFixed(1) }}°C
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ row.precipitation_mm?.toFixed(1) }} mm
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="data.length > 0" class="border-t p-4 text-sm text-muted-foreground">
            Showing {{ data.length }} records
        </div>
    </div>
</template>

