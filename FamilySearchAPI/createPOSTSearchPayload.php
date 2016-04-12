<?php
/**
* Changes to this function will include changing it from a post to a get method
*/
function createPOSTSearchPayload($POSTVars,$facade,$credentials,$placeId)
{
	$startYear = $POSTVars['startyear'];
	$giveOrTake = (int)$POSTVars['giveOrTake'];
	$payload = array();
	$fullName = $POSTVars['fullName'];
	$fullName = urlencode($fullName);
	$nameList = $facade->loadChildrenPlaces($placeId,$credentials);
	
	
	array_unshift($payload,buildPayload($POSTVars,$startYear,$fullName));
	
	foreach($nameList as $nm)
	{
		array_unshift($payload,buildPayload($POSTVars,$startYear,$nm));
	}
	for($i = 1; $i< $giveOrTake+1; $i++)
	{
	
			array_unshift($payload,buildPayload($POSTVars,$startYear+$i,$fullName));
			array_unshift($payload,buildPayload($POSTVars,$startYear-$i,$fullName));
		//For each sub place of the provided place
		foreach($nameList as $nm)
		{	
		
			array_unshift($payload,buildPayload($POSTVars,$startYear+$i,$nm));
			array_unshift($payload,buildPayload($POSTVars,$startYear-$i,$nm));
		}
	}
	//var_dump($payload);
	return $payload;
		
}
function buildPayloadArray($event,$eventPlace,$city,$state,$county,$startYear,$giveOrTake)
{
	//First, search City, County ,State, Country

	//Second, search 1 year removed both ends (etc until give or take is full)
	
	
	
}
function buildPayload($POSTVars,$startYear,$fullName)
{
		// Set up variables
	$event = $POSTVars['event'];
	$eventPlace = "";
	//$placeID = $POSTVars['place'];
		
	//$state = $POSTVars['state'];
	//$county = $POSTVars['county'];
	//$country = $POSTVars['country'];
	//$city = $POSTVars['city'];
	
	
	if($event == "birth")
	{
		$event = "birthLikeDate";
		$eventPlace = "birthLikePlace";
	}
	if($event == "death")
	{
		$event = "deathLikeDate";
		$eventPlace = "deathLikePlace";
	}
	
	if (isset($startYear)==false)
	{
		echo "<h1>Start Year undefined</h1>";
	}
	
	$startYearInt = (int)$startYear;
	$startRange= $startYearInt - $giveOrTake;
	$endRange= $startYearInt + $giveOrTake;
	
	$payload = "q=".$event.":"."\"1%20January%20".$startRange."\"-\""."31%20December%20".$endRange."\"%20";
	
	$payload .= $eventPlace.":$fullName";//TODO add ~ to include non exact matches

	$payload = str_replace("+","%20",$payload);
	$payload = str_replace("%20%20","%20",$payload);
	$payload = str_replace("%20/%20","%20",$payload);
	$payload = str_replace("%20,%20","%20",$payload); //, ,

	return $payload;
	
}

?>