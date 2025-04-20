import dotenv from 'dotenv';
import fetch from 'node-fetch';

dotenv.config();

// Test coordinates
const testData = {
    // New York to Los Angeles coordinates
    start_lat: 40.7128,
    start_lng: -74.0060,
    end_lat: 34.0522,
    end_lng: -118.2437
};

async function testGoogleMapsAPI() {
    try {
        console.log('Testing Google Maps API route calculation...');
        
        const apiKey = process.env.GOOGLE_MAPS_API_KEY;
        const url = `https://maps.googleapis.com/maps/api/directions/json?origin=${testData.start_lat},${testData.start_lng}&destination=${testData.end_lat},${testData.end_lng}&key=${apiKey}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.status === 'OK' && data.routes && data.routes[0]) {
            const route = data.routes[0];
            
            // Calculate total distance and duration
            let totalDistance = 0;
            let totalDuration = 0;

            route.legs.forEach(leg => {
                totalDistance += leg.distance.value; // Distance in meters
                totalDuration += leg.duration.value; // Duration in seconds
            });

            // Convert meters to miles
            const totalDistanceMiles = (totalDistance * 0.000621371).toFixed(2);

            // Convert seconds to hours and minutes
            const hours = Math.floor(totalDuration / 3600);
            const minutes = Math.floor((totalDuration % 3600) / 60);

            console.log('\nRoute Details:');
            console.log('------------------');
            console.log(`Total Distance: ${totalDistanceMiles} miles`);
            console.log(`Estimated Duration: ${hours} hours ${minutes} minutes`);
            
            console.log('\nRoute Overview:');
            console.log('------------------');
            route.legs[0].steps.slice(0, 3).forEach((step, index) => {
                console.log(`Step ${index + 1}: ${step.html_instructions.replace(/<[^>]*>/g, '')}`);
            });
            console.log('... (more steps)');

            if (route.overview_polyline) {
                console.log('\nPolyline Data:');
                console.log('------------------');
                console.log('Overview Polyline:', route.overview_polyline.points.slice(0, 50) + '...');
            }

        } else {
            console.error('Error:', data.status, data.error_message || 'No route found');
        }
    } catch (error) {
        console.error('Test failed:', error);
    }
}

testGoogleMapsAPI(); 