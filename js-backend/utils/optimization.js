import { calculateDistance, calculatePolylineDistance } from './distance.js';

export function markOptimumFuelStations(tripDetailResponse) {
    if (!tripDetailResponse) return null;

    const { trip, vehicle, fuelStations, polyline } = tripDetailResponse.data;
    const start = trip.start;
    const end = trip.end;

    // Calculate truck's travelable distance
    const truckTravelableDistanceInMiles = vehicle.mpg * vehicle.fuelLeft;

    // Add distanceFromStart to every fuel station
    let processedStations = fuelStations.map(station => ({
        ...station,
        distanceFromStart: calculatePolylineDistance(
            start,
            { lat: station.ftpLat, lng: station.ftpLng },
            polyline
        )
    }));

    // Find the cheapest station overall
    const cheapestStation = processedStations
        .sort((a, b) => a.price - b.price)[0];
    
    if (cheapestStation) {
        cheapestStation.isOptimal = true;
    }

    // Separate stations into in-range and out-of-range
    const inRangeStations = processedStations.filter(
        station => station.distanceFromStart < truckTravelableDistanceInMiles
    );

    const outOfRangeStations = processedStations.filter(
        station => station.distanceFromStart >= truckTravelableDistanceInMiles
    );

    if (inRangeStations.length === 0) return false;

    // Find first cheapest in range
    const firstCheapestInRange = inRangeStations
        .sort((a, b) => a.price - b.price)[0];

    // Find second cheapest that's cheaper than first but after it
    const secondCheapestInRange = outOfRangeStations
        .filter(station => 
            station.price < firstCheapestInRange.price &&
            station.price > cheapestStation.price &&
            station.distanceFromStart < cheapestStation.distanceFromStart
        )
        .sort((a, b) => a.price - b.price)[0];

    // Find mid-optimal station
    const midOptimal = outOfRangeStations
        .filter(station =>
            firstCheapestInRange &&
            secondCheapestInRange &&
            station.price < firstCheapestInRange.price &&
            station.distanceFromStart < secondCheapestInRange.distanceFromStart
        )
        .sort((a, b) => a.distanceFromStart - b.distanceFromStart)[0];

    // Mark stations as optimal
    if (secondCheapestInRange && firstCheapestInRange) {
        if (secondCheapestInRange.price < firstCheapestInRange.price) {
            secondCheapestInRange.secondOptimal = true;
        }
    }

    // Remove stations if they are farther than optimal
    if (cheapestStation) {
        if (secondCheapestInRange && 
            secondCheapestInRange.distanceFromStart > cheapestStation.distanceFromStart) {
            delete secondCheapestInRange.secondOptimal;
        }
        if (firstCheapestInRange && 
            firstCheapestInRange.distanceFromStart > cheapestStation.distanceFromStart) {
            delete firstCheapestInRange.firstOptimal;
        }
    }

    // Mark remaining optimal stations
    if (firstCheapestInRange) {
        firstCheapestInRange.firstOptimal = true;
    }
    if (midOptimal) {
        midOptimal.midOptimal = true;
    }

    // Calculate fuel requirements for each optimal station
    const optimalStations = [
        cheapestStation,
        firstCheapestInRange,
        midOptimal,
        secondCheapestInRange
    ].filter(Boolean);

    // Calculate gallons to buy at each station
    for (let i = 0; i < optimalStations.length - 1; i++) {
        const currentStation = optimalStations[i];
        const nextStation = optimalStations[i + 1];

        if (currentStation && nextStation) {
            const distanceBetweenStations = nextStation.distanceFromStart - currentStation.distanceFromStart;
            const fuelNeeded = distanceBetweenStations / vehicle.mpg;
            const fuelLeftAfterReachingCurrent = vehicle.fuelLeft - (currentStation.distanceFromStart / vehicle.mpg);

            if (fuelLeftAfterReachingCurrent < fuelNeeded) {
                currentStation.gallons_to_buy = fuelNeeded - fuelLeftAfterReachingCurrent;
            }
        }
    }

    // Calculate fuel needed to reach destination for last optimal station
    const lastStation = optimalStations[optimalStations.length - 1];
    if (lastStation) {
        const distanceToEnd = calculateDistance(
            { lat: lastStation.ftpLat, lng: lastStation.ftpLng },
            end
        );
        const fuelNeededToEnd = distanceToEnd / vehicle.mpg;
        const fuelLeftAtLastStation = vehicle.fuelLeft - (lastStation.distanceFromStart / vehicle.mpg);

        if (fuelLeftAtLastStation < fuelNeededToEnd) {
            lastStation.gallons_to_buy = (lastStation.gallons_to_buy || 0) + (fuelNeededToEnd - fuelLeftAtLastStation);
        }
    }

    return processedStations;
} 