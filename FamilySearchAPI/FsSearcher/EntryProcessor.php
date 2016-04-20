<?php
include_once('/../jsonToXmlConverter.php');

/**
* Encapsolation of processing the root generation search.
*
* When the Root Generation is queried from Family search, each person in the root generation will be searched for relatives. 
*
*/
class EntryProcessor {

	/**
	* Processes an entry from family search Root 
	* @pre search is not null
	* @param search - The json response from Family Search
	* @param counter - The current tally of root generation people found
	* @param credentials - array holding authentication credentials
	* @param XMLString - The results of the search to be written to file. 
	* @param mainUrl - Family Search url
	* @param direction - Foreward or backwards.
	* @param minGen - User set filter for how many generations a root member must have in order to be included in the results.
	* @param searcher - Extends the ISearcher Abstract class. Either Forward Searcher or Backward Searcher. Uses State patturn to drive functionality
	* @param persons - list of people searched
	*/
	public function process(&$search,&$counter,&$credentials,&$XMLString,$mainURL,$direction,$minGen,&$fsConnect,&$searcher,&$persons,$max)
	{
		error_log("Starting Entry Processing");
		if (!isset($search))
		{
			throw new Exception("Search is not set");
		}
		if (count($search['content']['gedcomx']['persons'])==0)
		{
			throw new Exception("No People in the entry");
		}
		
		foreach($search['content']['gedcomx']['persons'] as $searchedPerson)
		{
			error_log("Discovering person");
			//$searchedPerson = $search['content']['gedcomx']['persons'][0];
			//echo json_encode($search);
			// Increment the counter
		
			if ($counter>$max)
			{
				return;
			}
			error_log("Attempting to Process Person");
			$this->processPerson($counter,$persons,$credentials,$searchedPerson,$XMLString,$mainURL,$direction,$minGen,$fsConnect,$searcher);
		}
		error_log("Entry processed");
	}

	private function processPerson(&$counter,&$persons,$credentials,$searchedPerson,&$XMLString,$mainURL,$direction,$minGen,&$fsConnect,&$searcher)
	{
		error_log("Starting to Process Person");
	$html = "";
	$queryURL =$searchedPerson['links']['person']['href'];
	$personReadResponse = $fsConnect->getFSXMLResponse($credentials, $queryURL);
	unset($queryURL);

	$person = $personReadResponse['persons'][0];							
	if (meetsReqs($person,$direction,$place))
	{
		
		$maxGen = 0;	
		
		$partialXML = $this->getXMLOfAncestors($person,$counter,$direction,$credentials,$mainURL,$maxGen,$html,$fsConnect,$searcher);							
									
		
		//Test partial XML
		if ($maxGen >=$minGen)
		{
		array_push($persons,$person);// as $person
		echo $html;
		$html = "";
		$counter++;
		error_log("Counter incrimented to $counter");
		$XMLString.=$partialXML;
		}
		else
		{
			error_log("Root Member Rejected: Current Generation - $maxGen Minimum Generation - $minGen");
		}
	}
	else
	{
		throw new Exception("Bad Root Member Exception");
	}
	error_log("Person processed");
}

	private function getXMLOfAncestors($person,$personFromPlaceIndex,$direction,$credentials,$mainURL,&$maxGen,&$html,&$fsConnect,&$searcher)
	{
		$id = $person["id"];
					$stats = getStats($person);
					$lineNum = $personFromPlaceIndex+1;
					
					$html.="<h3>Root Member $lineNum</h3><div> <a class = 'searchIcon' data-number='4' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img id='expImg' src=\"img/Root.png\" title=$stats height=\"20\" width=\"20\" /></a>";
					 
							
					$placesMap = array();
					
					$ancestors = array();
					$generation = 0;
					$searcher->solve($ancestors,$mainURL,$credentials,$person,$fsConnect,$generation,$maxGen,$html);
					
					$html.="</div>";
					$personsPedigree = $searcher->getRelatives($ancestors);
					
					// Insert the current personsPedigree array into the placePersonsPedigree array
		
						$XMLString = '<family>';
						foreach($personsPedigree as $person)
						{
							$gen= $searcher->getMaxGen($person,$maxGen);
							
							if ($gen>$maxGen)
							{
								$maxGen = $gen;
							}
							
							$XMLString .= convertToXml($person,$credentials,$mainURL,$direction);//->asXML();
							
						}
						$XMLString .= '</family>';
						$XMLString = strtr($XMLString,'<family></family>', "" );
						return $XMLString;
		
	}


}
?>