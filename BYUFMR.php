<?php
// NEW FMR APPLICATION CODE - USES POST QUERY METHOD
// BYU Family Migration Research Main Script
// Author: Brian Bunker, Mike Farnsworth
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: 
// Purpose: This file contains all of the main script of the BYUFMR project.
// Last Modified: 26 Jan 2010

//*************************************************//
// Display the document DOCTYPE and head to the browser //
//*************************************************//
ini_set("log_errors", 1);
ini_set('display_errors', 0);
ini_set('display_startup_errors', TRUE);
error_reporting(E_ALL & ~E_NOTICE);
ini_set("error_log", "/web/fmr/logs/php-error.log");

function shutdown()
{
    // This is our shutdown function, in 
    // here we can do any last operations
    // before the script is complete.

   error_log("Script Terminated");
}

register_shutdown_function('shutdown');


// Set the timeout limit to be infinite so that the queries can work until complete
set_time_limit (0);

// Require files with necessesary functions and parts of the program to perform queries
require_once('FamilySearchAPI/FS_Connection.php'); // Connects to FS, performs GET and POST queries, and Logs out of FS
require_once('FamilySearchAPI/getFSXMLResponse.php'); // Handles auto-throttling from FS to perform a query
require_once('FamilySearchAPI/prettyPrintXML.php'); // Makes XML human readable by indentation and  separating lines
require_once('FamilySearchAPI/dataTester.php'); // Tool for analyzing collected data stored in .FMR files
require_once('FamilySearchAPI/map.php'); // Create a simple Google Maps map to view the birthplaces of individuals in .FMR files
require_once('FamilySearchAPI/HTMLMenu.php'); // Creates and echos to the browser HTML menu and output for user interfacing
require_once('FamilySearchAPI/createPOSTSearchPayload.php'); // Creates and returns an XML POST payload string based on the input parameters
require_once('FamilySearchAPI/HTTP/Net/Serializer.php');
require_once('FamilySearchAPI/jsonToXmlConverter.php');
require_once('FamilySearchAPI/appState.php');
require_once('FamilySearchAPI/DataSearch/RootSearcher/personVarifier.php');
require_once('FamilySearchAPI/DataSearch/html_components.php');
require_once("FamilySearchAPI/preCondition.php");
require_once("FamilySearchAPI/fmrFactory.php");
require_once("FamilySearchAPI/FsSearcher/BackwardSearcher.php");
require_once("FamilySearchAPI/FsSearcher/ForwardSearcher.php");
require_once("FamilySearchAPI/cookie_utils/CookieManager.php");
require_once("FamilySearchAPI/model/QueryPlace.php");
require_once("FamilySearchAPI/FsSearcher/EntryProcessor.php");
require_once("FamilySearchAPI/view/HeaderShower.php");

//*********************************************************//
// Define variables to be used in login process and the main program //
//*********************************************************//
$mainURL = 'https://familysearch.org/';


gc_enable();

$url = array(
				'login'=>'identity/v2/login?key=',
				'logout'=>'identity/v2/logout?',
				'user'=>'platform/users/current?',
				'search'=>'platform/tree/search?',
				'read'=>'platform/tree/person/');

$credentials = array(// Production Key
					'key' => 'a0T3000000BZUxWEAX',
					'user' => '',
					'password' => '',
					'agent' => 'FMR/v1.0 (BYU-Geography)',
					'sessionID'=> '',
					//Used in Oauth2 authentication. traded for an access token
					'authCode' => '',
					'accessToken' =>'',
					'statusCode'=>'',
					'loggedOn' => False,
					'mainURL'=>$mainURL
					);
//Root Generation					
$max = 200;					
setcookie('key', "DPYC-ZB7C-5M9N-M4PS-M8YJ-T6SB-H5VM-C658", time() + 18000);
setcookie('agent', 'FMR/v1.0 (BYU-Geography)', time() + 18000);
setcookie("mainURL", $mainURL, time() + 18000);

preAccessToken($credentials);

if(isset($_POST['step']))
{
	$step = $_POST['step'];
}
else 
{
	$step = 'authenticate';
}

if (isset($_GET['code']) && $step == 'authenticate')
{
	//echo "Auth Code Collected";
	$credentials['authCode'] = $_GET['code'];
	//$step = 'authenticate';
	
}
				
