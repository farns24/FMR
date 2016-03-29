<?php
// Family Search Logon, Query (GET and POST), and Logout Script
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: functions.php
// Purpose: This file contains all the functions that BYUFMR.php needs to correctly connect to the Family Search API.

// Include Request Methods - allows easy access to log in and GET query
include_once('HTTP/Request.php');
require_once('appState.php');

// logOn
// accepts base URL (maniURL) ending with "/" - identity endpoint is accessed to authenticate a new session
// credentials is an array of current state login information - when logon is called it should be in a not-logged-on state
function logOn($mainURL, $credentials)
{
	//echo "Start authentication";
	// create a new HTTP_Request object to be used in the login process
	$request = new HTTP_Request();
	
	// set the URL of the HTTP_Request object to the family search identity/login endpoint
	//$request->setUrl($mainURL."identity/v1/login?key=".$credentials['key']);
	$request->setMethod("POST");
	//https://sandbox.familysearch.org
	//$request->setUrl("https://sandbox.familysearch.org/cis-web/oauth2/v3/token");
	
	$request->setUrl("https://ident.familysearch.org/cis-web/oauth2/v3/token");
	$request->_useBrackets = false;
	$request->addHeader("User-Agent", $credentials['agent']);
	$request->addHeader("x-frame-options", $credentials['sameorgin']);
	$request->setBody(getTokenRequest($credentials));

	$request->sendRequest();
	
	//the response will come in the form of an html file
	$response = $request->getResponseBody();
	


	
	
	$json_a=json_decode($response,true);
	$credentials['accessToken'] = $json_a['access_token'];
	setcookie("accessToken", $credentials["accessToken"], time() + 18000);
	return $credentials;
}

// FSQuery - GET query using HTTP_Request object
// $queryUrl has to include root, module(familytree, identity, authorities), version number, services, and parameters
function FSQuery($credentials, $queryURL)
{
	//$queryURL = rawurlencode($queryURL);

	
	
	//echo "<div class='well'> $queryURL</div>";
	$request = new HTTP_Request($queryURL);
	//Authorization
	$request->setMethod("GET");
	//$request->addHeader("Authorization", $credentials['accessToken']);
	$request->addHeader("Accept", "application/json");
	$request->addHeader("Authorization","Bearer ".$credentials['accessToken']);
	$request->sendRequest();
	$response = $request->getResponseBody();
	$credentials['statusCode']= $request->getResponseCode();
	//echo "<br>Access Token at time of query [".$credentials['accessToken']."]<br>";
	//setcookie("statusCode",$credentials['statusCode'], time() + 18000);
	
	if ($credentials['statusCode']!="200" or $credentials['statusCode']!="204")
	{
		//echo "<br>".$credentials['statusCode']."<br>";
	}

	return $response;
}

// FSQuery - POST query using HTTP_Request object
// $queryUrl has to include root, module(familytree, identity, authorities), version number, service but NOT parameters
function FSPOSTQuery($url, $data, $credentials, $optional_headers = 'Content-type: text/xml')
{
	$url = $url."access_token=".$credentials['accessToken'];
	$params = array('http' => array(
		'method' => 'POST',
		'Content-type'=>'application/json',
		'content' => $data		//XML Payload
	));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = file_get_contents($url, false, $ctx);
	if (!$fp) {
		throw new Exception("Problem with $url, $php_errormsg");
	}
	$response = $fp;
	//$response = @stream_get_contents($fp);
	if ($response === false) {
		throw new Exception("Problem reading data from $url, $php_errormsg");
	}
	
	return $response;
}

function logOut($mainURL, $credentials)
{
	$request = new HTTP_Request($mainURL."/identity/v1/logout?sessionId=".$credentials['sessionID']);
	$request->addHeader("User-Agent", $credentials['agent']);
	$request->sendRequest();

	if($request->getResponseCode() == 200)
	{
		$credentials["sessionID"] = "";
		$credentials["loggedOn"] = FALSE;
		setcookie("user", $credentials["user"], time() - 3600);
		setcookie("password", $credentials["password"], time() - 3600);
		setcookie("agent", $credentials["agent"], time() - 3600);
		setcookie("sessionID", $credentials["sessionID"], time() - 3600);
		setcookie("loggedOn", $credentials["loggedOn"], time() - 3600);
		setcookie("mainURL", $mainURL, time() - 3600);
	}
	return $credentials;
}

function getTokenRequest($credentials)
{
$request = "grant_type=authorization_code&code=".$credentials['authCode']."&client_id=".$credentials['key'];
//echo $request;
return $request;
}



?>