<?php
require_once('DynamicCache.php');
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
	
		public function getLocation($placeId)
		{
			$result = array();
			
			//Check Cache
			$cacheRow = $this->cache->get($placeId);
			
			if (sizeof($cacheRow)==3)
			{
					$result[0] = $cacheRow['lat'];					
					$result[1] = $cacheRow['lon'];					
					$result[2] = $cacheRow['iso'];
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

					$result = $this->getPlaceFromFS($placeId,$credentials,$pName);
					
					$result[0] = $result["lat"];
					$result[1] = $result["lon"];
					$result[2] = $result["iso"];

					$this->cache->add($placeId,$result["iso"],$result["lat"],$result["lon"]);
					
				}
				return $result;
			}
		}
		
		
		public function getPlaceFromFS($placeId,$credentials,$pName) {
				$credentials = array();
			
			$credentials["agent"] = $_COOKIE["agent"];
			$credentials["accessToken"] = $_COOKIE["accessToken"];
			$credentials["loggedOn"] = $_COOKIE["loggedOn"];
			$credentials["mainURL"] = $_COOKIE["mainURL"];
			
			$latLngURL = $credentials["mainURL"].'platform/places/'.$placeId;
			$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);

			
			$key = $placeId;
			$normalized = '';
			$lat = -999;
			$lng = -999;
			$iso = -999;
			$insertFlag = false;
			
			
			$this->getLatLonFromRequest($lat,$lon,$latLngXML,$insertFlag,$iso, $key,$normalized,$latLngURL,$pName);
			
			if (!$insertFlag)
			{
				//try description
				$latLngURL = $credentials["mainURL"].'platform/places/description/'.$placeId;//.'?';
				$latLngXML = $this->fsConnect->getFSXMLResponse($credentials, $latLngURL);
				$this->getLatLonFromRequest($lat,$lon,$latLngXML,$insertFlag,$iso, $key,$normalized,$latLngURL,$pName);
			}

			if($insertFlag) {
					$normalized = str_replace("'", "", $normalized);

			
					try{
					
						$this->dao->insertISOLocation($key, $normalized, $lat, $lng, $iso);
					}
					catch(Exception $e)
					{
					
					}
			
			}
			//pg_close();
			
			$placeArray = array("lat" => $lat, "lon" => $lon, "iso" => $iso);
			
			return $placeArray;
		}
	
			public function getLatLonFromRequest(&$lat,&$lon,$latLngXML,&$insertFlag, &$iso, &$key,&$normalized,$latLngURL,$pName)
		 {
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
						if(isset($place["latitude"]) && isset($place["longitude"]) &&isset($place["names"][0]["value"]) &&isset($place['id']))
						{
							$lat = $place["latitude"];
							$lon = $place["longitude"];
							$normalized = "'".$place["names"][0]["value"]."'";
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
		*
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
						
						if (isset($latLngXML["entries"][0]))
						{
							
							if (isset($latLngXML["entries"][0]["content"]["gedcomx"]["places"]))
							{
								
								if (isset($latLngXML["entries"][0]["content"]["gedcomx"]["places"][0]))	
								{
									
									return $latLngXML["entries"][0]["content"]["gedcomx"]["places"][0]["id"];
								}
							}
						}
					}
				}
				
			return "";
		}
	

	}

?>