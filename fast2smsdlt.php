<?php
/*
|---------------------------------------------------------------
| Fast2SMS Gateway Controller
|---------------------------------------------------------------
| This file handles sending SMS via Fast2SMS API.
| Replace placeholders with your actual Fast2SMS credentials.
|
| @author blrcoderbro
| @provider Fast2SMS
|---------------------------------------------------------------
*/

function fast2sms_send($to, $message, $api_key = '', $sender_id = '', $route = 'q', $country = '91')
{
    // Fast2SMS API endpoint
    $url = 'https://www.fast2sms.com/dev/bulkV2';

    // Prepare payload
    $data = [
        'sender_id'   => $sender_id, // Sender ID assigned by Fast2SMS
        'message'     => $message,
        'language'    => 'english',
        'route'       => $route, // Default route
        'numbers'     => $to, // Comma separated numbers
    ];

    // Setup headers
    $headers = [
        'authorization: ' . $api_key,
        'Content-Type: application/json'
    ];

    // Initialize curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute & get response
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Handle response
    if ($error) {
        return [
            'status' => 'error',
            'message' => $error
        ];
    } else {
        return [
            'status' => 'success',
            'response' => $response
        ];
    }
}

// Example usage (replace with actual credentials and numbers)
// $result = fast2sms_send('9876543210', 'Hello from Fast2SMS!', 'YOUR_API_KEY', 'SENDERID');
// print_r($result);

?>
