<?php

	abstract class ChrFinder
	{
		public function solve($firstList,$secondList,$thirdList,$fourthList,$analysisFileName,&$analysisFileOutput)
		{
			$this->calcGen($firstList,"r1",$analysisFileName,$analysisFileOutput,1);
			$this->calcGen($secondList,"r2",$analysisFileName,$analysisFileOutput,2);
			$this->calcGen($thirdList,"r3",$analysisFileName,$analysisFileOutput,3);
			$this->calcGen($fourthList,"r4",$analysisFileName,$analysisFileOutput,4);
		}
		
		private function calcGen($genList,$label,$analysisFileName,&$analysisFileOutput,$gen)
		{
			$chrroot = $this->calc($genList->getTotal(), $label, $analysisFileName);
			$this->addGenerationFlag($analysisFileOutput,$gen);
			$analysisFileOutput .= $chrroot;
		
		}
		
		abstract public function calc($genlist,$label,$analysisFileName);
		
		abstract public function addGenerationFlag(&$analysisFileOutput,$gen);
		
		public function findParentBirthPlaces($arrayName,&$chrCount)
		{
			$parentISO = array();
			foreach($arrayName as $person){
				$ISO = $person->getBirthPlace()->ISO();
				//echo $person->getId().", ".$ISO."<br />";
				// ($ISO == -999 || $ISO == null)? $parentISO[] =  "Unknown":  $parentISO[] = $ISO;
				if($ISO == -999) {
					$parentISO[] =  "Unknown(-999)";
					
				}
				elseif($ISO == null ) {
					$parentISO[] = "Unknown(Null Value)";
					
				}
				else {
					$parentISO[] = $ISO;
					
				}
			}
			// Next we need to aggregate duplicate values in the $parentISO array
			$chrCount = count($parentISO);

			$parentISO = @array_count_values($parentISO);
			
			// Associative array reverse sort
			arsort($parentISO);
			return $parentISO;
		}
		
		public function writeDataToFile($rFile,$chrCount,&$chrrootArray,&$parentISO)
		{
			$js = file_get_contents("data/v2/chr_map_data/$rFile.txt");
			$data = "data.addRows(".($chrCount).");";
			$chrroot = "";

			
			// Place pointer back at begining of array
			reset($parentISO);
			$count = 0;
			
			foreach($parentISO as $key => $val){
				$data .= "data.setValue($count, 0, '".$key."');";
				$data .= "data.setValue($count, 1, ".$val.");";
				$percentage = number_format(($val/$chrCount)*100, 2, '.', '');
				$chrrootArray["$key"] = $percentage;
				$count++;
			}
			$js = str_replace("%data%", $data, $js);
			file_put_contents("data/v2/chr_map_data/$rFile.js", $js);

		}
	}
?>