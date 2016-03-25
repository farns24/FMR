<?php 
// Mean distance helper function
function findMeanDistance($arrayName, $parentLevel)
{
	$mdTotal = 0;
	$mdCount = 0;
	foreach($arrayName as $person)
	{
		if ($person->getDistTo($parentLevel) != -999)
		{
			$mdTotal = $mdTotal + $person->getDistTo($parentLevel);
			$mdCount = $mdCount + 1;
		}
	}
	if ($mdCount != 0)
	{
		return number_format(($mdTotal / $mdCount), 4, '.', '');
	}
	else
	{
		return "N/A";
	}
}

?>