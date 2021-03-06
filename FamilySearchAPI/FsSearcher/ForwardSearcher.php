<?php
require_once("ISearcher.php");
class ForwardSearcher extends ISearcher {
	

/***************************************************************************************************************************************************************************
*				Forward Search Recursive Changes
*
*
*    Base Case: Generation is 2. 
*    * pull remaining 2 generations
*
*    If generation is less than 2,
*    pull one generation
*
****************************************************************************************************************************************************************************/

	public function solve(&$ancestors,$mainURL,$credentials,$personObj,&$fsConnect,$generation,&$maxGen,&$html) 
	{
	//throw new Exception("Break Here");
	//echo json_encode($person);
	$person = (string)$personObj['id'];
	$pullGen = 2;

	$pedigreeURL = $mainURL.'platform/tree/descendancy?person='.$person."&generations=$pullGen&personDetails=";
	
					
		$personPedigreeResponse =$fsConnect->getFSXMLResponse($credentials, $pedigreeURL);
	
		// Set the query URL
		$queryURL = $mainURL.'platform/tree/person/';
					
		foreach($personPedigreeResponse['persons'] as $searchedPerson)
		{	
		$desc = $searchedPerson["display"]["descendancyNumber"];
		
		
			$html.= $this->showGenerationPic($searchedPerson, $generation,$maxGen);
			
			if ($generation !=2 && substr_count($desc,".")==2)
			{
		
				$this->solve($ancestors,$mainURL,$credentials,$searchedPerson,$fsConnect,$generation+2,$maxGen,$html);
			}
			
			if (isset($searchedPerson) && isset($searchedPerson["id"]))
			{
				$ancestors[$searchedPerson["id"]] = $searchedPerson;
			}
		}
		//throw new Exception("Break Here");
		return;
					
}

 private function showGenerationPic($person, $gen,&$maxGen){
	$desc = $person["display"]["descendancyNumber"];
	$id = $person["id"];
	$stats = getStats($person);
	$thisGen =0;
	//echo $desc;
	if( $gen!=2)
	{
		if( substr_count($desc,".")==2)
		{
			$thisGen= 2;
			$html = "<a class = 'searchIcon' data-number='2' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen2.png\" title=$stats height=\"20\" width=\"20\" /></a>";
		}
		else if( substr_count($desc,".")==1)
		{
			
			$thisGen= 1;
			$html = "<a class = 'searchIcon' data-number='1' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen1.png\" title=$stats height=\"20\" width=\"20\" /></a>";
		}
		else
		{
			$html = "<a class = 'searchIcon' data-number='0' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/root.png\" height=\"20\" width=\"20\" /></a>";
		}
			
	}
	else
	{
		if( substr_count($desc,".")==2)
		{
			
			$thisGen= 4;
			$html = "<a class = 'searchIcon' data-number='4' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen4.png\" title=$stats height=\"20\" width=\"20\" /></a>";
		}
		else if( substr_count($desc,".")==1)
		{
			
			$thisGen= 3;
			$html = "<a class = 'searchIcon' data-number='3' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen3.png\" title=$stats height=\"20\" width=\"20\" /></a>";
		}
		else
		{
			$html = "<a class = 'searchIcon' data-number='0' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/root.png\" height=\"20\" width=\"20\" /></a>";
		}
		
	}
	//echo $thisGen;
	if ($thisGen> $maxGen)
	{
		
		$maxGen = $thisGen;
	}
	return $html;
}
	public function getRelatives($ancestors) 
	{
		return $this->findAnsestorIds($ancestors);
	}
	
	public function getMaxGen($person,&$maxGen)
	{
		$num = $person["display"]["descendancyNumber"];
		return $maxGen;
	}
}


?>