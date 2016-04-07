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
	}
?>