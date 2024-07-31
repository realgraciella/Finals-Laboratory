<?php
require_once 'lib/Request.php';

$place = '';
$region = '';
$island = '';
$coordinates = '';
$errorMessage = '';

// Create an instance of the Request class
$requestModel = new Request();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['place_name'])) {
    $place = trim($_POST['place_name']);
    $placeDetails = $requestModel->getPlaceDetails($place);

    if (!$placeDetails) {
        $errorMessage = "Place not found or unable to retrieve information.";
    } else {
        $region = $placeDetails['region'] ?? 'N/A';
        $island = $requestModel->getIslandFromRegion($region);
        $coordinates = implode(',', $placeDetails['coordinates']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Region and Island by Place Name</title>
    <link href="assets/css/style.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://maps.geoapify.com/v1/maps/maps-api.js?apiKey=c3cc075edd114626843f8ae1ef2fc599"></script>
</head>
<body>
    <div class="txt-heading">Get Region and Island by Place Name</div>

    <!-- Form to input place name -->
    <form method="post" action="">
        <label for="place_name">Place Name:</label>
        <input type="text" id="place_name" name="place_name" value="<?php echo htmlspecialchars($place); ?>" required>
        <button type="submit">Submit</button>
    </form>

    <!-- Display error message if any -->
    <?php if ($errorMessage): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <!-- Display place details if available -->
    <?php if ($region && $island): ?>
        <div id="location">
            <div class="geo-location-detail">
                <div class="row">
                    <div class="form-label">
                        Region: <?php echo htmlspecialchars($region); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-label">
                        Island: <?php echo htmlspecialchars($island); ?>
                    </div>
                </div>
            </div>
            <!-- Display map -->
            <div id="map" style="height: 400px;"></div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var coordinates = "<?php echo htmlspecialchars($coordinates); ?>";
                    console.log("Coordinates: " + coordinates);

                    if (coordinates) {
                        var coordsArray = coordinates.split(',');
                        var lat = parseFloat(coordsArray[1]);
                        var lng = parseFloat(coordsArray[0]);

                        console.log("Latitude: " + lat + ", Longitude: " + lng);

                        var map = L.map('map').setView([lat, lng], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);
                        L.marker([lat, lng]).addTo(map)
                            .bindPopup('<?php echo htmlspecialchars($place); ?>')
                            .openPopup();
                    }
                });
            </script>
        </div>
    <?php endif; ?>
</body>
</html>