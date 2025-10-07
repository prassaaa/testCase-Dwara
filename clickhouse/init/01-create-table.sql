-- Create database if not exists
CREATE DATABASE IF NOT EXISTS weather;

-- Use the weather database
USE weather;

-- Create the NOAA weather table
CREATE TABLE IF NOT EXISTS noaa
(
    station_id LowCardinality(String) COMMENT 'Weather station ID',
    date Date32 COMMENT 'Measurement date',
    tempAvg Int32 COMMENT 'Average temperature (tenths of degrees C)',
    tempMax Int32 COMMENT 'Maximum temperature (tenths of degrees C)',
    tempMin Int32 COMMENT 'Minimum temperature (tenths of degrees C)',
    precipitation UInt32 COMMENT 'Precipitation (tenths of mm)',
    snowfall UInt32 COMMENT 'Snowfall (mm)',
    snowDepth UInt32 COMMENT 'Snow depth (mm)',
    percentDailySun UInt8 COMMENT 'Daily percent of possible sunshine (percent)',
    averageWindSpeed UInt32 COMMENT 'Average daily wind speed (tenths of meters per second)',
    maxWindSpeed UInt32 COMMENT 'Peak gust wind speed (tenths of meters per second)',
    weatherType Enum8(
        'Normal' = 0, 
        'Fog' = 1, 
        'Heavy Fog' = 2, 
        'Thunder' = 3, 
        'Small Hail' = 4, 
        'Hail' = 5, 
        'Glaze' = 6, 
        'Dust/Ash' = 7, 
        'Smoke/Haze' = 8, 
        'Blowing/Drifting Snow' = 9, 
        'Tornado' = 10, 
        'High Winds' = 11, 
        'Blowing Spray' = 12, 
        'Mist' = 13, 
        'Drizzle' = 14, 
        'Freezing Drizzle' = 15, 
        'Rain' = 16, 
        'Freezing Rain' = 17, 
        'Snow' = 18, 
        'Unknown Precipitation' = 19, 
        'Ground Fog' = 21, 
        'Freezing Fog' = 22
    ) COMMENT 'Weather type',
    location Tuple(Float64, Float64) COMMENT 'Location coordinates (longitude, latitude)',
    elevation Float32 COMMENT 'Station elevation in meters',
    name LowCardinality(String) COMMENT 'Station name'
) 
ENGINE = MergeTree()
ORDER BY (station_id, date)
COMMENT 'NOAA Global Historical Climatology Network weather data';

-- Create a view for easier querying with converted units
CREATE OR REPLACE VIEW noaa_readable AS
SELECT
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
FROM noaa;

