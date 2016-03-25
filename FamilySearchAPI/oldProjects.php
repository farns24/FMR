<?php

	class Entry { 
	
	public $name = ''; 
	
	public function __construct(  $_name){
      
            $this->$name = $_name;
 
    }
    
    
} 





	$dir    = "../data/v2/";
	$files = scandir($dir);
	$htmloutput ="";
	$projectList = array();
	
	if(count($files) != 0)
	{
		foreach($files as $fileName)
		{
			if($fileName != "." && $fileName != "..")
			{
				$element = array();
				$element['name'] = $fileName;
				
			array_push($projectList, $element);
			}
		}
	}
	
	
	echo json_encode($projectList);
	
	

?>