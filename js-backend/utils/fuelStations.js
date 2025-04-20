import { Client } from 'basic-ftp';
import { calculateHaversineDistance } from './distance.js';

// Cache for FTP data to avoid frequent fetches
let ftpDataCache = {
    data: null,
    timestamp: null,
    expiryTime: 30 * 60 * 1000 // 30 minutes
};

// Optimized bounding box calculation
function createBoundingBox(polyline, radiusMiles = 12) {
    let minLat = polyline[0].lat;
    let maxLat = polyline[0].lat;
    let minLng = polyline[0].lng;
    let maxLng = polyline[0].lng;

    // Sample points from polyline (take every 10th point)
    for (let i = 0; i < polyline.length; i += 10) {
        const point = polyline[i];
        minLat = Math.min(minLat, point.lat);
        maxLat = Math.max(maxLat, point.lat);
        minLng = Math.min(minLng, point.lng);
        maxLng = Math.max(maxLng, point.lng);
    }

    // Add buffer for radius (approximate conversion)
    const latBuffer = radiusMiles / 69;
    const lngBuffer = radiusMiles / (Math.cos(minLat * Math.PI / 180) * 69);

    return {
        minLat: minLat - latBuffer,
        maxLat: maxLat + latBuffer,
        minLng: minLng - lngBuffer,
        maxLng: maxLng + lngBuffer
    };
}

function isInBoundingBox(lat, lng, box) {
    return lat >= box.minLat && lat <= box.maxLat && 
           lng >= box.minLng && lng <= box.maxLng;
}

async function fetchFTPData() {
    // Check cache first
    const now = Date.now();
    if (ftpDataCache.data && ftpDataCache.timestamp && 
        (now - ftpDataCache.timestamp) < ftpDataCache.expiryTime) {
        console.log('Using cached FTP data');
        return ftpDataCache.data;
    }

    const client = new Client();
    client.ftp.verbose = false;

    try {
        console.log('Fetching fresh FTP data...');
        await client.access({
            host: process.env.FTP_HOST,
            user: process.env.FTP_USERNAME,
            password: process.env.FTP_PASSWORD,
            port: parseInt(process.env.FTP_PORT),
            secure: false
        });
        
        const tempFile = './temp_ftp_data.txt';
        await client.downloadTo(tempFile, process.env.FTP_FILE);
        
        const fs = await import('fs/promises');
        const content = await fs.readFile(tempFile, 'utf8');
        await fs.unlink(tempFile);

        // Update cache
        ftpDataCache.data = content;
        ftpDataCache.timestamp = now;
        
        return content;
    } catch (err) {
        console.error('FTP Error:', err);
        throw new Error(`Failed to fetch FTP data: ${err.message}`);
    } finally {
        await client.close();
    }
}

export async function loadAndParseFTPData(decodedPolyline) {
    try {
        const fileContent = await fetchFTPData();
        if (!fileContent) return [];

        // Create optimized bounding box
        const boundingBox = createBoundingBox(decodedPolyline);
        
        // Pre-process polyline for distance calculations
        const samplePoints = [];
        for (let i = 0; i < decodedPolyline.length; i += 20) { // Sample every 20th point
            samplePoints.push(decodedPolyline[i]);
        }
        if (samplePoints[samplePoints.length - 1] !== decodedPolyline[decodedPolyline.length - 1]) {
            samplePoints.push(decodedPolyline[decodedPolyline.length - 1]);
        }

        // Process FTP data in chunks
        const chunkSize = 1000;
        const rows = fileContent.trim().split('\n');
        const filteredData = [];
        const uniqueRecords = new Map();

        for (let i = 0; i < rows.length; i += chunkSize) {
            const chunk = rows.slice(i, i + chunkSize);
            
            chunk.forEach(line => {
                const row = line.split('|');
                if (!row[8] || !row[9]) return;

                const lat = Number(row[8]);
                const lng = Number(row[9]);
                if (isNaN(lat) || isNaN(lng)) return;

                if (!isInBoundingBox(lat, lng, boundingBox)) return;

                const uniqueKey = `${lat.toFixed(4)},${lng.toFixed(4)}`;
                if (uniqueRecords.has(uniqueKey)) return;

                // Find minimum distance to sampled points
                let minDistance = Number.POSITIVE_INFINITY;
                for (const point of samplePoints) {
                    const distance = calculateHaversineDistance(point.lat, point.lng, lat, lng);
                    if (distance < 12) { // Early exit if within range
                        minDistance = distance;
                        break;
                    }
                    minDistance = Math.min(minDistance, distance);
                }

                if (minDistance < 12) {
                    filteredData.push({
                        fuel_station_name: row[1] || 'N/A',
                        ftpLat: lat.toFixed(4),
                        ftpLng: lng.toFixed(4),
                        lastprice: parseFloat(row[10]) || 0.00,
                        price: parseFloat(row[11]) || 0.00,
                        discount: parseFloat(row[12]) || 0.00,
                        IFTA_tax: parseFloat(row[18]) || 0.00,
                        address: row[3] || 'N/A'
                    });
                    uniqueRecords.set(uniqueKey, true);
                }
            });
        }

        return filteredData;
    } catch (error) {
        console.error('Error loading FTP data:', error);
        return [];
    }
}

export function calculateFuelRequirements(station, tripData) {
    const { mpg, fuelLeft } = tripData.vehicle;
    const distanceToStation = station.distanceFromStart;
    const fuelNeeded = distanceToStation / mpg;
    
    return fuelLeft < fuelNeeded ? 
        { ...station, gallons_to_buy: fuelNeeded - fuelLeft } : 
        station;
} 