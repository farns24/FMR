<?php

class DynamicCache {

	private $data = array();

	public function add($pId,$iso,$lat,$lon)
	{
		$entry = array();
		
		$entry['iso'] = $iso;
		$entry['lat'] = $lat;
		$entry['lon'] = $lon;
		
		$this->data[$pId] = $entry;
	}

	public function get($pid)
	{
		if (array_key_exists($pid,$this->data))
		{
			return $this->data[$pid];
		}
		else
		{
			return array();
		}
	}
	
	public function loadFromDb()
	{
	

	}
}

?>