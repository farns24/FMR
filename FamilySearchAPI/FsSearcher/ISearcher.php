<?php

abstract class ISearcher {
	
	abstract public function solve(&$ancestors,$mainURL,$credentials,$person,&$fsConnect,$generation,&$maxGen,&$html);	
	
	protected function findAnsestorIds($ancestors)
	{
		$personsPedigree = array();
		foreach($ancestors as $key => $personId)
		{
			array_push($personsPedigree, $personId);
		}
		return $personsPedigree;
	}
	
	abstract public function getRelatives($ancestors);
	
	abstract public function getMaxGen($person,&$maxGen);
}
?>