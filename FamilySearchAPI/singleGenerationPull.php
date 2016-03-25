<?php 
/*
Single Generation pull. 

*/
require_once('appState.php');
require_once('getFSXMLResponse.php');

$credentials = array(// Production Key
					//"key" => "DPYC-ZB7C-5M9N-M4PS-M8YJ-T6SB-H5VM-C658",
					'key' => 'a0T3000000BZUxWEAX',
					// Reference Key
					//"key" => "WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK",
					'user' => '',
					'password' => '',
					'agent' => 'FMR/v1.0 (BYU-Geography)',
					'sessionID'=> '',
					//Used in Oauth2 authentication. traded for an access token
					'authCode' => '',
					'accessToken' =>'',
					'statusCode'=>'',
					'loggedOn' => False,
					'mainURL'=>$mainURL
					);
	$credentials["accessToken"] = $_COOKIE["accessToken"];
	//$credentials["accessToken"];
		//Form the POST XML payload by passing user defined variables to function
		$payload = createPOSTSearchPayload();
		// Set URL
		
	
		// Repeat POST request using contextID until all results returned (currently limited to 500)
		// Set counter - counts by 40 (number of returned people in search)
		$counter = 1;	
		$max = $_GET['searchSize'];
		$persons = array();
		
		
		
		$mainURL = $_COOKIE["mainURL"];
		//Blank first time through loop. constructed and used second time through loop
		appState::$context = "";
		
		//first sample pull url 
		$url = $mainURL.'platform/tree/search?count=40&'.appState::$context.'start=0&';	
		
do
	{
					
  try {
					
					if ($counter<$max)
					{
						echo $payload;
						$rawResponse = getFSXMLPOSTResponse($url, $payload, $credentials);
						echo $rawResponse;
						$response = json_decode($rawResponse,true);//this is important
						// Do a person read for each person returned

						$url = $response['links']['next']['href'];
						
						// The number of people returned in search
						$searchCount = count($response['results']);

						// Loop through each record from the first response
						if ($searchCount>0)
						{
							//For each person in the search
							foreach($response['entries'] as $search)
							{
								
								$searchedPerson = $search['content']['gedcomx']['persons'][0];
								// Increment the counter
							
								if ($counter>$max)
								{
									break;
								}

								$queryURL =$searchedPerson['links']['person']['href'];
								$personReadResponse = getFSXMLResponse($credentials, $queryURL);
								unset($queryURL);
								
								array_push($persons,$personReadResponse['persons'][0]);// as $person
								$counter++;
							
							}
							
						}
						else
						{
							break;
						}
					}
				}
				catch(Exception $e)
				{
					echo "<h2>Error thrown in search</h2>";
				    continue;
				}
			}
			while ($counter<=$max);





echo json_encode($persons);

function createPOSTSearchPayload()
{
	// Set up variables


	$placeID = $_GET['placeId'];
	$startYear = $_GET['startYear'];	
	$event = "birthDate";
	$eventPlace = "birthPlace";
	
	$payload = "q=".$event.":\"1%20Jan%20".$startYear."-"."31%20Dec%20".$startYear."\"%20";
	$payload .= $eventPlace.":\"".$placeID."\"";
	$payload= str_replace($payload,"%20"," ");
	return $payload;
}
?>