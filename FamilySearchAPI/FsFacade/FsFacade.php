<?php

	class FsFacade {
	
			private $dao = null;
			private $fsConnect = null;
		function __construct()
		{
					$this->dao = FmrFactory::createDao();
					$this->fsConnect = FmrFactory::createFsConnect();
		}
	
		public function getLocation($placeId)
		{
			$result = array();
			$row = $this->dao->fetchFromDb($placeId);
				
				// If the family search id is in the places database table, use it
				$isInDatabase = $row[0] != null && $row[0] != -999;
				
	
				if($isInDatabase)
				{
					echo "<div class = 'well'>";
					var_dump($row);
					echo "</div>";
					$result[0] = $row[0];					
					$result[1] = $row[1];					
					$result[2] = $row[2];
					
					//If what is in the database does not match the location we are looking for
				}
		
				//If the family search id is NOT in the places database table, then perform a query and add the place to the database
				if (!$isInDatabase)
				{

					$result = $this->getPlaceFromFS($placeId,$credentials,$pName);
					
					$this->lat = $result["lat"];
					$this->lon = $result["lon"];
					$this->iso = $result["iso"];

				}
		
		}
		
		
		public function getPlaceFromFS($placeId,$credentials,$pName) {
				//$credentials = array();
			/*
			$credentials["agent"] = $_COOKIE["agent"];
			$credentials["accessToken"] = $_COOKIE["accessToken"];
			$credentials["loggedOn"] = $_COOKIE["loggedOn"];
			$credentials["mainURL"] = $_COOKIE["mainURL"];
			*/
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
			echo "<div class = 'well'>$insertFlag</div>";
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
			echo "<div class = 'well'>";
			var_dump($placeArray);
			echo"</div>";
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
							break;
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
						//echo "1";
						if (isset($latLngXML["entries"][0]))
						{
							//echo "2";
							if (isset($latLngXML["entries"][0]["content"]["gedcomx"]["places"]))
							{
								//echo "3";
								if (isset($latLngXML["entries"][0]["content"]["gedcomx"]["places"][0]))	
								{
									//echo "4";
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