<?php
	set_time_limit (0);
	
	// Read a file in and split it into an array of place ids
	$rawfile = file_get_contents("../data/v2/analysisCSV/California_places.txt");
	$places = explode(",", $rawfile);
	
	//************************************************************************************//
	// Code snippet that interacts with PostgreSQL datadase
	$pgConnection = pg_connect('host=localhost port=5433 dbname=familysearch user=familysearch password=familysearch');
	foreach($places as $placeId)
	{
		$result = pg_query($pgConnection, "SELECT name, lat, lng FROM fsplacesiso WHERE fsid=$placeId;");
		$row = pg_fetch_row($result);
		// If the family search id is in the places database table, use it
		if(isset($row))
		{
			//$name = $row[0];
			//$lat = $row[1];
			//$lon = $row[2];
			$line = $row[0]."\t".$row[1]."\t".$row[2]."\r\n";
			file_put_contents("../data/v2/analysisCSV/California_latlons.txt", $line, FILE_APPEND);
		}
	}
	//************************************************************************************//
?>