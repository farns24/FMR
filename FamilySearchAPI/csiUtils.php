<?php
//############################################################################//		
// START CSI Calculations
		// Community Stability Index helper functions
		function csi($arrayName, $parentLevel, $rootArray,$credentials)
		{
			$lt25mi = 0;
			$gt25mi = 0;
			global $valueArray;
			
			foreach($arrayName as $person)
			{
				//echo "<br> attempt to update birth place (dataTester.php) <br>RootArray 0 = ".$rootArray[0]."<br>RootArray 1 = ".$rootArray[1]."<br>";
				//var_dump($rootArray);
				
				$person->setBirthPlace(new Place(2, -999, $rootArray[0], $rootArray[1],$credentials,""));
				csiDist($person, $person, 0, $parentLevel);
			}
			if (isset($dist) && sizeof($dist)>0)
			{
				foreach($valueArray as $dist)
				{
					($dist <= 25)? $lt25mi++ : $gt25mi++;
				}
			}
			
			if ($gt25mi != 0 && $lt25mi != 0)
			{
				$ratio = $lt25mi / $gt25mi;
				$ratioString = "$lt25mi/$gt25mi";
				$lt25mi = 0;
				$gt25mi = 0;
				$valueArray = NULL;
				return $ratioString;
			}
			elseif($lt25mi != 0)
			{
				$valueArray = NULL;
				return "$lt25mi/0";
			}
			elseif($gt25mi != 0)
			{
				$valueArray = NULL;
				return "0/$gt25mi";
			}
			else
			{
				$valueArray = NULL;
				return "N/A";
			}
		}
		
		function csiDist($currentMember, $targetMember, $currentGen, $targetGen)
		{
			global $valueArray;
			// Check return/fail cases
			if ($currentGen > $targetGen) return;
			
			if ($currentGen == $targetGen)
			{
				$value = distVincenty($targetMember->getBirthPlace()->Lat(),
										$targetMember->getBirthPlace()->Lon(),
										$currentMember->getBirthPlace()->Lat(),
										$currentMember->getBirthPlace()->Lon());
				if($value != null) {
					$valueArray[] = $value;
				}
			}
			
			if ($currentGen < $targetGen)
			{
				if ($currentMember->getFather() != null)
				{
					csiDist($currentMember->getFather(), $targetMember, ($currentGen + 1), $targetGen);
				}
				if ($currentMember->getMother() != null)
				{
					csiDist($currentMember->getMother(), $targetMember, ($currentGen + 1), $targetGen);
				}
			}
			
			return;
		}
// END CSI Calculations
 ?>