<?php

class DynamicCache {

	private $data = array();

	public function add($pId,$pName,$lat,$lon)
	{
		$entry = array();
		
		$entry['name'] = $pName;
		$entry['lat'] = $lat;
		$entry['lon'] = $lon;
		
		this->data[$pId] = $entry;
	}

	public function get($pid)
	{

	
		return $this->data[$pid];
	}
	
	public function loadFromDb()
	{
	

	}
}

?>