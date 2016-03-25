<?
// BYUFMR Spatial Analysis
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Professor in Charge: Dr. Samuel Otterstrom
// Title: analyze.php
// Purpose: Perform spatial analysis on .FMR data.
error_reporting(E_ALL);

require_once('PointGeoStatistics.php');

function analyze($fileName, $directory)
{
	// Read in the passed .FMR file
	$fileContents = simplexml_load_file("data/".$directory."/$fileName");
	
	// Analyze spatial info on each person in .FMR file
	// Needed to answer questions:
	// 	How far person migrates from birth to death - add up all segments
	// 	How far person migrates from birth place - just birth place to death place, no segments (as the crow flies)
	$personEventsDist = array();
	$personToParentDist = array();
	$personDeathLatArray = array();
	$personDeathLonArray = array();
	$meanCenterPersonDeath = null;
	$parentBirthLatArray = array();
	$parentBirthLonArray = array();
	$meanCenterParentBirth = null;
	$placeLatLon = array();
	foreach($fileContents->person as $person)
	{
		$counter = null;
		$prevLat = null;
		$prevLng = null;
		$curLat = null;
		$curLng = null;

		$totalDist = -1;
		$segDist = array();
		foreach($person->event as $event)
		{
			if($event["type"] == "birth")
			{
				$childLat = $event->place->lat;
				$childLng = $event->place->lng;
				array_push($placeLatLon,$childLat);
				array_push($placeLatLon,$childLng);
				foreach($person->parent as $parent)
				{
					foreach($parent->event as $pEvent)
					{
						if($pEvent["type"] == "birth")
						{
							$parentLat = $pEvent->place->lat;
							$parentLng = $pEvent->place->lng;
							if((double)$parentLat != 0 && (double)$parentLng != 0)
							{
								array_push($parentBirthLatArray, (double)$parentLat);
								array_push($parentBirthLonArray, (double)$parentLng);
							}
							$personParentDist = greatCircleDist($childLat, $childLng, $parentLat, $parentLng);
							$pKey = $person["id"]."+".$parent["id"];
							$personToParentDist["$pKey"] = $personParentDist;
						}
					}
				}
			}
			if($event["type"] == "death")
			{
				$deathLat = $event->place->lat;
				$deathLng = $event->place->lng;
				if((double)$deathLat != 0 && (double)$deathLng)
				{
					array_push($personDeathLatArray, (double)$deathLat);
					array_push($personDeathLonArray, (double)$deathLng);
				}
			}
			$counter++;
			if($counter > 1)
			{
				$prevLat = $curLat;
				$prevLng = $curLng;
				$curLat = $event->place->lat;
				$curLng = $event->place->lng;
				$dist = greatCircleDist($prevLat, $prevLng, $curLat, $curLng);
				$totalDist += $dist;
				$segDist[] = (double)$dist;
			}
			$curLat = $event->place->lat;
			$curLng = $event->place->lng;
		}
		$key = $person["id"];
		$personEventsDist["$key"] = $totalDist;
	}
	//Calculate the mean center of all the parent's birth events
//	echo "<br><br>Parent Lat: ";
//	print_r($parentBirthLatArray);
//	echo "<br>Parent Lon: ";
//	print_r($parentBirthLonArray);
//	echo "<br>Mean Center: ";
	$meanCenterPersonDeath = meanCenter($personDeathLatArray, $personDeathLonArray);
	$directionPersonBirthToMeanCenterDeath = greatCircleDirection( $meanCenterPersonDeath[0],$meanCenterPersonDeath[1],$placeLatLon[0],$placeLatLon[1]);
//	echo "<br><br>Person Birth to Death Direction: ".$directionPersonBirthToMeanCenterDeath;
	$meanCenterParentBirth = meanCenter($parentBirthLatArray, $parentBirthLonArray);
	$directionPersonBirthToMeanCenterParentBirth = greatCircleDirection($meanCenterParentBirth[0],$meanCenterParentBirth[1],$placeLatLon[0],$placeLatLon[1]);
	
	$parentBirthLatLonArray = array();
	$i = 0;
	foreach($parentBirthLatArray as $lat)
	{
		$latlonstring = "".$parentBirthLatArray[$i].",".$parentBirthLonArray[$i];
		array_push($parentBirthLatLonArray,$latlonstring);
		$i++;
	}
	$stDistMeanParentBirthToParentBirths = standardDistance($meanCenterParentBirth[0],$meanCenterParentBirth[1], $parentBirthLatLonArray);
//	echo "<br><br>Person to Parent Direction: ".$directionPersonBirthToMeanCenterParentBirth;
//	print_r($meanCenterParentBirth);
	//echo "<pre>Total Dist: ";
	//print_r($personEventsDist);
	//echo "<br />Parent Birth to Person Birth: ";
	//print_r($personToParentDist);
	//echo "</pre>";

	$fileName = str_replace('.fmr', '.fmra', $fileName);
	$distanceAnalysis = '<analysis>';
	//average distance per person
	$counter = count($personEventsDist);
	$numberOfNonNull = 0;
	$total = 0;
	for($i=0; $i<$counter; $i++)
	{
		if(current($personEventsDist) >= 0)
		{
			$numberOfNonNull++;
			$total += current($personEventsDist);
		}
		next($personEventsDist);
	}
	if($numberOfNonNull != 0)
	{
		$distanceAnalysis .= "<avgDistPerson>".$total/$numberOfNonNull."</avgDistPerson>";
		$distanceAnalysis .= "<personBirthLatLng>".$placeLatLon[0].",".$placeLatLon[1]."</personBirthLatLng>";
		$distanceAnalysis .= "<meanCenterOfPersonDeath>".$meanCenterPersonDeath[0].",".$meanCenterPersonDeath[1]."</meanCenterOfPersonDeath>";
		$distanceAnalysis .= "<directionFromBirthToMeanCenterDeath>".$directionPersonBirthToMeanCenterDeath."</directionFromBirthToMeanCenterDeath>";
		reset($personEventsDist);
		for($i=0; $i<$counter; $i++)
		{
			$key = key($personEventsDist);
			if($personEventsDist[$key] >= 0)
			{
				$distanceAnalysis .= "<distance id='$key'>".$personEventsDist[$key]."</distance>";
			}
			next($personEventsDist);
		}
	}
	//average distance
	$counter = count($personToParentDist);
	$numberOfNonNull = 0;
	$total = 0;
	for($i=0; $i<$counter; $i++)
	{
		if(current($personToParentDist) >= 0)
		{
			$numberOfNonNull++;
			$total += current($personToParentDist);
		}
		next($personToParentDist);
	}
	if($numberOfNonNull != 0)
	{
		$distanceAnalysis .= "<avgDistPersonToParent>".$total/$numberOfNonNull."</avgDistPersonToParent>";
		$distanceAnalysis .= "<meanCenterOfParentBirth>".$meanCenterParentBirth[0].",".$meanCenterParentBirth[1]."</meanCenterOfParentBirth>";
		$distanceAnalysis .= "<standardDistanceParentMeanCenterToParentBirth>".$stDistMeanParentBirthToParentBirths."</standardDistanceParentMeanCenterToParentBirth>";
		$distanceAnalysis .= "<directionFromPersonBirthToMeanCenterParentBirth>".$directionPersonBirthToMeanCenterParentBirth."</directionFromPersonBirthToMeanCenterParentBirth>";
		reset($personToParentDist);
		for($i=0; $i<$counter; $i++)
		{
			$key = key($personToParentDist);
			if($personToParentDist[$key] >= 0)
			{
				$distanceAnalysis .= "<distance id='$key'>".$personToParentDist[$key]."</distance>";
			}
			next($personToParentDist);
		}
		$distanceAnalysis .= '</analysis>';
	}
	file_put_contents("data/analysis/$fileName", $distanceAnalysis);
}

?>