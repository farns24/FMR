<?php

	class GenResultsFinder {

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