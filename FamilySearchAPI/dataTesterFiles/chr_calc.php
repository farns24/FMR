<?php
// function to determine Albion's Seed info for Great Britain to America
require_once("ChrFinder.php");
class ChrCalculator extends ChrFinder
{
	public function calc($arrayName, $rFile, $analysisFileName)
	{
		// We need to calculate the chr here before replacing the text of the html file or updating the map javascript files
		// First, loop through the parents of the root and extract their birthplace iso
		$parentISO = array();
		foreach($arrayName as $person)
		{
			$ISO = $person->getBirthPlace()->ISO();
			//echo $person->getId().", ".$ISO."<br />";
			// ($ISO == -999 || $ISO == null)? $parentISO[] =  "Unknown":  $parentISO[] = $ISO;
			if($ISO == -999) {
				$parentISO[] =  "Unknown";
				//echo "Unknown, ".$person->getId()."<br />";
			}
			elseif($ISO == null ) {
				$parentISO[] = "Known unknown";
				//echo "Known problem, ".$person->getId()."<br />";
			}
			else {
				$parentISO[] = $ISO;
				//echo "$ISO, ".$person->getId()."<br />";
			}
		}
		// Next we need to aggregate duplicate values in the $parentISO array
		$chrCount = count($parentISO);

		$parentISO = @array_count_values($parentISO);
		
		// Associative array reverse sort
		arsort($parentISO);
		
		
		// Now we open the .js file to write the dynamic data to it.
		// Read in the text of the js so that we can manipulate it
		$js = file_get_contents("data/v2/chr_map_data/$rFile.txt");
		
		$data = "data.addRows(".($chrCount).");";
		$chrroot = "";
		$chrrootArray = array();
		// Place pointer back at begining of array
		reset($parentISO);
		$count = 0;
		foreach($parentISO as $key => $val)
		{
			$data .= "data.setValue($count, 0, '".$key."');";
			$data .= "data.setValue($count, 1, ".$val.");";
			$percentage = number_format(($val/$chrCount)*100, 2, '.', '');
			$chrrootArray["$key"] = $percentage;
			$count++;
		}
		$js = str_replace("%data%", $data, $js);
		file_put_contents("data/v2/chr_map_data/$rFile.js", $js);
		// Aggregate the Albion's Seed places: EA,SoE,MdlE,BrdrE,[Mass, Virg, DE+PA+NJ, backcountry],OtherClny,Unknwn
		$EA = 0;
		$SoE = 0;
		$MdlE = 0;
		$BrdrE = 0;
		$Ire = 0;
		$ENotSpec = 0;
		$Virg = 0;
		$DEPANJ = 0;
		$Bckcntry = 0;
		$MA = 0;
		$PA = 0;
		$OtherClny = 0;
		$Unknown = 0;
		$knownUnknown = 0;
		$other = 0;
		foreach($chrrootArray as $key => $val)
		{
			echo "<Br>Case ".$key;
			switch($key){
				case 'GB-CHS':
				case 'GB-CHA':
				case 'GB-GSY':
				case 'GB-JSY':
				case 'GB-CAM':
				case 'GB-ESS':
				case 'GB-LIN':
				case 'GB-NFK':
				case 'GB-PTE':
				case 'GB-SOS':
				case 'GB-KEN':
				case 'GB-THR':
				case 'GB-MDW':
				case 'GB-HRT':
				case 'GB-BDF':
				case 'GB-LUT':
				case 'GB-SFK': $EA += $val;
				break;
				case 'GB-ENG': $ENotSpec += $val;
				break;
				case 'GB-BDG':
				case 'GB-BNE':
				case 'GB-BAS':
				case 'GB-BDF':
				case 'GB-BEX':
				case 'GB-BIR':
				case 'GB-BMH':
				case 'GB-BRC':
				case 'GB-BEN':
				case 'GB-BNH':
				case 'GB-BST':
				case 'GB-BRY':
				case 'GB-BKM':
				case 'GB-CMD':
				case 'GB-CON':
				case 'GB-COV':
				case 'GB-CRY':
				case 'GB-DEV':
				case 'GB-DOR':
				case 'GB-DUD':
				case 'GB-EAL':
				case 'GB-ESX':
				case 'GB-ENF':
				case 'GB-GLS':
				case 'GB-GRE':
				case 'GB-HCK':
				case 'GB-HMF':
				case 'GB-HAM':
				case 'GB-HRY':
				case 'GB-HRW':
				case 'GB-HAV':
				case 'GB-HEF':
				case 'GB-HIL':
				case 'GB-HNS':
				case 'GB-IOW':
				case 'GB-IOS':
				case 'GB-ISL':
				case 'GB-KEC':
				case 'GB-KTT':
				case 'GB-LBH':
				case 'GB-LCE':
				case 'GB-LEC':
				case 'GB-LEW':
				case 'GB-LND':
				case 'GB-MRT':
				case 'GB-MIK':
				case 'GB-NWM':
				case 'GB-NSM':
				case 'GB-NTH':
				case 'GB-OXF':
				case 'GB-PLY':
				case 'GB-POL':
				case 'GB-POR':
				case 'GB-RDG':
				case 'GB-RDB':
				case 'GB-RIC':
				case 'GB-RUT':
				case 'GB-SAW':
				case 'GB-SHR':
				case 'GB-SLG':
				case 'GB-SOL':
				case 'GB-SOM':
				case 'GB-SGC':
				case 'GB-STH':
				case 'GB-SWK':
				case 'GB-STS':
				case 'GB-SRY':
				case 'GB-STN':
				case 'GB-SWD':
				case 'GB-TFW':
				case 'GB-TOB':
				case 'GB-TWH':
				case 'GB-WLL':
				case 'GB-WFT':
				case 'GB-WND':
				case 'GB-WAR':
				case 'GB-WBK':
				case 'GB-WSX':
				case 'GB-WSM':
				case 'GB-WIL':
				case 'GB-WNM':
				case 'GB-WOK':
				case 'GB-WLV':
				case 'GB-WOR':
				case 'GB-WLS':
				case 'GB-BGW':
				case 'GB-BGE':
				case 'GB-POG':
				case 'GB-CAY':
				case 'GB-CAF':
				case 'GB-CRF':
				case 'GB-CRD':
				case 'GB-CMN':
				case 'GB-GFY':
				case 'GB-CGN':
				case 'GB-MTY':
				case 'GB-MTU':
				case 'GB-MON':
				case 'GB-FYN':
				case 'GB-NTL':
				case 'GB-CTL':
				case 'GB-NWP':
				case 'GB-CNW':
				case 'GB-PEM':
				case 'CB-BNF':
				case 'GB-POW':
				case 'GB-RCT':
				case 'GB-SWA':
				case 'GB-ATA':
				case 'GB-TOF':
				case 'GB-VGL':
				case 'GB-BMG': $SoE += $val;
				break;
				case 'GB-BNS':
				case 'GB-BBD':
				case 'GB-BPL':
				case 'GB-BOL':
				case 'GB-BRD':
				case 'GB-BUR':
				case 'GB-CLD':
				case 'GB-DER':
				case 'GB-DBY':
				case 'GB-DNC':
				case 'GB-ERY':
				case 'GB-HAL':
				case 'GB-KHL':
				case 'GB-KIR':
				case 'GB-KWL':
				case 'GB-LAN':
				case 'GB-LDS':
				case 'GB-LIV':
				case 'GB-MAN':
				case 'GB-NEL':
				case 'GB-NLN':
				case 'GB-NYK':
				case 'GB-NGM':
				case 'GB-NTT':
				case 'GB-OLD':
				case 'GB-RCH':
				case 'GB-ROT':
				case 'GB-SHN':
				case 'GB-SLF':
				case 'GB-SFT':
				case 'GB-SHF':
				case 'GB-SKP':
				case 'GB-STE':
				case 'GB-TAM':
				case 'GB-TRF':
				case 'GB-WKF':
				case 'GB-WRT':
				case 'GB-WGN':
				case 'GB-WRL':
				case 'GB-YOR':
				case 'GB-CWY':
				case 'GB-DEN':
				case 'GB-DDB':
				case 'GB-FLN':
				case 'GB-FFL':
				case 'GB-GWN':
				case 'GB-AGY':
				case 'GB-YNM':
				case 'GB-WRX':
				case 'GB-WRC': $MdlE += $val;
				break;
				case 'IM':
				case 'GB-CMA':
				case 'GB-DAL':
				case 'GB-DUR':
				case 'GB-GAT':
				case 'GB-HPL':
				case 'GB-MDB':
				case 'GB-NET':
				case 'GB-NTY':
				case 'GB-NBL':
				case 'GB-RCC':
				case 'GB-STY':
				case 'GB-STT':
				case 'GB-SND':
				case 'GB-IOM':
				case 'GB-NIR':
				case 'GB-ANT':
				case 'GB-ARD':
				case 'GB-ARM':
				case 'GB-BLA':
				case 'GB-BLY':
				case 'GB-BNB':
				case 'GB-BFS':
				case 'GB-CKF':
				case 'GB-CSR':
				case 'GB-CLR':
				case 'GB-CKT':
				case 'GB-CGV':
				case 'GB-DRY':
				case 'GB-DOW':
				case 'GB-DGN':
				case 'GB-FER':
				case 'GB-LRN':
				case 'GB-LMV':
				case 'GB-LSB':
				case 'GB-MFT':
				case 'GB-MYL':
				case 'GB-NYM':
				case 'GB-NTA':
				case 'GB-NDN':
				case 'GB-OMH':
				case 'GB-STB':
				case 'GB-SCT':
				case 'GB-ABE':
				case 'GB-ABD':
				case 'GB-ANS':
				case 'GB-AGB':
				case 'GB-CLK':
				case 'GB-DGY':
				case 'GB-DND':
				case 'GB-EAY':
				case 'GB-EDU':
				case 'GB-ELN':
				case 'GB-ERW':
				case 'GB-EDH':
				case 'GB-ELS':
				case 'GB-FAL':
				case 'GB-FIF':
				case 'GB-GLG':
				case 'GB-HLD':
				case 'GB-IVC':
				case 'GB-MLN':
				case 'GB-MRY':
				case 'GB-NAY':
				case 'GB-NLK':
				case 'GB-ORK':
				case 'GB-PKN':
				case 'GB-RFW':
				case 'GB-SCB':
				case 'GB-ZET':
				case 'GB-SAY':
				case 'GB-SLK':
				case 'GB-STG':
				case 'GB-WDU':
				case 'GB-WLN': $BrdrE += $val;
				break;
				case 'IE':
				case 'IE-CP':
				case 'IE-G':
				case 'IE-LM':
				case 'IE-MO':
				case 'IE-RN':
				case 'IE-SO':
				case 'IE-LP':
				case 'IE-CW':
				case 'IE-D':
				case 'IE-KE':
				case 'IE-KK':
				case 'IE-LS':
				case 'IE-LD':
				case 'IE-LH':
				case 'IE-MH':
				case 'IE-OY':
				case 'IE-WH':
				case 'IE-WX':
				case 'IE-WW':
				case 'IE-M':
				case 'IE-UP':
				case 'IE-CN':
				case 'IE-DL':
				case 'IE-MN': $Ire += $val;
				break;
				case 'US-GA':
				case 'US-NH':
				case 'US-ME':
				case 'US-NY':
				case 'US-MD':
				case 'US-TN':
				case 'US-NC':
				case 'US-NY':
				case 'US-SC': $Bckcntry += $val;
				break;
				case 'US-CT':
				case 'US-RI':
				case 'US-VT': $OtherClny += $val;
				break;
				case 'US-DE':
				case 'US-NJ': $DEPANJ += $val;
				break;
				case 'US-MA': $MA +=$val;
				break;
				case 'US-VA': $Virg += $val;
				break;
				case 'US-PA': $PA += $val;
				break;
				case 'Unknown': $Unknown += $val;
				break;
				case 'Known unknown': $knownUnknown += $val;
				break;
				default: $other += $val;
			}
		}
		// EA,SoE,MdlE,BrdrE,[Mass, Virg, DE+PA+NJ, backcountry],OtherClny,Unknwn
		switch($analysisFileName)
		{
			case "Midlands to Delaware.txt": $OtherClny = $OtherClny + $Virg + $MA + $Bckcntry;
				$DEPANJ = $PA + $DEPANJ;
				$chrroot = $EA.":".$SoE.":".$MdlE.":".$BrdrE.":".$ENotSpec.":".$Ire.":".$DEPANJ.":".$OtherClny.":".$Unknown.":".$knownUnknown.":".$other.":";
				break;
			case "SOE to Virginia.txt": $OtherClny = $OtherClny + $PA + $DEPANJ + $MA + $Bckcntry;
				$chrroot = $EA.":".$SoE.":".$MdlE.":".$BrdrE.":".$ENotSpec.":".$Ire.":".$Virg.":".$OtherClny.":".$Unknown.":".$knownUnknown.":".$other.":";
				break;
			case "BrdrE to Backcountry.txt":  $OtherClny = $OtherClny + $DEPANJ;
				$Bckcntry = $Bckcntry + $MA + $PA + $Virg;
				$chrroot = $EA.":".$SoE.":".$MdlE.":".$BrdrE.":".$ENotSpec.":".$Ire.":".$Bckcntry.":".$OtherClny.":".$Unknown.":".$knownUnknown.":".$other.":";
				break;
			case "East Anglia to Massachusetts.txt":  $OtherClny = $OtherClny + $PA + $DEPANJ + $Bckcntry + $Virg;
				$chrroot = $EA.":".$SoE.":".$MdlE.":".$BrdrE.":".$ENotSpec.":".$Ire.":".$MA.":".$OtherClny.":".$Unknown.":".$knownUnknown.":".$other.":";
				break;
			default:
				echo '<div class="col-md-3 col-sm-6">';
				echo "EA ".$EA."<br \>";
				echo "SoE ".$SoE."<br \>";
				echo "MdlE ".$MdlE."<br \>";
				echo "BrdrE ".$BrdrE."<br \>";
				echo "Ire ".$Ire."<br \>";			
				echo "Virg ".$Virg."<br \>";
				echo "DEPANJ ".$DEPANJ."<br \>";
				echo "Bckcntry ".$Bckcntry."<br \>";
				echo "MA ".$MA."<br \>";
				echo "PA ".$PA."<br \>";
				echo "OtherClny ".$OtherClny."<br \>";
				echo "Unknown ".$Unknown."<br \>";
				echo "other ".$other."<br \>";
				$total = $EA+$SoE+$MdlE+$BrdrE+$Ire+$Virg+$DEPANJ+$Bckcntry+$MA+$PA+$OtherClny+$Unknown+$other;
				echo "TOTAL ".$total."<br \><br \>";
				echo "</div>";
		}
		//unset($chrroot);
		return $chrroot;
	}
	
	public function addGenerationFlag(&$analysisFileOutput,$gen)
	{
		//Do Nothing
	}
	
}

 ?>