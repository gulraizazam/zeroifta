const express = require('express');
const http = require('http');
const axios = require('axios'); // For making API calls
const polyline = require('@mapbox/polyline'); // For decoding polylines
const app = express();
const server = http.createServer(app);
const io = require('socket.io')(server, {
    cors: { origin: "*" }
});

// Array to hold driver data
let drivers = [];

// Function to calculate distance in miles using Haversine formula
function getDistance(lat1, lon1, lat2, lon2) {
    const R = 3958.8; // Radius of the Earth in miles
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLon = (lon2 - lon1) * (Math.PI / 180);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * (Math.PI / 180)) *
        Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Function to check if driver is within 10 miles of any polyline point
function isWithinRange(driverLat, driverLng, polylinePoints) {
    for (const [lat, lng] of polylinePoints) {
        const distance = getDistance(driverLat, driverLng, lat, lng);
        if (distance <= 10) {
            return true; // Driver is within range of at least one point
        }
    }
    return false; // Driver is too far from all polyline points
}
async function sendDeviationNotification(user_id, trip_id) {
    try {
        // 1. Get Company ID for the driver
        const companyResponse = await axios.post('https://staging.zeroifta.com/api/get-company-by-driver', { driver_id: user_id });
        const company_id = companyResponse.data.company_id;

        if (!company_id) {
            console.log("Company not found for driver:", user_id);
            return;
        }

        // 2. Get FCM tokens for the company
        const fcmResponse = await axios.post('https://staging.zeroifta.com/api/get-company-fcm-tokens', { company_id });
        const fcmTokens = fcmResponse.data.tokens;

        if (!fcmTokens || fcmTokens.length === 0) {
            console.log("No FCM tokens found for company:", company_id);
            return;
        }

        // 3. Send Push Notification
        const message = {
            notification: {
                title: "Driver Route Deviation",
                body: `Driver ${user_id} has deviated from the route on trip ${trip_id}.`
            },
            tokens: fcmTokens
        };

        const response = await admin.messaging().sendMulticast(message);
        console.log("Push notification sent successfully:", response);

    } catch (error) {
        console.error("Error sending push notification:", error.response ? error.response.data : error.message);
    }
}
io.on('connection', (socket) => {
    console.log('User connected');

    // Listen for location updates from the Android app
    socket.on('userLocation', (data) => {
        console.log(`Received location for user ${data.user_id}: ${data.lat}, ${data.lng}`);

        // Check if the driver already exists in the drivers array
        const driverIndex = drivers.findIndex(driver => driver.id === data.user_id);

        if (driverIndex !== -1) {
            // Update the existing driver's location
            drivers[driverIndex].lat = data.lat;
            drivers[driverIndex].lng = data.lng;
        } else {
            // Add new driver to the array if they don't exist
            drivers.push({
                id: data.user_id,
                lat: data.lat,
                lng: data.lng,
            });
        }

        // Broadcast updated location to all connected clients
        io.emit('driverLocationUpdate', {
            driver_id: data.user_id,
            lat: data.lat,
            lng: data.lng
        });
    });

    // New event to handle trip deviation check
    const driverStatus = {}; // Track driver deviation status & last updated trip route

   

    socket.on('checkTripDeviation', async (data) => {
        const { trip_id, user_id, lat, lng } = data;
        console.log(`Checking trip deviation for user ${user_id} on trip ${trip_id}`);
    
        try {
            let trip;
    
            // Check if we already have the updated trip details
            if (driverStatus[user_id] && driverStatus[user_id].trip) {
                trip = driverStatus[user_id].trip;
            } else {
                // Fetch trip details from Laravel API
                const tripResponse = await axios.post('https://staging.zeroifta.com/api/check-active-trip', { trip_id });
                trip = tripResponse.data.trip;
    
                if (!trip) {
                    console.log("Trip not found");
                    return;
                }
    
                // Store trip details in memory
                driverStatus[user_id] = { trip };
            }
    
            const { start_lat, start_lng, end_lat, end_lng } = trip;
    
            // Check if polyline is already stored, else fetch from Google
            if (!driverStatus[user_id].polylinePoints) {
                console.log(`Fetching route for user ${user_id}`);
    
                const polylineResponse = await axios.get(`https://maps.googleapis.com/maps/api/directions/json`, {
                    params: {
                        origin: `${start_lat},${start_lng}`,
                        destination: `${end_lat},${end_lng}`,
                        key: "AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg"
                    }
                });
    
                if (polylineResponse.data.routes.length === 0) {
                    console.log("No route found");
                    return;
                }
    
                const encodedPolyline = polylineResponse.data.routes[0].overview_polyline.points;
                driverStatus[user_id].polylinePoints = polyline.decode(encodedPolyline);
            }
    
            // Check if the driver is within 10 miles of any polyline point
            const withinRange = isWithinRange(lat, lng, driverStatus[user_id].polylinePoints);
    
            if (!withinRange) {
                // Driver is off-route
                if (!driverStatus[user_id].isDeviated) {
                    driverStatus[user_id].isDeviated = true;
    
                    console.log(`Driver ${user_id} is off-route. Recalculating route...`);
    
                    // Emit event to frontend about deviation
                    socket.emit('routeDeviation', {
                        user_id,
                        trip_id,
                        message: "Driver has deviated from the route. Recalculating..."
                    });
    
                    // Call the update trip API to update the start location
                    try {
                        const updateResponse = await axios.post('https://staging.zeroifta.com/api/trip/update', {
                            trip_id,
                            start_lat: lat,
                            start_lng: lng,
                            end_lat,
                            end_lng,
                            truck_mpg: trip.truck_mpg,
                            fuel_tank_capacity: trip.fuel_tank_capacity,
                            total_gallons_present: trip.fuel_left,
                            reserve_fuel: trip.reserve_fuel,
                        });
    
                        console.log("Trip updated successfully:", updateResponse.data);
                       
                        // Emit event to frontend about updated trip
    
                        socket.emit('tripUpdated', {
                            user_id,
                            trip_id,
                            trip_data: updateResponse.data, // Send the full API response
                            message: "Trip updated successfully after deviation."
                        });
                        await sendDeviationNotification(user_id, trip_id);
    
                    } catch (updateError) {
                        console.error("Failed to update trip:", updateError.response ? updateError.response.data : updateError.message);
                    }
                }
            } else {
                // Driver is on-route
                if (driverStatus[user_id].isDeviated) {
                    driverStatus[user_id].isDeviated = false;
                    console.log(`Driver ${user_id} is back on route.`);
                }
            }
        } catch (error) {
            console.error("Error checking trip deviation:", error.response ? error.response.data : error.message);
        }
    });
    

    socket.on('disconnect', () => {
        console.log('A user disconnected');
    });
});

// Start the server
server.listen(3000, () => {
    console.log('Socket.IO server is running on port 3000');
});
