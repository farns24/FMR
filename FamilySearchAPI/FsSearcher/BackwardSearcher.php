<?php
require_once("ISearcher.php");

/**
* Searches for ancsetors of a provided person up to four generations. Extends ISearcher Abstract Class.
*/
class BackwardSearcher extends ISearcher {
	
	public function solve(&$ancestors,$mainURL,$credentials,$person,&$fsConnect,$generation,&$maxGen,&$html) 
	{
		
	$html ="";
	$pedigreeURL = $mainURL.'platform/tree/ancestry?person='.$person['id'];
	$pedigreeURL = $pedigreeURL.'&generations=4&personDetails=';
					// Get their pedigree via FS pedigree service  -  https://api.familysearch.org/familytree/v2/pedigree/{id} 
					
	$personPedigreeResponse =$fsConnect->getFSXMLResponse($credentials, $pedigreeURL);
				
					// Set the query URL
	$queryURL = $mainURL.'platform/tree/person/';
			
					// To avoid duplicate person reads, insert each unique id into an array and then do a person read on each element of that array
	
	foreach($personPedigreeResponse['persons'] as $person)
	{
		$html.=$this->showBackGenPic($person);
		$ancestors[$person["id"]] = $person;
	}
	return;
}
 private function showBackGenPic($person){
	$html;
	$stats = getStats($person);
	$id = $person['id'];
	
	if (isset($person["display"]["ascendancyNumber"]))
	{
		$acend = $person["display"]["ascendancyNumber"];
		if($acend>1 && $acend<4)
			{
				$html = "<a class = 'searchIcon' data-number='1' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen1.png\" title=$stats height=\"20\" width=\"20\" /></a>";
			}
		else if( $acend>3 && $acend<8)
			{
				$html = "<a class = 'searchIcon' data-number='2' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen2.png\" title=$stats height=\"20\" width=\"20\" /></a>";
			}
		else if( $acend>7 && $acend<16)
			{
				$html = "<a class = 'searchIcon' data-number='3' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'> <img src=\"img/gen3.png\" title=$stats height=\"20\" width=\"20\"/></a>";
			}
		else if( $acend>15 && $acend<32)
			{
				$html = "<a class = 'searchIcon' data-number='4' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/gen4.png\" title=$stats height=\"20\" width=\"20\" /></a>";
			}
	}
	else
	{
		$html = "<a class = 'searchIcon' data-number='0' target='_blank' href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'><img src=\"img/RootSpouse.png\" height=\"20\" width=\"20\" /></a>";
	}
	return $html;
}
	public function getRelatives($ancestors)
	{
		$ansestors = $this-> findAnsestorIds($ancestors);	
		krsort($ansestors);
		return $ansestors;
	}
	/**
	* Get the generation of the person
	*/
	public function getMaxGen($person,&$maxGen)
	{
		$num = $person["display"]["ascendancyNumber"];
		if (is_numeric($num))
		{
			$numInt = (int)$num;
			
			if ($numInt>15)
			{
				return 4;
			}
			else if ($numInt>7)
			{
				return 3;
			}
			else if ($numInt>3)
			{
				return 2;
			}
			else
			{
				return 1;
			}
		}
		else
		{
		
			return 1;
			//throw new Exception("Max Generation Not set");
		}
		return $maxGen;
	}
}


?>