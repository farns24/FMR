<?php 

	class CookieManager{
	
		public function loadFromCookies(&$credentials)
		{
			$credentials["user"] = $_COOKIE["user"];
			$credentials["password"] = $_COOKIE["password"];
			$credentials["agent"] = $_COOKIE["agent"];
			$credentials["sessionID"] = $_COOKIE["sessionID"];
			$credentials["loggedOn"] = $_COOKIE["loggedOn"];
		}
		
		public function loadForMaps(&$credentials,&$fileName)
		{
			loadFromCookies($credentials);
			$fileName = $_COOKIE['fileName'];
		}
		
		public function loadForQuery(&$credentials,&$direction,&$max,&$minGen,&$project,&$fileName)
		{
			$credentials["accessToken"] = $_COOKIE["accessToken"];
			$direction = $_COOKIE["direction"];
			$max = $_COOKIE["searchSize"];
			$minGen = $_COOKIE['minGen'];
			$project = $_COOKIE['project'];
			$fileName = $_COOKIE['fileName'];
		}
		
		/**
		* @pre: direction set
		* @pre: min Generation set
		*
		*/
		public function saveForPrequery($place,$searchDirection,$city,$county,$counrty,$project,$state,$max,$fileName,$giveOrTake,$minGen)
		{
			if (!isset($searchDirection))
			{
				throw new Exception("Search direction not set");
			}
				//Store place in a cookie
			setcookie('direction', $searchDirection, time() + 18000);
			setcookie('city', $city, time() + 18000);
			setcookie('county', $county, time() + 18000);
			setcookie('country', $counrty, time() + 18000);
			setcookie('project',$project,time() + 18000);
			setcookie('state', $state, time() + 18000);
			setcookie('searchSize', $max, time() + 18000);
			setcookie('fileName', $fileName, time() + 18000);
			setcookie('giveOrTake', $giveOrTake, time() + 18000);
			
			
			
			if (!isset($minGen) || !is_numeric($minGen))
			{
				echo $minGen;
				throw new Exception("Min Generation not set");
			}
			setcookie('minGen', $minGen, time() + 18000);
		}
		
		public function loadPlace()
		{
			$city = $_COOKIE['city'];
			$state = $_COOKIE['state'];
			$county = $_COOKIE['county'];
			$country = $_COOKIE['country'];
			$fullName = $_POST['fullName'];
			$queryPlace = new QueryPlace($city,$county,$state,$country,$fullName);
			return $queryPlace;
		}
	}


?>