import dotenv from 'dotenv';
import fetch from 'node-fetch';

dotenv.config();

const API_URL = process.env.API_URL || 'http://localhost:3000';

// Test data
const sampleTripData = {
    // New York to Los Angeles route
    user_id: 1,
    start_lat: 40.7128,
    start_lng: -74.0060,
    end_lat: 34.0522,
    end_lng: -118.2437,
    truck_mpg: "6.5",
    fuel_tank_capacity: "150",
    total_gallons_present: "100",
    reserve_fuel: "20"
};

async function testTripCalculation() {
    console.log('Testing Trip Calculation API...\n');
    
    try {
        console.log('Test Data:', JSON.stringify(sampleTripData, null, 2), '\n');
        
        const response = await fetch(`${API_URL}/api/trip/calculate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(sampleTripData)
        });

        const data = await response.json();
        
        console.log('API Response Status:', response.status);
        console.log('Response Data:', JSON.stringify(data, null, 2));
        
        // Validate response structure
        if (data.status === 200) {
            console.log('\nValidation Results:');
            console.log('✓ Response status is 200');
            
            const routeData = data.data.route_data;
            if (routeData) {
                console.log('✓ Route data is present');
                console.log(`✓ Distance: ${routeData.distance}`);
                console.log(`✓ Duration: ${routeData.duration}`);
                console.log(`✓ Start Address: ${routeData.start_address}`);
                console.log(`✓ End Address: ${routeData.end_address}`);
            }
            
            const fuelStations = data.data.fuel_stations;
            if (fuelStations && Array.isArray(fuelStations)) {
                console.log(`✓ Found ${fuelStations.length} fuel stations`);
                console.log('\nSample Fuel Station Data:');
                console.log(JSON.stringify(fuelStations[0], null, 2));
            }
        } else {
            console.log('\n❌ Test Failed:');
            console.log('Error:', data.message);
        }
        
    } catch (error) {
        console.error('\n❌ Test Error:', error.message);
    }
}

// Run the test
testTripCalculation(); 