// XML strings to store compiled data
$xfmr = '';
$xfmrCurrentEvent = '';
$fsConnect = FmrFactory::createFsConnect();
$headerShower = new HeaderShower();
initDb();
//********************************//
// MAIN PROGRAM FUNCTIONALITY//
//********************************//
// Switch determines what portion of the php script to run depending on step
switch($step)
{
  // FAMILYSEARCH.ORG AUTHENTICATION CASE
  case('relogin'):
		echo file_get_contents("index.html");
  break;
  case('addProject'):
		if (isset($_POST["projectName"])&& $_POST["projectName"]!="")
		{
			create_project($_POST["projectName"]);
		}
		else
		{
			showEmptyProjectNameError();
			break;
		}
        $step = 'authenticate';
 	
  case('authenticate'):
  // Removed. New authentication removes the need for these steps in the authentication process
	//echo "Next Step of authentication";
	// Logon using FS_Connection using passed credentials
	$credentials = logOn($mainURL, $credentials);
	
	//Get information on current user
	$response = FSQuery($credentials, $mainURL.$url["user"]);
	//echo $response;
	$json = json_decode($response,true);
	if(!isset($json))
	{
		echo  'Parsing error';
		//echo $json;
		$json['statusCode'] = 401;
	}
	if(isset($json)){
		HTMLMenu("200", $json);
	}
	setcookie('loggedOn', true, time() + 18000);
	break;

// Pre-Query place selection
  case('prequery'):
  
  	// Get cookie vars
	//Gather information
	$credentials["accessToken"] = $_COOKIE["accessToken"];	
	$minGen = $_POST['minGen'];
	$event = $_POST['event'];
	$state = $_POST['state'];
	$county = $_POST['county'];
	$country = $_POST['country'];
	$project = $_POST['project'];
	$city = $_POST['city'];
	$max = $_POST["searchSize"];
	$fileName = $_POST['projectFile'];
	$startYear = $_POST['startyear'];
	$giveOrTake = $_POST['giveOrTake'];
	$searchancestors = $_POST['searchancestors'];//example TRUE/FALSE
	$searchDirection = $_POST['searchdirection'];//example Forward/Backward
	preAccessToken($credentials);
	showPrequeryHeader();
	
	$city = strtr($city,' ', '+');
	$county = strtr($county,' ', '+');
	$state = strtr($state,' ', '+');
	$country = strtr($country,' ', '+');
	$queryURL = $mainURL.'platform/places/search?access_token='.$credentials['accessToken'].'&q=';
	
	//Form request based on search type (death or birth search)

		$queryURL.="name:\"";
		$place = '';
	//Fill in location information
	if(($city != '') && ($city != 'City'))
	{
		$place .= $city.',';
	}
	if(($county != '') && ($county != 'County'))
	{
		$place .= $county.',';
	}
	if(($state != '') && ($state != 'State'))
	{
		$place .= $state.',';
	}
	if(($country != '') && ($country != 'Country'))
	{
		$place .= $country;
	}
	$queryURL .= $place;
	$cm = new CookieManager();
	$cm->saveForPrequery($place,$searchDirection,$city,$county,$counrty,$project,$state,$max,$fileName,$giveOrTake,$minGen);
	$queryURL .= "\"%20";
	
	//form date query based on search type
		$queryURL.='date:';
		
	//add date query data
	if(($state != '') && ($state != 'State'))
	{
		$queryURL .= $startYear.'-'.$startYear;
	}
	
	$response = $fsConnect->getFSXMLResponse($credentials, $queryURL);
	
	if (sizeof($response['entries'])>0)
	{
		foreach($response['entries'] as $entry)
		{
		$place = $entry['content']['gedcomx']['places'][0];
		$fullName = $place['display']['fullName'];
		$fullName = strtr($fullName,' ', '$20');
		echo "<a class='searchLoc'>".$place['display']['type'].': '.$place['display']['fullName']."</a>";//$place->normalized->form;
		$id = $place['id'];
		echo <<<_EndOfHTML2
		<form class='searchLoc' action="BYUFMR.php" method="POST">
			<input type="hidden" name="event" value=$event />
			<input type="hidden" name="place" value=$id />
			<input type="hidden" name="city" value=$city />
			<input type="hidden" name="county" value=$county />
			<input type="hidden" name="country" value=$country />
			<input type="hidden" name="state" value=$state />
			<input type="hidden" name="giveOrTake" value=$giveOrTake />
			<input type="hidden" name="startyear" value=$startYear />
			<input type="hidden" name="searchancestors" value=$searchancestors />
			<input type="hidden" name="fullName" value = $fullName />
			<input type="hidden" name="minGen" value=$minGen />
			<input type="hidden" name="step" value="query" />
			<input class='searchBtn' type="submit" onclick ="return showSpinner();" value= "Use Location" />
		</form>
		<br />
_EndOfHTML2;
		}
	}
	else
	{
		echo "<h2>Sorry. No locations were found </h2>";
	}
	
	startLoader($fileName,$max);

	break;
	
  // FAMILYSEARCH.ORG API SEARCH AND PARSE CASE
  case('query'):
	$cookieManager = new CookieManager();
	$queryPlace = $cookieManager->loadPlace();
	$cookieManager->loadForQuery($credentials,$direction,$max,$minGen,$project,$fileName);
	// Get cookie vars
	$placeId = $_POST["place"];	
		// set up the xfmr variable to store the xml data to be used in the .fmr file to be saved from the query
		$xfmr .= '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE persons SYSTEM "FMRXML.dtd"><persons>';
		$event = $_POST['event'];
		// $place is the unique FS place ID
		$startYearOrig = $_POST['startyear'];
		$startYear = $_POST['startyear'];
		$searchancestors = $_POST['searchancestors'];
		$placeId= $_POST['place'];
		echo "Search for ancestors";
		// print some HTML data
		printHeader();
	
		// user input cleanup
		if(!is_numeric($startYear))
		{
			echo 'Start Year is not a valid number';
			break;
		}
		$htmloutput =<<<_EndOfHTML4
			<br /><br />
			<div id="loading" class="queryResult">
				<img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/preloader_circle-lines.gif" />
				Data Collection For: $place ~ $city, $county, $state ~ $startYear.fmr
				<br />
				Pease wait while the query is working...
				<br /><br /><br /><br /><br /><br /><br />
			</div>
_EndOfHTML4;
		echo $htmloutput;
		unset($htmloutput);
		$startTime = time();
	
	//***********************************************************************************************************************************************//
	// The main logic section - uses POST HTTP requests to do initial query and extract multiple generations of ancestors from a specified birth/death year and place		 //
	//																											//
	//DESCRIPTION OF FUNCTIONALITY																					//
	//Search for people from user defined input																				//
	//	(x)Use POST search																							//
	//		(x)Build XML payload																						//
	//		(x)Use getFSXMLPOSTResponse() to deliver payload and get back a simpleXML object												//
	//		     Note:  The reason for using a POST request is that we can define the place we want results from in the POST payload.  As of Mar 2010, the results are	//
	//			      still fuzzy, meaning that all the results will not be an exact match to the place.  If FamilySearch implements the exact search capability in the	//
	//			      future, the POST method will enable our program to take advantage of that.												//
	//			(x)Extract the contextID from the response for use in person searches													//
	//			     Note:  We use POST to get a response as described above, but we extract the contextID so that all subsequent searches will be done using a GET	//
	//				      request.  The contextID acts as a reminder to FamilySearch of what we initially requested, which includes a place match.  GET is a little	//
	//				      easier to use, though.																			//
	//		(x)Query using the context id																					//
	//			(x)Perform person reads on all the returned people 																//
	//(x)Select only people from user defined place																				//
	//		(x)Make a list (array) of only people from place defined by user															//
	//(x)Get a list of ancestors foreach person																					//
	//(x)Perform person read calls on all ancestors																				//
	//(x)Store the resultant information																						//
	//	(x)Integrate the original person read with their ancestors person reads															//
	//	(x)Save the collected data from user defined place in XML structured file.  Group people in families.										//
	//		The file structure is XML based and retains the FS structure from <person>	on.  Each person is in a <family> and all families are in a <place>.			//
	//		ex: <place><family><person /><person /></family><family><person /><person /><person /></family></place>									//
	//**********************************************************************************************************************************************//
	
	// Set up a variable to store the person read results in an array of simple XML objects
		addSearchResultKey();
		
		// Request the pedegree of each person in $personsFromPlace and then do a person read for each record in the returned pedigree.  Store the results in $personsPedigree array
	// Only if "Include Ancestors" is checked
		
	$myFile =$queryPlace->getFileName($startYear,$direction,$fileName);
	
	$XMLString = '<?xml version="1.0" encoding="UTF-8"?><place>';
	$XMLString .= '<header><place><name>'.$place;
	$XMLString .= '</name><id>'.$placeId.'</id></place><year><calendar>'.$startYear.'</calendar><astro><begin>'.juliantojd(1,1,$startYear).'</begin><end>'.juliantojd(12,31,$startYear).'</end></astro></year></header>';
		
		//Use of State patturn to distinguish forward and backward searches
		if ($direction =='TRUE' or $direction=='Backward')
		{
			$searcher = new BackwardSearcher();
		}
		else 
		{
			$searcher = new ForwardSearcher();
		}
		
		$persons = array();
	
		//Form the POST XML payload by passing user defined variables to function
		$fsFacade = FmrFactory::getFacade();
		$payload = createPOSTSearchPayload($_POST,$fsFacade,$credentials,$placeId);
		// Set URL
		// Repeat POST request using contextID until all results returned (currently limited to 500)
		// Set counter - counts by 40 (number of returned people in search)
		$counter = 1;	
		
		//Blank first time through loop. constructed and used second time through loop
		$context = "";
		
		//first sample pull url 
		$url = $mainURL.'platform/tree/search?count=40&'.$context.'start=0&';	
		$entryProcessor = new EntryProcessor();
		//While pulled people is smaller or equal to those pulled.
			do
			{
				error_log("Processing Root Generation Member $counter of $max");
				
                try {
					
					if ($counter<$max)
					{
						$response = $fsFacade->getRootGeneration($url,$searchCount,$credentials,$payload);
						
						// Loop through each record from the first response
						if ($searchCount>0)
						{
							
							
							if ($searchCount<40 && ($max - $counter)>40)
							{
								updatePayload($payload,$counter);
							}
						
							//For each person in the search
							foreach($response['entries'] as $search)
							{
								error_log("Attempting to Process Entry");
								$entryProcessor->process($search,$counter,$credentials,$XMLString,$mainURL,$direction,$minGen,$fsConnect,$searcher,$persons,$max);
								if ($counter>$max)
								{
									break;
								}
							}
							
						}
						else
						{
							
							updatePayload($payload,$counter);
						}
					}
					else
					{
						break;
					}
				}
				catch(Exception $e)
				{
					error_log("Error thrown in search");
					error_log('Caught exception: '.  $e->getMessage());
					$counter++;
				    continue;
					
				}
				//throw new Exception("Break here");
			}
			while ($counter<=$max);

		if($searchancestors == 'TRUE')
		{
			$placePersonsPedigree = array();
			$pedigreeURL = $mainURL.'platform/tree/ancestry/';
			//$personFromPlaceIndex = 0;
		}	

		$XMLString .= '</place>';
		$myFile= strtr($myFile," ","");
		$worked = file_put_contents("data/v2/$myFile", $XMLString);
		insert_project($myFile,$project,$fileName);
		
		
	if($worked)
	{
		$htmloutput =<<<EndOfHTML
		<br />
		<div class="queryResult">
			<img onload="removeLoading();" class="floatLeft" src="http://pintura.byu.edu/fmr/img/green_checkmark.png" />
			$myFile created!
			<br />
			Query finished.
		</div>
EndOfHTML;
		echo $htmloutput;
	}
	// If saving file does not work
	else
	{
		$htmloutput =<<<EndOfHTML
		<br />
		<div class="queryResult">
			<img onload="removeLoading();" class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/red_x-mark.png" />
			Query failed or file did not write.
			<br />
		</div>
EndOfHTML;
		echo $htmloutput;
	}
	
	//**********************************************************************************************************************************************//
	//End main logic section																							//
	//**********************************************************************************************************************************************//
	//**********************************************************************************************************************************************//
	$endTime = time();
	$elapsedTime = ($endTime-$startTime)/60;
	echo "<br />Elapsed query time in minutes: $elapsedTime<br />";
	$xfmr = '';
	$treeEnd =appState::$treeEnd;
	echo "<h4>Dead end Generations</h4> $treeEnd";
	
	//button to return to index
	echo <<<Return
	<script> alertDone();</script>
	
	<br />
	<div id="backToMenu">
			<form >
				<input type="submit" value="Back To Menu" onclick="backToMenu();">
			</form>
			</div>
Return;
	echo '</body>';

	
	break;

	
// Data Display Case
  case('display'):
  	// Get cookie vars
	$cm = new CookieManager();
	$cm->loadFromCookies($credentials);
	$fileName = $_POST['fileName'];

	showDisplayScreenHeader();
	
	$xml = xmlpp($fileName, TRUE);
	//echo "<pre>".$xml."</pre>";
	break;
	
  case('analyzeProxy'):
  
  
	$fileName = $_POST['fileName'];
	setcookie('fileName', $fileName, time() + 18000);
	showAnalizeScreenHeader();
	echo <<<HTML
	
	<h2>Please wait as we analize the data...</h2>
	<p>Depending on the size of your search, this could take a while</p>
	<div class="progress">
		<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		<span class="sr-only">Loading</span>
		</div>
	</div>
	<div class='well'>
	<h4>"... the work of patience boils down to this:<br>
	keep the commandments; trust in God, our Heavenly Father; <br>
	serve Him with meekness <br>
	and Christlike love; exercise faith and hope in the Savior; <br>
	and never give up." -President Dieter F. Uchtdorf</h4>
	<img src="./img/marshmallow.jpg"/><br/>
	</div>
	<a target="_blank" href="https://qtrial2016q1az1.az1.qualtrics.com/jfe/form/SV_cHfztzsZL8W5XnL" class="btn btn-default">While you are waiting, do you mind filling out a quick survey on this web app?</a>
	
	<body onLoad="mail.submit()">
	
	
	<!--Auto Post to Analize-->
   <form method="POST" name="mail" class="adjacent" action="BYUFMR.php">
		<input type="hidden" name="step" value="analyze" />
		<input type="hidden" name="analysisFile" value="none" />
   </form>
	</body>
HTML;
	
	
  break;	
  // Data Analysis Case
  case('analyze'):
  	// Get cookie vars
	//echo $mainURL;
	setcookie("mainURL", $mainURL, time() + 18000);
	

	$credentials["agent"] = $_COOKIE["agent"];
	$credentials["accessToken"] = $_COOKIE["accessToken"];
	$credentials["loggedOn"] = TRUE;// $_COOKIE["loggedOn"];
	
	
	$fileName = $_COOKIE['fileName'];

	showAnalizeScreenHeader();
	
	if ($fileName!="")
	{
		echo "Data Tester";
	dataTester($fileName,$credentials);
	}
	else
	{
		echo "<h1>File name is blank</h1>";
	}
	break;

  // GOOGLE MAPS API INTERFACE CASE
  case('map'):
  	// Get cookie vars
	$cm =new CookieManager();
	$cm->loadForMaps($credentials,$fileName);
	map($fileName);
	break;
	
// LOGOUT CASE
  case('logout'):
  	// Get cookie vars
	$cm = new CookieManager();
	$cm->loadFromCookies($credentials);
	
	$headerShower->show();
	$credentials = logOut($mainURL, $credentials);
	if($credentials["loggedOn"] == FALSE)
	{
		$htmloutput = '<p class="userWelcome">You have been successfully logged out.</p>';
	}
	echo $htmloutput;
  
	break;
	
	
}


