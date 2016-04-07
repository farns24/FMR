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
require_once("dataTesterFiles/chr_calcUS.php");
require_once("mapUtils.php");
require_once("csiUtils.php");
require_once("preCondition.php");
require_once("FamilyBuilder\AncestorBuilder.php");
require_once("FamilyBuilder\DecendenceBuilder.php");
require_once("DataSearch\Model\StatsList.php");
require_once("dataTesterFiles\meanDistance\LeanMeanDistanceFinder.php");
require_once("dataTesterFiles\meanDistance\MeanCenterFinder.php");
require_once("dataTesterFiles\eventPlaceGenerator\BirthPlaceGenerator.php");
require_once("dataTesterFiles\eventPlaceGenerator\DeathPlaceGenerator.php");
require_once("dataTesterFiles\generationStats\GenResultsFinder.php");
require_once("dataTesterFiles\FileManager.php");

global $rootLatLonArray;

/**
* @pre $credentials contain access Token
*
*/
function dataTester($fileName,$credentials)
{
	preAccessToken($credentials);
	
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
			$rootList = new StatsList();
			$firstList = new StatsList();
			$secondList = new StatsList();
			$thirdList = new StatsList();
			$fourthList = new StatsList();
			
			$famCount = 0;
			$famBuilder = null;
						
			if ($direction=="Backward")
			{
			// Recursively set generations for the rest of the family
				$famBuilder = new AncestorBuilder();
			}
			else
			{
				$famBuilder = new DecendenceBuilder();
			}
			
			foreach($xml->family as $fam)
			{
				// Iterate through the people in each family - from the XML, not the family array
				$personCount = 1;
				 
				foreach($fam->person as $person)
				{
					// Set the person to be used in this loop (Calling classFamily::getMember();)
					$member = $famArray[$famCount]->getMember((string)$person['id']);
		
					// If the person is the first in the list, they are by default the ROOT generation
					if($personCount == 1)
					{
						// Set generation to ROOT
						$member->setGen(0);
						$rootList->insert($member);
						$famBuilder->connect($famArray[$famCount], $member, 1);
						
					}
					$personCount++;

					//******************************************************************************//
					// Calculate migration between generations								//
					//******************************************************************************//
					// Now populate 5 arrays - one for each generation							//
					//******************************************************************************//
					
					// Switch on the generation of the member Person
					$gen = $member->getGen();

					switch($gen)
					{
						case 0:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$member->setDistTo(2,distAncestors($member, $member, 0, 2,$direction));
							$member->setDistTo(3,distAncestors($member, $member, 0, 3,$direction));
							$member->setDistTo(4,distAncestors($member, $member, 0, 4,$direction));
							break;
						case 1:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$member->setDistTo(2,distAncestors($member, $member, 0, 2,$direction));
							$member->setDistTo(3,distAncestors($member, $member, 0, 3,$direction));
							
							$firstList->insert($member);
							break;
						case 2:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							$member->setDistTo(2,distAncestors($member, $member, 0, 2,$direction));
							
							$secondList->insert($member);
							break;
						case 3:
							$member->setDistTo(1,distAncestors($member, $member, 0, 1,$direction));
							
							$thirdList->insert($member);
							break;
						case 4:
						
							$fourthList->insert($member);
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
			$FMRfileManager = new FMRFileManager();
			$FMRfileManager->storeGenerationData($rootList->getTotal(),$firstList->getTotal(),$secondList->getTotal(),$thirdList->getTotal(),$fourthList->getTotal());
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
			
			$genFinder = new GenResultsFinder();
			$genFinder->solve($firstList,1,$analysisFileOutput, $htmlOut,$numFamilies);
			$genFinder->solve($secondList,2,$analysisFileOutput, $htmlOut,$numFamilies);
			$genFinder->solve($thirdList,3,$analysisFileOutput, $htmlOut,$numFamilies);
			$genFinder->solve($fourthList,4,$analysisFileOutput, $htmlOut,$numFamilies);
			
			// Start Mean Distance of migration matrix
			$leanMeanDistanceFinder = new LeanMeanDistanceFinder();
			$leanMeanDistanceFinder->solveMeanDistances("ALL",$rootList,$firstList,$secondList,$thirdList,$htmlOut,$analysisFileOutput);
			$leanMeanDistanceFinder->solveMeanDistances("MALE",$rootList,$firstList,$secondList,$thirdList,$htmlOut,$analysisFileOutput);
			$leanMeanDistanceFinder->solveMeanDistances("FEMALE",$rootList,$firstList,$secondList,$thirdList,$htmlOut,$analysisFileOutput);
			
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
			$mcRoot = meanCenterPersonArray($rootList->getTotal());
			$rootLatLonArray = $mcRoot;
			$htmlOut = str_replace("%mcRoot%", (number_format($mcRoot[0], 4, '.', '').", ".number_format($mcRoot[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mcRoot[0], 4, '.', '').":".number_format($mcRoot[1], 4, '.', '').":";
			
			// Mean center, 1st gen
			$mc1 = meanCenterPersonArray($firstList->getTotal());
			mcmap("mcmap1", $mc1[0], $mc1[1]);
			$htmlOut = str_replace("%mc1%", (number_format($mc1[0], 4, '.', '').", ".number_format($mc1[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc1[0], 4, '.', '').":".number_format($mc1[1], 4, '.', '').":";

			// Mean center, 2nd gen
			$mc2 = meanCenterPersonArray($secondList->getTotal());
			mcmap("mcmap2", $mc2[0], $mc2[1]);
			$htmlOut = str_replace("%mc2%", (number_format($mc2[0], 4, '.', '').", ".number_format($mc2[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc2[0], 4, '.', '').":".number_format($mc2[1], 4, '.', '').":";
			
			//Mean center, 3rd gen
			$mc3 = meanCenterPersonArray($thirdList->getTotal());
			mcmap("mcmap3", $mc3[0], $mc3[1]);
			$htmlOut = str_replace("%mc3%", (number_format($mc3[0], 4, '.', '').", ".number_format($mc3[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mc3[0], 4, '.', '').":".number_format($mc3[1], 4, '.', '').":";
			
			// Mean center, 4th gen
			$mc4 = meanCenterPersonArray($fourthList->getTotal());
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
			$sd1 = standardDistancePersonArray($mc1[0], $mc1[1], $firstList->getTotal());
			$htmlOut = str_replace("%sd1%", number_format($sd1, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd1.":";
			
			// Standard distance, 2nd gen
			$sd2 = standardDistancePersonArray($mc2[0], $mc2[1], $secondList->getTotal());
			$htmlOut = str_replace("%sd2%", number_format($sd2, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd2.":";
			
			// Standard distance, 3rd gen
			$sd3 = standardDistancePersonArray($mc3[0], $mc3[1], $thirdList->getTotal());
			$htmlOut = str_replace("%sd3%", number_format($sd3, 4, '.', ''), $htmlOut);
			$analysisFileOutput .= $sd3.":";
			
			// Standard distance, 4th gen
			$sd4 = standardDistancePersonArray($mc4[0], $mc4[1], $fourthList->getTotal());
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
			$csi1 = csi($rootList->getTotal(), 1, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot1%", $csi1, $htmlOut);
			$analysisFileOutput .= $csi1.":";
			$csi2 = csi($rootList->getTotal(), 2, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot2%", $csi2, $htmlOut);
			$analysisFileOutput .= $csi2.":";
			$csi3 = csi($rootList->getTotal(), 3, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot3%", $csi3, $htmlOut);
			$analysisFileOutput .= $csi3.":";
			$csi4 = csi($rootList->getTotal(), 4, $mcRoot,$credentials);
			$htmlOut = str_replace("%csiroot4%", $csi4, $htmlOut);
			$analysisFileOutput .= $csi4.":";
			
	//########################################################################//
	// Ravenstiens' Law of Close Migration -RCM

			unset($valueArray);
			$valueArray = array();
			$rcm1 = RCM($rootList->getTotal(), 1);
			$analysisFileOutput .= $rcm1.":";
			$rcm2 = RCM($rootList->getTotal(), 2);
			$analysisFileOutput .= $rcm2.":";
			$rcm3 = RCM($rootList->getTotal(), 3);
			$analysisFileOutput .= $rcm3.":";
			$rcm4 = RCM($rootList->getTotal(), 4);
			$analysisFileOutput .= $rcm4.":";

	//########################################################################//
			/* Refactor
			* template method pattern to remove code duplication
			*
			*/
			$chrSolver = null;
			// Community Heritage Ratios
			if($analysisFileName == "California.txt") {
			$chrSolver = new CAChrCalculator();
			/*
				$chrroot1 = chr_calcCA($firstList->getTotal(), "r1", $analysisFileName);
				$analysisFileOutput .= $chrroot1;
				$chrroot2 = chr_calcCA($secondList->getTotal(), "r2", $analysisFileName);
				$analysisFileOutput .= $chrroot2;
				$chrroot3 = chr_calcCA($thirdList->getTotal(), "r3", $analysisFileName);
				$analysisFileOutput .= $chrroot3;
				$chrroot4 = chr_calcCA($fourthList->getTotal(), "r4", $analysisFileName);
				$analysisFileOutput .= $chrroot4;
				*/
			}
			elseif($analysisFileName == "CommunityHeritageRatioUS.txt"){
			$chrSolver = new USChrCalculator();
				/*
				$chrroot1 = chr_calcUS($firstList->getTotal(), "r1", $analysisFileName);
				$analysisFileOutput .= "\r\nFirst Generation\r\n";
				$analysisFileOutput .= $chrroot1;
				$chrroot2 = chr_calcUS($secondList->getTotal(), "r2", $analysisFileName);
				$analysisFileOutput .= "Second Generation\r\n";
				$analysisFileOutput .= $chrroot2;
				$chrroot3 = chr_calcUS($thirdList->getTotal(), "r3", $analysisFileName);
				$analysisFileOutput .= "Third Generation\r\n";
				$analysisFileOutput .= $chrroot3;
				$chrroot4 = chr_calcUS($fourthList->getTotal(), "r4", $analysisFileName);
				$analysisFileOutput .= "Fourth Generation\r\n";
				$analysisFileOutput .= $chrroot4;
				*/
			}
			else {
			$chrSolver = new ChrCalculator();
				/*
				$chrroot1 = chr_calc($firstList->getTotal(), "r1", $analysisFileName);
				$analysisFileOutput .= $chrroot1;
				$chrroot2 = chr_calc($secondList->getTotal(), "r2", $analysisFileName);
				$analysisFileOutput .= $chrroot2;
				$chrroot3 = chr_calc($thirdList->getTotal(), "r3", $analysisFileName);
				$analysisFileOutput .= $chrroot3;
				$chrroot4 = chr_calc($fourthList->getTotal(), "r4", $analysisFileName);
				$analysisFileOutput .= $chrroot4;
				*/
			}
			$chrSolver->solve($firstList,$secondList,$thirdList,$fourthList,$analysisFileName,$analysisFileOutput);

			// Then we need to plug the percentage of each origin place into an HTML string
			$htmlOut = str_replace("%chrroot1%", str_replace("\r\n", "</br>", $chrroot1), $htmlOut);
			$htmlOut = str_replace("%chrroot2%", str_replace("\r\n", "</br>", $chrroot2), $htmlOut);
			$htmlOut = str_replace("%chrroot3%", str_replace("\r\n", "</br>", $chrroot3), $htmlOut);
			$htmlOut = str_replace("%chrroot4%", str_replace("\r\n", "</br>", $chrroot4), $htmlOut);
			
			$analysisFileOutput .= "\r\n";
			file_put_contents("data/v2/analysisCSV/$analysisFileName", $analysisFileOutput);

			$htmlOut .= "<a href='data/v2/analysisCSV/$analysisFileName' target='_blank'>Download Analysis</a>";
			
			// Insert the mean distance the root generation migrated birth to death
			$mdrbd = mdRootDeath($rootList->getTotal());
			$htmlOut = str_replace("%mdrbd%", $mdrbd[0], $htmlOut);
			$htmlOut = str_replace("%numBD%", $mdrbd[1], $htmlOut);
			
			// Display the HTML that we've altered to include calculated stats
			echo $htmlOut;
			echo "<h3>Generation Details</h3>";
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen1head'>Generation 1</h4>";
			mapGen($firstList->getTotal(), "map1", "birth");
			echo "</div>";
			
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen2head'>Generation 2</h4>";
			mapGen($secondList->getTotal(), "map2", "birth");
			echo "</div>";
			
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen3head'>Generation 3</h4>";
			mapGen($thirdList->getTotal(), "map3", "birth");
			echo "</div>";
			
			echo "<div class='col-md-3'>";
			echo "<h4 id='gen4head'>Generation 4</h4>";
			mapGen($fourthList->getTotal(), "map4", "birth");
			echo "</div>";

			unset($htmlOut,$xml,$analysisFileOutput,$person,$famArray,$rootList,$firstList,$secondList,$thirdList,$fourthList);

		}
		catch(Exception $e)
		{
			print_r($e);
			break;
		}
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

function mdRootDeath($rootList)
{
	$mdTotal = 0;
	$mdCount = 0;
	$mdBDArray = array();
	foreach($rootList as $person)
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
	mapGen($rootList, "root_death_map", "death");
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
						$birthGenerator = new BirthPlaceGenerator();
						$deathGenerator = new DeathPlaceGenerator();
						foreach($person->assertions->events->event as $event)
						{
							
							if($event->value['type'] == "Birth")
							{
								$birthGenerator->setPlace($event,$$persId,$credentials);
							}
							else if($event->value['type'] == "Death")
							{
								$deathGenerator->setPlace($event,$$persId,$credentials);
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
/*
function getFileData($rootList){
	$rootOut = "";
	foreach($rootList as $p)
	{
		$rootOut .= $p->getId().",";
	}
	return $rootOut;
}
function storeGenerationData($rootList,$firstArray,$secondArray,$thirdArray,$fourthArray)
{
	$rootOut = getFileData($rootList);
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
*/
function testSetChildren($test)
{
	$test["name"]= "";
	return $test;
}	

?>
