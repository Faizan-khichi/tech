<?php
// --- YOUR SECRET TOKEN ---
// Anyone calling this script must provide this token.
$requiredToken = '123'; // <-- CHANGE THIS!

// Check if the token was provided and is correct
if (!isset($_GET['token']) || $_GET['token'] !== $requiredToken) {
    http_response_code(403); // Forbidden
    die('<h1>403 Forbidden</h1><p>Access denied. Invalid or missing token.</p>');
}

// Check if a script URL was provided
if (isset($_GET['script_url'])) {
    $scriptUrl = $_GET['script_url']; // Vercel automatically decodes it

    // Fetch the code from the external URL
    // Using cURL is more robust than file_get_contents
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $scriptUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    $codeToExecute = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $codeToExecute !== false) {
        // IMPORTANT: The 'eval' function is extremely powerful and can be dangerous.
        // This setup is safe ONLY because you are protecting it with a secret token.
        // Never expose an open eval processor to the public.
        eval('?>' . $codeToExecute);
    } else {
        http_response_code(500);
        echo "Error: Failed to fetch the script from the provided URL. (HTTP Status: $httpCode)";
    }
} else {
    http_response_code(400); // Bad Request
    echo "<h1>400 Bad Request</h1><p>Please provide a 'script_url' parameter.</p>";
}
?>
