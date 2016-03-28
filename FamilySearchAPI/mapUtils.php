<?php

/**
* Maps generations from an array of names
*
*@pre 
*
*@post Every element in the array is either printed to the map, or listed as not found.
*/
function mapGen($arrayName, $fileName, $birthOrDeath)
{
	$size = sizeof($arrayName);
	$missed = 0;
	
	$birth = "birth";
	
	// Make the maps to show birthplaces of each person in each generation
	$maphtml = file_get_contents("data/v2/chr_map_data/$fileName.txt");
	$dataPoints = "";
	$count = 0;
	
	$locationCountSet = array();
	$locationNameSet = array();
	
	//Map <Lat Lon String,Names html>
	$locationPointDuplication = array();
	
	
	//The death locations are mapped but not displayed.  
	if ($birthOrDeath==$birth)
	{
		echo "<h5 class='$fileName'>No location found</h5><ul class='$fileName'>";
	}
	else
	{
		echo $birthOrDeath;
	}
	
	
	//For every person in the array:
	foreach($arrayName as $person)
	{
		
	
		if($birthOrDeath == $birth)
		{
			$lat = $person->getBirthPlace()->Lat();
			$lon = $person->getBirthPlace()->Lon();
			
			
		}
		else
		{
	
			if ($person->getGen()!=0)
			{
				continue;
			}
			$lat = $person->getDeathPlace()->Lat();
			$lon = $person->getDeathPlace()->Lon();
			
		}
		//echo "$lat $lon";
		
		if(!empty($lat) && !empty($lon) && $lat != -999 && $lon != -999)
		{
			
			$locationKey = (string)$person->getBirthPlaceStr();//(string)$lat.(string)$lon;
			if (array_key_exists($locationKey,$locationCountSet))
			{

				$locationCountSet[$locationKey] = $locationCountSet[$locationKey]+1;
			}
			else
			{

				$locationCountSet[$locationKey] = 1;
			}
			
			$locationNameSet[$locationKey];
			$dataInfo = $person->getMapLayout();
			$id = $person->getId();
			$dataPoints .= "var point$count = new GLatLng($lat, $lon);\r\n";
			$dataPoints .= "var mark$count = new GMarker(point$count);\r\n";
			$dataPoints .="GEvent.addListener(mark$count,'mouseover',function(){
				mark$count.openInfoWindowHtml('$dataInfo')
				});\r\n";
			$dataPoints .="GEvent.addListener(mark$count,'click',function(){
			mark$count.openInfoWindowHtml('<a href=\"MapPersonDetails.php?id=$id\">Click for more details</a>')
			});\r\n";	
			$dataPoints .= "map.addOverlay(mark$count);\r\n";

			$count++;
		}
		else
		{
			$info =$person->getMapLayout();
			$id = $person->getId();
			if ($birthOrDeath==$birth)
			{
				echo "<li class='$fileName'><a href='https://familysearch.org/tree/#view=tree&section=pedigree&person=$id'>$info</a></li>";
			}
			$missed++;
		}
	}
	if ($birthOrDeath==$birth)
	{
		echo "</ul><h5>Hits per location</h5><ul class='$fileName'>";
	
		foreach($locationCountSet as $key=>$value)
		{
		
		
			echo "<li class='$fileName'>$key: $value Matches</li>";
		
		}
		echo "</ul >";
	}
	
	if ($count + $missed != $size)
	{
		echo <<<HTML
		
		
		<Dialog>
			<div class="alert alert-danger" role="alert">
				<strong>Oh snap!</strong>Some people fell through the cracks.
			</div>
		</Dialog>

		
HTML;
	
	}
	
	$maphtml = str_replace("%data%", $dataPoints, $maphtml);
	//echo "<h2>File written to </h2>data/v2/chr_map_data/$fileName.html<h3>data</h3><code>$dataPoints</code>";
	file_put_contents("data/v2/chr_map_data/$fileName.html", $maphtml);
}

// Function to map mean centers
function mcmap($fileName, $lat, $lon)
{
	$maphtml = file_get_contents("data/v2/chr_map_data/$fileName.txt");
	$dataPoints = "var point = new GLatLng($lat, $lon);\r\n";
	$dataPoints .= "map.addOverlay(new GMarker(point));\r\n";
	$maphtml = str_replace("%data%", $dataPoints, $maphtml);
	file_put_contents("data/v2/chr_map_data/$fileName.html", $maphtml);
}




?>