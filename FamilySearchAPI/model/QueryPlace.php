<?php
class QueryPlace
{
	private $state;
	private $county;
	private $country;
	private $city;

	/**
	* @invariant: inPlace not null
	*/
	public function __construct($incity,$incounty,$instate,$incountry,$inplace)
	{
		$this->state = $instate;
		$this->county = $incounty;
		$this->city = $incity;
		$this->country = $incountry;
		$this->place = $inplace;
		
		if (!isset($inplace))
		{	
			throw new Exception("Invarient Exception: $inplace not set");
		}
	}
	
	public function getFileName($startYear,$direction,$fileName)
	{
		return $this->place.'_'.$direction.'_'.$fileName.'.fmr';
	}
}
?>