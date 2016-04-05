<?php
	
	abstract class PlaceGenerator{
	
		public function setPlace($event, &$persId,$credentials)
		{	
			// If the place is recorded in the person record, create a new Place object and populate it with lat/long values from the XML
			if(isset($event->value->place->normalized['id']) and $event->value->place->normalized['id']!="")
			{
				//echo "<h1>Death Place</h1>";
				$pid = $event->value->place->normalized['id'];
				$pName = $event->value->place->original;
				$this->setEventPlace($persId,new Place(1, $pid, -999, -999,$credentials,$pName));
			}
			// If the place is not recorded, create a 'dummy' place to act as a placeholder for analysis
			else
			{
				$this->setEventPlace($persId,new Place(0, "-999", -999, -999,$credentials,""));
			}
			if(isset($event->value->date->normalized))
			{
				$this->setEventDate($persId,$event->value->date->normalized);
			}
		}
		
		abstract public function setEventPlace(&$persId,$place);
		
		abstract public function setEventDate(&$persId,$date);

	}
		
?>