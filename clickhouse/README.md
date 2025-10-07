# ClickHouse Setup for NOAA Weather Dashboard

## 📦 Quick Start

### 1. Start ClickHouse Server
```bash
docker-compose up -d
```

### 2. Check if ClickHouse is Running
```bash
docker-compose ps
```

### 3. Access ClickHouse Client
```bash
docker exec -it clickhouse-server clickhouse-client
```

## 🔌 Connection Details

- **HTTP Interface**: http://localhost:8123
- **Native Port**: 9000
- **Database**: weather
- **Username**: default
- **Password**: clickhouse

## 📊 Sample Queries

### Check if data is loaded
```sql
USE weather;
SELECT count() FROM noaa;
```

### Get weather data for Jakarta
```sql
SELECT * FROM noaa_readable 
WHERE station_name LIKE '%JAKARTA%' 
ORDER BY date DESC 
LIMIT 10;
```

### Get average temperature by location
```sql
SELECT 
    station_name,
    avg(temp_avg_celsius) as avg_temp,
    count() as measurements
FROM noaa_readable
GROUP BY station_name
ORDER BY avg_temp DESC;
```

### Filter by date range
```sql
SELECT * FROM noaa_readable
WHERE date BETWEEN '2024-01-01' AND '2024-01-31'
ORDER BY date DESC;
```

## 🛠️ Useful Commands

### Stop ClickHouse
```bash
docker-compose down
```

### Stop and remove all data
```bash
docker-compose down -v
```

### View logs
```bash
docker-compose logs -f clickhouse
```

### Restart ClickHouse
```bash
docker-compose restart
```

## 📥 Loading Real NOAA Data

If you want to load the full NOAA dataset:

```bash
# Download the pre-prepared Parquet file (6.4GB)
wget https://datasets-documentation.s3.eu-west-3.amazonaws.com/noaa/noaa_enriched.parquet

# Insert into ClickHouse
docker exec -i clickhouse-server clickhouse-client --query="INSERT INTO weather.noaa FORMAT Parquet" < noaa_enriched.parquet
```

## 🔍 Testing Connection from PHP

```php
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8123/?query=SELECT%20version()");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "default:clickhouse");
$result = curl_exec($ch);
curl_close($ch);
echo $result;
```

