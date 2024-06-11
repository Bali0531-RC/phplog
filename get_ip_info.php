<?php
header('Content-Type: application/json');

// Function to get the user's IP address
function getUserIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Get the user's IP address
$user_ip = getUserIpAddr();

// Get location info based on IP address using ip-api.com
$api_url = "http://ip-api.com/json/{$user_ip}";
$location_info = file_get_contents($api_url);
$location_info = json_decode($location_info, true);

// Prepare the response
$response = array(
    'ip' => $user_ip,
    'country' => $location_info['country'] ?? 'Unknown',
    'city' => $location_info['city'] ?? 'Unknown',
    'hostName' => gethostbyaddr($user_ip),
    'isp' => $location_info['isp'] ?? 'Unknown'
);

echo json_encode($response);
?>
