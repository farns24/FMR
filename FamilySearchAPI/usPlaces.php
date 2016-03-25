<?php
include_once('HTTP/Request.php');

$allPlaces = "";

$dir    = "../data/v2/Midlands to Delaware/";
$files = scandir($dir);
$files = array_slice($files, 2);

print_r("<pre>".$files."</pre>");

//dataTester($files[0]);
foreach($files as $place)
{
	$allPlaces .= $place.",\r\n";
}

// foreach($placesToGeocode as $place) {
	// $place = str_replace("\"", "", $place);

	// // create a new HTTP_Request object to be used in the geocoding process
	// $request = new HTTP_Request();
	
	// // set the URL of the HTTP_Request object to the family search identity/login endpoint
	// $request->setUrl("http://maps.googleapis.com/maps/api/geocode/xml?address=".$place."&sensor=false");
	// $request->sendRequest();
	
	// // echo "<pre>".$request->getResponseBody()."</pre>";
	
	
	// // check the HTTP response code to ensure success
	// if($request->getResponseCode() == 200)
	// {
		// // convert the response from the HTTP_Request to a SimpleXMLElement object - to be checked for errors, parsed and consumed
		// $GeocodeResponse = new SimpleXMLElement($request->getResponseBody());
		// // file_put_contents("../data/placexml/".$place.".xml", $GeocodeResponse->asXML());
		// $lat = $GeocodeResponse->result->geometry->location->lat;
		// $lng = $GeocodeResponse->result->geometry->location->lng;
		// $allPlaces .= str_replace("+", " ", $place)."\t".$lat."\t".$lng."\r\n";
	// }
	
// }

file_put_contents("../data/placexml/delawareplaces.txt", $allPlaces);

?>