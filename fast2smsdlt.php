<?php
/*
|---------------------------------------------------------------
| Fast2SMS Gateway Boilerplate
|---------------------------------------------------------------
| This file is a boilerplate for sending SMS via Fast2SMS API.
| It supports DLT, Quick, and OTP routes.
|
| @author Your Name
| @provider Fast2SMS
|---------------------------------------------------------------
*/

/**
 * Sends an SMS using the Fast2SMS API.
 *
 * @param array $params An array of parameters for the API call.
 *                      'api_key' (string) - Your Fast2SMS API key. (Required)
 *                      'to' (string) - Comma-separated list of recipient numbers. (Required)
 *                      'route' (string) - The route to use ('dlt', 'dlt_manual', 'q', 'otp'). (Required)
 *                      'sender_id' (string) - Your DLT-approved Sender ID. (Required for 'dlt' and 'dlt_manual')
 *                      'message' (string|int) - The message content or DLT message ID. (Required for 'dlt', 'dlt_manual', 'q')
 *                      'variables_values' (string) - Pipe-separated values for template variables. (Optional for 'dlt', required for 'otp')
 *                      'template_id' (string) - DLT Content Template ID. (Required for 'dlt_manual')
 *                      'entity_id' (string) - DLT Principal Entity ID. (Required for 'dlt_manual')
 *                      'language' (string) - The language of the message ('english' or 'unicode'). Defaults to 'english'. (Optional for 'q')
 *                      'flash' (int) - Set to 1 for flash SMS, 0 otherwise. Defaults to 0. (Optional)
 *
 * @return array An array containing the status and response from the API.
 */
function fast2sms_send(array $params)
{
    // Validate required parameters
    if (empty($params['api_key']) || empty($params['to']) || empty($params['route'])) {
        return [
            'status' => 'error',
            'message' => 'Missing required parameters: api_key, to, or route.'
        ];
    }

    // Fast2SMS API endpoint
    $url = 'https://www.fast2sms.com/dev/bulkV2';

    // Prepare payload
    $data = [
        'route'   => $params['route'],
        'numbers' => $params['to'],
    ];

    // Add route-specific parameters
    switch ($params['route']) {
        case 'dlt':
            if (empty($params['sender_id']) || empty($params['message'])) {
                return ['status' => 'error', 'message' => 'Missing sender_id or message for DLT route.'];
            }
            $data['sender_id'] = $params['sender_id'];
            $data['message'] = $params['message'];
            if (!empty($params['variables_values'])) {
                $data['variables_values'] = $params['variables_values'];
            }
            break;

        case 'dlt_manual':
            if (empty($params['sender_id']) || empty($params['message']) || empty($params['template_id']) || empty($params['entity_id'])) {
                return ['status' => 'error', 'message' => 'Missing sender_id, message, template_id, or entity_id for DLT Manual route.'];
            }
            $data['sender_id'] = $params['sender_id'];
            $data['message'] = $params['message'];
            $data['template_id'] = $params['template_id'];
            $data['entity_id'] = $params['entity_id'];
            break;

        case 'q':
            if (empty($params['message'])) {
                return ['status' => 'error', 'message' => 'Missing message for Quick SMS route.'];
            }
            $data['message'] = $params['message'];
            $data['language'] = $params['language'] ?? 'english';
            break;

        case 'otp':
            if (empty($params['variables_values'])) {
                return ['status' => 'error', 'message' => 'Missing variables_values for OTP route.'];
            }
            $data['variables_values'] = $params['variables_values'];
            break;

        default:
            return ['status' => 'error', 'message' => 'Invalid route specified.'];
    }

    if (isset($params['flash'])) {
        $data['flash'] = $params['flash'];
    }

    // Setup headers
    $headers = [
        'authorization: ' . $params['api_key'],
        'Content-Type: application/json'
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

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
    }

    $decoded_response = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($decoded_response['return']) && $decoded_response['return'] === true) {
         return [
            'status' => 'success',
            'response' => $decoded_response
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'API Error',
            'response' => $response
        ];
    }
}

/*
// --- Example Usage ---

// DLT SMS
$dlt_params = [
    'api_key' => 'YOUR_API_KEY',
    'to' => '9876543210,1234567890',
    'route' => 'dlt',
    'sender_id' => 'FSTSMS',
    'message' => 12345, // Your DLT Message ID
    'variables_values' => 'value1|value2'
];
// $result = fast2sms_send($dlt_params);
// print_r($result);


// DLT Manual SMS
$dlt_manual_params = [
    'api_key' => 'YOUR_API_KEY',
    'to' => '9876543210',
    'route' => 'dlt_manual',
    'sender_id' => 'FSTSMS',
    'message' => 'Your actual DLT approved message content with variables replaced.',
    'template_id' => 'YOUR_DLT_TEMPLATE_ID',
    'entity_id' => 'YOUR_DLT_ENTITY_ID',
];
// $result = fast2sms_send($dlt_manual_params);
// print_r($result);


// Quick SMS
$quick_params = [
    'api_key' => 'YOUR_API_KEY',
    'to' => '9876543210',
    'route' => 'q',
    'message' => 'This is a test message from your application.'
];
// $result = fast2sms_send($quick_params);
// print_r($result);


// OTP SMS
$otp_params = [
    'api_key' => 'YOUR_API_KEY',
    'to' => '9876543210',
    'route' => 'otp',
    'variables_values' => '123456' // Your OTP
];
// $result = fast2sms_send($otp_params);
// print_r($result);
*/
?>
