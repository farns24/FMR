<?php
// BYUFMR Spatial Analysis
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Professor in Charge: Dr. Samuel Otterstrom
// Title: extractParentBirthPlaces.php
// Purpose: Perform spatial analysis on .FMR data.
error_reporting(E_ALL);

function extractParentBirthPlaces($fileName, $directory)
{
	// Read in the passed .FMR file
	$fileContents = simplexml_load_file("data/".$directory."/$fileName");
	
	echo "<pre>";
	print_r($fileContents);
	echo "</pre>";
	
	$parentLatLonCS = "Name,Lat,Lon\r\n";
	foreach($fileContents->person as $person)
	{
		foreach($person->parent as $parent)
		{
			$parentNameLatLon = (string)$parent->name.",";
			
			foreach($parent->event as $pEvent)
			{
				if($pEvent["type"] == "birth")
				{
					$parentLat = $pEvent->place->lat;
					$parentLng = $pEvent->place->lng;
					if((double)$parentLat != 0 && (double)$parentLng != 0)
					{
						$parentNameLatLon .= $parentLat.",".$parentLng."\r\n";
						$parentLatLonCS .= (string)$parentNameLatLon;
					}
				}
			}
		}
	}
	
	$fileName = substr($fileName, 0, -4);
	file_put_contents("data/YearSubsets/ParentBirthLatLonFiles/$fileName.csv", $parentLatLonCS);
}

//extractParentBirthPlaces("0YearClairville,Sonoma,California~birth~1880~2009-08-31.fmr","YearSubsets");
?>