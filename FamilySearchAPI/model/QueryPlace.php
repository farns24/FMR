<?php
class QueryPlace
{
	private $state;
	private $county;
	private $country;
	private $city;

	public function __construct($incity,$incounty,$instate,$incountry,$inplace)
	{
		$this->state = $instate;
		$this->county = $incounty;
		$this->city = $incity;
		$this->country = $incountry;
		$this->place = $inplace;
	}
	
	public function getFileName($startYear,$direction,$fileName)
	{
		return $this->place.'_'.$direction.'_'.$fileName.'.fmr';
	}
}
?>