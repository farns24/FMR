<?php
	require_once('getFSXMLResponse.php'); // Handles auto-throttling from FS to perform a query
	set_time_limit (0);
	
	// Create the credentials
  	// Get cookie vars
	$credentials["user"] = $_COOKIE["user"];
	$credentials["password"] = $_COOKIE["password"];
	$credentials["agent"] = $_COOKIE["agent"];
	$credentials["access_token"] = $_COOKIE["access_token"];
	$credentials["loggedOn"] = $_COOKIE["loggedOn"];

	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
	// Create a new table
	$result = pg_query($pgConnection, "CREATE TABLE fsplacesISO (fsid integer PRIMARY KEY, name character varying(100), lat double precision, lng double precision, iso character varying(10));");
	$result = pg_query($pgConnection, "DELETE FROM fsplacesISO WHERE iso = 'n/a';");
	
	for($i = 1; $i < 2; $i++)
	{
		// For updating the session 
		$credentials["access_token"] = $_COOKIE["access_token"];
		echo "<br>access_token in PlaceISOBuilder.php [".$credentials["access_token"]."]<br>";
		$fsid = "-999";
		$name = "n/a";
		$iso = "n/a";
		
		// Query familysearch to find the ISO for the place
		//$queryURL = "https://api.familysearch.org/authorities/v1/place/".$i."?";
		$queryURL = "https://api.familysearch.org/platform/places/search?";
		
		echo "PlaceISOBuilder.php";
		// query fs and get an xml structure back
		$json = getFSXMLResponse($credentials, $queryURL);

		if(isset($json->places->place["iso"]))
		{
			$fsid = $json->places->place["id"];
			$name = $json->places->place->normalized->form;
			$iso = $json->places->place["iso"];
		}
		//Uncommented code below
		pg_query($pgConnection, "INSERT INTO fsplacesiso (fsid, name, lat, lng, iso) VALUES('$fsid', '$name', '$lat', '$lng', '$iso');");
	}
?>