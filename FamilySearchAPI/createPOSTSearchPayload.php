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
	$nameList = array();
	$facade->loadChildrenPlaces($placeId,$credentials,$nameList);
	
	
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

/**
* The Family search API is very literal in what it will return and sometimes isn't very intuitive. For a general search, this can be a problem. For example,
* If the user is looking for 200 people from dublin ireland between 1820 and 1840, one would expect to use:
*
*         /platform/tree/search?count=200&q=birthLikePlace:"Dublin, Ireland" birthLikeDate:"1820-1830"
*
* On the surface, this request seems to be asking 
*
*     "Give me all the people who were born sometime between 1820 and 1830 in Dublin Ireland or one of its suburbs. Yours truly, BYUFMR"
* 
* However, Family search seems to have interpreted the request to mean 
*
*     "Give me all the people where we're not sure when or where they were born, but we think it was sometime 
*     between 1820 and 1830 somewhere in Dublin. Usually, when a birthdate is not specificly known, people who do family history will put a birth range, rather than a birthdate.  
*     That birthrange is what we want. None of these Birthdates. Also, if specifics of the birthplace are not known, It is common practice to put a general place like 
*     Dublin or the United States. These General places are what we're interested in. Only give me people where the birthdate and birth place is unknown. Under no circumstances should 
*     I see a result where we know exactly where and when they were born. Remember family search, we only want results with a birth range, not a birth date. Sorry if I'm repeating 
*     myself, but it is absolutely crucial that nothing too specific comes through. Regaurds, BYUFMR". 
* 
* Our tests using that query returned only results where an individual was given a birth range that included 1820 - 1830. We did not get people who were born specificly 
* on a date in that range. We also only get results where the place is recorded as being Dublin ireland, as in Somewhere in Dublin. Results where the suburb was specified were not
* returned. 
*
* Another attempt was made by puting a ~ behind the query to mark it to include close matches. This one was a little better, but familysearch was too liberal in the results it 
* returned for the results to be of any use for research. 
*
* To address this problem, a new tactic was used. Enter the payload. The payload is a list of possible specific searches to attempt to populate the search from. 
* It starts with the center year and the most general place. then it adds searches for any suburbs of the general place at the center year. The Suburb search is recursive, 
* so any place that is a suburb of a suburb of the original place will be included. This is repeated for every year in the range. The searches are ordered with the center year and most general 
* locality first.
*
* TODO: Find a way of phrasing a query to have this happen in one query. This would speed up the search time imensely. 
*
*/
function buildPayload($POSTVars,$startYear,$fullName)
{
		// Set up variables
	$event = $POSTVars['event'];
	$eventPlace = "";

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
	$payload = str_replace("%20,%20","%20",$payload); 

	return $payload;
	
}

?>