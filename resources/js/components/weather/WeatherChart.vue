<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    data: Array<{
        date: string;
        temp_avg_celsius: number;
        temp_max_celsius: number;
        temp_min_celsius: number;
        precipitation_mm: number;
        station_name: string;
    }>;
    loading: boolean;
}

const props = defineProps<Props>();

const chartType = ref<'temperature' | 'precipitation'>('temperature');

// Sort data by date
const sortedData = computed(() => {
    return [...props.data].sort((a, b) => 
        new Date(a.date).getTime() - new Date(b.date).getTime()
    ).slice(0, 30); // Limit to 30 points for better visualization
});

// Calculate chart dimensions and scales
const chartHeight = 300;
const chartWidth = 800;
const padding = { top: 20, right: 20, bottom: 40, left: 50 };

const tempRange = computed(() => {
    if (sortedData.value.length === 0) return { min: 0, max: 100 };
    
    const temps = sortedData.value.flatMap(d => [
        d.temp_min_celsius,
        d.temp_avg_celsius,
        d.temp_max_celsius,
    ]);
    
    const min = Math.floor(Math.min(...temps) - 5);
    const max = Math.ceil(Math.max(...temps) + 5);
    
    return { min, max };
});

const precipRange = computed(() => {
    if (sortedData.value.length === 0) return { min: 0, max: 100 };
    
    const precips = sortedData.value.map(d => d.precipitation_mm);
    const max = Math.ceil(Math.max(...precips) + 10);
    
    return { min: 0, max };
});

// Generate SVG path for line chart
const generatePath = (data: number[], range: { min: number; max: number }) => {
    if (data.length === 0) return '';
    
    const xStep = (chartWidth - padding.left - padding.right) / (data.length - 1 || 1);
    const yScale = (chartHeight - padding.top - padding.bottom) / (range.max - range.min);
    
    return data.map((value, index) => {
        const x = padding.left + index * xStep;
        const y = chartHeight - padding.bottom - (value - range.min) * yScale;
        return `${index === 0 ? 'M' : 'L'} ${x} ${y}`;
    }).join(' ');
};

const tempAvgPath = computed(() => {
    const data = sortedData.value.map(d => d.temp_avg_celsius);
    return generatePath(data, tempRange.value);
});

const tempMaxPath = computed(() => {
    const data = sortedData.value.map(d => d.temp_max_celsius);
    return generatePath(data, tempRange.value);
});

const tempMinPath = computed(() => {
    const data = sortedData.value.map(d => d.temp_min_celsius);
    return generatePath(data, tempRange.value);
});

const precipPath = computed(() => {
    const data = sortedData.value.map(d => d.precipitation_mm);
    return generatePath(data, precipRange.value);
});

// Y-axis labels
const yAxisLabels = computed(() => {
    const range = chartType.value === 'temperature' ? tempRange.value : precipRange.value;
    const step = (range.max - range.min) / 5;
    
    return Array.from({ length: 6 }, (_, i) => {
        const value = range.min + step * i;
        const y = chartHeight - padding.bottom - ((value - range.min) / (range.max - range.min)) * (chartHeight - padding.top - padding.bottom);
        return { value: value.toFixed(1), y };
    });
});

// X-axis labels
const xAxisLabels = computed(() => {
    const step = Math.ceil(sortedData.value.length / 6);
    return sortedData.value.filter((_, i) => i % step === 0).map((d, i) => {
        const xStep = (chartWidth - padding.left - padding.right) / (sortedData.value.length - 1 || 1);
        const x = padding.left + (i * step) * xStep;
        return {
            label: new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
            x,
        };
    });
});
</script>

<template>
    <div class="rounded-xl border bg-card p-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Weather Trends</h2>
                <p class="text-sm text-muted-foreground">
                    Historical weather patterns
                </p>
            </div>
            
            <div class="flex gap-2">
                <button
                    type="button"
                    :class="[
                        'rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                        chartType === 'temperature'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-muted hover:bg-muted/80',
                    ]"
                    @click="chartType = 'temperature'"
                >
                    Temperature
                </button>
                <button
                    type="button"
                    :class="[
                        'rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                        chartType === 'precipitation'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-muted hover:bg-muted/80',
                    ]"
                    @click="chartType = 'precipitation'"
                >
                    Precipitation
                </button>
            </div>
        </div>
        
        <div v-if="loading" class="flex h-[300px] items-center justify-center">
            <svg class="h-8 w-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
        </div>
        
        <div v-else-if="sortedData.length === 0" class="flex h-[300px] items-center justify-center text-muted-foreground">
            No data to display
        </div>
        
        <div v-else class="overflow-x-auto">
            <svg :width="chartWidth" :height="chartHeight" class="w-full">
                <!-- Grid lines -->
                <g v-for="label in yAxisLabels" :key="label.value">
                    <line
                        :x1="padding.left"
                        :y1="label.y"
                        :x2="chartWidth - padding.right"
                        :y2="label.y"
                        stroke="currentColor"
                        stroke-opacity="0.1"
                        stroke-dasharray="2,2"
                    />
                    <text
                        :x="padding.left - 10"
                        :y="label.y + 4"
                        text-anchor="end"
                        class="fill-current text-xs text-muted-foreground"
                    >
                        {{ label.value }}
                    </text>
                </g>
                
                <!-- X-axis labels -->
                <g v-for="label in xAxisLabels" :key="label.label">
                    <text
                        :x="label.x"
                        :y="chartHeight - padding.bottom + 20"
                        text-anchor="middle"
                        class="fill-current text-xs text-muted-foreground"
                    >
                        {{ label.label }}
                    </text>
                </g>
                
                <!-- Temperature lines -->
                <g v-if="chartType === 'temperature'">
                    <path
                        :d="tempMaxPath"
                        fill="none"
                        stroke="#ef4444"
                        stroke-width="2"
                        stroke-opacity="0.6"
                    />
                    <path
                        :d="tempAvgPath"
                        fill="none"
                        stroke="#3b82f6"
                        stroke-width="3"
                    />
                    <path
                        :d="tempMinPath"
                        fill="none"
                        stroke="#06b6d4"
                        stroke-width="2"
                        stroke-opacity="0.6"
                    />
                </g>
                
                <!-- Precipitation bars -->
                <g v-else>
                    <path
                        :d="precipPath"
                        fill="none"
                        stroke="#3b82f6"
                        stroke-width="2"
                    />
                </g>
            </svg>
            
            <!-- Legend -->
            <div class="mt-4 flex justify-center gap-6 text-sm">
                <div v-if="chartType === 'temperature'" class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-red-500 opacity-60" />
                        <span>Max Temp</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-blue-500" />
                        <span>Avg Temp</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-cyan-500 opacity-60" />
                        <span>Min Temp</span>
                    </div>
                </div>
                <div v-else class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-blue-500" />
                    <span>Precipitation</span>
                </div>
            </div>
        </div>
    </div>
</template>

