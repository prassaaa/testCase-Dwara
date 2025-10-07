# Weather Dashboard Integration Guide

Dokumen ini menjelaskan bagaimana Weather Dashboard terintegrasi dengan Laravel Vue Starter Kit.

## 📐 Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────┐         ┌──────────────────────────┐      │
│  │   Routes     │────────▶│  WeatherDashboard        │      │
│  │  (web.php)   │         │  Controller              │      │
│  └──────────────┘         └──────────┬───────────────┘      │
│                                       │                     │
│                                       ▼                     │
│                           ┌──────────────────────┐          │
│                           │  ClickHouseService   │          │
│                           └──────────┬───────────┘          │
│                                      │                      │
└──────────────────────────────────────┼──────────────────────┘
                                       │
                                       ▼
                           ┌──────────────────────┐
                           │   ClickHouse DB      │
                           │   (Docker)           │
                           └──────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    Vue.js Frontend                           │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────┐       ┌──────────────────────┐        │
│  │  Dashboard.vue   │       │  WeatherDashboard    │        │
│  │  (Main)          │       │  .vue (Full)         │        │
│  │                  │       │                      │        │
│  │  - Summary Cards │       │  - Filters           │        │
│  │  - Recent Data   │       │  - Charts            │        │
│  │  - Quick Actions │       │  - Data Table        │        │
│  └──────────────────┘       └──────────────────────┘        │
│                                                               │
│  ┌──────────────────────────────────────────────────┐        │
│  │         Weather Components                       │        │
│  │  - WeatherFilters.vue                            │        │
│  │  - WeatherStats.vue                              │        │
│  │  - WeatherChart.vue                              │        │
│  │  - WeatherTable.vue                              │        │
│  └──────────────────────────────────────────────────┘        │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## 🔗 Integration Points

### 1. **Backend Integration**

#### Routes (`routes/web.php`)
```php
// Main Dashboard (with auth)
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Weather Dashboard Routes (requires authentication)
Route::prefix('weather')->name('weather.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [WeatherDashboardController::class, 'index']);
    Route::get('/data', [WeatherDashboardController::class, 'getData']);
    Route::get('/statistics', [WeatherDashboardController::class, 'getStatistics']);
    Route::get('/stations', [WeatherDashboardController::class, 'getStations']);
});
```

#### Controller (`app/Http/Controllers/WeatherDashboardController.php`)
- `index()` - Render Weather Dashboard page
- `getData()` - API endpoint untuk weather data dengan filters
- `getStatistics()` - API endpoint untuk agregasi statistik
- `getStations()` - API endpoint untuk list stasiun

#### Service (`app/Services/ClickHouseService.php`)
- `getWeatherData($filters)` - Query data dengan filter
- `getWeatherStatistics($filters)` - Query agregasi
- `getAvailableStations()` - Get list stasiun
- `getDateRange()` - Get rentang tanggal data

### 2. **Frontend Integration**

#### Main Dashboard (`resources/js/pages/Dashboard.vue`)
**Features:**
- Weather summary cards (4 cards)
- Recent weather data (5 latest measurements)
- Quick actions (links to full dashboard)
- Real-time data fetch dari API

**API Calls:**
```typescript
// Fetch last 7 days statistics
GET /weather/statistics?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD

// Fetch recent data
GET /weather/data?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD&limit=5
```

#### Weather Dashboard (`resources/js/pages/WeatherDashboard.vue`)
**Features:**
- Advanced filters (date range, country, station)
- Interactive charts (temperature & precipitation)
- Detailed data table
- Statistics cards

**Components Used:**
- `WeatherFilters.vue` - Filter form
- `WeatherStats.vue` - Statistics cards
- `WeatherChart.vue` - Line charts
- `WeatherTable.vue` - Data table

#### Navigation (`resources/js/components/AppSidebar.vue`)
```typescript
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Weather Dashboard',
        href: '/weather',
        icon: Cloud, // ☁️
    },
];
```

### 3. **Data Flow**

#### Main Dashboard Data Flow
```
User visits /dashboard
    ↓
Dashboard.vue mounted
    ↓
Fetch last 7 days data
    ↓
API: GET /weather/statistics + /weather/data
    ↓
WeatherDashboardController
    ↓
ClickHouseService
    ↓
ClickHouse DB
    ↓
Return JSON response
    ↓
Display in cards & recent data list
```

