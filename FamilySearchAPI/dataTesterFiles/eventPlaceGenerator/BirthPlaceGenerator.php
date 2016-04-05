<?php
	require_once("PlaceGenerator.php");
	class BirthPlaceGenerator extends PlaceGenerator{
	
		public function setEventPlace(&$persId,$place)
		{
			$persId->setBirthPlace($place);
		}
		
		public function setEventDate(&$persId,$date)
		{
			$persId->setBirthDate($date);
		}
	}
?>