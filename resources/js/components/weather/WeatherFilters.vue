<script setup lang="ts">
import { ref, watch } from 'vue';

interface Props {
    stations: Array<{
        station_id: string;
        station_name: string;
        country_code: string;
    }>;
    dateRange: {
        min_date: string | null;
        max_date: string | null;
    };
    filters: {
        start_date: string;
        end_date: string;
        station_id: string;
        region: string;
        country: string;
        limit: number;
    };
    loading: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'filter-change', filters: any): void;
    (e: 'reset'): void;
}>();

const localFilters = ref({ ...props.filters });

watch(
    () => props.filters,
    (newFilters) => {
        localFilters.value = { ...newFilters };
    },
    { deep: true }
);

const applyFilters = () => {
    emit('filter-change', localFilters.value);
};

const resetFilters = () => {
    emit('reset');
};

// Get unique countries from stations
const countries = ref<Array<{ code: string; name: string }>>([
    { code: 'ID', name: 'Indonesia' },
    { code: 'US', name: 'United States' },
    { code: 'JA', name: 'Japan' },
    { code: 'UK', name: 'United Kingdom' },
    { code: 'AS', name: 'Australia' },
]);
</script>

<template>
    <div class="rounded-xl border bg-card p-4">
        <h2 class="mb-4 text-lg font-semibold">Filters</h2>
        
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Start Date -->
            <div class="space-y-2">
                <label class="text-sm font-medium" for="start_date">
                    Start Date
                </label>
                <input
                    id="start_date"
                    v-model="localFilters.start_date"
                    type="date"
                    :min="dateRange.min_date || undefined"
                    :max="dateRange.max_date || undefined"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                />
            </div>

            <!-- End Date -->
            <div class="space-y-2">
                <label class="text-sm font-medium" for="end_date">
                    End Date
                </label>
                <input
                    id="end_date"
                    v-model="localFilters.end_date"
                    type="date"
                    :min="localFilters.start_date || dateRange.min_date || undefined"
                    :max="dateRange.max_date || undefined"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                />
            </div>

            <!-- Country -->
            <div class="space-y-2">
                <label class="text-sm font-medium" for="country">
                    Country
                </label>
                <select
                    id="country"
                    v-model="localFilters.country"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                >
                    <option value="">All Countries</option>
                    <option
                        v-for="country in countries"
                        :key="country.code"
                        :value="country.code"
                    >
                        {{ country.name }}
                    </option>
                </select>
            </div>

            <!-- Station -->
            <div class="space-y-2">
                <label class="text-sm font-medium" for="station">
                    Station
                </label>
                <select
                    id="station"
                    v-model="localFilters.station_id"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                >
                    <option value="">All Stations</option>
                    <option
                        v-for="station in stations"
                        :key="station.station_id"
                        :value="station.station_id"
                    >
                        {{ station.station_name }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex gap-2">
            <button
                type="button"
                class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                :disabled="loading"
                @click="applyFilters"
            >
                <svg
                    v-if="loading"
                    class="mr-2 h-4 w-4 animate-spin"
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
                Apply Filters
            </button>
            
            <button
                type="button"
                class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                :disabled="loading"
                @click="resetFilters"
            >
                Reset
            </button>
        </div>
    </div>
</template>

