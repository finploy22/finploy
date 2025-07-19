<?php

// Define global variables
$apiBaseUrl = 'https://watifly.in/api'; // Replace with your actual base URL
$vendorUid = '533a7c8a-7e5c-4638-8221-1f609ac88eb0'; // Replace with your actual vendor UID
$fromPhoneNumberId = '513971428461678'; // Replace with your actual phone number ID
$templateName = 'youwerereferred';
$token = 'y0EXTEfXNiilSCNX2UoqsOoLFkvnRs3uGdlp8NaVX0zTGIEUKZflI00PoVOsufrD';
function sendMessageToCandidate($phoneNumber, $templateName, $candidateName, $partnerName, $location)
{
    global $apiBaseUrl, $vendorUid, $fromPhoneNumberId, $token;

    $url = "$apiBaseUrl/$vendorUid/contact/send-template-message?token=$token";

    $payload = [
        "from_phone_number_id" => $fromPhoneNumberId,
        "phone_number" => $phoneNumber,
        "template_name" => $templateName,
        "template_language" => "en_US",

        // Template-specific fields
        "field_1" => $candidateName,
        "field_2" => $partnerName,
        "field_3" => $location
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
        // Add auth header if required: 'Authorization: Bearer YOUR_TOKEN'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["success" => false, "error" => $error];
    }

    curl_close($ch);

    return [
        "success" => $httpCode >= 200 && $httpCode < 300,
        "http_code" => $httpCode,
        "response" => json_decode($response, true)
    ];
}

function sendMessageToPartner($phoneNumber, $templateName, $candidateName, $partnerName, $location)
{
    global $apiBaseUrl, $vendorUid, $fromPhoneNumberId, $token;

    $url = "$apiBaseUrl/$vendorUid/contact/send-template-message?token=$token";

    $payload = [
        "from_phone_number_id" => $fromPhoneNumberId,
        "phone_number" => $phoneNumber,
        "template_name" => 'youreferred',
        "template_language" => "en_US",

        // Template-specific fields
        "field_1" => $partnerName,
        "field_2" => $candidateName,
        "field_3" => $location
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
        // Add auth header if required: 'Authorization: Bearer YOUR_TOKEN'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["success" => false, "error" => $error];
    }

    curl_close($ch);

    return [
        "success" => $httpCode >= 200 && $httpCode < 300,
        "http_code" => $httpCode,
        "response" => json_decode($response, true)
    ];
}

//  Example ///
// // Sample values to test
// $phoneNumber = '916379328342'; // Replace with a valid number
// $candidateName = 'candidate';
// $partnerName = 'Partner';
// $location = 'Bangalore';

// // Optional contact data (can be left empty)
// $contactData = [
//     "email" => "john@example.com",
//     "country" => "India",
//     "groups" => "referrals"
// ];

// // Call the function
// $response = sendTemplateMessage($phoneNumber, $templateName, $candidateName, $partnerName, $location);

// // Print the result
// echo "<pre>";
// print_r($response);
// echo "</pre>";
?>
