-- Insert sample data for testing
-- This creates realistic weather data for various locations

USE weather;

-- Sample data for Jakarta, Indonesia
INSERT INTO noaa VALUES
('IDM00096745', '2025-01-01', 280, 320, 240, 150, 0, 0, 60, 25, 40, 'Normal', (106.8456, -6.2088), 8, 'JAKARTA OBSERVATORY'),
('IDM00096745', '2025-01-02', 285, 325, 245, 200, 0, 0, 55, 30, 45, 'Rain', (106.8456, -6.2088), 8, 'JAKARTA OBSERVATORY'),
('IDM00096745', '2025-01-03', 275, 315, 235, 100, 0, 0, 70, 20, 35, 'Normal', (106.8456, -6.2088), 8, 'JAKARTA OBSERVATORY'),
('IDM00096745', '2025-01-04', 290, 330, 250, 180, 0, 0, 50, 28, 42, 'Rain', (106.8456, -6.2088), 8, 'JAKARTA OBSERVATORY'),
('IDM00096745', '2025-01-05', 282, 322, 242, 120, 0, 0, 65, 22, 38, 'Normal', (106.8456, -6.2088), 8, 'JAKARTA OBSERVATORY');

-- Sample data for New York, USA
INSERT INTO noaa VALUES
('USW00094728', '2025-01-01', 20, 50, -10, 50, 100, 150, 40, 45, 70, 'Snow', (-73.9712, 40.7831), 10, 'NEW YORK CENTRAL PARK'),
('USW00094728', '2025-01-02', 15, 45, -15, 30, 80, 200, 45, 40, 65, 'Snow', (-73.9712, 40.7831), 10, 'NEW YORK CENTRAL PARK'),
('USW00094728', '2025-01-03', 25, 55, -5, 20, 50, 180, 50, 35, 60, 'Normal', (-73.9712, 40.7831), 10, 'NEW YORK CENTRAL PARK'),
('USW00094728', '2025-01-04', 30, 60, 0, 0, 0, 150, 60, 30, 55, 'Normal', (-73.9712, 40.7831), 10, 'NEW YORK CENTRAL PARK'),
('USW00094728', '2025-01-05', 18, 48, -12, 40, 90, 170, 35, 42, 68, 'Snow', (-73.9712, 40.7831), 10, 'NEW YORK CENTRAL PARK');

-- Sample data for Tokyo, Japan
INSERT INTO noaa VALUES
('JA000047662', '2025-01-01', 80, 120, 40, 20, 0, 0, 70, 30, 50, 'Normal', (139.6917, 35.6895), 40, 'TOKYO'),
('JA000047662', '2025-01-02', 75, 115, 35, 50, 0, 0, 60, 35, 55, 'Rain', (139.6917, 35.6895), 40, 'TOKYO'),
('JA000047662', '2025-01-03', 85, 125, 45, 10, 0, 0, 75, 25, 45, 'Normal', (139.6917, 35.6895), 40, 'TOKYO'),
('JA000047662', '2025-01-04', 78, 118, 38, 30, 0, 0, 65, 32, 52, 'Rain', (139.6917, 35.6895), 40, 'TOKYO'),
('JA000047662', '2025-01-05', 82, 122, 42, 15, 0, 0, 72, 28, 48, 'Normal', (139.6917, 35.6895), 40, 'TOKYO');

-- Sample data for London, UK
INSERT INTO noaa VALUES
('UKM00003772', '2025-01-01', 60, 90, 30, 80, 0, 0, 30, 40, 65, 'Rain', (-0.1278, 51.5074), 11, 'LONDON HEATHROW'),
('UKM00003772', '2025-01-02', 55, 85, 25, 100, 0, 0, 25, 45, 70, 'Rain', (-0.1278, 51.5074), 11, 'LONDON HEATHROW'),
('UKM00003772', '2025-01-03', 65, 95, 35, 60, 0, 0, 40, 35, 60, 'Rain', (-0.1278, 51.5074), 11, 'LONDON HEATHROW'),
('UKM00003772', '2025-01-04', 70, 100, 40, 40, 0, 0, 50, 30, 55, 'Normal', (-0.1278, 51.5074), 11, 'LONDON HEATHROW'),
('UKM00003772', '2025-01-05', 58, 88, 28, 90, 0, 0, 28, 42, 68, 'Rain', (-0.1278, 51.5074), 11, 'LONDON HEATHROW');

-- Sample data for Sydney, Australia
INSERT INTO noaa VALUES
('ASN00066062', '2025-01-01', 240, 280, 200, 30, 0, 0, 80, 35, 55, 'Normal', (151.2093, -33.8688), 39, 'SYDNEY OBSERVATORY HILL'),
('ASN00066062', '2025-01-02', 245, 285, 205, 20, 0, 0, 85, 30, 50, 'Normal', (151.2093, -33.8688), 39, 'SYDNEY OBSERVATORY HILL'),
('ASN00066062', '2025-01-03', 238, 278, 198, 50, 0, 0, 75, 38, 58, 'Rain', (151.2093, -33.8688), 39, 'SYDNEY OBSERVATORY HILL'),
('ASN00066062', '2025-01-04', 242, 282, 202, 25, 0, 0, 82, 32, 52, 'Normal', (151.2093, -33.8688), 39, 'SYDNEY OBSERVATORY HILL'),
('ASN00066062', '2025-01-05', 248, 288, 208, 15, 0, 0, 88, 28, 48, 'Normal', (151.2093, -33.8688), 39, 'SYDNEY OBSERVATORY HILL');

-- Add more dates for better testing (generate data for 9 months: Jan-Oct 2025)
-- Generate data from Jan 6 to Oct 7, 2025 (275 days)
-- Total: 5 stations × 280 days = 1,400 rows
INSERT INTO noaa
SELECT
    t.station_id,
    addDays(toDate('2025-01-05'), n.number + 1) AS date,
    t.tempAvg + (rand() % 20 - 10) AS tempAvg,
    t.tempMax + (rand() % 20 - 10) AS tempMax,
    t.tempMin + (rand() % 20 - 10) AS tempMin,
    t.precipitation + (rand() % 50) AS precipitation,
    t.snowfall,
    t.snowDepth,
    t.percentDailySun + (rand() % 20 - 10) AS percentDailySun,
    t.averageWindSpeed + (rand() % 10 - 5) AS averageWindSpeed,
    t.maxWindSpeed + (rand() % 10 - 5) AS maxWindSpeed,
    t.weatherType,
    t.location,
    t.elevation,
    t.name
FROM (SELECT * FROM noaa WHERE date = '2025-01-05') AS t
CROSS JOIN (SELECT number FROM numbers(275)) AS n;

