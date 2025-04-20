const EARTH_RADIUS_MILES = 3958.8;

// Pre-calculate common values
const TO_RAD = Math.PI / 180;
const TO_DEG = 180 / Math.PI;

export function calculateHaversineDistance(lat1, lng1, lat2, lng2) {
    // Convert coordinates to radians
    const lat1Rad = lat1 * TO_RAD;
    const lng1Rad = lng1 * TO_RAD;
    const lat2Rad = lat2 * TO_RAD;
    const lng2Rad = lng2 * TO_RAD;
    
    const dLat = lat2Rad - lat1Rad;
    const dLng = lng2Rad - lng1Rad;
    
    const sinDLat = Math.sin(dLat / 2);
    const sinDLng = Math.sin(dLng / 2);
    const cosLat1 = Math.cos(lat1Rad);
    const cosLat2 = Math.cos(lat2Rad);
    
    const a = sinDLat * sinDLat + cosLat1 * cosLat2 * sinDLng * sinDLng;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    
    return EARTH_RADIUS_MILES * c;
}

// Optimized polyline decoder with reduced object creation
export function decodePolyline(encoded) {
    const points = new Array(Math.floor(encoded.length / 2)); // Pre-allocate array
    let index = 0;
    let lat = 0;
    let lng = 0;
    let pointIndex = 0;

    while (index < encoded.length) {
        let b;
        let shift = 0;
        let result = 0;

        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);

        lat += ((result & 1) ? ~(result >> 1) : (result >> 1));

        shift = 0;
        result = 0;

        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);

        lng += ((result & 1) ? ~(result >> 1) : (result >> 1));

        points[pointIndex++] = {
            lat: lat * 1e-5,
            lng: lng * 1e-5
        };
    }

    points.length = pointIndex; // Trim array to actual size
    return points;
}

// Optimized point-to-polyline distance calculation
export function calculateDistance(coord1, coord2) {
    return calculateHaversineDistance(
        coord1.lat,
        coord1.lng,
        coord2.lat,
        coord2.lng
    );
}

// Binary search for finding nearest point
export function findNearestPoint(location, polyline) {
    let left = 0;
    let right = polyline.length - 1;
    let nearestIndex = 0;
    let minDistance = Number.MAX_VALUE;

    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        const distance = calculateDistance(location, polyline[mid]);

        if (distance < minDistance) {
            minDistance = distance;
            nearestIndex = mid;
        }

        // Check surrounding points
        const prevDistance = mid > 0 ? calculateDistance(location, polyline[mid - 1]) : Number.MAX_VALUE;
        const nextDistance = mid < polyline.length - 1 ? calculateDistance(location, polyline[mid + 1]) : Number.MAX_VALUE;

        if (prevDistance < distance) {
            right = mid - 1;
        } else if (nextDistance < distance) {
            left = mid + 1;
        } else {
            break;
        }
    }

    return nearestIndex;
}

// Optimized polyline distance calculation with sampling
export function calculatePolylineDistance(userLocation, destination, polyline) {
    const startIndex = findNearestPoint(userLocation, polyline);
    const endIndex = findNearestPoint(destination, polyline);

    let totalDistance = 0;
    const step = Math.max(1, Math.floor((endIndex - startIndex) / 100)); // Sample at most 100 points

    for (let i = startIndex; i < endIndex; i += step) {
        const nextIndex = Math.min(i + step, endIndex);
        totalDistance += calculateDistance(polyline[i], polyline[nextIndex]);
    }

    return totalDistance;
} 