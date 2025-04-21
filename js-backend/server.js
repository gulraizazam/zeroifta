import express from "express";
import dotenv from "dotenv";
import {
    calculateHaversineDistance,
    decodePolyline,
    calculateDistance,
} from "./utils/distance.js";
import { getAddressFromCoordinates } from "./utils/geocoding.js";
import { loadAndParseFTPData } from "./utils/fuelStations.js";
import { markOptimumFuelStations } from "./utils/optimization.js";
import Trip from "./models/Trip.js";
import Vehicle from "./models/Vehicle.js";
import FuelStation from "./models/FuelStation.js";
import sequelize from "./config/database.js";

dotenv.config();

const app = express();
app.use(express.json());

// Database connection test
sequelize
    .authenticate()
    .then(() => {
        console.log("Database connection established successfully.");
    })
    .catch((err) => {
        console.error("Unable to connect to the database:", err);
    });

// Start Trip API endpoint
app.post("/api/trip/calculate", async (req, res) => {
    try {
        // Validate required fields
        const requiredFields = [
            "start_lat",
            "start_lng",
            "end_lat",
            "end_lng",
            "truck_mpg",
            "fuel_tank_capacity",
            "total_gallons_present",
        ];

        for (const field of requiredFields) {
            if (!req.body[field]) {
                return res.status(400).json({
                    status: 400,
                    message: `Missing required field: ${field}`,
                    data: {},
                });
            }
        }

        // Get route from Google Maps API
        const apiKey = process.env.GOOGLE_MAPS_API_KEY;
        const url = `https://maps.googleapis.com/maps/api/directions/json?origin=${req.body.start_lat},${req.body.start_lng}&destination=${req.body.end_lat},${req.body.end_lng}&key=${apiKey}`;

        const response = await fetch(url);
        const data = await response.json();

        if (!data.routes || !data.routes[0]) {
            return res.status(500).json({
                status: 500,
                message: "Failed to fetch data from Google Maps API.",
                data: {},
            });
        }

        // Process route data
        const route = data.routes[0];
        const steps = route.legs[0].steps;
        const decodedCoordinates = [];
        const stepSize = 3;

        // Decode and sample route coordinates
        for (const step of steps) {
            if (step.polyline?.points) {
                const points = decodePolyline(step.polyline.points);
                for (let i = 0; i < points.length; i += stepSize) {
                    decodedCoordinates.push(points[i]);
                }
            }
        }

        // Calculate total distance and duration
        let totalDistance = 0;
        let totalDuration = 0;

        for (const leg of route.legs) {
            totalDistance += leg.distance.value;
            totalDuration += leg.duration.value;
        }

        // Convert distance to miles and format duration
        const totalDistanceMiles = (totalDistance * 0.000621371).toFixed(2);
        const hours = Math.floor(totalDuration / 3600);
        const minutes = Math.floor((totalDuration % 3600) / 60);
        const formattedDistance = `${totalDistanceMiles} miles`;
        const formattedDuration =
            hours > 0 ? `${hours} hr ${minutes} min` : `${minutes} min`;

        // Get addresses for start and end locations
        const startLocation = await getAddressFromCoordinates(
            req.body.start_lat,
            req.body.start_lng
        );
        const endLocation = await getAddressFromCoordinates(
            req.body.end_lat,
            req.body.end_lng
        );

        // Find fuel stations along route
        const matchingRecords = await loadAndParseFTPData(decodedCoordinates);

        // Store total count of fuel stations
        const totalFuelStations = matchingRecords.length;

        // Prepare trip detail response for optimization
        const tripDetailResponse = {
            data: {
                trip: {
                    start: {
                        latitude: req.body.start_lat,
                        longitude: req.body.start_lng,
                    },
                    end: {
                        latitude: req.body.end_lat,
                        longitude: req.body.end_lng,
                    },
                },
                vehicle: {
                    mpg: req.body.truck_mpg,
                    fuelLeft:
                        req.body.total_gallons_present +
                        (req.body.reserve_fuel || 0),
                },
                fuelStations: matchingRecords,
                polyline: decodedCoordinates,
            },
        };

        // Mark optimal fuel stations
        const optimizedFuelStations =
            markOptimumFuelStations(tripDetailResponse) || matchingRecords;

        // Prepare calculation results
        const calculationResults = {
            route_data: {
                start_lat: req.body.start_lat,
                start_lng: req.body.start_lng,
                end_lat: req.body.end_lat,
                end_lng: req.body.end_lng,
                polyline: route.legs[0].steps
                    .map((step) => step.polyline?.points)
                    .filter(Boolean),
                polyline_encoded: route.overview_polyline.points,
                distance: formattedDistance,
                duration: formattedDuration,
                start_address: startLocation.full_address,
                end_address: endLocation.full_address,
                start_city: startLocation.city,
                start_state: startLocation.state,
                end_city: endLocation.city,
                end_state: endLocation.state,
            },
            fuel_stations: optimizedFuelStations.map(station => ({
                name: station.fuel_station_name,
                latitude: station.ftpLat,
                longitude: station.ftpLng,
                price: station.price,
                lastprice: station.lastprice,
                discount: station.discount,
                ifta_tax: station.IFTA_tax,
                is_optimal: station.isOptimal || false,
                firstOptimal: station.firstOptimal || false,
                midOptimal: station.midOptimal || false,
                secondOptimal: station.secondOptimal || false,
                address: station.address,
                distanceFromStart: station.distanceFromStart,
                gallons_to_buy: station.gallons_to_buy || 0
            })),
            total_fuel_stations: totalFuelStations,
            optimal_stations_count: optimizedFuelStations.filter(s => 
                s.isOptimal || s.firstOptimal || s.midOptimal || s.secondOptimal
            ).length
        };

        return res.status(200).json({
            status: 200,
            message: "Trip calculations completed successfully.",
            data: calculationResults
        });
    } catch (error) {
        console.error("Error calculating trip:", error);
        return res.status(500).json({
            status: 500,
            message: "Internal server error",
            data: {},
        });
    }
});

const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});
