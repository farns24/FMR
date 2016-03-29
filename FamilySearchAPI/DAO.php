<?php
require_once('Singleton.php');
/**
* Inserts a location into family search
*@pre $key is not null
*@pre $key is not a duplicate
*@pre $lat $lon, and $iso are not null
*
*@post location is stored in database
*/

class DAO {

	
	private function isValidField($param)
	{
		if (!isset($param))
		{
			return false;
		}
		else if ($param == "")
		{
			return false;
		}
		else if ($param == -999)
		{
			return false;
		}
		else if ($param == "-999")
		{
			return false;
		}
	
		return true;
	}
	/**
	*@pre $key is type intiger
	*/
	public function insertISOLocation($key, $normalized, $lat, $lng, $iso)
	{
		if ($this->isValidField($normalized) && $this->isValidField($lat) && $this->isValidField($lon))
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
	
	public function clean()
	{
		$command = "SELECT fsid,lat,lng FROM fsplacesiso;";
		$result = $this->runCommand($command);
		
		
		$rows = pg_fetch_all($result);
		
		foreach($rows as $row)
		{
			if ($this->isValidField($row[1]) && $this->isValidField($row[2]))
			{
				continue;
			}
			else
			{
				//echo "<b>Removed</b><br/>";
				//var_dump($row);
				$this->deleteFromDb((int)$row[0]);
			}
			
		}
		pg_close();
		
	}
	
	public function fetchByName($placeName)
	{
		$query = "SELECT placeName, pid FROM PlaceToId WHERE placeName='$placeName';";
		$result = $this->runCommand($query);
		$row = pg_fetch_row($result);
							
		pg_close();
		return $row;
	}

	public function fetchFromDb($placeId)
	{
		//echo "<b>$placeId</b><br/>";
		$result = $this->runCommand("SELECT lat, lng, iso, name FROM fsplacesiso WHERE fsid=$placeId;");
								
		$row = pg_fetch_row($result);
		//var_dump($row);
		pg_close();
		return $row;
		
	}

	public function deleteFromDb($placeId)
	{
		$result = $this->runCommand("DELETE FROM fsplacesiso WHERE fsid=$placeId;");
		pg_close();	
		
	}

	public function clearISOTable()
	{
		$command = "DELETE FROM fsplacesiso;";

		
		$this->runCommand($command);
		pg_close();
	}

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
}


?>