function getStats($person)
{
	$name = $person['display']['name'];
	$id =$person['id'];
	if (isset($person['display']) and isset($person['display']['birthPlace']))
	{
		$birthPlace = $person['display']['birthPlace'];
	}
	$asNum = $person['display']['ascendancyNumber'];
	$result = "'Name: $name
	
	
	
ID: $id
BirthPlace: $birthPlace
Ascendancy Number: $asNum
	'";
	return $result;
}
/**
* TABLE: PlaceToId
*     placeName - text
*     pid - text
*
* TABLE: Project
*     projectName - text
*	  projectId - integer
*	  
* TABLE: ProjectFile
*     fileName - text
*     projectId - integer
*     filePath - text  
*/
function initDb(){
	//FmrFactory::createDao()->clean();
	//FmrFactory::createDao()->dbDump();
};

function insert_project($myFile,$project,$fileName)
{
	$query = "INSERT INTO ProjectFile(fileName, projectId, filePath) VALUES('$fileName', $project, '$myFile');";
	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
				$result = pg_query($pgConnection, $query);
	
}

function create_project($projectName)
{
	$id = 0;
	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
	$query = "SELECT projectId FROM Project ORDER BY projectId DESC LIMIT 1";
		$result = pg_query($pgConnection, $query);
		while ($row = pg_fetch_row($result)) {
			$id = $row[0] +1;
			}

	$query = "INSERT INTO Project(ProjectName, projectId) VALUES('$projectName', $id);";
				$result = pg_query($pgConnection, $query);
	
}

/**
*
*
*/
function updatePayload(&$payload,&$counter)
{
	if (count($payload)==0)
	{
		$totalResults = $counter + $searchCount-1;
			echo "<div class='alert alert-info' role='alert'><b>Only $totalResults matches found</b></div>";
			showSearchHelp();
	}
	else
	{
		array_pop($payload);
	}
}


?>