<?
// NEEDS COMMENTING

// BYUFMR Event Place Builder
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: eventPlaceFMRXMLBuilder.php
// Purpose: Creates basic XML skeleton for life event places of extracted people.

// Builds XML for event place
// Calls getFSXMLResponse()
// This is where we will make a database connection to see if we can't pull off the coordinates w/o a call to FS
function eventPlaceFMRXMLBuilder($credentials, $event)
{
	$mainURL = $_COOKIE['mainURL'];
	
	//Scrub the original date input for <,>, and & - will mess up the xml structure
	$cleanDate = str_replace('<', '', $event->value->date->original);
	$cleanDate = str_replace('>', '', $cleanDate);
	$cleanDate = str_replace('&', '', $cleanDate);
	$xfmrCurrentEvent = '<event type="'.strtolower($event->value['type']).'"><date><original>'.$cleanDate.'</original>';
	if(isset($event->value->date->astro->earliest))
	{
		$xfmrCurrentEvent .= '<astro>'.$event->value->date->astro->earliest.'</astro></date>';
	}
	else
	{
		if(strtolower($event->value['type']) == 'birth' || strtolower($event->value['type']) == 'christening')
		{
			$xfmrCurrentEvent .= '<astro>0000000</astro></date>';
		}
		else if(strtolower($event->value['type']) == 'death' || strtolower($event->value['type']) == 'burial')
		{
			$xfmrCurrentEvent .= '<astro>9999999</astro></date>';
		}
		else
		{
			$xfmrCurrentEvent .= '<astro>-9999999</astro></date>';
		}
	}
	if(isset($event->value->place->normalized['id']))
	{
		$xfmrCurrentEvent .= '<place id=';
		$FSPlaceID = $event->value->place->normalized['id'];
		$xfmrCurrentEvent .= "\"$FSPlaceID\">";
		if($pgConnection = pg_connect('host=localhost port=5433 dbname=familysearch user=familysearch password=familysearch'))
		{
			// echo "Connected to PostgreSQL<br />";
			// Search the SQL database using the id # as the unique key -> extract the lat & lng if found
			$result = pg_query($pgConnection, "SELECT name, lat, lng FROM fsplaces WHERE fsid=$FSPlaceID;");
			$row = pg_fetch_row($result);
			if($row)
			{
				$xfmrCurrentEvent .= '<normalized>'.$row[0].'</normalized><lat>'.$row[1].'</lat><lng>'.$row[2].'</lng>';
			}
			else
			{
				$eventLatLongQueryURL = $mainURL.'authorities/v1/place/'.$event->value->place->normalized['id'].'?';
				$eventLatLongXML = getFSXMLResponse($credentials, $eventLatLongQueryURL);
				$xfmrCurrentEvent .= getLatLng($eventLatLongXML, $pgConnection);
			}
		}
		if($pgConnection)
		{
			pg_close();
		}
		$xfmrCurrentEvent .= '</place>';
	}
	else if(isset($event->value->place->normalized))
	{
		$xfmrCurrentEvent .= '<place>';
		$place = str_replace(' ', '+', $event->value->place->normalized);
		$eventLatLongQueryURL = $mainURL.'authorities/v1/place?place='.$place.'&';
		$eventLatLongXML = getFSXMLResponse($credentials, $eventLatLongQueryURL);
		$xfmrCurrentEvent .= getLatLng($eventLatLongXML);
		$xfmrCurrentEvent .= '</place>';
	}
	$xfmrCurrentEvent .= '</event>';
	return $xfmrCurrentEvent;
}

?>