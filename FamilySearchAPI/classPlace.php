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

		// 0 - no place provided, give empty place object
		// 1 - Place ID provided, get place lat, lon, and ISO from web service
		// 2 - Latitude and Longitude provided, set them and give empty ISO
		//echo "<br> Place type (classPlace.php)".$type."<br>";
		switch ($type) {
		
			case 0:
				$this->NoPlace();
			break;
			
			case 1:
				
				$location = FmrFactory::getFacade()->getLocation($placeId);
				
				echo "<div class = 'well'>";
					var_dump($location);
				echo "</div>";
					
				$this->lat = $location[0];					
				$this->lon = $location[1];					
				$this->iso = $location[2];
				
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