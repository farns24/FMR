<?php

/**
* Stores place information in Dynamic memory. Faster access time than using the Database.
*
* Written to speed up the search process. Place information stored in cache will be faster to access than the same information stored in the Postgres Database
*
* NOTE: Disabled due to memmory leaks. Too much information is being placed in dynamic memory for the server's limited resources to handle. 
*
*/
class DynamicCache {

	private $data = array();
	
	private $idLookup = array();

	/**
	* Tempararily disabled: The Dynamic Cache is causing the server to crash. 
	* There is too much information being cached. Working on a solution to filter out results that are not common.
	*/
	public function add($pId,$iso,$lat,$lon,$name)
	{
		/*$entry = array();
		
		$entry['iso'] = $iso;
		$entry['lat'] = $lat;
		$entry['lon'] = $lon;
		$entry['name']= $name;
		$this->data[(string)$pId] = $entry;
		
		$this->idLookup[$name] = $pId;*/
	}
	
	/**
	* Gets place information from id.
	*/
	public function get($pid)
	{
		if (array_key_exists((string)$pid,$this->data))
		{
			return $this->data[(string)$pid];
		}
		else
		{
			return array();
		}
	}
	
	/**
	* Gets place id by name of place.
	*/
	public function getId($name)
	{
		if ($this->hasName($name))
		{
			return $this->idLookup[$name];
		}
		else
		{
			throw new Exception("Name not contained");
		}
	}
	
	/**
	* Tests to see if name is contained. 
	* @return true if contained. false otherwise. 
	*/
	public function hasName($placeName)
	{
		//var_dump($this->idLookup);
		return (isset($this->idLookup[$placeName])||array_key_exists($placeName,$this->idLookup));
	}
	
}

?>