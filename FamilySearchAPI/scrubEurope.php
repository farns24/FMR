<?php 

function scrubArrayOfEurope($whichArray)
{
	$cleanArray = Array();
	foreach($whichArray as $person)
	{
		if ($person->getBirthPlace()->Lon() < -60)
		{
			if ($person->getBirthPlace()->Lat() > 25)
			{
				$cleanArray[] = $person;
			}
		}
	}
	return $cleanArray;
}

?>