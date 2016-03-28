<?php
// BYUFMR Analyze a .FMR file
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: dataTester.php
// Purpose: Performs analysis to build analysis HTML tables for display

set_time_limit (0);
require_once("classPerson.php");
require_once("classFamily.php");
require_once("classPlace.php");
require_once("PointGeoStatistics.php");
require_once("dataTesterFiles/RCM.php");
require_once("dataTesterFiles/chr_calcCA.php");
require_once("dataTesterFiles/chr_calc.php");
require_once("scrubEurope.php");
require_once("findMeanDistance.php");
require_once("dataTesterFiles/chr_calcUS.php");
require_once("mapUtils.php");
require_once("csiUtils.php");
require_once("preCondition.php");
global $rootLatLonArray;

/**
* @pre $credentials contain access Token
*
*/
function dataTester($fileName,$credentials)
{
	preAccessToken($credentials);
	

	//var_dump($credentials);
	
	if (strpos($fileName,"Forward"))
	{
		$direction = "Forward";
	}
	else if (strpos($fileName,"Backward"))
	{
		$direction = "Backward";
	}
	else
	{
		$direction = "Backward";
		
	}

	echo "File Name ".$fileName."<br>";
	//IF THERE WAS NO FILENAME PASSED, ANALYZE EVERY FILE IN THE DIRECTORY
	if($fileName == "Choose a file")
	{
		$analysisFileName = $_POST['analysisFile'];

		echo $analysisFileName." :analysisFileName<br> ";
		
		// Clear out the previous data in the file
		$analysisFileOutput = file_get_contents("data/v2/analysisCSV/$analysisFileName");
		list($header, $body) = explode("~", $analysisFileOutput);
		$analysisFileOutput = $header."~\r\n";
		file_put_contents("data/v2/analysisCSV/$analysisFileName", $analysisFileOutput);
		
		
		$analysisFileName = str_replace(".txt", "", $analysisFileName);
		$dir    = 'data/v2/'.$analysisFileName."/";
		$files = scandir($dir);
		$files = array_slice($files, 2);
		
		print_r($files);
		
		foreach($files as $file)
		{
			dataTester($analysisFileName."/".$file,$credentials);
		}
	}

	else
	{
		//****************************************************//
		// Open the file to be tested and load it as a simpleXML object //
		//***************************************************//
		try
		{
			//Step: Pull the xml file from the search
			$xml = simplexml_load_file("data/v2/$fileName");
			echo $analysisFileName = $_POST['analysisFile'];
		
			//$analysisFileOutput = file_get_contents("data/v2/analysisCSV/$analysisFileName");
			//******************************************************************************//
			// Uses a two-pass method of populating Family, Person, and Place structures 			//
			//	First pass:													//
			//		- Make all the structures (Family, Person, and Place)					//
			//		- Fill in the info that we can, as we build the structures. This includes:		//
			//			- All the info for Family and Place structures					//
			//			- Id, birthplace, deathplace for Person structures.					//
			//				- Also store a string for mother and father for references		//
			//		- We can't fill in the mother, father, or generation fields in Person structures	//
			//		   because they are dependant on other Person structures that may not have been	//
			//		   made yet.												//
			//	Second pass:												//
			//		- See next COMMENT BLOCK for a description	of second pass			//
			//******************************************************************************//
			// Create a Family object for each family in the FMR file.
			//  Create an array to store Family objects
			// Variable to count and name the Family objects
			$famCount = 1;
			$famArray = array();

			// Count for number of families
			$totalFamCount = count($xml->family);
			
			//Loop through each family
			
			foreach($xml->family as $fam)
			{
	
				$famArray = processFamily($famCount,$fam,$famArray,$direction,$credentials);
				$famCount++;
				
				
			}

			//return;
			//******************************************************************************//
			// At this point we have:												//
			//	- An array of Family structures that contains all the families in a .FMR file			//
			//	- All families from the .FMR file in Family structures which contain:				//
			//		- An array of Person which are family members						//
			//	- All persons in each family in a Person structure which contain:				//
			//		- A birth place and death place represented as Place structures			//
			// We still need to assign a generation to each person.							//
			// Each person also needs to be linked to a father and a mother Person structure			//
			//															//
			//	Second pass:												//
			//		- Fill in the rest of the fields for each Person structure					//
			//			- Set the Father, Mother, and Generation of each Person Structure		//
			//******************************************************************************//
			
			// Initialize 5 generation arrays - one for each generation //
			$rootArray = array();
			$rootArrayMale = array();
			$rootArrayFemale = array();
			$firstArray = array();
			$firstArrayMale = array();
			$firstArrayFemale = array();
			$secondArray = array();
			$secondArrayMale = array();
			$secondArrayFemale = array();
			$thirdArray = array();
			$thirdArrayMale = array();
			$thirdArrayFemale = array();
			$fourthArray = array();
			$fourthArrayMale = array();
			$fourthArrayFemale = array();
			
			$famCount = 0;
			//echo "<div class='well'>Sanity Check</div>";
			foreach($xml->family as $fam)
			{
				//echo "<div class='well'>Family</div>";
				// Iterate through the people in each family - from the XML, not the family array
				$personCount = 1;
				 
				foreach($fam->person as $person)
				{
					//echo "<div class='well'>Family person</div>";
					// Set the person to be used in this loop (Calling classFamily::getMember();)
					$member = $famArray[$famCount]->getMember((string)$person['id']);
		
					// If the person is the first in the list, they are by default the ROOT generation
					if($personCount == 1)
					{
						//echo "<div class='well'>Root Person analized</div>";
						// Set generation to ROOT
						$member->setGen(0);
						$rootArray[] = $member;
						if ($direction=="Backward")
						{
							// Recursively set generations for the rest of the family
							setParents($famArray[$famCount], $member, 1);
						}
						else
						{
						
							setChildren($famArray[$famCount], $member, 1);
						}
					}
					$personCount++;

					//******************************************************************************//
					// Calculate migration between generations								//
					//******************************************************************************//
					// Now populate 5 arrays - one for each generation							//
					//******************************************************************************//
					
					// Switch on the generation of the member Person
					$gen = $member->getGen();

					echo "<h2>Generation $gen</h2>";
					//var_dump($gen);
					switch($gen)
					{
						case 0:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$member->setDistTo(2,distAncestors($member, $member, 0, 2,$direction));
							$member->setDistTo(3,distAncestors($member, $member, 0, 3,$direction));
							$member->setDistTo(4,distAncestors($member, $member, 0, 4,$direction));

							if(strtolower($member->getGender()) == "male")
								$rootArrayMale[] = $member;
							if(strtolower($member->getgender()) == "female")
								$rootArrayFemale[] = $member;
							break;
						case 1:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$member->setDistTo(2,distAncestors($member, $member, 0, 2,$direction));
							$member->setDistTo(3,distAncestors($member, $member, 0, 3,$direction));
							
							
							$firstArray[] = $member;

							if(strtolower($member->getGender()) == "male")
								$firstArrayMale[] = $member;
							if(strtolower($member->getgender()) == "female")
								$firstArrayFemale[] = $member;
							break;
						case 2:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$member->setDistTo(2,distAncestors($member, $member, 0, 2,$direction));
							$secondArray[] = $member;
							//echo json_encode($secondArray);
							if(strtolower($member->getGender()) == "male")
								$secondArrayMale[] = $member;
							if(strtolower($member->getgender()) == "female")
								$secondArrayFemale[] = $member;
							break;
						case 3:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$thirdArray[] = $member;
							//echo json_encode($thirdArray);
							if(strtolower($member->getGender()) == "male")
								$thirdArrayMale[] = $member;
							if(strtolower($member->getgender()) == "female")
								$thirdArrayFemale[] = $member;
							break;
						case 4:
							$fourthArray[] = $member;
							//echo json_encode($fourthArray);
							if(strtolower($member->getGender()) == "male")
								$fourthArrayMale[] = $member;
							if(strtolower($member->getgender()) == "female")
								$fourthArrayFemale[] = $member;
							break;
						default:
							echo "Person not included in a generation array!";
					}
					//******************************************************************************//
					// End calculating migration between generations								//
					//******************************************************************************//
				}			
				// Increment the counter
				$famCount++;
			}

			storeGenerationData($rootArray,$firstArray,$secondArray,$thirdArray,$fourthArray);
			//******************************************************************************//
			// Second pass is completed.											//
			// Statistical analysis of the generations can be performed.						//
			//******************************************************************************//
			
			// Read in the text of the html so that we can manipulate it
			$htmlOut = file_get_contents("FamilySearchAPI/HTMLAnalysis.html");
			
			// Filename of analyzed file
			$htmlOut = str_replace("%fileName%", "<a href=\"/fmr/data/v2/".$fileName."\" target=\"_blank\">$fileName</a>", $htmlOut);
		
			$htmlOut = str_replace("%searchFile%",$fileName,$htmlOut);
		
			$analysisFileOutput =addFileName($fileName,$analysisFileOutput);
			
			// Total number of families
			$numFamilies = count($famArray);
			$htmlOut = str_replace("%numFamilies%", $numFamilies, $htmlOut);
			($numFamilies != 0)? $analysisFileOutput .= $numFamilies.":" : $analysisFileOutput .= "N/A:";
			
			// Total number of people
			$numPeople = getTotalPeopleCount($famArray);	
			$htmlOut = str_replace("%numPeople%", $numPeople, $htmlOut);		
			$gen1know = getGenCount($firstArray);
			
			// Number of people, 1st gen
			$numPeople1 = count($firstArray);
			echo "<br>Number of people in first generation ".count($firstArray);
			$htmlOut = str_replace("%1Genposs%", ($numFamilies*2), $htmlOut);
			(($numFamilies*2) != 0)? $analysisFileOutput .= ($numFamilies*2).":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%1Genknow%", $gen1know, $htmlOut);
			($gen1know != 0)? $analysisFileOutput .= $gen1know.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%1Genuniq%", $numPeople1, $htmlOut);
			($numPeople1 != 0)? $analysisFileOutput .= $numPeople1.":" : $analysisFileOutput .= "N/A:";

			$gen2know = getGenCount($secondArray);
			
			// Number of people, 2nd gen
			$numPeople2 = count($secondArray);
			$htmlOut = str_replace("%2Genposs%", ($numFamilies*4), $htmlOut);
			(($numFamilies*4) != 0)? $analysisFileOutput .= ($numFamilies*4).":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%2Genknow%", $gen2know, $htmlOut);
			($gen2know != 0)? $analysisFileOutput .= $gen2know.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%2Genuniq%", $numPeople2, $htmlOut);
			($numPeople2 != 0)? $analysisFileOutput .= $numPeople2.":" : $analysisFileOutput .= "N/A:";

			$gen3know = getGenCount($thirdArray);
			
			// Number of people, 3rd gen
			$numPeople3 = count($thirdArray);
			$htmlOut = str_replace("%3Genposs%", ($numFamilies*8), $htmlOut);
			(($numFamilies*8) != 0)? $analysisFileOutput .= ($numFamilies*8).":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%3Genknow%", $gen3know, $htmlOut);
			($gen3know != 0)? $analysisFileOutput .= $gen3know.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%3Genuniq%", $numPeople3, $htmlOut);
			($numPeople3 != 0)? $analysisFileOutput .= $numPeople3.":" : $analysisFileOutput .= "N/A:";
			
			$gen4know = getGenCount($fourthArray);

			// Number of people, 4th gen
			$numPeople4 = count($fourthArray);
			$htmlOut = str_replace("%4Genposs%", ($numFamilies*16), $htmlOut);
			(($numFamilies*16) != 0)? $analysisFileOutput .= ($numFamilies*16).":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%4Genknow%", $gen4know, $htmlOut);
			($gen4know != 0)? $analysisFileOutput .= $gen4know.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%4Genuniq%", $numPeople4, $htmlOut);
			($numPeople4 != 0)? $analysisFileOutput .= $numPeople4.":" : $analysisFileOutput .= "N/A:";
			
			// Start Mean Distance of migration matrix
			// Mdist, root - 1 (Parents)
			$mdroot1 = findMeanDistance($rootArray, 1);
			$htmlOut = str_replace("%mdDistRoot1%", $mdroot1, $htmlOut);
			$analysisFileOutput .= $mdroot1.":";
			
			// Mdist, root - 2 (GParents)
			$mdroot2 = findMeanDistance($rootArray, 2);
			$htmlOut = str_replace("%mdDistRoot2%", $mdroot2, $htmlOut);
			$analysisFileOutput .= $mdroot2.":";
			
			// Mdist, root - 3 (GGParents)
			$mdroot3 = findMeanDistance($rootArray, 3);
			$htmlOut = str_replace("%mdDistRoot3%", $mdroot3, $htmlOut);
			$analysisFileOutput .= $mdroot3.":";
			
			// Mdist, root - 4 (GGGParents)
			$mdroot4 = findMeanDistance($rootArray, 4);
			$htmlOut = str_replace("%mdDistRoot4%", $mdroot4, $htmlOut);
			$analysisFileOutput .= $mdroot4.":";
			
			// Mdist, first - 1 (Parents)
			$md12 = findMeanDistance($firstArray, 1);
			$htmlOut = str_replace("%mdDist12%", $md12, $htmlOut);
			$analysisFileOutput .= $md12.":";
			
			// Mdist, first - 2  (GParents)
			$md13 = findMeanDistance($firstArray, 2);
			$htmlOut = str_replace("%mdDist13%", $md13, $htmlOut);
			$analysisFileOutput .= $md13.":";
			
			// Mdist, first - 3 (GGParents)
			$md14 = findMeanDistance($firstArray, 3);
			$htmlOut = str_replace("%mdDist14%", $md14, $htmlOut);
			$analysisFileOutput .= $md14.":";

			// Mdist, second - 1 (Parents)
			$md23 = findMeanDistance($secondArray, 1);
			$htmlOut = str_replace("%mdDist23%", $md23, $htmlOut);
			$analysisFileOutput .= $md23.":";
			
			// Mdist, Second - 2 (GParents)
			$md24 = findMeanDistance($secondArray, 2);
			$htmlOut = str_replace("%mdDist24%", $md24, $htmlOut);
			$analysisFileOutput .= $md24.":";
		
			// Mdist, Third - 1 (Parents)
			$md34 = findMeanDistance($thirdArray, 1);
			$htmlOut = str_replace("%mdDist34%", $md34, $htmlOut);
			$analysisFileOutput .= $md34.":";

			// Males Only
			// Mdist, root - 1 (Parents)
			$mdroot1m = findMeanDistance($rootArrayMale, 1);
			$htmlOut = str_replace("%mdDistRoot1m%", $mdroot1m, $htmlOut);
			$analysisFileOutput .= $mdroot1m.":";
			
			// Mdist, root - 2 (GParents)
			$mdroot2m = findMeanDistance($rootArrayMale, 2);
			$htmlOut = str_replace("%mdDistRoot2m%", $mdroot2m, $htmlOut);
			$analysisFileOutput .= $mdroot2m.":";
			
			// Mdist, root - 3 (GGParents)
			$mdroot3m = findMeanDistance($rootArrayMale, 3);
			$htmlOut = str_replace("%mdDistRoot3m%", $mdroot3m, $htmlOut);
			$analysisFileOutput .= $mdroot3m.":";
			
			// Mdist, root - 4 (GGGParents)
			$mdroot4m = findMeanDistance($rootArrayMale, 4);
			$htmlOut = str_replace("%mdDistRoot4m%", $mdroot4m, $htmlOut);
			$analysisFileOutput .= $mdroot4m.":";
			
			// Mdist, first - 1 (Parents)
			$md12m = findMeanDistance($firstArrayMale, 1);
			$htmlOut = str_replace("%mdDist12m%", $md12m, $htmlOut);
			$analysisFileOutput .= $md12m.":";
			
			// Mdist, first - 2  (GParents)
			$md13m = findMeanDistance($firstArrayMale, 2);
			$htmlOut = str_replace("%mdDist13m%", $md13m, $htmlOut);
			$analysisFileOutput .= $md13m.":";
			
			// Mdist, first - 3 (GGParents)
			$md14m = findMeanDistance($firstArrayMale, 3);
			$htmlOut = str_replace("%mdDist14m%", $md14m, $htmlOut);
			$analysisFileOutput .= $md14m.":";

			// Mdist, second - 1 (Parents)
			$md23m = findMeanDistance($secondArrayMale, 1);
			$htmlOut = str_replace("%mdDist23m%", $md23m, $htmlOut);
			$analysisFileOutput .= $md23m.":";
			
			// Mdist, Second - 2 (GParents)
			$md24m = findMeanDistance($secondArrayMale, 2);
			$htmlOut = str_replace("%mdDist24m%", $md24m, $htmlOut);
			$analysisFileOutput .= $md24m.":";
			
			// Mdist, Third - 1 (Parents)
			$md34m = findMeanDistance($thirdArrayMale, 1);
			$htmlOut = str_replace("%mdDist34m%", $md34m, $htmlOut);
			$analysisFileOutput .= $md34m.":";

			// Females Only
			// Mdist, root - 1 (Parents)
			$mdroot1f = findMeanDistance($rootArrayFemale, 1);
			$htmlOut = str_replace("%mdDistRoot1f%", $mdroot1f, $htmlOut);
			$analysisFileOutput .= $mdroot1f.":";
			
			// Mdist, root - 2 (GParents)
			$mdroot2f = findMeanDistance($rootArrayFemale, 2);
			$htmlOut = str_replace("%mdDistRoot2f%", $mdroot2f, $htmlOut);
			$analysisFileOutput .= $mdroot2f.":";
			
			// Mdist, root - 3 (GGParents)
			$mdroot3f = findMeanDistance($rootArrayFemale, 3);
			$htmlOut = str_replace("%mdDistRoot3f%", $mdroot3f, $htmlOut);
			$analysisFileOutput .= $mdroot3f.":";
			
			// Mdist, root - 4 (GGGParents)
			$mdroot4f = findMeanDistance($rootArrayFemale, 4);
			$htmlOut = str_replace("%mdDistRoot4f%", $mdroot4f, $htmlOut);
			$analysisFileOutput .= $mdroot4f.":";
			
			// Mdist, first - 1 (Parents)
			$md12f = findMeanDistance($firstArrayFemale, 1);
			$htmlOut = str_replace("%mdDist12f%", $md12f, $htmlOut);
			$analysisFileOutput .= $md12f.":";
			
			// Mdist, first - 2  (GParents)
			$md13f = findMeanDistance($firstArrayFemale, 2);
			$htmlOut = str_replace("%mdDist13f%", $md13f, $htmlOut);
			$analysisFileOutput .= $md13f.":";
			
			// Mdist, first - 3 (GGParents)
			$md14f = findMeanDistance($firstArrayFemale, 3);
			$htmlOut = str_replace("%mdDist14f%", $md14f, $htmlOut);
			$analysisFileOutput .= $md14f.":";

			// Mdist, second - 1 (Parents)
			$md23f = findMeanDistance($secondArrayFemale, 1);
			$htmlOut = str_replace("%mdDist23f%", $md23f, $htmlOut);
			$analysisFileOutput .= $md23f.":";
			
			// Mdist, Second - 2 (GParents)
			$md24f = findMeanDistance($secondArrayFemale, 2);
			$htmlOut = str_replace("%mdDist24f%", $md24f, $htmlOut);
			$analysisFileOutput .= $md24f.":";
			
			// Mdist, Third - 1 (Parents)
			$md34f = findMeanDistance($thirdArrayFemale, 1);
			$htmlOut = str_replace("%mdDist34f%", $md34f, $htmlOut);
			$analysisFileOutput .= $md34f.":";

			
			// Ancestor Dispersal Indexes		
			if ($mdroot1!=0)
			{
				$htmlOut = str_replace("%adi12%", number_format(($md12/$mdroot1), 2, '.', ''), $htmlOut);
				$analysisFileOutput .= ($md12/$mdroot1).":";
				$htmlOut = str_replace("%adi23%", number_format(($md23/$mdroot1), 2, '.', ''), $htmlOut);
				$analysisFileOutput .= ($md23/$mdroot1).":";
				$htmlOut = str_replace("%adi34%", number_format(($md34/$mdroot1), 2, '.', ''), $htmlOut);
				$analysisFileOutput .= ($md34/$mdroot1).":";
			}
			
			// Mean center, root gen
			$mcRoot = meanCenterPersonArray($rootArray);
			$rootLatLonArray = $mcRoot;
			$htmlOut = str_replace("%mcRoot%", (number_format($mcRoot[0], 4, '.', '').", ".number_format($mcRoot[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mcRoot[0], 4, '.', '').":".number_format($mcRoot[1], 4, '.', '').":";
			
			// Mean center, 1st gen
			$mc1 = meanCenterPersonArray($firstArray);
			mcmap("mcmap1", $mc1[0], $mc1[1]);
			$htmlOut = str_replace("%mc1%", (number_format($mc1[0], 4, '.', '').", ".number_format($mc1[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc1[0], 4, '.', '').":".number_format($mc1[1], 4, '.', '').":";

			// Mean center, 2nd gen
			$mc2 = meanCenterPersonArray($secondArray);
			mcmap("mcmap2", $mc2[0], $mc2[1]);
			$htmlOut = str_replace("%mc2%", (number_format($mc2[0], 4, '.', '').", ".number_format($mc2[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc2[0], 4, '.', '').":".number_format($mc2[1], 4, '.', '').":";
			
			//Mean center, 3rd gen
			$mc3 = meanCenterPersonArray($thirdArray);
			mcmap("mcmap3", $mc3[0], $mc3[1]);
			$htmlOut = str_replace("%mc3%", (number_format($mc3[0], 4, '.', '').", ".number_format($mc3[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc3[0], 4, '.', '').":".number_format($mc3[1], 4, '.', '').":";
			
			// Mean center, 4th gen
			$mc4 = meanCenterPersonArray($fourthArray);
			mcmap("mcmap4", $mc4[0], $mc4[1]);
			$htmlOut = str_replace("%mc4%", (number_format($mc4[0], 4, '.', '').", ".number_format($mc4[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc4[0], 4, '.', '').":".number_format($mc4[1], 4, '.', '').":";
			
			
			// Start MC Distance matrix
			// MC dist, root - 1 - %mcDistRoot1%
			$htmlOut = str_replace("%mcDistRoot1%", number_format(distVincenty($mcRoot[0], $mcRoot[1], $mc1[0], $mc1[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mcRoot[0], $mcRoot[1], $mc1[0], $mc1[1]).":";
			
			// MC dist, root - 2
			$htmlOut = str_replace("%mcDistRoot2%", number_format(distVincenty($mcRoot[0], $mcRoot[1], $mc2[0], $mc2[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mcRoot[0], $mcRoot[1], $mc2[0], $mc2[1]).":";
			
			// MC dist, root - 3
			$htmlOut = str_replace("%mcDistRoot3%", number_format(distVincenty($mcRoot[0], $mcRoot[1], $mc3[0], $mc3[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mcRoot[0], $mcRoot[1], $mc3[0], $mc3[1]).":";
			
			// MC dist, root - 4
			$htmlOut = str_replace("%mcDistRoot4%", number_format(distVincenty($mcRoot[0], $mcRoot[1], $mc4[0], $mc4[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mcRoot[0], $mcRoot[1], $mc4[0], $mc4[1]).":";
			
			// MC dist, 1 - 2
			$htmlOut = str_replace("%mcDist12%", number_format(distVincenty($mc1[0], $mc1[1], $mc2[0], $mc2[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mc1[0], $mc1[1], $mc2[0], $mc2[1]).":";
			
			// MC dist, 1 - 3
			$htmlOut = str_replace("%mcDist13%", number_format(distVincenty($mc1[0], $mc1[1], $mc3[0], $mc3[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mc1[0], $mc1[1], $mc3[0], $mc3[1]).":";
			
			// MC dist, 1 - 4
			$htmlOut = str_replace("%mcDist14%", number_format(distVincenty($mc1[0], $mc1[1], $mc4[0], $mc4[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mc1[0], $mc1[1], $mc4[0], $mc4[1]).":";
			
			// MC dist, 2 - 3
			$htmlOut = str_replace("%mcDist23%", number_format(distVincenty($mc2[0], $mc2[1], $mc3[0], $mc3[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mc2[0], $mc2[1], $mc3[0], $mc3[1]).":";
			
			// MC dist, 2 - 4
			$htmlOut = str_replace("%mcDist24%", number_format(distVincenty($mc2[0], $mc2[1], $mc4[0], $mc4[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mc2[0], $mc2[1], $mc4[0], $mc4[1]).":";
			
			// MC dist, 3 - 4
			$htmlOut = str_replace("%mcDist34%", number_format(distVincenty($mc3[0], $mc3[1], $mc4[0], $mc4[1]), 4, '.', ''), $htmlOut);
			$analysisFileOutput .= distVincenty($mc3[0], $mc3[1], $mc4[0], $mc4[1]).":";
				
			// Standard distance, root gen is always 0
			// Standard distance, 1st gen - %sd1%
			$sd1 = standardDistancePersonArray($mc1[0], $mc1[1], $firstArray);
			$htmlOut = str_replace("%sd1%", number_format($sd1, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd1.":";
			
			// Standard distance, 2nd gen
			$sd2 = standardDistancePersonArray($mc2[0], $mc2[1], $secondArray);
			$htmlOut = str_replace("%sd2%", number_format($sd2, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd2.":";
			
			// Standard distance, 3rd gen
			$sd3 = standardDistancePersonArray($mc3[0], $mc3[1], $thirdArray);
			$htmlOut = str_replace("%sd3%", number_format($sd3, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd3.":";
			
			// Standard distance, 4th gen
			$sd4 = standardDistancePersonArray($mc4[0], $mc4[1], $fourthArray);
			$htmlOut = str_replace("%sd4%", number_format($sd4, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd4.":";

			// Ancestor Concentration Indexes
			if($sd1!=0){$htmlOut = str_replace("%aci12%", number_format(($sd2/$sd1), 2, '.', ''), $htmlOut);}
			($sd1!=0 && $sd2/$sd1 != 0)? $analysisFileOutput .= number_format(($sd2/$sd1), 3, '.', '').":" : $analysisFileOutput .= "N/A:";
			if ($sd1!=0){$htmlOut = str_replace("%aci13%", number_format(($sd3/$sd1), 2, '.', ''), $htmlOut);}
			($sd1 !=0 && $sd3/$sd1 != 0)? $analysisFileOutput .= number_format(($sd3/$sd1), 3, '.', '').":" : $analysisFileOutput .= "N/A:";
			if ($sd1!=0){$htmlOut = str_replace("%aci14%", number_format(($sd4/$sd1), 2, '.', ''), $htmlOut);}
			($sd1!=0 && $sd4/$sd1 != 0)? $analysisFileOutput .= number_format(($sd4/$sd1), 3, '.', '').":" : $analysisFileOutput .= "N/A:";
			if ($sd2!=0){$htmlOut = str_replace("%aci23%", number_format(($sd3/$sd2), 2, '.', ''), $htmlOut);}
			($sd2!=0 && $sd3/$sd2 != 0)? $analysisFileOutput .= number_format(($sd3/$sd2), 3, '.', '').":" : $analysisFileOutput .= "N/A:";
			if ($sd2!=0){$htmlOut = str_replace("%aci24%", number_format(($sd4/$sd2), 2, '.', ''), $htmlOut);}
			($sd2!=0 && $sd4/$sd2 != 0)? $analysisFileOutput .= number_format(($sd4/$sd2), 3, '.', '').":" : $analysisFileOutput .= "N/A:";
			if ($sd3!=0){$htmlOut = str_replace("%aci34%", number_format(($sd4/$sd3), 2, '.', ''), $htmlOut);}
			($sd3!=0 && $sd4/$sd3 != 0)? $analysisFileOutput .= number_format(($sd4/$sd3), 3, '.', '').":" : $analysisFileOutput .= "N/A:";
			
			// Community Stability Indexes		
			$valueArray = array();
			$csi1 = csi($rootArray, 1, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot1%", $csi1, $htmlOut);
			$analysisFileOutput .= $csi1.":";
			$csi2 = csi($rootArray, 2, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot2%", $csi2, $htmlOut);
			$analysisFileOutput .= $csi2.":";
			$csi3 = csi($rootArray, 3, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot3%", $csi3, $htmlOut);
			$analysisFileOutput .= $csi3.":";
			$csi4 = csi($rootArray, 4, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot4%", $csi4, $htmlOut);
			$analysisFileOutput .= $csi4.":";
			
	//########################################################################//
	// Ravenstiens' Law of Close Migration -RCM

			unset($valueArray);
			$valueArray = array();
			$rcm1 = RCM($rootArray, 1);
			$analysisFileOutput .= $rcm1.":";
			$rcm2 = RCM($rootArray, 2);
			$analysisFileOutput .= $rcm2.":";
			$rcm3 = RCM($rootArray, 3);
			$analysisFileOutput .= $rcm3.":";
			$rcm4 = RCM($rootArray, 4);
			$analysisFileOutput .= $rcm4.":";

	//########################################################################//
			
			
			// Community Heritage Ratios
			if($analysisFileName == "California.txt") {
				$chrroot1 = chr_calcCA($firstArray, "r1", $analysisFileName);
				$analysisFileOutput .= $chrroot1;
				$chrroot2 = chr_calcCA($secondArray, "r2", $analysisFileName);
				$analysisFileOutput .= $chrroot2;
				$chrroot3 = chr_calcCA($thirdArray, "r3", $analysisFileName);
				$analysisFileOutput .= $chrroot3;
				$chrroot4 = chr_calcCA($fourthArray, "r4", $analysisFileName);
				$analysisFileOutput .= $chrroot4;
			}
			elseif($analysisFileName == "CommunityHeritageRatioUS.txt"){
				$chrroot1 = chr_calcUS($firstArray, "r1", $analysisFileName);
				$analysisFileOutput .= "\r\nFirst Generation\r\n";
				$analysisFileOutput .= $chrroot1;
				$chrroot2 = chr_calcUS($secondArray, "r2", $analysisFileName);
				$analysisFileOutput .= "Second Generation\r\n";
				$analysisFileOutput .= $chrroot2;
				$chrroot3 = chr_calcUS($thirdArray, "r3", $analysisFileName);
				$analysisFileOutput .= "Third Generation\r\n";
				$analysisFileOutput .= $chrroot3;
				$chrroot4 = chr_calcUS($fourthArray, "r4", $analysisFileName);
				$analysisFileOutput .= "Fourth Generation\r\n";
				$analysisFileOutput .= $chrroot4;
			}
			else {
				$chrroot1 = chr_calc($firstArray, "r1", $analysisFileName);
				$analysisFileOutput .= $chrroot1;
				$chrroot2 = chr_calc($secondArray, "r2", $analysisFileName);
				$analysisFileOutput .= $chrroot2;
				$chrroot3 = chr_calc($thirdArray, "r3", $analysisFileName);
				$analysisFileOutput .= $chrroot3;
				$chrroot4 = chr_calc($fourthArray, "r4", $analysisFileName);
				$analysisFileOutput .= $chrroot4;
			}

			// Then we need to plug the percentage of each origin place into an HTML string
			$htmlOut = str_replace("%chrroot1%", str_replace("\r\n", "</br>", $chrroot1), $htmlOut);
			$htmlOut = str_replace("%chrroot2%", str_replace("\r\n", "</br>", $chrroot2), $htmlOut);
			$htmlOut = str_replace("%chrroot3%", str_replace("\r\n", "</br>", $chrroot3), $htmlOut);
			$htmlOut = str_replace("%chrroot4%", str_replace("\r\n", "</br>", $chrroot4), $htmlOut);
			
			$analysisFileOutput .= "\r\n";
			file_put_contents("data/v2/analysisCSV/$analysisFileName", $analysisFileOutput);

			$htmlOut .= "<a href='data/v2/analysisCSV/$analysisFileName' target='_blank'>Download Analysis</a>";
			
			// Insert the mean distance the root generation migrated birth to death
			$mdrbd = mdRootDeath($rootArray);
			$htmlOut = str_replace("%mdrbd%", $mdrbd[0], $htmlOut);
			$htmlOut = str_replace("%numBD%", $mdrbd[1], $htmlOut);
			
			// Display the HTML that we've altered to include calculated stats
			echo $htmlOut;
			echo "<h3>Generation Details</h3>";
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen1head'>Generation 1</h4>";
			mapGen($firstArray, "map1", "birth");
			echo "</div>";
			
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen2head'>Generation 2</h4>";
			mapGen($secondArray, "map2", "birth");
			echo "</div>";
			
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen3head'>Generation 3</h4>";
			mapGen($thirdArray, "map3", "birth");
			echo "</div>";
			
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen4head'>Generation 4</h4>";
			mapGen($fourthArray, "map4", "birth");
			echo "</div>";

			unset($htmlOut,$xml,$analysisFileOutput,$person,$famArray,$rootArray,$rootArrayMale,$rootArrayFemale,$firstArray,$firstArrayMale,$firstArrayFemale,$secondArray,$secondArrayMale,$secondArrayFemale,$thirdArray,$thirdArrayMale,$thirdArrayFemale,$fourthArray,$fourthArrayMale,$fourthArrayFemale
	);

		}
		catch(Exception $e)
		{
			print_r($e);
			break;
		}
	}
}

// Recursive function to set the generations of an entire family

/**
* Loop through the children of the member provided
*
*for each child of the target member
*
*Look up member in the family array
*
*if found, set the 
*
*/
function setChildren(&$family, $member, $gen)
{
	//echo "<h1>Set Children Generation</h1>$gen";
	// Check generation - cannot be over 4
	if($gen > 4)
	{
		//echo "<div class='well'>$gen out of bounds</div>";
		return;
	}
	// Check if father is in the $family array

	//For each child of the member
	if ($member->getChildArray()){
		foreach($member->getChildArray() as $key => $child){
			
			//If the child listed is included in the family map
			if( array_key_exists((string)$child->attributes()->id, $family->members ))
			{
				//Indicates that individual is not being added to family array.
				//echo "<h1>Key exists</h1>";
				// Set the Child of the $member if the child ID is DIFFERENT than the parent ID
				if($family->getMember((string)$member->getId()) != $family->getMember($key))
				{
					$family->getMember($member->getId())->addChildModel($family->members[(string)$key]);
					
						//$tempMember = $family->members[(string)$key];
						$family->getMember((string)$member->getId())->getChildModel((string)$key)->addChild();

						
						// Set the generation
					$family->getMember((string)$member->getId())->getChildModel((string)$key)->setGen($gen);
					//echo "<div class='well'>$gen +1</div>";
					setChildren($family, $family->getMember((string)$member->getId())->getChildModel((string)$key), ($gen + 1));
						

				}
				else
				{
					echo "the father member has the same id as the child id";
				}
			}
		}
	}
	
}

// Recursive function to set the generations of an entire family
function setParents(&$family, &$member, $gen)
{
	// Check generation - cannot be over 4
	if($gen > 4)
	{
		return;
		//echo "<div class='well'>Gen too big</div>";
	}
	// Check if father is in the $family array
	
	if(array_key_exists((string)$member->getFatherString(), $family->members ))
	{
		// Set the father of the $member if the father ID is DIFFERENT than the child ID
		if($family->getMember($member->getId()) != $family->getMember($member->getFatherString()))
		{
			$family->getMember($member->getId())->setFather($family->members[(string)$member->getFatherString()]);
		
			
			$family->members[(string)$member->getFatherString()]->addChild();
			
			// Set the generation
			$family->getMember($member->getId())->getFather()->setGen($gen);
			
			// Call setParents to set the next generation
			//echo "<div class='well'>$gen +1</div>";
			setParents($family, $family->getMember($member->getId())->getFather(), ($gen + 1));
		}

	}
	else
	{
		//echo "<div class='well'>No Father Found</div>";
	}

	
	// Check if mother is in the family array
	if(array_key_exists((string)$member->getMotherString(), $family->members ))
	{
		// Set the father of the $member
		if($family->getMember($member->getId()) != $family->getMember($member->getMotherString()))
		{
			$family->getMember($member->getId())->setMother($family->members[(string)$member->getMotherString()]);
			$family->members[(string)$member->getMotherString()]->addChild();
			//echo $family->getMember($member->getId())->getMother()->getId();
			
			// Set the generation
			$family->getMember($member->getId())->getMother()->setGen($gen);
			//echo " Mother ".$family->getMember($member->getId())->getMother()->getGen()."<br />";
			
			// Call setParents to set the next generation
			//echo "<div class='well'>$gen +1</div>";
			setParents($family, $family->getMember($member->getId())->getMother(), ($gen + 1));
		}
	}
	else
	{
		//echo "<div class='well'>No Mother Found</div>";
	}

}

// Function to help set the distances between generations
// $currentGen should always be set as 1 in the function call
function distAncestors($currentMember, $targetMember, $currentGen, $targetGen,$direction)
{
	// Check return/fail cases
	if ($currentGen > $targetGen) return;
	
	$distToFather = -999;
	$distToMother = -999;
	$distToChild = -999;
	$sumDistToChild = -999;
	$count = 0;
	
	if ($currentGen == $targetGen)
	{
		return distVincenty($targetMember->getBirthPlace()->Lat(),
							$targetMember->getBirthPlace()->Lon(),
							$currentMember->getBirthPlace()->Lat(),
							$currentMember->getBirthPlace()->Lon());
	}
	
	if ($currentGen < $targetGen)
	{
		if ($direction=="Backward")
		{
			if ($currentMember->getFather() != null)
			{
				$distToFather = distAncestors($currentMember->getFather(), $targetMember, ($currentGen + 1), $targetGen, $direction);
				if ($distToFather != -999) $count++;
			}
			if ($currentMember->getMother() != null)
			{
				$distToMother = distAncestors($currentMember->getMother(), $targetMember, ($currentGen + 1), $targetGen, $direction);
				if ($distToMother != -999) $count++;
			}
		}
		else
		{
			foreach ($currentMember->getChildModelArray() as $childModel)
			{
				$distToChild = distAncestors($childModel, $targetMember, ($currentGen + 1), $targetGen,$direction);
				if ($distToChild != -999) $count++;
				
				if ($sumDistToChild ==-999)
				{
					$sumDistToChild = $distToChild;
				}
				else if ($distToChild != -999)
				{
					$sumDistToChild += $distToChild;
				}
			}
		}
	}
	
	if ($count != 0)
	{
		if ($direction == "Backward")
		{
			($distToFather == -999)? $distToFather = 0: $distToFather = $distToFather;
			($distToMother == -999)? $distToMother = 0: $distToMother = $distToMother;
			$avgDist = ($distToFather + $distToMother) / $count;
		}
		else
		{
			$avgDist = $sumDistToChild /$count;
		}
		return $avgDist;
	}
	
	return -999;
}

function mdRootDeath($rootArray)
{
	$mdTotal = 0;
	$mdCount = 0;
	$mdBDArray = array();
	foreach($rootArray as $person)
	{
		if ($person->getDeathPlace()->Lat() != -999 && $person->getDeathPlace()->Lon() != -999 
			&& $person->getBirthPlace()->Lat() != -999 && $person->getBirthPlace()->Lon() != -999
			&& $person->getBirthDate() != -999 && $person->getDeathDate() != -999)
		{
			$diff = ($person->getDeathDate()-$person->getBirthDate());
			//echo "<h2>diff</h2>$diff";
			if($diff >= 7300)
			{
				$distance = distVincenty($person->getBirthPlace()->Lat(),
										$person->getBirthPlace()->Lon(),
										$person->getDeathPlace()->Lat(),
										$person->getDeathPlace()->Lon());
				$mdTotal = $mdTotal + $distance;
				
				$mdBDArray[] = $person;
				$mdCount++;
			}
		}
	}
	//echo "<h1>Size of mdCount</h1>$mdCount";
	mapGen($rootArray, "root_death_map", "death");
	if ($mdCount != 0)
	{
		return array(number_format(($mdTotal / $mdCount), 4, '.', ''), $mdCount);
	}
	else
	{
		return "N/A";
	}
}

function processFamily($famCount,$fam,$famArray,$direction,$credentials)
{
					// Create a variable to name the Family object
				$famId = "family".$famCount;
				
				// Create the Family object and give it the name of the famId variable
				$$famId = new Family($famId);
			
				
				// Count for the number of people in this family
				$totalPersonCount = count($fam->person);
				
				// Populate the Family object with Person objects
				foreach($fam->person as $person)
				{
					// Create a variable to name the Person object
					$persId = $person['id'];
					
					
					
					// Create the Person object and give it the name of the persId variable
					$$persId = new Person($persId,$credentials);
					
					$$persId->setName($person->assertions->names->name->value->forms->form->fullText);
					if (isset($person->assertions->events->event[0]->value))
					{
					$$persId->setBirthPlaceStr($person->assertions->events->event[0]->value);
						
					}
					
					if (isset($person->assertions->events->event[1]->value))
					{
					$$persId->setDeathPlaceStr($person->assertions->events->event[1]->value);
					}
				
				
					// Populate the birth and death places of the Person object
					
					// Set the motherString and FatherString references for the second pass
				
					//If we are analizing a backward search
					if ($direction=="Backward")
					{
						//echo "Backwards Registered";
						if(isset($person->parents->couple->parent ) )
						{
							foreach($person->parents->couple->parent as $parent)
							{
								if($parent['gender'] == "Male")
								{	
									$$persId->setFatherString($parent['id']);
								}
								else if($parent['gender'] == "Female")
								{
									$$persId->setMotherString($parent['id']);
								}
							}
						}
						
					}
					//Else if we are analizing a forward search
					else
					{
						if (isset($person->children))
						{
							foreach($person->children->child as $child)
							{
								//Builds an associative array where the key is the id and the value is the person's information
								$$persId->addNewChild($child);
								
							}
						}
						
					}
					
				
					// set the gender
					if(isset($person->assertions->genders->gender->value))
					{
						$$persId->setGender($person->assertions->genders->gender->value);
					}
					else
					{
						//echo "Gender Not set <br>";
					}
					// Set the events
					if(isset($person->assertions->events->event))
					{
						
						foreach($person->assertions->events->event as $event)
						{
							
							if($event->value['type'] == "Birth")
							{
								// If the place is recorded in the person record, create a new Place object and populate it with lat/long values from the XML
								if(isset($event->value->place->normalized['id']) and $event->value->place->normalized['id']!="")
								{
									//echo "<h1>Birth Place</h1>";
									$pName = $event->value->place->original;
									$$persId->setBirthPlace(new Place(1, $event->value->place->normalized['id'], -999, -999,$credentials,$pName));
								}
								// If the place is not recorded, create a 'dummy' place to act as a placeholder for analysis
								else
								{
									
									$$persId->setBirthPlace(new Place(0, "-999", -999, -999,$credentials,""));
								}
								if(isset($event->value->date->normalized))
								{
									$$persId->setBirthDate($event->value->date->normalized);
								}
							}
							else if($event->value['type'] == "Death")
							{
								
								// If the place is recorded in the person record, create a new Place object and populate it with lat/long values from the XML
								if(isset($event->value->place->normalized['id']) and $event->value->place->normalized['id']!="")
								{
									//echo "<h1>Death Place</h1>";
									$pid = $event->value->place->normalized['id'];
									$pName = $event->value->place->original;
									$$persId->setDeathPlace(new Place(1, $pid, -999, -999,$credentials,$pName));
								}
								// If the place is not recorded, create a 'dummy' place to act as a placeholder for analysis
								else
								{
									
									$$persId->setDeathPlace(new Place(0, "-999", -999, -999,$credentials,""));
								}
								if(isset($event->value->date->normalized))
								{
									//echo "<h1>Death Date</h1>";
									$$persId->setDeathDate($event->value->date->normalized);
								}
							}
								
						}
					}
					
					// Add the person to the family
					//echo json_encode(get_object_vars($$persId));
					$$famId->addMember($$persId);
					
				}
				
				// Add the family to the famArray
				$famArray[] = $$famId;// adds to the array
				//echo "<h2>famId</h2>".json_encode($$famId)."<h2>famArray</h2>".json_encode($famArray);
				return $famArray;
				
	
}
function addFileName($fileName,$analysisFileOutput){
		/*
			The commented code below was found when we took the project. we got a warning
			that $analysisFileOutput was undefined and decided to make it an asignment rather than an appending in cases
			where the $analysisFileOutput is null
			
			*/
			//$analysisFileOutput .= $fileName.":";
			if (isset($analysisFileOutput))
			{
				$analysisFileOutput .= $fileName.":";
			}	
			else	
			{	
				$analysisFileOutput = $fileName.":";
			}
	
}
function getTotalPeopleCount($famArray){
	$numPeople = 0;
			foreach($famArray as $family)
			{
				
				$numPeople += count($family->members);
			}
	return $numPeople;		
}
function getGenCount($firstArray){
	
	$gen1know = 0;
			foreach($firstArray as $person)
			{
				$gen1know += $person->getNumChild();
			}
	return $gen1know;
}
function getFileData($rootArray){
	$rootOut = "";
	foreach($rootArray as $p)
	{
		$rootOut .= $p->getId().",";
	}
	return $rootOut;
}
function storeGenerationData($rootArray,$firstArray,$secondArray,$thirdArray,$fourthArray)
{
	$rootOut = getFileData($rootArray);
	file_put_contents("data/root.txt", $rootOut);
	$firstOut = getFileData($firstArray);
	file_put_contents("data/first.txt", $firstOut);
	$secondOut =getFileData($secondArray);
	file_put_contents("data/second.txt", $secondOut);
	$thirdOut = getFileData($thirdArray);
	file_put_contents("data/third.txt", $thirdOut);
	$fourthOut = getFileData($fourthArray);
	file_put_contents("data/fourth.txt", $fourthOut);
	
}

function testSetChildren($test)
{
	$test["name"]= "";

	
	
	
	
	return $test;
}	

?>
