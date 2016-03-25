<?php
// Google Maps implementation

	// PARSE PLACE DATA SENT IN XML STRING AND DETERMINE THE LAT-LONG POINTS THAT ARE FURTHEST FROM EACH OTHER

	// To find the N/E and S/W boundary points we just have to find the most extreme and most not extreme Lats and Longs
	// N/E = The largest North/South # goes with the largest(-) East/West #
	// S/W = The smallest North/South # goes with the smallest(-) East/West #

function map($fileName)
{
echo $fileName."<pre>";
$fileName = "data/YearSubsets/ParentBirthLatLonFiles/".$fileName;
$fileContents = file_get_contents($fileName);
//echo $fileContents;
$contentArray = explode("\r\n", $fileContents);
//print_r($contentArray);
	$htmloutput = <<<EndOfHTML
		<script src="http://maps.google.com/maps?file=api&v=2&key=ABQIAAAAtlwEWH8tzFF5uyFqqSonbBTtpCvlTKD2A10-zFBzoxXUpYfkABS6zp_03QuiNt5kpju0uKuwsn2bjw"type="text/javascript">
		</script>
		<script type="text/javascript">
		var map = null;
		function initialize() {
			if (GBrowserIsCompatible())
			{
				map = new GMap2(document.getElementById("map_canvas"));
				map.setUIToDefault();

				var southWest = new GLatLng(25,-35);
				var northEast = new GLatLng(25,-35);	
				var bounds = new GLatLngBounds(southWest, northEast);
				map.setCenter(bounds.getCenter(), 3);
EndOfHTML;
//parse the CSV file and get the lat lon into the $htmloutput string as points
//SKIP $contentArray[0] because it is the header line
for($i=1;$i<count($contentArray)-1;$i++)
{
	$lineContent = explode(",", $contentArray[$i]);
	//print_r($lineContent);
	$htmloutput .= "var point".$i." = new GLatLng(".$lineContent[1].",".$lineContent[2].");";
	$htmloutput .= "map.addOverlay(new GMarker(point".$i."));";
}
	$htmloutput .= <<<EndOfHTML
			}
		}

		</script>
		</head>
		<body onload="initialize()" onunload="GUnload()" style="height:100%;margin:0px;">
		<div id="map_canvas" style="width:100%; height:100%; margin-left:auto;  margin-right:auto;"></div>
EndOfHTML;
echo "</pre>";
	echo $htmloutput;
}
?>