<?php
// Definition of class Place

require_once('getFSXMLResponse.php');
require_once('DAO.php');
require_once('fmrFactory.php');
require_once('placeUtils.php');
require_once('stubTestClasses/StubFsConnect.php');

/**
* @invarient
*/
class Place
{
	// Private data members
	private $lat = null;
	private $lon = null;
	private $iso = null;
	private $dao = null;
	private $fsConnect = null;
	
	// Public Properties
	public function setLatLon($lat, $lon)
	{
		$this->lat = $lat;
		$this->lon = $lon;
		$this->iso = -999;
	}
	
	public function NoPlace()
	{
		$this->lat = -999;
		$this->lon = -999;
		$this->iso = -999;
	}
	
	public function Place($type, $placeId, $placeLat, $placeLon,$credentials,$pName)
	{
		$this->dao = FmrFactory::createDao();
		$this->fsConnect = FmrFactory::createFsConnect();
		// 0 - no place provided, give empty place object
		// 1 - Place ID provided, get place lat, lon, and ISO from web service
		// 2 - Latitude and Longitude provided, set them and give empty ISO
		//echo "<br> Place type (classPlace.php)".$type."<br>";
		switch ($type) {
		
			case 0:
				$this->NoPlace();
			break;
			
			case 1:
				
				$row = $this->dao->fetchFromDb($placeId);
				
				// If the family search id is in the places database table, use it
				$isInDatabase = $row[0] != null && $row[0] != -999;
				
				if($isInDatabase)
				{
			
					$this->lat = $row[0];					
					$this->lon = $row[1];					
					$this->iso = $row[2];
					
					$name = $row[3];
					
					//If what is in the database does not match the location we are looking for
					/*if (!namesMatch($name, $pName))
					{
						
						//Delete old entry
						$this->dao->deleteFromDb($placeId);
						$isInDatabase = false;
						

						
					}*/
 
				}
		
				//If the family search id is NOT in the places database table, then perform a query and add the place to the database
				if (!$isInDatabase)
				{
					echo "<h1>Not Found in Database</h1>";
					$placeId = $this->getIdFromName($pName,$credentials);

					$result = $this->getPlaceFromFS($placeId,$credentials,$pName);
					
					$this->lat = $result["lat"];
					$this->lon = $result["lon"];
					$this->iso = $result["iso"];

				}
			break;
			
			case 2:
				$this->lat = $placeLat;
				$this->lon = $placeLon;
				$this->iso = -999;
			break;
		}
	}
	
	public function Lat() { return $this->lat; }
	public function Lon() { return $this->lon; }
	public function ISO() { return $this->iso; }


/**
*
*/
public function getIdFromName($pName,$credentials)
{
	//var_dump($credentials);
	$latLngURL = "https://familysearch.org/platform/places/search?access_token=".$credentials['accessToken']."&q=name:\"$pName\"";
	$latLngURL = str_replace(" ","%20",$latLngURL);
	//echo "$latLngURL";
	$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);
	
		if (isset($latLngXML))
		{
			if (isset($latLngXML["entries"]))
			{
				//echo "1";
				if (isset($latLngXML["entries"][0]))
				{
					//echo "2";
					if (isset($latLngXML["entries"][0]["content"]["gedcomx"]["places"]))
					{
						//echo "3";
						if (isset($latLngXML["entries"][0]["content"]["gedcomx"]["places"][0]))	
						{
							//echo "4";
							return $latLngXML["entries"][0]["content"]["gedcomx"]["places"][0]["id"];
						}
					}
				}
			}
		}
		
	return "";
}


public function getLatLonFromRequest(&$lat,&$lon,$latLngXML,&$insertFlag, &$iso, &$key,&$normalized,$latLngURL,$pName)
 {
	 if (sizeof($latLngXML[places])>0)
	{
		//$error = "<h2>No Lat/Lon found for The Following</h2><ul><li>";
		foreach($latLngXML[places] as $place)
		{
			//Check to see if the lat lon are set
			if (isset($place['latitude'])&&isset($place['longitude']))
			{
				$lat = $place["latitude"];
				$lon = $place["longitude"];
				
				if(isset($place['iso'])) {
					$iso = (string)$place['iso'];
				}
				if(isset($place["names"][0]["value"]))
				{
					$normalized = "'".$place["names"][0]["value"]."'";
				}
				if(isset($place['id']))
				{

					$key = (string)$place['id'];
				}
				if (strpos($pName, $place["names"][0]["value"]) !== FALSE)

				{

					$insertFlag = true;
					break;
				}

			}
			else
			{
				continue;
			}
		}	
			
	}
	else
	{
		$insertFlag = false;
		
	}
	 
	 
 }
 
 
