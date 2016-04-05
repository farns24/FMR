<?php

require_once("MeanDistanceFinder.php");
/**
*
* Refactored out of Datatester to remove code duplication
*/
	class LeanMeanDistanceFinder extends MeanDistanceFinder
	{
		
		public function solveMeanDistances($type,$rootList,$firstList,$secondList,$thirdList,&$htmlOut,&$analysisFileOutput)
		{
		$ex = "";
		$searchListR = null;
		$searchList1 = null;
		$searchList2 = null;
		$searchList3 = null;
			if ($type == "MALE")
			{
				$ex = "m";
				$searchListR = $rootList->getMales();
				$searchList1 = $firstList->getMales();
				$searchList2 = $secondList->getMales();
				$searchList3 = $thirdList->getMales();
			}
			else if ($type == "FEMALE")
			{
				$ex = "f";
				$searchListR = $rootList->getFemales();
				$searchList1 = $firstList->getFemales();
				$searchList2 = $secondList->getFemales();
				$searchList3 = $thirdList->getFemales();
			}
			else
			{
				$searchListR = $rootList->getTotal();
				$searchList1 = $firstList->getTotal();
				$searchList2 = $secondList->getTotal();
				$searchList3 = $thirdList->getTotal();
			}
			// Mdist, root - 1 (Parents)
			$this->calculate("%mdDistRoot1".$ex."%",1,$searchListR,$htmlOut,$analysisFileOutput);
			// Mdist, root - 2 (GParents)
			$this->calculate("%mdDistRoot2".$ex."%",2,$searchListR,$htmlOut,$analysisFileOutput);
			// Mdist, root - 3 (GGParents)
			$this->calculate("%mdDistRoot3".$ex."%",3,$searchListR,$htmlOut,$analysisFileOutput);
			// Mdist, root - 4 (GGGParents)
			$this->calculate("%mdDistRoot4".$ex."%",4,$searchListR,$htmlOut,$analysisFileOutput);
			// Mdist, first - 1 (Parents)
			$this->calculate("%mdDist12".$ex."%",1,$searchList1,$htmlOut,$analysisFileOutput);
			// Mdist, first - 2  (GParents)
			$this->calculate("%mdDist13".$ex."%",2,$searchList1,$htmlOut,$analysisFileOutput);
			// Mdist, first - 3 (GGParents)
			$this->calculate("%mdDist14".$ex."%",3,$searchList1,$htmlOut,$analysisFileOutput);
			// Mdist, second - 1 (Parents)
			$this->calculate("%mdDist23".$ex."%",1,$searchList2,$htmlOut,$analysisFileOutput);
			// Mdist, Second - 2 (GParents)
			$this->calculate("%mdDist24".$ex."%",2,$searchList2,$htmlOut,$analysisFileOutput);
			// Mdist, Third - 1 (Parents)
			$this->calculate("%mdDist34".$ex."%",1,$searchList3,$htmlOut,$analysisFileOutput);
		
		}
	
	
	
	}

?>