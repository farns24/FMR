<?php
	require_once("PlaceGenerator.php");
	class DeathPlaceGenerator extends PlaceGenerator{
		
		public function setEventPlace(&$persId,$place)
		{
			$persId->setDeathPlace($place);
		}
		
		public function setEventDate(&$persId,$date)
		{
			$persId->setDeathDate($date);
		}
	}
?>