<?php
function RCM($arrayName, $parentLevel)
{
	$lt50mi = 0;
	$total = 0;
	global $valueArray;
	
	foreach($arrayName as $person)
	{
		csiDist($person, $person, 0, $parentLevel);
		//echo "<pre>".$person->getId().": ".count($valueArray)."<br /></pre>";
	}
	if (isset($dist) && sizeof($dist)>0)
	{
		foreach($valueArray as $dist)
		{
			if($dist <= 50) $lt50mi++;
		}
	}
	$total = count($valueArray);
	
	$valueArray = NULL;

	return "$lt50mi/$total";
}
?>