public function getPlaceFromFS($placeId,$credentials,$pName) {
		$credentials = array();
	
	$credentials["agent"] = $_COOKIE["agent"];
	$credentials["accessToken"] = $_COOKIE["accessToken"];
	$credentials["loggedOn"] = $_COOKIE["loggedOn"];
	$credentials["mainURL"] = $_COOKIE["mainURL"];
	$latLngURL = $credentials["mainURL"].'platform/places/'.$placeId;
	$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);

	
	$key = $placeId;
	$normalized = '';
	$lat = -999;
	$lng = -999;
	$iso = -999;
	$insertFlag = false;
	
	
	$this->getLatLonFromRequest($lat,$lon,$latLngXML,$insertFlag,$iso, $key,$normalized,$latLngURL,$pName);
	
	if (!$insertFlag)
	{
		//try description
		$latLngURL = $credentials["mainURL"].'platform/places/description/'.$placeId;//.'?';
		$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);
		$this->getLatLonFromRequest($lat,$lon,$latLngXML,$insertFlag,$iso, $key,$normalized,$latLngURL,$pName);
	}
	
	if($insertFlag) {
			$normalized = str_replace("'", "", $normalized);

	
			try{
			
				$this->dao->insertISOLocation($key, $normalized, $lat, $lng, $iso);
			}
			catch(Exception $e)
			{
			
			}
	
	}
	//pg_close();
	
	$placeArray = array("lat" => $lat, "lon" => $lon, "iso" => $iso);
	
	return $placeArray;
}

public function getLatLonFromGoogle ($place) {
	 $lat = -999;
	 $lng = -999;
	echo "<br> attepted to get the lat lon from google (classPlace.php)<br>";
	 // create a new HTTP_Request object to be used in the geocoding process
	 $request = new HTTP_Request();
	
	 // set the URL of the HTTP_Request object to the family search identity/login endpoint
	$request->setUrl("http://maps.googleapis.com/maps/api/geocode/xml?address=".$place."&sensor=false");
	 $request->sendRequest();
	
	 //echo "<pre>".$request->getResponseBody()."</pre>";
	
	
	 // check the HTTP response code to ensure success
	 if($request->getResponseCode() == 200)
	 {
		// // convert the response from the HTTP_Request to a SimpleXMLElement object - to be checked for errors, parsed and consumed
		// $GeocodeResponse = new SimpleXMLElement($request->getResponseBody());	
		// //file_put_contents("../data/placexml/".$place.".xml", $GeocodeResponse->asXML());
		// $lat = $GeocodeResponse->result->geometry->location->lat;
		// $lng = $GeocodeResponse->result->geometry->location->lng;
	 }
	 else {
		// $lat = -999;
		// $lng = -999;
	 }
	
	 $latLonArray = array("lat" => $lat, "lon" => $lng);
	
	 return $latLonArray;
 }
 
public function getPlaceName()
 {
	 return "Birth Place";
 }
}

function testPlace($test)
{
	$test["name"] = "Test place";
	
	//Use Dependency injection to stub out the FMR Connection
	FmrFactory::setFsConnect(new StubFsConnect());
	//FmrFactory::createDao()->clean();
	//FmrFactory::createDao()->dbDump();
	
	
	//CASE 1 Id provided && ID exists in the database, No Name
	$type = 1;
	$placeId = "8265086";
	$place = new Place($type, $placeId, $placeLat, $placeLon,$credentials,$pName);
	$expLat = "-999";
	$expLon = "-999";
	if ($place->Lat()!=$expLat&& $place->Lon() !=$expLon)
	{
		array_push($test['info'],"Passed San Jose Just Id Test");
	}
	else
	{
		array_push($test['info'],"Failed San Jose Just Id Test");
	}
	
	//Cleanup, Set dependencies back to what they should be
	FmrFactory::setFsConnect(new FsConnect());
	return $test;
}
?>