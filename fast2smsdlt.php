<?php

/**
 * FAST2SMS Gateway
 * @url https://www.fast2sms.com
 * @author PrithviS (@blrcoderbro)
 * DLT SMS Gateway
 * @version 1.1
 * @date 2025-03-07
 */

define("FAST2SMS_GATEWAY", [
    "apiUrl" => "https://www.fast2sms.com/dev/bulkV2",
    "apiKey" => "YOUR_API_KEY", // Your FAST2SMS API key
    "senderId" => "DLT_SENDER_ID", // Your DLT sender ID
]);

function sendSMS($phoneNumbers, $message, $route = 'dlt', $variablesValues = '', &$system)
{
    /**
     * Implement sending here
     * @return bool:true
     * @return bool:false
     */

    // Prepare the URL for the API request
    $url = FAST2SMS_GATEWAY["apiUrl"] . "?authorization=" . FAST2SMS_GATEWAY["apiKey"] .
        "&sender_id=" . FAST2SMS_GATEWAY["senderId"] .
        "&message=" . urlencode($message) .
        "&route=" . $route .
        "&numbers=" . urlencode(implode(',', (array)$phoneNumbers));

    if ($route === 'dlt') {
        $url .= "&variables_values=" . urlencode($variablesValues);
    } else {
        $url .= "&language=english";
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false; // Return false on error
    } else {
        return true; // Return true on success
    }
}

function callback($request, &$system)
{
    /**
     * Implement status callback here if gateway supports it
     * @return array:MessageID
     * @return array:Empty
     */
}

return [
    "send" => "sendSMS",
    "callback" => "callback"
];
