<?php

require_once "dbconfig.php";
// Set POST variables
$url = 'https://android.googleapis.com/gcm/send';


// Message to be sent
$message = "Su camion esta por llegar...";
echo $message;

$registrationid = "YOUR REGISTRATION ID";
$fields = array(
    'registration_ids' => array($registrationid),
    'data' => array("message" => $message),
);

$headers = array(
    'Authorization: key=' . GOOGLE_API_KEY,
    'Content-Type: application/json'
);

// Open connection
$ch = curl_init();

// Set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

// Execute post
$result = curl_exec($ch);
if ($result) {
    echo "sI";
} else {
    echo "no";
}

// Close connection
curl_close($ch);

echo $result;
?>