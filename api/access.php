<?php
	require_once("./util.php");

	loadEnviroment();

	$requestData = postToJSON();

	$access = false;
	$labNumber = $requestData["lab"];

	if(array_key_exists("password", $requestData)) {
		$password = $requestData["password"];
		$access = passwordAccessCheck($password, $labNumber);
	} else {
		$card = $requestData["card"];
		$access = cardAccessCheck($card, $labNumber);
	}

	$responseData = array();
	$responseData["access"] = $access;

	header("Content-Type: application/json");
	echo json_encode($responseData);
?>
