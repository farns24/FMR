<?php

// BYUFMR getFSXMLResponse
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: getFSXMLResponse.php
// Purpose: Access Family Search and return a simpleXML object.

require_once('FS_Connection.php');

// Returns an XML object from a GET request
// Handles Auto-Throttling from Family Search by waiting specified amounts of time before trying query again.

class FsConnect{

	public function getFSXMLResponse($credentials, &$queryURL)
	{
	//echo $queryURL;
		$waitTime = 1;
		do
		{
			do
			{
				//Capture response body from response
				$response = FSQuery($credentials, $queryURL);
				try
				{
					
					$json = json_decode($response,true);
					
						
				}
				catch (Exception $e)
				{
					
					return "restart query";
				}
			}while($json === FALSE);
			if(($credentials["statusCode"] > 200) && ($credentials["statusCode"] < 503))
			{
				echo $credentials["statusCode"];
				break;
			}
			if($credentials["statusCode"] == 503)
			{
				sleep($waitTime);
				if($waitTime < 31)
				{
					$waitTime *= 2;
				}
			}
		}while($credentials["statusCode"] > 200 && $credentials["statusCode"] < 300);
		
		return $json;
	}

	// Returns an XML object from a POST request
	// Handles Auto-Throttling from Family Search by waiting specified amounts of time before trying query again.
	public function getFSXMLPOSTResponse($url, $data, $credentials)
	{
	//echo $url.$data;
		// The maximum wait time as recommended by FS
		$MAXWAITTIME = 32;
		// Start the wait time between query tries at 1 second
		$waitTime = 1;
		do
		{
			// Perform the query until $response can be converted to JSON
			// Doesn't take into account HTTP status code here
			do
			{
				// Perform the query
				//echo $url.$data;
				$response = FSQuery($credentials, $url.$data);
				
			}while($response === FALSE);
			// Check HTTP status code
			if(($credentials["statusCode"] >= 200) && ($credentials["statusCode"] < 503))
			{
				//echo '<br />Status Code: '.$json['statusCode'];
				break;
			}
			// If the HTTP status code is FS 503 - "Not enough time between queries" - perform FS recommended wait proceedure
			// FS wait proceedure - wait 1, 2, 4, 8, 16, 32 seconds.  Max wait time is 32 seconds.  If reached, program waits 32 sec until response is not 503.
			if($credentials["statusCode"] == 503)
			{
				sleep($waitTime);
				if($waitTime <= $MAXWAITTIME)
				{
					$waitTime *= 2;
				}
			}
		}while($credentials["statusCode"] >= 200 &&$credentials["statusCode"]<300);
		
		return $response;
	}
}
?>
