import dotenv from 'dotenv';
import fetch from 'node-fetch';

dotenv.config();

const PHP_API_URL = process.env.PHP_API_URL || 'http://localhost:8000/api';
const NODE_API_URL = process.env.API_URL || 'http://localhost:3000';

// Test data
const sampleTripData = {
    user_id: 1,
    start_lat: 40.7128,
    start_lng: -74.0060,
    end_lat: 34.0522,
    end_lng: -118.2437,
    truck_mpg: "6.5",
    fuel_tank_capacity: "150",
    total_gallons_present: "100",
    reserve_fuel: "20",
    vehicle_id: 1  // Make sure this exists in your database
};

async function testCompleteFlow() {
    console.log('Testing Complete Trip API Flow...\n');
    
    try {
        // 1. Test Node.js Calculation Endpoint
        console.log('1. Testing Node.js Trip Calculation...');
        const nodeResponse = await fetch(`${NODE_API_URL}/api/trip/calculate`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(sampleTripData)
        });

        const nodeData = await nodeResponse.json();
        console.log('Node.js API Status:', nodeResponse.status);
        if (nodeResponse.status === 200) {
            console.log('✓ Node.js calculation successful');
        } else {
            console.log('❌ Node.js calculation failed:', nodeData.message);
            return;
        }

        // 2. Test PHP Trip Start Endpoint
        console.log('\n2. Testing PHP Trip Start API...');
        const phpResponse = await fetch(`${PHP_API_URL}/trip/start`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(sampleTripData)
        });

        const phpData = await phpResponse.json();
        console.log('PHP API Status:', phpResponse.status);
        
        if (phpResponse.status === 200) {
            console.log('\nValidation Results:');
            console.log('✓ Trip created successfully');
            
            const tripData = phpData.data;
            if (tripData) {
                console.log('✓ Trip ID:', tripData.trip_id);
                console.log('✓ Route data present');
                console.log(`✓ Found ${tripData.fuel_stations.length} fuel stations`);
                
                // Validate vehicle data
                if (tripData.vehicle) {
                    console.log('✓ Vehicle data present');
                    console.log('Vehicle MPG:', tripData.vehicle.mpg);
                    console.log('Fuel Left:', tripData.vehicle.fuel_left);
                }

                // Print sample fuel station
                if (tripData.fuel_stations.length > 0) {
                    console.log('\nSample Optimal Fuel Station:');
                    const optimalStation = tripData.fuel_stations.find(s => s.is_optimal);
                    if (optimalStation) {
                        console.log(JSON.stringify(optimalStation, null, 2));
                    }
                }
            }
        } else {
            console.log('\n❌ Trip creation failed:');
            console.log('Error:', phpData.message);
        }

    } catch (error) {
        console.error('\n❌ Test Error:', error.message);
    }
}

// Run the complete test
console.log('Starting API Tests...\n');
testCompleteFlow(); 