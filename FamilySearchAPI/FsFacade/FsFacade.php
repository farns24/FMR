<?php
require_once('DynamicCache.php');
include_once("/../fmrFactory.php");

	/**
	* Fs Facade wraps the persistance subsystem for family search information. It allows for one place for the program to go to to get information
	* From family search. 
	*
	* There are 3 places where information can be pulled. 
	*
	* The first is the family search databases. These are accessed through the FamilySearch web api. They are the largest 
	* collection of information. They are also the slowest to access. 
	* 
	* The second is through the Local Postgress Database. When places are found in searches, they are stored in the database for future reference.
	* The Database is quicker to access, but contains less data.
	*
	* Finally, the Dynamic Memory Cache of place information. It is the fastest to access. NOTE: Due to memory leaks, the Dynamic Memory Cache has been disabled. 
	* Plans have been made to make the cache regulate its own size. Until work is completed on that, The Database and Family search are the only places to acces memory
	*/
	class FsFacade {
	
			private $dao = null;
			private $fsConnect = null;
			
			private $cache = null;
		function __construct()
		{
			$this->dao = FmrFactory::createDao();
			$this->fsConnect = FmrFactory::createFsConnect();
			$this->cache = new DynamicCache();
		}
		/**
		* Gets Ids from Place Name
		* @Pre- $placeName is not empty
		* @Pre- $credentials are not empty
		* @Pre- $credentials is an array
		* @Post- $Id is not empty
		*/
		public function getId($placeName, $credentials)
		{
			if (empty($placeName))
			{
				throw new Exception('emptyId');
			}
			
			if (!isset($credentials)||!is_array($credentials))
			{
				throw new Exception('null Credentials');
			}
			
			$fsConnect = FmrFactory::createFsConnect();
			$placeName = str_replace ("'","",$placeName);
			
			//$id = null;
			if ($this->cache->hasName($placeName))
			{
				//echo "<h2>Duplicate found</h2>";

				$id = $this->cache->getId($placeName);
				if (empty($id) ||!is_numeric($id))
				{
			
					throw new Exception('Cache corruption error');
				}
				
				//echo "<H1>Returned from Cache</H1>";
				return $id;
			}
			else
			{
			
				$row = $this->dao->fetchByName($placeName);
						//Store result in posgress table
					if(is_numeric($row[1]))
					{
						//echo "Using local db<br />";
						$id = $row[1];
						//echo "<h1>$id pulled from $placeName</h1>";
						if (!isset($id) || empty($id) ||!is_numeric($id))
						{
			
							throw new Exception('database Corruption error');
						}

							$this->cache->add($id,$row[4],$row[2],$row[3],$placeName);
						
					//	echo "<H1>Returned from DB</H1>";
						return $id;
					}
					else
					{
				
					$path = urlencode($placeName);
			
					$url = $credentials["mainURL"]."platform/places/search?access_token=".$credentials["accessToken"]."&q=name:\"".$path."\"";
					$response = $fsConnect->getFSXMLResponse($credentials,$url);
			
					$props = $response["entries"][0]["content"]["gedcomx"]["places"][0];
					$id = $props["id"];
					$lat = $props["latitude"];
					$lng = $props["longitude"];
						//var_dump($props);
						if (empty($id))
						{
							echo "<div class = 'well'>";
							echo $url;
							echo "<br/>";
							echo json_encode($response);
							echo "</div>";
							throw new Exception('emptyId');
						}
						else
						{

							$this->cache->add($id,"-999",$lat,$lng,$placeName);
						
							//Store result in posgress table
							$this->dao->insertISOLocation($id, $placeName, $lat, $lng, "-999");
						}
					}
			if (!isset($id) || empty($id)||!is_numeric($id))
			{
				echo $id;
				throw new Exception('emptyId');
			}
			//echo "<H1>Returned from FS</H1>";
			return $id;
		}
		
		}
		
		/**
		* gets location from place id.
		* Searches Memory Cache first, then Database, then Family Search API
		* @param $placeId 
		* @return place that matches that id
		* @throws Place Not Found Exception.
		*/
		public function getLocation($placeId)
		{
			$result = array();
			
			//Check Cache
			$cacheRow = $this->cache->get($placeId);
			
			if (sizeof($cacheRow)==3 && isset($cacheRow['lat']))
			{
					$result[0] = $cacheRow['lat'];					
					$result[1] = $cacheRow['lon'];					
					$result[2] = $cacheRow['iso'];
					//var_dump($result);
					return $result;
			}
			else 
			{
				$row = $this->dao->fetchFromDb($placeId);
				// If the family search id is in the places database table, use it
				$isInDatabase = $row[0] != null && $row[0] != -999;
				
	
				if($isInDatabase)
				{
					
					$result[0] = $row[0];					
					$result[1] = $row[1];					
					$result[2] = $row[2];
					
					//If what is in the database does not match the location we are looking for
				}
		
				//If the family search id is NOT in the places database table, then perform a query and add the place to the database
				if (!$isInDatabase)
				{

					$fsresult = $this->getPlaceFromFS($placeId,$credentials,$pName);
					
					$result[0] = $fsresult["lat"];
					$result[1] = $fsresult["lon"];
					$result[2] = $fsresult["iso"];

					$this->cache->add($placeId,$result["iso"],$result["lat"],$result["lon"],$pName);
					
				}
				//var_dump($result);
				return $result;
			}
		}
		
		/**
		* Pulls Place from family Search by id. 
		*
		* @param placeId - Id of Place
		* @param credentials - Array of login details
		* @param pName - Name of place: can be used to search by name if id is depricated.
		*
		*/
		public function getPlaceFromFS($placeId,$credentials,$pName) {
				$credentials = array();
			
			$credentials["agent"] = $_COOKIE["agent"];
			$credentials["accessToken"] = $_COOKIE["accessToken"];
			$credentials["loggedOn"] = $_COOKIE["loggedOn"];
			$credentials["mainURL"] = $_COOKIE["mainURL"];
			
			$latLngURL = $credentials["mainURL"].'platform/places/description/'.$placeId;
			$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);

			
			$key = $placeId;
			$normalized = '';
			$lat = -999;
			$lon = -999;
			$iso = -999;
			$insertFlag = false;
			
			
			$this->getLatLonFromRequest($lat,$lon,$latLngXML,$insertFlag,$iso, $key,$normalized,$latLngURL,$pName);
			
			if (!$insertFlag)
			{
				//try description
				$latLngURL = $credentials["mainURL"].'platform/places/'.$placeId;//.'?';
				$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);
				$this->getLatLonFromRequest($lat,$lon,$latLngXML,$insertFlag,$iso, $key,$normalized,$latLngURL,$pName);
			}

			if($insertFlag) {
					$normalized = str_replace("'", "", $normalized);

			
					try{
					
						$this->dao->insertISOLocation($placeId, $normalized, $lat, $lon, $iso);
					}
					catch(Exception $e)
					{
					
					}
			
			}
			//pg_close();
			
			$placeArray = array("lat" => $lat, "lon" => $lon, "iso" => $iso);
			
			return $placeArray;
		}
	
	/**
	* Gets Latitude and Longitud from response from family search
	*/
	public function getLatLonFromRequest(&$lat,&$lon,$latLngXML,&$insertFlag, &$iso, &$key,&$normalized,$latLngURL,$pName){
			 if (sizeof($latLngXML[places])>0)
			{
				
				foreach($latLngXML[places] as $place)
				{
					//Check to see if the lat lon are set
					if (isset($place['latitude'])&&isset($place['longitude']))
					{
						
						
						if(isset($place['iso'])) {
							$iso = (string)$place['iso'];
						}
						if(isset($place["names"][0]["value"]) &&isset($place['id']))
						{
							$lat = $place["latitude"];
							$lon = $place["longitude"];
							$normalized = "'".$place["display"]["fullName"]."'";
							$key = (string)$place['id'];
							$insertFlag = true;
							return;
						}
							

					}
					else
					{
						continue;
					}
				}	
					
			}
			else
			{
				$insertFlag = false;
				
			}
			 
			 
		 }
		 /**
		* finds Id From name of place
		*/
		public function getIdFromName($pName,$credentials)
		{

			$latLngURL = "https://familysearch.org/platform/places/search?access_token=".$credentials['accessToken']."&q=name:\"$pName\"";
			$latLngURL = str_replace(" ","%20",$latLngURL);

			$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);
			
				if (isset($latLngXML))
				{
					if (isset($latLngXML["entries"]))
					{
						$entries = $latLngXML["entries"];
						if (isset($entries[0]) and isset($entries[0]["content"]["gedcomx"]["places"]))
						{
								
							if (isset($entries[0]["content"]["gedcomx"]["places"][0]))	
							{
								return $entries[0]["content"]["gedcomx"]["places"][0]["id"];
							}
							
						}
					}
				}
				
			return "";
		}
		/**
		* @Pre $placeId is numeric
		*
		*/
		public function loadChildrenPlaces($placeId,$credentials,&$names)
		{
			if (!is_numeric($placeId))
			{
				throw new Exception("Place id is not numeric");
			}
			
			$latLngURL = "https://familysearch.org/platform/places/description/$placeId/children?access_token=".$credentials['accessToken'];
			$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);
			//echo json_encode($latLngXML);
			if (isset($latLngXML['places']) && count($latLngXML['places'])>0)
			{
				foreach ($latLngXML['places'] as $place)
				{
				
					$fullName = $place["display"]["fullName"];
					$fullName = urlencode($fullName);
					//echo $fullName;
					array_push($names, $fullName);
					
					$this->cache->add($place["id"],$fullName,$place["latitude"],$place["longitude"],$fullName);
					$this->dao->insertISOLocation($place["id"], $fullName, $place["latitude"],$place["longitude"], "-999");
					
					$this->loadChildrenPlaces($place["id"],$credentials,$names);
				}
			}
		}	
	
	/**
	* @pre: url is not empty
	* @post: SearchCount must be non 0 by the end
	*/
	public function getRootGeneration($url,&$searchCount,$credentials,$payload)
	{
		if (empty($url))
		{
			throw new Exception("Empty URL Exception");
		}
		
		$rawResponse =  $this->fsConnect->getFSXMLPOSTResponse($url, end($payload), $credentials);
		
		$response = json_decode($rawResponse,true);//this is important
		// Do a person read for each person returned
		//echo "$rawResponse";
		$url = $response['links']['next']['href'];
		
		// The number of people returned in search
		$searchCount = sizeof($response['entries'],0);
		
		if ($searchCount == 0)
		{
			throw new Exception("No Search Results found exception");
		}
		
		error_log("$searchCount Search results Found");
		//exit();
		return $response;
	}
	

}

?>