<?php
	require_once('getFSXMLResponse.php'); // Handles auto-throttling from FS to perform a query
	set_time_limit (0);
	
	// Create the credentials
  	// Get cookie vars
	$credentials["user"] = $_COOKIE["user"];
	$credentials["password"] = $_COOKIE["password"];
	$credentials["agent"] = $_COOKIE["agent"];
	$credentials["sessionID"] = $_COOKIE["sessionID"];
	$credentials["loggedOn"] = $_COOKIE["loggedOn"];

	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
	// Create a new table
	$result = pg_query($pgConnection, "CREATE TABLE fsplacesISO (fsid integer PRIMARY KEY, name character varying(100), lat double precision, lng double precision, iso character varying(10));");
	$result = pg_query($pgConnection, "DELETE FROM fsplacesISO WHERE iso = 'n/a';");
	
	// Get all the data from the old table that isn't in the new one
	$result = pg_query($pgConnection, "SELECT * FROM fsplaces EXCEPT SELECT fsid, name, lat, lng FROM fsplacesiso;");
	$rows = pg_fetch_all($result);
	
	foreach($rows as $row)
	{
		// For updating the session 
		$credentials["sessionID"] = $_COOKIE["sessionID"];
		
		$fsid = $row['fsid'];
		$name = $row['name'];
		($row['lat'] == 0)? $lat = -999 : $lat = $row['lat'];
		($row['lng'] == 0)? $lng = -999 : $lng = $row['lng'];
		$iso = "n/a";
		// Query familysearch to find the ISO for the place
		//$queryURL = "https://api.familysearch.org/authorities/v1/place/".$row['fsid']."?";
		echo "<br>PlaceISOTransfer.php<br>";
		$queryURL = "https://api.familysearch.org/platform/places/".$row['fsid']."?";
		// query fs and get an xml structure back
		$json = getFSXMLResponse($credentials, $queryURL);
		if(isset($json->places->place["iso"]))
		{
			$iso = $json->places->place["iso"];
		}
		pg_query($pgConnection, "INSERT INTO fsplacesiso (fsid, name, lat, lng, iso) VALUES('$fsid', '$name', '$lat', '$lng', '$iso');");
	}
?>