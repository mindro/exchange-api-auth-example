<?php
session_start();

$url = 'https://api-exchange.bankera.com';
$tokenUrl = '/oauth/token?grant_type=client_credentials';
$usersInfoUrl = '/users/info';
$clientId = '';
$clientSecret = '';
$encodedCredentials = base64_encode($clientId.":".$clientSecret);

//Auth
$opts = array('http' =>
	array(
		'method' => "GET",
		'header' => sprintf("Authorization: Basic %s", $encodedCredentials)
	)
);

$context = stream_context_create($opts);
$response = file_get_contents($url.$tokenUrl, false, $context);

$responseBody = json_decode($response);

$authToken = ucfirst($responseBody->token_type) . " " . $responseBody->access_token;

//Call users info method with token
$opts = array('http' =>
	array(
		'method' => "GET",
		'header' => sprintf("Authorization: %s", $authToken)
	)
);

$context = stream_context_create($opts);
$userResponse = file_get_contents($url.$usersInfoUrl, false, $context);

$userResponseBody = json_decode($userResponse);

print_r($userResponseBody);
