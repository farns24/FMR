<?php
require_once("ChrFinder.php");
class USChrCalculator extends ChrFinder
{
	// function to determine community heritage - US based(like Albion's Seed)
	public function calc($arrayName, $rFile, $analysisFileName)
	{
		// We need to calculate the chr here before replacing the text of the html file or updating the map javascript files
		// First, loop through the parents of the root and extract their birthplace iso
		$chrCount = 0;
		$parentISO = $this-> findParentBirthPlaces($arrayName,$chrCount);
		$chrrootArray = array();
		$this->writeDataToFile($rFile,$chrCount,$chrrootArray,$parentISO);
		// Now we open the .js file to write the dynamic data to it.
		// Read in the text of the js so that we can manipulate it

		//now we need to dynamically instantiate aggregation variables based on ISO code
		//make a list of group variables
		$groupedISO = array();
		$ISOs = array();

		//check which groupings are in the chrrootarray
		foreach($chrrootArray as $key => $val){
			//first strip off the ISO2 part of the ISO code so as to only have first order ISO codes
			$ISO = $key;
			//check to see if it's a US state - if it's not, lop off the ISO2 code
			if(strcmp(substr($ISO, 0, 2), "US") <> 0 && strcmp(substr($ISO, 0, 2), "Un") <> 0){
				$ISO = substr($key, 0, 2);
				//$ISO = $ISO;
			}
			//check the ISO code to see if it is in the array already
			if(in_array("$ISO", $ISOs)){
				//if it's in the array, add the percentage to the existing percentage
				$groupedISO["$ISO"] += $val;
			}
			else{
				//if it's not in the array already, create a spot for it and assign it a percentage
				$groupedISO["$ISO"] = $val;
				$ISOs[] = $ISO;
			}
		}
		
		$total = 0;
		foreach($groupedISO as $key => $val){
			//echo $key." ".$val."<br \>";
			$chrroot .= $key.":".$val."\r\n";
			$total = $total + $val;
		}
		$chrroot .=  "\r\nTOTAL ".$total."\r\n\r\n";
		
		//unset($chrroot);
		return $chrroot;
	}
	
	public function addGenerationFlag(&$analysisFileOutput,$gen)
	{
		if ($gen==1){
			$analysisFileOutput .= "\r\nFirst Generation\r\n";
		}
		else if ($gen==2){
			$analysisFileOutput .= "Second Generation\r\n";
		}
		else if ($gen==3){	
			$analysisFileOutput .= "Third Generation\r\n";
		}
		else if ($gen==4){
			$analysisFileOutput .= "Fourth Generation\r\n";
		}
	}
}
 ?>