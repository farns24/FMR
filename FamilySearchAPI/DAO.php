<?php
require_once('Singleton.php');

/**
* Database Access Object for Postgres Familysearch Database.
*
*/
class DAO {

	/**
	* Tests field for validity
	*/
	private function isValidField($param)
	{
		//var_dump($param);
		if (!isset($param))
		{
			//echo "<div class='well'>$param is not set</div>";
			return false;
		}
		else if (empty($param) and $param !='0' and $param!=0)
		{
			//echo "<div class='well'>$param ==\"\"</div></br>";
			return false;
		}
		else if ($param == -999)
		{
			//echo "<div class='well'>$param == -999</div>";
			return false;
		}
		else if ($param == "-999")
		{
			//echo "<div class='well'>$param == \"-999\"</div>";
			return false;
		}
		
		return true;
	}
	
	/**
	* Inserts a location into family search
	*@pre $key is not null
	*@pre $key is not a duplicate
	*@pre $lat $lon, and $iso are not null
	*
	*@post location is stored in database
	*/
	public function insertISOLocation($key, $normalized, $lat, $lng, $iso)
	{
		$this->prepName($normalized);
		if ( $this->isValidField($lat) && $this->isValidField($lng) and $normalized!="")
		{

		$command = "INSERT INTO fsplacesiso (fsid, name, lat, lng, iso) SELECT '$key', '$normalized', '$lat', $lng, '$iso' WHERE
		   NOT EXISTS (
				SELECT fsid FROM fsplacesiso WHERE fsid = '$key'
			);";
		
		
		$this->runCommand($command);
		pg_close();
		}
		else
		{
			echo "<div class = 'well>$key $lat $lng Not inserted into database</div>";
			//throw new Exception();
		}

	}
	
	/**
	* Removes duplicates from the database
	*/
	public function clean()
	{
		$command = "SELECT fsid,name,lat,lng FROM fsplacesiso;";
		$result = $this->runCommand($command);
		
		
		$rows = pg_fetch_all($result);
		
		foreach($rows as $row)
		{
			if ($row[1]=="")
			{
			
				//echo "<b>Removed</b><br/>";
				//var_dump($row);
				$this->deleteFromDb((int)$row[0]);
			}
			
		}
		pg_close();
		
	}
	/**
	* 
	* Looks up item by name
	*
	* @param - place name to be searched
	* @pre - not url encoded
	* 
	* @return place details if place is contained. null otherwise.
	*/
	public function fetchByName($placeName)
	{
		$this->prepName($placeName);
		
		//$query = "SELECT placeName, pid FROM PlaceToId WHERE placeName='$placeName';";
		$query = "SELECT name, fsid, lat, lng, iso FROM fsplacesiso WHERE name='$placeName';";
		$result = $this->runCommand($query);
		$row = pg_fetch_row($result);
							
		pg_close();
		return $row;
	}

	/**
	* Finds place details in database by PlaceId
	*
	* @param: Place Id
	*/
	public function fetchFromDb($placeId)
	{
		//echo "<b>$placeId</b><br/>";
		$result = $this->runCommand("SELECT lat, lng, iso, name FROM fsplacesiso WHERE fsid=$placeId;");
								
		$row = pg_fetch_row($result);
		//var_dump($row);
		pg_close();
		return $row;
		
	}

	/**
	* Deletes entry from database by Place Id
	*/
	public function deleteFromDb($placeId)
	{
		$result = $this->runCommand("DELETE FROM fsplacesiso WHERE fsid=$placeId;");
		pg_close();	
		
	}

	/**
	* Clears Database table
	*
	*/
	public function clearISOTable()
	{
		$command = "DELETE FROM fsplacesiso;";

		
		$this->runCommand($command);
		pg_close();
	}

	/**
	* Displays the contents of the database
	*/
	public function dbDump()
	{
		$command = "SELECT * FROM fsplacesiso;";
		$result = $this->runCommand($command);
		
		
		$rows = pg_fetch_all($result);
		
		foreach($rows as $row)
		{
			foreach($row as $field)
			{
				echo " $field ";
			}
			echo "</br>";
		}
		pg_close();
	}
	
