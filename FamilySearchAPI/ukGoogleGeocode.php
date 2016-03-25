<?php
include_once('HTTP/Request.php');

$placesToGeocode =	array(	"Ireland,+Ireland",
							"Connaught,+Ireland",
							"Galway,+Ireland",
							"Leitrim,+Ireland",
							"Mayo,+Ireland",
							"Roscommon,+Ireland",
							"Sligo,+Ireland",
							"Leinster,+Ireland",
							"Carlow,+Ireland",
							"Dublin,+Ireland",
							"Kildare,+Ireland",
							"Kilkenny,+Ireland",
							"Laois,+Ireland",
							"Longford,+Ireland",
							"Louth,+Ireland",
							"Meath,+Ireland",
							"Offaly,+Ireland",
							"Westmeath,+Ireland",
							"Wexford,+Ireland",
							"Wicklow,+Ireland",
							"Munster,+Ireland",
							"Ulster,+Ireland",
							"Cavan,+Ireland",
							"Donegal,+Ireland",
							"Monaghan,+Ireland");

$allPlaces = "";

foreach($placesToGeocode as $place) {
	$place = str_replace("\"", "", $place);

	// create a new HTTP_Request object to be used in the geocoding process
	$request = new HTTP_Request();
	
	// set the URL of the HTTP_Request object to the family search identity/login endpoint
	$request->setUrl("http://maps.googleapis.com/maps/api/geocode/xml?address=".$place."&sensor=false");
	$request->sendRequest();
	
	//echo "<pre>".$request->getResponseBody()."</pre>";
	
	
	// check the HTTP response code to ensure success
	if($request->getResponseCode() == 200)
	{
		// convert the response from the HTTP_Request to a SimpleXMLElement object - to be checked for errors, parsed and consumed
		$GeocodeResponse = new SimpleXMLElement($request->getResponseBody());
		//file_put_contents("../data/placexml/".$place.".xml", $GeocodeResponse->asXML());
		$lat = $GeocodeResponse->result->geometry->location->lat;
		$lng = $GeocodeResponse->result->geometry->location->lng;
		$allPlaces .= str_replace("+", " ", $place)."\t".$lat."\t".$lng."\r\n";
	}
	
}

file_put_contents("../data/placexml/irelandplaces.txt", $allPlaces);

?>