#### Weather Dashboard Data Flow
```
User visits /weather
    ↓
WeatherDashboard.vue mounted
    ↓
Load initial data (last 30 days)
    ↓
User applies filters
    ↓
API: GET /weather/data + /weather/statistics (with filters)
    ↓
Update charts, table, and stats
```

## 🎨 UI Components Reused

Weather Dashboard menggunakan komponen dari Laravel Vue Starter Kit:

### From Starter Kit:
- ✅ `AppLayout.vue` - Main layout dengan sidebar & header
- ✅ `AppSidebar.vue` - Sidebar navigation
- ✅ TailwindCSS classes - Consistent styling
- ✅ Dark mode support - Automatic theme switching
- ✅ Responsive design - Mobile-friendly

### Custom Weather Components:
- ✅ `WeatherFilters.vue` - Filter form
- ✅ `WeatherStats.vue` - Statistics cards
- ✅ `WeatherChart.vue` - SVG line charts
- ✅ `WeatherTable.vue` - Data table

## 🔐 Authentication

### Main Dashboard (`/dashboard`)
- **Requires authentication** via middleware `['auth', 'verified']`
- User must login to access
- Shows personalized weather summary

### Weather Dashboard (`/weather`)
- **Requires authentication** via middleware `['auth', 'verified']`
- User must login to access
- Full analytics features available

## 📊 Data Management

### Sample Data
- 30 rows dari 5 kota (Jakarta, New York, Tokyo, London, Sydney)
- 30 hari data (generated dengan variasi)
- Loaded automatically saat ClickHouse start

### Full Dataset (Optional)
- 2.6 billion rows
- 120+ tahun data
- Download & import manual (lihat README)

## 🚀 Deployment Considerations

### Development
```bash
# Start ClickHouse
docker-compose up -d

# Start Laravel
php artisan serve

# Watch assets (optional)
npm run dev
```

### Production
1. **ClickHouse**: Deploy container atau managed service
2. **Laravel**: Standard Laravel deployment
3. **Assets**: Build dengan `npm run build`
4. **Environment**: Set ClickHouse credentials di `.env`

### Environment Variables
```env
CLICKHOUSE_HOST=localhost
CLICKHOUSE_PORT=8123
CLICKHOUSE_USERNAME=default
CLICKHOUSE_PASSWORD=clickhouse
CLICKHOUSE_DATABASE=weather
```

## 🧪 Testing

### Test ClickHouse Connection
```bash
curl http://localhost:8000/weather/test-connection
```

### Test API Endpoints
```bash
# Statistics
curl "http://localhost:8000/weather/statistics?country=ID"

# Data with filters
curl "http://localhost:8000/weather/data?start_date=2024-01-01&end_date=2024-01-31&station_id=IDM00096745"
```

### Test UI
1. Login to the application
2. Visit http://localhost:8000/dashboard
3. Check weather summary cards
4. Click "View Full Dashboard"
5. Visit http://localhost:8000/weather
6. Test filters and charts

## 📝 Customization

### Add More Filters
Edit `WeatherFilters.vue`:
```vue
<select v-model="localFilters.new_filter">
    <option value="">All</option>
    <!-- Add options -->
</select>
```

Update `ClickHouseService.php`:
```php
if (!empty($filters['new_filter'])) {
    $conditions[] = "column = :new_filter";
    $params['new_filter'] = $filters['new_filter'];
}
```

### Add More Charts
Create new component in `resources/js/components/weather/`:
```vue
<script setup lang="ts">
// Chart logic
</script>

<template>
    <!-- Chart UI -->
</template>
```

Import in `WeatherDashboard.vue`:
```typescript
import NewChart from '@/components/weather/NewChart.vue';
```

## 🎯 Best Practices

1. **Performance**
   - Use ClickHouse for heavy aggregations
   - Limit query results (default: 100 rows)
   - Cache frequent queries (optional: Redis)

2. **Security**
   - Validate all user inputs
   - Use parameterized queries
   - Rate limit API endpoints (optional)

3. **UX**
   - Show loading states
   - Handle errors gracefully
   - Provide empty states

4. **Code Organization**
   - Keep components small and focused
   - Reuse starter kit components
   - Follow Laravel & Vue best practices

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [ClickHouse Documentation](https://clickhouse.com/docs)
- [NOAA Dataset](https://clickhouse.com/docs/getting-started/example-datasets/noaa)

