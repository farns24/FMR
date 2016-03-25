<?php
require_once('getFSXMLResponse.php');

function fargo ($placeId) {
	$pgConnection = pg_connect('host=localhost port=5433 dbname=familysearch user=familysearch password=familysearch');
	$result = pg_query($pgConnection, "SELECT lat, lng, iso FROM fsplacesiso WHERE fsid=$placeId;");
	$row = pg_fetch_row($result);

	$credentials = array();
	$credentials["user"] = $_COOKIE["user"];
	$credentials["password"] = $_COOKIE["password"];
	$credentials["agent"] = $_COOKIE["agent"];
	$credentials["sessionID"] = $_COOKIE["sessionID"];
	$credentials["loggedOn"] = $_COOKIE["loggedOn"];
	$credentials["mainURL"] = $_COOKIE["mainURL"];

	// put place id where the number is.
	$latLngURL = $credentials["mainURL"].'authorities/v1/place/'.$placeId.'?';
	$latLngXML = getFSXMLResponse($credentials, $latLngURL);

	//echo "<pre>";
	//print_r($latLngXML);
	//echo "</pre>";

	foreach($latLngXML->places->place as $place)
	{
		$key = '';
		$normalized = '';
		$lat = -999;
		$lng = -999;
		$iso = -999;
		
		if(isset($place['iso'])) {
			$iso = (string)$place['iso'];
		}
		if(isset($place->normalized->form))
		{
			$normalized = "'".$place->normalized->form."'";
		}
		if(isset($place['id']))
		{
			$key = (string)$place['id'];
		}
		if(isset($place->location))
		{
			if(isset($place->location->point->latitude))
			{
				$lat = $place->location->point->latitude;
			}
			if(isset($place->location->point->longitude))
			{
				$lng = $place->location->point->longitude;
			}
		}
		else
		{
			$lat = -999;
			$lon = -999;
		}
	// If not found, search FS and insert {key, normalized name, lat, lng, iso}
		if($key)
		{
			$normalized = str_replace("'", "", $normalized);
			pg_query($pgConnection, "DELETE FROM fsplacesiso WHERE fsid=$key;");
			pg_query($pgConnection, "INSERT INTO fsplacesiso (fsid, name, lat, lng, iso) VALUES($key, '$normalized', $lat, $lng, '$iso');");
		}
	}
	pg_close();
	
	$placeArray = array("lat" => $lat, "lon" => $lng, "iso" => $iso);
	
	return $placeArray;
}
?>