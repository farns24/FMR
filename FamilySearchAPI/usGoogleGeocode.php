<?php
include_once('HTTP/Request.php');

$placesToGeocode =	array(	"Bristol,Pennsylvania",
							"Darby,Delaware",
							"Radnor,Pennsylvania",
							"Bensalem,Pennsylvania",
							"Philadelphia,Pennsylvania",
							"Lewes,Delaware",
							"New+Castle,Delaware",
							"Newark,Delaware",
							"Wilmington,Delaware",
							"Dover,Delaware",
							"Burlington+County,New+Jersey",
							"Cumberland+County,New+Jersey",
							"Gloucester+County,New+Jersey",
							"Kent+County,Delaware",
							"New+Castle+County,Delaware",
							"Sussex+County,Delaware",
							"Salem+County,New+Jersey",
							"Bucks+County,Pennsylvania",
							"Chester+County,Pennsylvania",
							"Greenwich,New+Jersey",
							"Trenton,New+Jersey");

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

file_put_contents("../data/placexml/delawareplaces.txt", $allPlaces);

?>