	/**
	* Executes query in Postgres
	*/
	public function runCommand($command)
	{
		$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');

		$result = pg_query($pgConnection, $command);
		
		return $result; 
	}

	public function testDao($test)
	{
		$test["name"] = "DAO Test";
		
		
		//insert Fake entry into database
		$this->insertISOLocation(12, "16858 E Napa Drive", "100", "100", "ISO");
		 array_push($test["info"],"Insert Napa Drive");
		 
		 
		//test to see if the database entry exists
		$row = $this->fetchFromDb(12);
		if ($row[3]=="16858 E Napa Drive")
		{
			array_push($test["info"],"Passed Fetched Napa Drive");
		}
		else
		{
			array_push($test["info"],"Failed to fetch Napa Drive");
		}
		
		//remove entry
		 $this->deleteFromDb(12);
		 $row = $this->fetchFromDb(12);
		if (!$row)
		{
			array_push($test["info"],"Passed Remove Napa Drive");
		}
		else
		{
			array_push($test["info"],"Failed Remove Napa Drive");
		}
		//insert updated entry
		$this->insertISOLocation(12, "6013 S. Sedalia Ct", "100", "100", "ISO");
		array_push($test["info"],"Insert Sedalia CT");
		//test for existance
		$row = $this->fetchFromDb(12);
		if ($row[3]=="6013 S. Sedalia Ct")
		{
			array_push($test["info"],"Passed Fetch Sedalia CT");
		}
		else
		{
			array_push($test["info"],"Failed Fetch Sedalia CT");
		}
		
		//remove
		$this->deleteFromDb(12);
		$row = $this->fetchFromDb(12);
		if (!$row)
		{
			array_push($test["info"],"Passed Remove Sedalia CT");
		}
		else
		{
			array_push($test["info"],"Failed Remove Sedalia CT");
		}
		return $test;
	}
	
	public function clear()
	{
		$command = "DELETE FROM fsplacesiso;";
		$result = $this->runCommand($command);
	}

	public function testDaoNullISO($test)
	{
		$test["name"] = "DAO Null ISO Test";
		
		
		//insert Fake entry into database
		$this->insertISOLocation(12, "16858 E Napa Drive", "100", "100", "");
		 array_push($test["info"],"Insert Napa Drive");
		 
		 
		//test to see if the database entry exists
		$row = $this->fetchFromDb(12);
		if ($row[3]=="16858 E Napa Drive")
		{
			array_push($test["info"],"Passed Fetched Napa Drive");
		}
		else
		{
			array_push($test["info"],"Failed to fetch Napa Drive");
		}
		
		//remove entry
		$this->deleteFromDb(12);
		$row = $this->fetchFromDb(12);
		if (!$row)
		{
			array_push($test["info"],"Passed Remove Napa Drive");
		}
		else
		{
			array_push($test["info"],"Failed Remove Napa Drive");
		}
		//insert updated entry
		$this->insertISOLocation(12, "6013 S. Sedalia Ct", "100", "100", "");
		array_push($test["info"],"Insert Sedalia CT");
		//test for existance
		$row = $this->fetchFromDb(12);
		if ($row[3]=="6013 S. Sedalia Ct")
		{
			array_push($test["info"],"Passed Fetch Sedalia CT");
		}
		else
		{
			array_push($test["info"],"Failed Fetch Sedalia CT");
		}
		
		//remove
		$this->deleteFromDb(12);
		$row = $this->fetchFromDb(12);
		if (!$row)
		{
			array_push($test["info"],"Passed Remove Sedalia CT");
		}
		else
		{
			array_push($test["info"],"Failed Remove Sedalia CT");
		}
		return $test;
	}
	
	/**
	* Tests for url encoding. If name is url encoded, un-url encodes name. 
	*/
	private function prepName(&$placeName)
	{
		if (strpos($placeName,"%")!==false)
		{
			$placeName = urldecode($placeName);
			//throw new Exception("URL encoded Database item");
		}
		str_replace("'","",$placeName);
	
	}
}


?>