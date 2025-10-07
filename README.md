# NOAA Weather Dashboard

Dashboard cuaca interaktif terintegrasi dengan Laravel Vue Starter Kit, menggunakan ClickHouse untuk query data NOAA Global Historical Climatology Network.

## 🚀 Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3 + Inertia.js + TypeScript
- **Database**: ClickHouse (OLAP Database)
- **UI**: TailwindCSS + Reka UI (Laravel Vue Starter Kit)
- **Data**: NOAA GHCN (120+ years of weather data)

## 📋 Features

✅ **Filter berdasarkan Periode**
- Date range picker untuk memilih rentang tanggal
- Default: 30 hari terakhir

✅ **Filter berdasarkan Wilayah**
- Filter by Country (Indonesia, USA, Japan, UK, Australia)
- Filter by Station (Jakarta, New York, Tokyo, London, Sydney)

✅ **Visualisasi Data**
- 📊 Statistics Cards (Total Measurements, Avg/Max/Min Temperature, Precipitation)
- 📈 Interactive Charts (Temperature & Precipitation trends)
- 📋 Data Table dengan detail lengkap

✅ **Real-time Query**
- Query super cepat dengan ClickHouse
- Agregasi data dalam milliseconds

## 🛠️ Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- Docker Desktop (untuk ClickHouse)

### Setup Steps

1. **Clone Repository**
```bash
git clone https://github.com/prassaaa/testCase-Dwara.git
cd testCase-Dwara
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Start ClickHouse**
```bash
docker-compose up -d
```

Tunggu beberapa detik hingga ClickHouse selesai initialize dan load sample data.

5. **Verify ClickHouse**
```bash
docker exec clickhouse-server clickhouse-client --query="SELECT count() FROM weather.noaa"
```

Output harus menunjukkan `30` (30 rows sample data).

6. **Build Assets**
```bash
npm run build
# atau untuk development
npm run dev
```

7. **Start Laravel Server**
```bash
php artisan serve
```

8. **Access Application**
- **Welcome Page**: http://localhost:8000
- **Main Dashboard** (requires auth): http://localhost:8000/dashboard
- **Weather Dashboard** (requires auth): http://localhost:8000/weather

## 🎨 Dashboard Integration

Weather Dashboard sudah **terintegrasi penuh** dengan Laravel Vue Starter Kit:

### **Main Dashboard** (`/dashboard`)
- ✅ Weather summary cards (Avg Temp, Temp Range, Precipitation, Active Stations)
- ✅ Recent weather data (last 5 measurements)
- ✅ Quick actions (links to Weather Dashboard, NOAA docs, ClickHouse playground)
- ✅ Real-time data dari ClickHouse
- ✅ Requires authentication

### **Weather Dashboard** (`/weather`)
- ✅ Full weather analytics page
- ✅ Advanced filters (date range, country, station)
- ✅ Interactive charts (temperature & precipitation trends)
- ✅ Detailed data table
- ✅ Statistics cards
- ✅ Requires authentication

### **Navigation**
- Weather Dashboard menu di sidebar (dengan icon Cloud ☁️)
- Quick link dari Main Dashboard ke Weather Dashboard
- Breadcrumb navigation

## 📊 Sample Data

Project ini sudah include sample data dari 5 kota:
- 🇮🇩 Jakarta, Indonesia
- 🇺🇸 New York, USA
- 🇯🇵 Tokyo, Japan
- 🇬🇧 London, UK
- 🇦🇺 Sydney, Australia

Data mencakup 30 hari terakhir dengan measurements:
- Temperature (Avg, Max, Min)
- Precipitation
- Weather Type
- Wind Speed
- Snow data (untuk kota dengan salju)

## 🔧 ClickHouse Management

### Access ClickHouse Client
```bash
docker exec -it clickhouse-server clickhouse-client
```

### Useful Queries

**Check data count:**
```sql
SELECT count() FROM weather.noaa;
```

**View recent data:**
```sql
SELECT * FROM weather.noaa_readable 
ORDER BY date DESC 
LIMIT 10;
```

**Statistics by station:**
```sql
SELECT 
    station_name,
    avg(temp_avg_celsius) as avg_temp,
    count() as measurements
FROM weather.noaa_readable
GROUP BY station_name;
```

### Load Full NOAA Dataset (Optional)

Jika ingin load full dataset (2.6 billion rows):

```bash
# Download data (6.4GB)
wget https://datasets-documentation.s3.eu-west-3.amazonaws.com/noaa/noaa_enriched.parquet

# Insert to ClickHouse
docker exec -i clickhouse-server clickhouse-client \
  --query="INSERT INTO weather.noaa FORMAT Parquet" \
  < noaa_enriched.parquet
```

## 🎨 UI Components

### Weather Dashboard Components

- **WeatherFilters.vue** - Filter form (date range, country, station)
- **WeatherStats.vue** - Statistics cards
- **WeatherChart.vue** - Line chart untuk temperature & precipitation
- **WeatherTable.vue** - Data table dengan pagination

### API Endpoints

- `GET /weather` - Dashboard page
- `GET /weather/data` - Get weather data (with filters)
- `GET /weather/statistics` - Get aggregated statistics
- `GET /weather/stations` - Get available stations
- `GET /weather/test-connection` - Test ClickHouse connection

## 🧪 Testing

### Test ClickHouse Connection
```bash
curl http://localhost:8000/weather/test-connection
```

### Test API Endpoints
```bash
# Get weather data
curl "http://localhost:8000/weather/data?start_date=2024-01-01&end_date=2024-01-31"

# Get statistics
curl "http://localhost:8000/weather/statistics?country=ID"

# Get stations
curl http://localhost:8000/weather/stations
```

## 📁 Project Structure

```
testCase-Dwara/
├── app/
│   ├── Http/Controllers/
│   │   └── WeatherDashboardController.php
│   └── Services/
│       └── ClickHouseService.php
├── clickhouse/
│   ├── init/
│   │   ├── 01-create-table.sql
│   │   └── 02-insert-sample-data.sql
│   └── README.md
├── config/
│   └── clickhouse.php
├── resources/
│   └── js/
│       ├── components/
│       │   └── weather/
│       │       ├── WeatherFilters.vue
│       │       ├── WeatherStats.vue
│       │       ├── WeatherChart.vue
│       │       └── WeatherTable.vue
│       └── pages/
│           └── WeatherDashboard.vue
├── docker-compose.yml
└── README.md
```

## 🐛 Troubleshooting

### ClickHouse tidak bisa connect
```bash
# Check container status
docker-compose ps

# Check logs
docker-compose logs clickhouse

# Restart container
docker-compose restart clickhouse
```

### Build error (lightningcss/oxide)
```bash
npm install lightningcss-win32-x64-msvc --save-optional
npm install @tailwindcss/oxide-win32-x64-msvc --save-optional
npm run build
```

### No data showing
```bash
# Verify data exists
docker exec clickhouse-server clickhouse-client \
  --query="SELECT count() FROM weather.noaa"

# Re-run init scripts
docker-compose down -v
docker-compose up -d
```

## 📚 Resources

- [ClickHouse Documentation](https://clickhouse.com/docs)
- [NOAA GHCN Dataset](https://clickhouse.com/docs/getting-started/example-datasets/noaa)
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
