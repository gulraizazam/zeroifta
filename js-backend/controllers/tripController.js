const Vehicle = require('../models/Vehicle');
const { calculateDistance } = require('../utils/distanceCalculator');

const calculateTripDetails = async (req, res) => {
    try {
        const {
            vehicleId,
            startLocation,
            endLocation,
            startOdometer,
            endOdometer,
            fuelConsumed
        } = req.body;

        // Validate required fields
        if (!vehicleId || !startLocation || !endLocation || !startOdometer || !endOdometer || !fuelConsumed) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields'
            });
        }

        // Get vehicle details
        const vehicle = await Vehicle.findByPk(vehicleId);
        if (!vehicle) {
            return res.status(404).json({
                success: false,
                message: 'Vehicle not found'
            });
        }

        // Calculate distance between coordinates
        const distance = calculateDistance(
            startLocation.latitude,
            startLocation.longitude,
            endLocation.latitude,
            endLocation.longitude
        );

        // Calculate odometer difference
        const odometerDifference = endOdometer - startOdometer;

        // Calculate MPG
        const mpg = odometerDifference / fuelConsumed;

        // Calculate remaining fuel
        const remainingFuel = parseFloat(vehicle.fuel_left) - fuelConsumed;

        // Update vehicle data
        await vehicle.update({
            odometer_reading: endOdometer,
            fuel_left: remainingFuel.toString(),
            mpg: mpg.toString()
        });

        return res.status(200).json({
            success: true,
            data: {
                distance,
                odometerDifference,
                mpg,
                remainingFuel
            }
        });

    } catch (error) {
        console.error('Error calculating trip details:', error);
        return res.status(500).json({
            success: false,
            message: 'Internal server error',
            error: error.message
        });
    }
};

module.exports = {
    calculateTripDetails
}; 