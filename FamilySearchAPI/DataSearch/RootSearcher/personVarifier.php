<?php

    function meetsReqs($person,$direction,$place)
	{
		//Check location
		
			$locationIsGood = checkLocation($person,$place);
		
		
		
		
		return $locationIsGood;
		
	}

	function hasChildren($person)
	{
		
		
	}
	
	function hasParents($person)
	{
		
	}

	function checkLocation($person,$place)
	{
		$placeGood = false;
		//echo "<div class='well'>";
		//var_dump($person);
		//echo "</div>";
		$index = 0;
//		foreach($person['facts'] as $event)
	//		{
				// Check the event type to only get places of type "Birth"
				$event = $person['facts'][0];
				
				if($event['type'] == 'http://gedcomx.org/Birth')
				{
					/*echo <<<HTML
					<div class ='well'>
						We are trying to optomize the search by a factor of n by seeing if we can access Birth field directly.
						This access was made at index: $index
					</div>
					
HTML;*/
					
				// Check the place ID to see if it matches the user defined "place"
					if((int)$event['place']['original'] == (int)$place) //as passed to the function
					{
						// If places match, store the person read record in a new array of only persons from the specified place
						return true;
					}
				}
				$index++;
		//	}
		return $placeGood;
		
	}

?>