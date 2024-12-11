<?php
// Spotify API credentials
$clientID = "a424c6dcf84743e19a0837c0648c5e30";
$clientSecret = "4e57f788eeaf46af823df89a55940146";

// File to store the cached token
$tokenFile = __DIR__ . '/spotify_token.json';

// Function to get a new access token
function fetchNewAccessToken($clientID, $clientSecret) { 
    global $tokenFile;

    $url = "https://accounts.spotify.com/api/token"; 
    $authHeader = base64_encode("$clientID:$clientSecret"); 

    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, [ 
        "Authorization: Basic $authHeader", 
        "Content-Type: application/x-www-form-urlencoded" 
    ]); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 

    $response = curl_exec($ch); 

    if (curl_errno($ch)) { 
        throw new Exception("cURL error: " . curl_error($ch)); 
    } 

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch); 

    if ($httpCode !== 200) { 
        throw new Exception("Failed to fetch access token. HTTP Status Code: $httpCode. Response: $response"); 
    } 

    $response = json_decode($response, true); 

    if (isset($response['access_token'])) { 
        $tokenData = [
            'access_token' => $response['access_token'],
            'expires_at' => time() + $response['expires_in'],
        ];
        file_put_contents($tokenFile, json_encode($tokenData)); // Save token to file
        return $response['access_token']; 
    } 

    throw new Exception("Failed to fetch access token: " . json_encode($response)); 
}

// Function to get a valid access token (cached or new)
function getAccessToken($clientID, $clientSecret) {
    global $tokenFile;

    // Check if the token file exists
    if (file_exists($tokenFile)) {
        $tokenData = json_decode(file_get_contents($tokenFile), true);

        // Check if the token is still valid
        if (isset($tokenData['expires_at']) && $tokenData['expires_at'] > time()) {
            return $tokenData['access_token'];
        }
    }

    // If the token is missing or expired, fetch a new one
    return fetchNewAccessToken($clientID, $clientSecret);
}
?>
