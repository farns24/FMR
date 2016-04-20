<?php

/**
* Takes the root generation and finds relatives 
*/
abstract class ISearcher {
	
	/**
	* Solves for relatives. 
	*
	*@param ancestors: List of Relatives
	*@param mainUrl: Url to FamilySearch Api checkpoint
	*@param credentials: Array of credentials, including session information
	*@param person: Root Individual
	*@param fsConnect: Family Search Api Proxy
	*@param maxGen: Greatest distanc between root generation and one of the relatives. For example, if Pete was in the root generation, and Pete only has one parent recorded, the maxGen would be 1.
	*	However, if pete had a great grandparent in the results, the max gen would be 3.
	*@param html: The html representation of the search. This is a layout of person icons.	
	*/
	abstract public function solve(&$ancestors,$mainURL,$credentials,$person,&$fsConnect,$generation,&$maxGen,&$html);	
	
	/**
	* Converts Key value ancestor array to a list of ansetors.
	* @param ancestors: Key value pair
	* @return List of persons.
	*/
	protected function findAnsestorIds($ancestors)
	{
		$personsPedigree = array();
		foreach($ancestors as $key => $personId)
		{
			array_push($personsPedigree, $personId);
		}
		return $personsPedigree;
	}
	
	/**
	* Gets relatives of ancestor
	*/
	abstract public function getRelatives($ancestors);
	
	/**
	* Gets the generation count of person
	*/
	abstract public function getMaxGen($person,&$maxGen);
}
?>