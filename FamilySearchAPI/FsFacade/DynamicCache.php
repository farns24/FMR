<?php

class DynamicCache {

	private $data = array();

	public function add($pId,$iso,$lat,$lon)
	{
		$entry = array();
		
		$entry['iso'] = $iso;
		$entry['lat'] = $lat;
		$entry['lon'] = $lon;
		
		$this->data[(string)$pId] = $entry;
	}

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
	
}

?>