<?php
// BYUFMR HTML Menu and Output script
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: HTMLMenu.php
// Purpose: Creates and echos to the browser HTML menu and output for user interfacing

function HTMLMenu($JSONStatusCode, $json)
{
switch($JSONStatusCode)
{
  case('200'):
	$users = getUsers();
	$dir    = 'data/v2/analysisCSV/';
	$files = scandir($dir);
	// Print instructions for an  HTML menu to create a query
	$htmloutput =<<<EndOfHTML
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name='viewport' content='width=device-width, initial-scale=.5, user-scalable=no' />
		<title>BYUFMR</title>
		<link rel="stylesheet" type="text/css" href="CSS/BYUFMR.css" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.4/css/bootstrap-select.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.4/js/i18n/defaults-*.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.9.4/js/bootstrap-select.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script type="text/javascript" src="image.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
		<script type = "text/javascript">
		document.addEventListener("DOMContentLoaded", function() {
			var elements = document.getElementsByTagName("INPUT");
			for (var i = 0; i < elements.length; i++) {
			elements[i].oninvalid = function(e) {
            e.target.setCustomValidity("");
            if (!e.target.validity.valid) {
                e.target.setCustomValidity("This field cannot be left blank");
            }
			};
			elements[i].oninput = function(e) {
            e.target.setCustomValidity("");
        };
    }
})
		
		</script>
		<script src="./gremlins/gremlins.min.js"></script>
		<script>
		//gremlins.createHorde().unleash();
		</script>
		<script type="text/javascript">
		
		function showSpinner() { 
        document.getElementById('loadingAnalysis').showModal();
		
		
		
				
		return true;
		}
	</script>
		
	</head>
	<body>
	<header>
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
	<div class="userWelcome well">Welcome,
EndOfHTML;
	echo $htmloutput;
	echo ' '.$json['users'][0]['displayName'].'. <div><br/> </header>
	
					<dialog id="loadingAnalysis">
					<h3>Please Wait as we Perform the search</h3>
					
						<div class="progress">
						
							<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax=$max style="width: 100%">
								<span class="sr-only">100% Complete (success)</span>
							</div>
						</div>
					
					</dialog>	
					<div class="container-fluid">
					<div class="row">
						<div class="col-md-2">
							<ul class="list-group">
								<!--<li id="menuGettingStarted"class="list-group-item">Getting Started</li>-->
								<li id="menuSearch"class="list-group-item active">Search for Family Migration</li>
								<li id="menuAnalyze"class="list-group-item">Analyze past search</li>
								<li id="newProject" class="list-group-item">Create New Project</li>
								
							</ul>
						</div>
						<div id="basic" class="col-xs-12 col-md-10">
						<div id="newProjectWrapper">
						<h2>Create a new project </h2>
					<div class="alert alert-info hide_while_creating_projects hide_on_enter"><p>click <Strong>Add Project<Strong> again to exit</p></div>
					<div class="alert alert-info hide_while_creating_projects">
					<p>Note: Create the new project before performing the search</p>
					</div>
					
					
					<button id="createProjectBtn" type="button" class="btn btn-default btn-lg" >
					<form action="BYUFMR.php" method="post">
						<input type="hidden" name="step" value="add_project"
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Project
					</button>
					</form>
						<div id="createProjectDiv">
						</div>
						</div>
						<div id="searchWrapper">
						
						<img class="floatNormal" src="http://pintura.byu.edu/fmr/img/search.png" />
						<form action="BYUFMR.php" method="post">
						<div >
						<div class="alert alert-info" role="alert"><Strong>Cookies Must Be enabled</Strong>';
	$htmloutput =<<<EndOfHTML
	<p>If you don't know if your cookies are enabled, follow the links for help</p>
	<ul>
	<li>
	<a href="https://support.google.com/chrome/answer/95647?source=gsearch&hl=en">Chrome</a>
	</li>
	<li>
	<a href="https://support.mozilla.org/en-US/kb/enable-and-disable-cookies-website-preferences">Firefox</a>
	</li>
	<li>
	<a href="http://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies">Internet Explorer</a>
	</li>
	</ul>
	</div>
	
	<div class="alert alert-success">
		<h2>Instructions</h2>
		<div>
		Please use the menu below to create a query or correct, analyze, or map data.<br />
		Instructions for best query use:
		<ol>
			<li>Use the form to create a query 'sentence'. ex:
				Individuals who <strong>were born</strong> in <strong>Provo, Utah, Utah</strong>
				during <strong>1870</strong>.</li>
			<li>Not all the fields are required - only ones with asterisks(*) are required. Fill in as much information as you can.</li>
			<li>Double check spelling to avoid erroneous data aquisition.</li>
		</ol>
		</div></div>
							<h1>
								Data Collection
							</h1>
							<br /><br />
							<label>Individuals who:<span class="small">Select Event*</span></label>
							<select name="event" class="select">
								<option value="birth">were born</option>
								<option value="death">died</option>
							</select>
							<label class="section">in:</label>
							<label>
							
							<div id = "cityListR" ></div>
							<script type="text/babel" src="/fmr/react/cityList.js"></script>
							
							<div id = "countyListR"></div>
							<script type="text/babel" src="/fmr/react/countyList.js"></script>
							
							<div id = "stateListR"></div>
							<script type="text/babel" src="/fmr/react/stateList.js"></script>
							
							<div id = "countryListR"></div>
							<script type="text/babel" src="/fmr/react/countryList.js"></script>	
								
								
							<div class="input-group ">
							<span class="input-group-addon" >Search Size</span>
							<input class="form-control" type="text" name="searchSize" placeholder="*Size of search" /></div>
							</br>
							<label>during:
							
							<span class="small"></span></label>
							<div class="input-group ">
							<span class="input-group-addon" >Year</span>
							<input class="form-control" type="text" name="startyear" placeholder="*Year"  />
							</div>
							
							
							<span class="small"></span></label>
							<div class="input-group">
							<span class="input-group-addon" >Give or take (Years)</span>
							<input class="form-control" type="text" name="giveOrTake" placeholder="*Give Or take (Years)"  />
							</div>
							
							
							
							<div class="input-group ">
							<span class="input-group-addon" >Search Name</span>
							<input class="form-control" type="text" name="projectFile" placeholder="*Search Name" /></div>
							
							<div class="input-group"><span class="input-group-addon" >Minimum generations of ancestors</span>
							<div class="btn-toolbar" role="toolbar" data-toggle="buttons" aria-label="minGeneration">
								<label class="btn btn-primary active">
									<input type="radio" name="minGen" value="1" id="option1" checked>1
								</label>
								<label class="btn btn-primary">
									<input type="radio" name="minGen" value="2" id="option2">2
								</label>
								<label class="btn btn-primary">
									<input type="radio" name="minGen" value="3" id="option3">3
								</label>
								<label class="btn btn-primary">
									<input type="radio" name="minGen" value="4" id="option4">4
								</label>
							</div>
							</div>
							
							<h2>Place search results into project folder</h2>
							<p>Select from list</p>
							<select name="project">
EndOfHTML;
foreach($users as $key => $projectId)
	{
		$htmloutput .= '<option value="'.$projectId.'">'.$key.'</option>';
	}
$htmloutput.= <<<EndOfHTML
                            </select>
							</div>
					
					<!--<div id="createProjectDiv">
					</div>-->
					
					
							<label class="section">Include Ancestors:</label>	
								<div class="radio">
								<label ><input type="radio" name="searchancestors" value="TRUE" checked>Yes</label>
								<label ><input type="radio" name="searchancestors" value="FALSE">No</label>
								</div>
						
							
							
							<label class="section">Search Direction:</label>
							<div class="radio">
							<label><input type="radio"  name="searchdirection" value="Backward" checked>Backwards</label>
							</div>
							<div class="radio">
							<label><input type="radio" name="searchdirection" value="Forward">Forwards</label>
							</div>
                            <!--<div class="radio">
							<label><input type="radio" name="searchdirection" value="Single">Single Generation</label>
							</div>-->
							
							
							<input type="hidden" name="step" value="prequery" />
							<button type="submit" value="Submit Query">Submit Query</button>
							<p class="footer">Note: Queries often take a long time to work.  Please let the query finish and do not refresh the browser.</p>
							<p class="footer">Note 2: Queries include individuals born within +/- 5 years of specified year. It is recommended that you plan your searches accordingly. (Ex: Search years 1850 to 1900 incrementing each search by 10 years.)</p>
							<div class="spacer"></div>
						</form>
					</div>
					
					
					<div id="queryForm">
					<div class="well col-md-6">
					<img class="floatNormal" src="http://pintura.byu.edu/fmr/img/analyze.png" />
						<form action="BYUFMR.php" method="post" >
							<h1>
								Data Analysis
							</h1>
							<h2>Search by file name</h2>
							<p>Select from below</p>
							<br />
							<label>Analyze Data:<span class="small">Select File*</span></label>
					<div id="oldSearchR"></div>
					<script src="/fmr/react/oldSearches.js" type = "text/babel"></script>	
					
							<label>Store Analysis:<span class="small">Select Target*</span></label>
								<select name="analysisFile" class="select">
									<option value="none">None</option>
EndOfHTML;
// Populate the Select menu with the names of all files in the data directory

	if(count($files) != 0)
	{
		foreach($files as $fileName)
		{
			if($fileName != "." && $fileName != "..")
			{
				$htmloutput .= '<option value="'.$fileName.'">'.$fileName.'</option>';
			}
		}
	}
	$htmloutput .=<<<EndOfHTML
								</select>
							<input type="hidden" name="step" value="analyzeProxy" />
							<button type="submit" value="Analyze Data">Analyze Data</button>
							<div class="spacer"></div>
						</form>
						</div>
						<div class="well col-md-6">
						<h2>Search By Project</h2>
						<p>Scan for a project, then find a file</p>
	<!-- Sample Projects -->	
					<div id= "projectsR"/>
				
					
					</div>
					<script type="text/babel" src="/fmr/react/project.js"></script>
					</div>
					</div>	
					</div>
					</div>
				</div>
				</div>
			
			</div>	
	</p>
EndOfHTML;

	echo $htmloutput;
	break;
  case('400'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search recieved a bad request.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('401'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Your credentials were not accepted or your session timmed out.';
	echo "<br />Be sure to enter your Username and Password for FamilySearch.org as your login credentials and login again.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('403'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search will not allow you to access this information.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('404'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search can not find this information.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('409'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search can not perform this action.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('410'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search can not find this resource.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('430'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search recognizes this as a incorrect version of the object.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('431'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search recognizes this as an invalid developer key.';
	echo "<br />Please contact BYU Family Migration Research for assistance.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('500'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, Family Search has a server error.<br />';
	echo "<br />Please contact BYU Family Migration Research for assistance.";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
  case('503'):
	echo '<p class="title">BYU Family Migration Research<br />';
	echo '<sub class="header">Modeling Large-Scale Historical Migration Patterns Using Family History Records</sub><div id="logout"><form action="BYUFMR.php" method="post">';
	echo '<input type="hidden" name="step" value="logout" /><button type="submit" value="Logout">Logout</button></form></div></p>';
	echo '<p class="loginError"><img class="floatLeft" src="http://webmap.geog.byu.edu/fmr/img/warning.png" />Sorry, the Family Search system is currently down.';
	echo "<br />Please try again at a later time.<br />";
	echo '<br />Error Reference '.$json['statusCode'].'.</p>';
	break;
}
}
function getUsers()
{
	$users = array();
	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
		
		
	$result = pg_query($pgConnection, "SELECT projectName, projectId FROM Project;");
	if (!$result) {
	  echo "An error occurred.\n";
	  exit;
	}

	while ($row = pg_fetch_row($result)) {
	$users[$row[0]]= $row[1];
	}
	return $users;
}

function getFiles($projectId)
{
	
	$files = array();
	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
		
		
	$result = pg_query($pgConnection, "SELECT fileName,filePath FROM ProjectFile WHERE projectId=$projectId; ");
	if (!$result) {
	  echo "An error occurred.\n";
	  exit;
	}

	while ($row = pg_fetch_row($result)) {
	$files[$row[0]]= $row[1];
	}
	return $files;
	
	
}


?>