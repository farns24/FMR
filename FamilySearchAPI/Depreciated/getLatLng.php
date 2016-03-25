<?
// BYUFMR Get Latitude and Longitude
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: getLatLng.php
// Purpose: Creates XML string for places of extracted people including the place name, latitude, and longitude.

// Returns Latitude and Longitude XML string
function getLatLng($latLngXML, $pgConnection)
{
	$xfmrCurrentEventLatLng = '';
	foreach($latLngXML->places->place as $place)
	{
		$key = '';
		$normalized = '';
		$lat = '0.0';
		$lng = '0.0';
		if(isset($place['id']))
		{
			$key = $place['id'];
		}
		if(isset($place->normalized->form))
		{
			$normalized = "'".$place->normalized->form."'";
			$xfmrCurrentEventLatLng .= "<normalized>".$place->normalized->form."</normalized>";
		}
		if(isset($place->location->point->latitude))
		{
			$lat = $place->location->point->latitude;
			$xfmrCurrentEventLatLng .= '<lat>'.$place->location->point->latitude.'</lat>';
		}
		if(isset($place->location->point->longitude))
		{
			$lng = $place->location->point->longitude;
			$xfmrCurrentEventLatLng .= '<lng>'.$place->location->point->longitude.'</lng>';
		}
// If not found, search FS and insert {key, normalized name, lat, lng}
		if($key)
		{
			pg_query($pgConnection, "INSERT INTO fsplaces (fsid, name, lat, lng) VALUES($key, $normalized, $lat, $lng);");
		}
	}
	return $xfmrCurrentEventLatLng;
}

?>