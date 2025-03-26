<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Trip Deviation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Test Trip Deviation</h1>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card shadow-lg p-4 mb-4 bg-white rounded">
                    <div class="card-body">
                        <form id="deviationForm">
                            <div class="mb-3">
                                <label for="tripId" class="form-label">Trip ID</label>
                                <input type="text" class="form-control" id="tripId" value="125">
                            </div>
                            <div class="mb-3">
                                <label for="userId" class="form-label">User ID</label>
                                <input type="text" class="form-control" id="userId" value="22">
                            </div>
                            <div class="mb-3">
                                <label for="lat" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="lat" value="34.052235">
                            </div>
                            <div class="mb-3">
                                <label for="lng" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="lng" value="-118.243683">
                            </div>
                            <button type="submit" class="btn btn-primary">Check Deviation</button>
                        </form>
                    </div>
                </div>
                <div id="response" class="mt-4"></div>
            </div>
        </div>
    </div>

    <!-- Socket.IO Client Library -->
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
    <script>
        // Connect to the Socket.IO server
        const socket = io('https://ws.zeroifta.com');
        socket.on('connection', (socket) => {
        console.log('User connected:', socket.id);


        });
        // Listen for the form submission
        document.getElementById('deviationForm').addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent the form from submitting

            // Get the input values
            const tripId = document.getElementById('tripId').value;
            const userId = document.getElementById('userId').value;
            const lat = document.getElementById('lat').value;
            const lng = document.getElementById('lng').value;

            // Emit the 'checkTripDeviation' event to the server
            socket.emit('checkTripDeviation', {
                trip_id: tripId,
                user_id: userId,
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            });

            console.log('Emitted checkTripDeviation event with data:', {
                trip_id: tripId,
                user_id: userId,
                lat: lat,
                lng: lng
            });
        });

        // Listen for the 'routeDeviation' event from the server
        socket.on('routeDeviation', (data) => {
            console.log('data'.data);
            const responseDiv = document.getElementById('response');
            responseDiv.innerHTML = `
                <div class="alert alert-warning">
                    <strong>Route Deviation Detected!</strong><br>
                    User ID: ${data.user_id}<br>
                    Trip ID: ${data.trip_id}<br>
                    Message: ${data.message}
                </div>
            `;
        });

        // Listen for errors or other events
        socket.on('connect_error', (error) => {
            console.error('Connection error:', error);
        });
    </script>
</body>
</html>
