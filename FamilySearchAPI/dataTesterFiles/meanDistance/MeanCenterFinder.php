<?php
	include("../../PointGeoStatistics.php");
	
	class MeanCenterFinder
	{
		
		public function solve(&$htmlOut,&$analysisFileOutput,$rootList)
		{
			$mcRoot = meanCenterPersonArray($rootList);
			$rootLatLonArray = $mcRoot;
			$htmlOut = str_replace("%mcRoot%", (number_format($mcRoot[0], 4, '.', '').", ".number_format($mcRoot[1], 4, '.', '')), $htmlOut);
			$analysisFileOutput .= number_format($mcRoot[0], 4, '.', '').":".number_format($mcRoot[1], 4, '.', '').":";
		
		}
	
	
	}


?>