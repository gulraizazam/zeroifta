import dotenv from 'dotenv';
import { loadAndParseFTPData } from './utils/fuelStations.js';

dotenv.config();

// Test coordinates (example route point)
const testPolyline = [{
    lat: 40.7128,  // New York City coordinates
    lng: -74.0060
}];

async function testFTPConnection() {
    try {
        console.log('Testing FTP connection and data fetching...');
        const fuelStations = await loadAndParseFTPData(testPolyline);
        
        console.log('\nSample of fuel stations found:');
        console.log(fuelStations.slice(0, 3));
        
        console.log(`\nTotal fuel stations found: ${fuelStations.length}`);
    } catch (error) {
        console.error('Test failed:', error);
    }
}

testFTPConnection(); 