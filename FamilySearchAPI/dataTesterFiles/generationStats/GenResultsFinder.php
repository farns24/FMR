<?php
	/**
	* Finds number of locations possible, found, and unique for each generation.
	*/
	class GenResultsFinder {

		/**
		* Counts the number of results found, unique, and possible
		* 
		* @param $list - instance of StatList. Generation data.
		* @param $gen - number of generations removed from root
		* @param $analysisFileOutput - File output of the analysis
		* @param $htmlOut - html of statistics. 
		* @param $numFamilies - Number of familes in the base generation
		*/
		public function solve($list,$gen,&$analysisFileOutput, &$htmlOut,$numFamilies)
		{
			$genknow = $this->getGenCount($list->getTotal());
			$pow = pow(2, $gen);
			$max = $numFamilies*$pow;
			// Number of people, 1st gen
			$numPeople = count($list->getTotal());
			//echo "<br>Number of people in first generation ".count($list->getTotal());
			
			//Since we support forward searching, if the max possible is less than the actuals, we set max possible to be the actuals.
			if($max < $genKnow)
			{
				$max = $genKnow;
			}
			
			$htmlOut = str_replace("%".$gen."Genposs%", $max, $htmlOut);
			($max != 0)? $analysisFileOutput .= $max.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%".$gen."Genknow%", $genknow, $htmlOut);
			($genknow != 0)? $analysisFileOutput .= $genknow.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace("%".$gen."Genuniq%", $numPeople, $htmlOut);
			($numPeople != 0)? $analysisFileOutput .= $numPeople.":" : $analysisFileOutput .= "N/A:";
		
		}
		
		private function getGenCount($firstArray){
	
		$gen1know = 0;
			foreach($firstArray as $person)
			{
				$gen1know += $person->getNumChild();
			}
		return $gen1know;
		}
	}
?>