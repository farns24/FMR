<?php
// PHP program to analyze and display multigenerational statistics from FMR files

//*******************//
// Set the PHP options  //
//******************//
// Display errors to the browser for debugging
error_reporting(E_ALL);

// Set the timeout limit to be infinite so that the queries can work until complete
set_time_limit (0);

//***********************************//
// Include the required geostatistics files  //
//***********************************//
require_once('PointGeoStatistics.php'); // Provides access to geostatistic functions

//*************************************************//
// Display the document DOCTYPE and head to the browser //
//*************************************************//
echo <<<EndOfHTML

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html  style="height:100%;margin:0px;" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="../CSS/BYUFMR.css" />
		<link rel="stylesheet" type="text/css" href="../CSS/table.css" />
		<title>BYUFMR Statistical Summary</title>
	</head>
EndOfHTML;

//****************************************//
// HTML Header - standard across BYUFMR site //
//***************************************//
echo <<<EndOfHTML
	<body>
		<p class="title">
			BYU Family Migration Research
			<br />
			<sub class="header">
				Modeling Large-Scale Historical Migration Patterns Using Family History Records
			</sub>
			<div id="logout">
				<form action="BYUFMR.php" method="post">
					<input type="hidden" name="step" value="logout" />
					<button type="submit" value="Logout">Logout</button>
				</form>
			</div>
		</p>
EndOfHTML;

//*********************************//
// Start of Statistical Summary Display  //
// Test for readability			      //
//*********************************//

// Basic summary stats
echo <<<EndOfHTML
	<br /><br />
	<div class="sectionTitle">Basic Stats From Included Files</div>
	<hr />
	<br /><br />
	<table id="basicStats" class="statsTable">
		<tr>
			<th class="nobg">Filename</th>
			<th># Families</th>
			<th>Total # People</th>
			<th>Avg. # PPF 1st Gen</th>
			<th>Avg. # PPF 2nd Gen</th>
			<th>Avg. # PPF 3rd Gen</th>
			<th>Avg. # PPF 4th Gen</th>
		</tr>
		<tr>
			<th class="spec">1549005~Ventura,Ventura,California~1890.fmr</th>
			<td>31</td>
			<td>400</td>
			<td>10</td>
			<td>6</td>
			<td>2</td>
			<td>1</td>
		</tr>
		<tr>
			<th class="specalt">1549005~Ventura,Ventura,California~1880.fmr</th>
			<td class="alt">12</td>
			<td class="alt">265</td>
			<td class="alt">8</td>
			<td class="alt">6</td>
			<td class="alt">4</td>
			<td class="alt">2</td>
		</tr>
	</table>
	<div class="tableTitle">* PPF stands for 'People Per Family'</div>
EndOfHTML;

// More detailed stats
// Mean Center by generation
echo <<<EndOfHTML
	<br /><br /><br />
	<div class="sectionTitle">More Detailed Stats</div>
	<hr />
	<br /><br />
	<div class="tableTitle">Mean Center of Distribution by Generation</div>
	<table id="meanCenter" class="statsTable">
		<tr>
			<th class="nobg">Root</th>
			<th>1st Gen</th>
			<th>2nd Gen</th>
			<th>3rd Gen</th>
			<th>4th Gen</th>
		</tr>
		<tr>
			<th class="spec">31.5645, -113.6542</th>
			<td>31.5645, -113.6542</td>
			<td>31.5645, -113.6542</td>
			<td>31.5645, -113.6542</td>
			<td>31.5645, -113.6542</td>
		</tr>
	</table>
EndOfHTML;

// Mean Center Relationship Matrix
echo <<<EndOfHTML
	<br /><br />
	<div class="tableTitle">Mean Center Distance Between Generations Matrix</div>
	<table id="meanCenter" class="statsTable">
		<tr>
			<th class="nobg"></th>
			<th>Root</th>
			<th>1st Gen</th>
			<th>2nd Gen</th>
			<th>3rd Gen</th>
			<th>4th Gen</th>
		</tr>
		<tr>
			<th class="spec">Root</th>
			<td>0</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
		</tr>
		<tr>
			<th class="specalt">1st Gen</th>
			<td class="alt">54</td>
			<td class="alt">0</td>
			<td class="alt">-</td>
			<td class="alt">-</td>
			<td class="alt">-</td>
		</tr>
		<tr>
			<th class="spec">2nd Gen</th>
			<td>105</td>
			<td>58</td>
			<td>0</td>
			<td>-</td>
			<td>-</td>
		</tr>
		<tr>
			<th class="specalt">3rd Gen</th>
			<td class="alt">258</td>
			<td class="alt">105</td>
			<td class="alt">68</td>
			<td class="alt">0</td>
			<td class="alt">-</td>
		</tr>
		<tr>
			<th class="spec">4th Gen</th>
			<td>598</td>
			<td>367</td>
			<td>212</td>
			<td>92</td>
			<td>0</td>
		</tr>
	</table>
EndOfHTML;

// Standard Distance by generation
echo <<<EndOfHTML
	<br /><br />
	<div class="tableTitle">Standard Distance of Distribution by Generation</div>
	<table id="meanCenter" class="statsTable">
		<tr>
			<th class="nobg">Root</th>
			<th>1st Gen</th>
			<th>2nd Gen</th>
			<th>3rd Gen</th>
			<th>4th Gen</th>
		</tr>
		<tr>
			<th class="spec">0</th>
			<td>49</td>
			<td>32</td>
			<td>85</td>
			<td>918</td>
		</tr>
	</table>
EndOfHTML;

// Standard Distance Relationship Matrix
echo <<<EndOfHTML
	<br /><br />
	<div class="tableTitle">Standard Distance Difference Between Generations Matrix</div>
	<table id="meanCenter" class="statsTable">
		<tr>
			<th class="nobg"></th>
			<th>Root</th>
			<th>1st Gen</th>
			<th>2nd Gen</th>
			<th>3rd Gen</th>
			<th>4th Gen</th>
		</tr>
		<tr>
			<th class="spec">Root</th>
			<td>0</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
		</tr>
		<tr>
			<th class="specalt">1st Gen</th>
			<td class="alt">54</td>
			<td class="alt">0</td>
			<td class="alt">-</td>
			<td class="alt">-</td>
			<td class="alt">-</td>
		</tr>
		<tr>
			<th class="spec">2nd Gen</th>
			<td>105</td>
			<td>58</td>
			<td>0</td>
			<td>-</td>
			<td>-</td>
		</tr>
		<tr>
			<th class="specalt">3rd Gen</th>
			<td class="alt">258</td>
			<td class="alt">105</td>
			<td class="alt">68</td>
			<td class="alt">0</td>
			<td class="alt">-</td>
		</tr>
		<tr>
			<th class="spec">4th Gen</th>
			<td>598</td>
			<td>367</td>
			<td>212</td>
			<td>92</td>
			<td>0</td>
		</tr>
	</table>
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
EndOfHTML;

//***********************************************//
// End of the HTML section - close the body and html tags //
//***********************************************//
echo <<<EndOfHTML
		</body>
	</html>
EndOfHTML;

?>