export async function getAddressFromCoordinates(latitude, longitude) {
    try {
        const apiKey = process.env.GOOGLE_MAPS_API_KEY;
        const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.results && data.results[0]) {
            const addressComponents = data.results[0].address_components;
            const fullAddress = data.results[0].formatted_address;

            let city = '';
            let state = '';

            for (const component of addressComponents) {
                if (component.types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                }
                if (component.types.includes('locality')) {
                    city = component.long_name;
                }
            }

            return {
                full_address: fullAddress,
                city: city || 'City not found',
                state: state || 'State not found'
            };
        }

        return {
            full_address: 'Address not found',
            city: 'City not found',
            state: 'State not found'
        };
    } catch (error) {
        console.error('Error fetching address:', error);
        return {
            full_address: 'Error fetching address',
            city: 'Error',
            state: 'Error'
        };
    }
} 