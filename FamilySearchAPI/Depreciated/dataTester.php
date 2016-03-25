<?
// BYUFMR Analyze a .FMR file
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: dataTester.php
// Purpose: Creates a .FMRA file which contains the analysis of a .FMR file


set_time_limit (0);
require_once("analyze.php");
require_once("extractParentBirthPlaces.php");

/*****************************************************************/
// Tool for understanding and analyzing data collection techniques
/*****************************************************************/

function dataTester($fileName, $placeId, $yearOrigin)
{
	$people = array();
	$peopleFromPlace = array();
	$peopleFromPlaceWithCorrectYear = array();
	$peopleFromPlaceWith5YearRange = array();
	$peopleFromPlaceWith10YearRange = array();

	$peopleFromPlaceWithCorrectYearAndParent = array();
	$peopleFromPlaceWith5YearRangeAndParent = array();
	$peopleFromPlaceWith10YearRangeAndParent = array();

	$peopleFromPlaceString = "<persons>";
	$peopleFromPlaceWithCorrectYearString = "<persons>";
	$peopleFromPlaceWith5YearRangeString = "<persons>";
	$peopleFromPlaceWith10YearRangeString = "<persons>";

	$fileExplode = explode(",", $fileName);
	$fileExplode1 = explode("~", $fileExplode[2]);
	
//echo "File Tested: $fileName<br />";
	try
	{
		$fileContents = simplexml_load_file("data/$fileName");
	}
	catch(Exception $e)
	{
		print_r($e);
		break;
	}
	
	foreach($fileContents->person as $person)
	{
		$id = $person["id"];
		if(isset($people["$id"]))
		{
			$people["$id"] = "multiple assertions";
		}
		else
		{
			$people["$id"] = 1;
		}
		foreach($person->event as $event)
		{
			if($event["type"] == "birth")
			{
				if((int)$event->place["id"] == $placeId) //as passed to the function
				{
					$peopleFromPlaceString .= $person->asXML();
					$peopleFromPlace["$id"] = $id;
					$key = jdtogregorian((int)$event->date->astro);

					$_1yearStart = gregoriantojd(1, 1, $yearOrigin);
					$_1yearEnd = gregoriantojd(12, 31, $yearOrigin);
					if((int)$event->date->astro >= $_1yearStart && (int)$event->date->astro <= $_1yearEnd)
					{
						$peopleFromPlaceWithCorrectYear["$id"] = $key." ".(string)$event->date->astro;
						$peopleFromPlaceWithCorrectYearString .= $person->asXML();
						if(isset($person->parent))
						{
							$peopleFromPlaceWithCorrectYearAndParent["$id"] = $key." ".(string)$event->date->astro;
						}
					}

					$_5yearStart = gregoriantojd(1, 1, $yearOrigin - 2);
					$_5yearEnd = gregoriantojd(12, 31, $yearOrigin + 2);
					if((int)$event->date->astro >= $_5yearStart && (int)$event->date->astro <= $_5yearEnd)
					{
						$peopleFromPlaceWith5YearRange["$id"] = $key." ".(string)$event->date->astro;
						$peopleFromPlaceWith5YearRangeString .= $person->asXML();
						if(isset($person->parent))
						{
							$peopleFromPlaceWith5YearRangeAndParent["$id"] = $key." ".(string)$event->date->astro;
						}
					}

					$_10yearStart = gregoriantojd(1, 1, $yearOrigin - 5);
					$_10yearEnd = gregoriantojd(12, 31, $yearOrigin + 5);
					if((int)$event->date->astro >= $_10yearStart && (int)$event->date->astro <= $_10yearEnd )
					{
						$peopleFromPlaceWith10YearRange["$id"] = $key." ".(string)$event->date->astro;
						$peopleFromPlaceWith10YearRangeString .= $person->asXML();
						if(isset($person->parent))
						{
							$peopleFromPlaceWith10YearRangeAndParent["$id"] = $key." ".(string)$event->date->astro;
						}
					}
				}
			}
		}
	}

	$peopleFromPlaceString .= "</persons>";
	$peopleFromPlaceWithCorrectYearString .= "</persons>";
	$peopleFromPlaceWith5YearRangeString .= "</persons>";
	$peopleFromPlaceWith10YearRangeString .= "</persons>";

	file_put_contents("data/YearSubsets/fromPlaceOnly$fileName", $peopleFromPlaceString);
	analyze("fromPlaceOnly$fileName", "YearSubsets");
	extractParentBirthPlaces("fromPlaceOnly$fileName", "YearSubsets");
	file_put_contents("data/YearSubsets/0Year$fileName", $peopleFromPlaceWithCorrectYearString);
	analyze("0Year$fileName", "YearSubsets");
	extractParentBirthPlaces("0Year$fileName", "YearSubsets");
	file_put_contents("data/YearSubsets/5Year$fileName", $peopleFromPlaceWith5YearRangeString);
	analyze("5Year$fileName", "YearSubsets");
	extractParentBirthPlaces("5Year$fileName", "YearSubsets");
	file_put_contents("data/YearSubsets/10Year$fileName", $peopleFromPlaceWith10YearRangeString);
	analyze("10Year$fileName", "YearSubsets");
	extractParentBirthPlaces("10Year$fileName", "YearSubsets");
	
	$name = "$fileExplode[0], $fileExplode[1], $fileExplode1[0]";
	$peopleCount = count($people);
	$peopleFromPlaceCount = count($peopleFromPlace);
	$peopleFromPlaceCorrectYearCount = count($peopleFromPlaceWithCorrectYear);
	$peopleFromPlace5YearRangeCount = count($peopleFromPlaceWith5YearRange);
	$peopleFromPlace10YearRangeCount = count($peopleFromPlaceWith10YearRange);
	$peopleFromPlaceCorrectYearAndParentCount = count($peopleFromPlaceWithCorrectYearAndParent);
	$peopleFromPlace5YearRangeAndParentCount = count($peopleFromPlaceWith5YearRangeAndParent);
	$peopleFromPlace10YearRangAndParenteCount = count($peopleFromPlaceWith10YearRangeAndParent);
	
	echo "<br />Number of unique people recorded: ".count($people)."<br />";
	echo "<br />Number of people born in $name: $peopleFromPlaceCount<br />";
	echo "<br />Number of people born in $name in 1850: ".count($peopleFromPlaceWithCorrectYear)."<br /><br /><br />";
	/*echo "<pre><br />Person => Date & Astro: ";
	print_r($peopleFromPlaceWithCorrectYear);
	echo "</pre>";
	echo "<br />Number of people born in  $name between 1848 & 1852: ".count($peopleFromPlaceWith5YearRange)."<br />";
	echo "<pre><br />Person => Date & Astro: ";
	print_r($peopleFromPlaceWith5YearRange);
	echo "</pre>";
	echo "<br />Number of people born in  $name between 1845 & 1855: ".count($peopleFromPlaceWith10YearRange)."<br />";
	echo "<pre><br />Person => Date & Astro: ";
	print_r($peopleFromPlaceWith10YearRange);
	echo "</pre>";
	*/
	
	if($pgConnection = pg_connect('host=localhost port=5433 dbname=familysearch user=familysearch password=familysearch'))
	{
		//pg_query($pgConnection, "CREATE TABLE dataTester( name text PRIMARY KEY, numUniquePeople INT, numPeopleFromPlace INT, numPeopleFromPlaceAnd1850 INT, numPeopleFromPlace1848to1852 INT, numPeopleFromPlace1845to1855 INT, numPeopleFromPlaceAnd1850AndParent INT, numPeopleFromPlace1848to1852AndParent INT, numPeopleFromPlace1845to1855AndParent INT)");
		if(!pg_query($pgConnection, "INSERT INTO dataTester (name, numUniquePeople, numPeopleFromPlace, numPeopleFromPlaceAnd1850, numPeopleFromPlace1848to1852, numPeopleFromPlace1845to1855, numPeopleFromPlaceAnd1850AndParent, numPeopleFromPlace1848to1852AndParent, numPeopleFromPlace1845to1855AndParent) VALUES('$name', $peopleCount, $peopleFromPlaceCount, $peopleFromPlaceCorrectYearCount, $peopleFromPlace5YearRangeCount, $peopleFromPlace10YearRangeCount, $peopleFromPlaceCorrectYearAndParentCount, $peopleFromPlace5YearRangeAndParentCount, $peopleFromPlace10YearRangAndParenteCount);"))
		{
			echo "DISREGARD THIS WARNING";
		}
	}
	if($pgConnection)
	{
		pg_close();
	}
}

?>