<?php

	abstract class GenerationResultsFinder {
	
	
		public function run(&$analysisFileOutput,$baseArray,&$htmlOut)
		{
			$genKnow = getGenCount($baseArray);
			
			// Number of people, 1st gen
			$numPeople = count($baseArray);
			echo "<br>Number of people in first generation ".count($baseArray);
			$htmlOut = str_replace($this->getPosibleFlag(), ($numFamilies*2), $htmlOut);
			(($numFamilies*2) != 0)? $analysisFileOutput .= ($numFamilies*2).":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace($this->getKnownFlag(), $genKnow, $htmlOut);
			($genKnow != 0)? $analysisFileOutput .= $genKnow.":" : $analysisFileOutput .= "N/A:";
			$htmlOut = str_replace($this->getUniqueFlag(), $numPeople, $htmlOut);
			($numPeople != 0)? $analysisFileOutput .= $numPeople.":" : $analysisFileOutput .= "N/A:";
		
		}
		
		abstract protected function getPosibleFlag();
		
		abstract protected function getKnownFlag();
		
		abstract protected function getUniqueFlag();
	}
	
	class Gen1ResultsFinder extends GenerationResultsFinder {
	
		protected function getPosibleFlag()
		{
			return "%1Genposs%";
		}
		
		protected function getKnownFlag()
		{
			return "%1Genknow%";
		}
		
		protected function getUniqueFlag()
		{
			return "%1Genuniq%";
		}
	}
	
	class Gen2ResultsFinder extends GenerationResultsFinder {
	
		protected function getPosibleFlag()
		{
			return "%2Genposs%";
		}
		
		protected function getKnownFlag()
		{
			return "%2Genknow%";
		}
		
		protected function getUniqueFlag()
		{
			return "%2Genuniq%";
		}
	}
	
	class Gen3ResultsFinder extends GenerationResultsFinder {
	
		protected function getPosibleFlag()
		{
			return "%3Genposs%";
		}
		
		protected function getKnownFlag()
		{
			return "%3Genknow%";
		}
		
		protected function getUniqueFlag()
		{
			return "%3Genuniq%";
		}
	}
	class Gen4ResultsFinder extends GenerationResultsFinder {
	
		protected function getPosibleFlag()
		{
			return "%4Genposs%";
		}
		
		protected function getKnownFlag()
		{
			return "%4Genknow%";
		}
		
		protected function getUniqueFlag()
		{
			return "%4Genuniq%";
		}
	}

?>