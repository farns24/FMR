<?php

	class GenResultsFinder {

		public function solve($list,$gen,&$analysisFileOutput, &$htmlOut,$numFamilies)
		{
			$genknow = $this->getGenCount($list->getTotal());
			$pow = pow(2, $gen);
			// Number of people, 1st gen
			$numPeople = count($list->getTotal());
			//echo "<br>Number of people in first generation ".count($list->getTotal());
			$htmlOut = str_replace("%".$gen."Genposs%", ($numFamilies*$pow), $htmlOut);
			(($numFamilies*$pow) != 0)? $analysisFileOutput .= ($numFamilies*$pow).":" : $analysisFileOutput .= "N/A:";
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