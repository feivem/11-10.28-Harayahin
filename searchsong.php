<?php 
require_once 'spotify_auth.php';

try {
    // Get a valid access token
    $accessToken = getAccessToken(
        "a424c6dcf84743e19a0837c0648c5e30", 
        "4e57f788eeaf46af823df89a55940146"
    );
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Check if a search query is provided
$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';

if (!$query) {
    echo "Please enter a search term.";
    exit;
}

// Spotify API endpoint for searching
$url = "https://api.spotify.com/v1/search?q=" . urlencode($query) . "&type=track&limit=10";

// API request with Authorization header
$options = [
    "http" => [
        "header" => "Authorization: Bearer $accessToken",
        "method" => "GET",
    ],
];

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

// Handle API errors
if ($response === false) {
    $error = error_get_last();
    echo "Error: " . htmlspecialchars($error['message']);
    exit;
}

$response = json_decode($response, true);

// Handle Spotify API response errors
if (isset($response['error'])) {
    echo "Error: " . htmlspecialchars($response['error']['message']);
    exit;
}

if (!isset($response['tracks']) || empty($response['tracks']['items'])) {
    echo "No results found.";
    exit;
}

// Display results with "Add" button
foreach ($response['tracks']['items'] as $track) {
    $trackName = htmlspecialchars($track['name']);
    $artistName = htmlspecialchars($track['artists'][0]['name']);
    $trackId = htmlspecialchars($track['id']); // Track ID for embedded URL
    $trackUrl = "https://open.spotify.com/embed/track/$trackId"; // Embedded URL
    
    echo "<div class='song-card'>";
    echo "<p>$trackName by $artistName</p>";

    // Wrapper for iframe and Add button
    echo "<div class='song-wrapper'>";
    echo "<iframe src='$trackUrl' allow='encrypted-media'></iframe>";
    echo "<button class='add-song' onclick='addSong(\"$trackName\", \"$artistName\", \"$trackUrl\", this.parentElement)'>Add</button>";
    echo "</div>";
    
    echo "</div>";
}

?>
