<?php
session_start();

$url = 'https://api-exchange.bankera.com/oauth/token?grant_type=client_credentials';
$clientId = '';
$clientSecret = '';
$encodedCredentials = base64_encode($clientId.":".$clientSecret);

$opts = array('http' =>
	array(
		'method' => "GET",
		'header' => sprintf("Authorization: Basic %s", $encodedCredentials)
	)
);

$context = stream_context_create($opts);
$response = file_get_contents($url, false, $context);

$responseBody = json_decode($response);

print_r(ucfirst($responseBody->token_type) . " " . $responseBody->access_token);