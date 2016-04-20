<?php 

/**
* Decorator for DynamicCache. Adds memory management algorithm.  
*/
class MemManagedCache {

	private $cache = new DynamicCache();

	public function add($pId,$iso,$lat,$lon,$name)
	{
		//TODO: Add memory management algorithm here. One way to do it would be to keep the 100 or so most recently used in cache and clear the rest. 
		//Another idea is to reset the array when it hits a certain size. 
		
		$cache->add($pId,$iso,$lat,$lon,$name);
		
		
	}


}

?>