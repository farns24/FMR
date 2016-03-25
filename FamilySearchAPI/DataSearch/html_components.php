<?php

	function addSearchResultKey()
	{
		echo <<<HTML
		
		<div class="alert alert-info">
		<h2>Key</h2>
		<h3>Root Generation</h3>
		<img src="img/Root.png" height="20" width="20" />
		<h3>First Generation</h3>
		<img src="img/gen1.png" height="20" width="20" />
		<h3>Second Generation</h3>
		<img src="img/gen2.png" height="20" width="20" />
		<h3>Third Generation</h3>
		<img src="img/gen3.png" height="20" width="20" />
		<h3>Fourth Generation</h3>
		<img src="img/gen4.png" height="20" width="20" />
		<h3>Error</h3>
		<img src="img/err.png" height="20" width="20" />
		</div>
		<h2>Search Results</h2>
		
		
HTML;
			
		
	}
	
	function startLoader($fileName,$max) {
$maxBar = $max*6;
echo <<<_HTML
<dialog id="loading">
<h3>Please Wait as we Perform the search</h3>
<div class="progress">
  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax=$max style="width: 0%">
    <span class="sr-only">40% Complete (success)</span>
  </div>
</div>
</dialog>
_HTML;
}

function showEmptyProjectNameError()
{
	echo <<<HTML
	<head>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
</head>
	<div class="alert alert-warning" role="alert">
  <H2>Oops!</H2>
  <strong>The New Project needs a name.</strong>
  <H3>What to do?</h3>
  <p>To fix this</p>
  <ol>
  <li>Click the Back Button at the top of your browser</li>
  <li>Create a new Project or perform a search to be added to an existing project</li>
  </ol>
</div>
	
HTML;
	
}

function showSearchHelp()
{
	
	
	echo  <<<_html
	<div class="alert alert-warning" role="alert">
	<H2>What Happened Here?</H2>
	This web app will ask family search for a bunch of people who were born in a certain place at a certain time. 
	Sometimes, familysearch cannot find the number of people that you are looking for. There are many reasons why this might be the case.
	
	<H2>What can I do about it?</H2>
	<p>Most likely the information is not available. However, sometimes the problem is in the phrasing of the question.</p>
	
	<H3>What place do we want to search?</H3>
	<p>For example, a search for Los Angeles, Los Angeles, California is not the same search as Los Angeles, California. 
	The reason for this is that family search has different levels of specificity for locations. This App deals with four different types.
	<ol><li>Contries</li><li>States</li><li>Counties</li><li>Cities</li></ol>
	<p>Asking who was born in LA County and asking who was born in LA city are two different questions. 
	If the results are not being returned, Odds are good that the search is too specific</p>
	<h4>How do I fix it?</h4>
	<p>Consider modifying the search to include just the county and the state. Then search the county. 
	<img src ="/fmr/img/Remove County.png"/>
	
	Many people who put the data into familysearch only input the city and state. By making the search place less specific, more results can be turned up.</p> 
	</p>
	<H3>What time range do we want?</H3>
	<p>Another reason why a search may retern fewer results than expected is that the date range is too specific. When performing a search, it's good to ask:
	<ul><li>Do I need to know specificly the people born in 1830?</li>
	<li>Can I expand that range +-5 years to be 1825 - 1835</li></ul>
	<h4>How do I fix it?</h4>
	<p>In most cases, there will be enough data recorded on the server that expanding the ranges will not be necesary. 
	However, if there are not enough results being returned, consider expanding the range."</p>
	</div>
_html;

}

function showDisplayScreenHeader()
{
		$htmloutput =<<<EndOfHTML
	<link rel="stylesheet" type="text/css" href="CSS/BYUFMR.css" />
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
	<script type='text/javascript' src='js/jquery.js'></script>	
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
	<script type="text/javascript" src="image.js?a=1"></script>	
	<script type="text/javascript"></script>
	</head>
	<body>
		<div id="headerR"></div>
		<script type = "text/babel" src = "/fmr/react/header.js"></script>
		<p class="userWelcome">Raw Data Display: $fileName</p>
        <div class="container">
EndOfHTML;

	echo $htmloutput;
	
}

function showAnalizeScreenHeader(){
		$htmloutput = <<<EndOfHTML
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name='viewport' content='width=device-width, initial-scale=.5, user-scalable=no' />
		<title>BYUFMR</title>
		<link rel="stylesheet" type="text/css" href="CSS/BYUFMR.css" />
		<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
	<script type='text/javascript' src='js/jquery.js'></script>	
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
	<script type="text/javascript" src="image.js?a=1"></script>	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
	<script type="text/javascript"></script>
	</head>
	<body>
	
		<div id="headerR"></div>
		<script type = "text/babel" src = "/fmr/react/header.js"></script>
		<br /><br />
EndOfHTML;

echo $htmloutput;
	
	
}

function printHeader()
{
		$htmloutput =<<<_EndOfHTML3
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>BYUFMR</title>
			<link rel="stylesheet" type="text/css" href="CSS/BYUFMR.css" />
			<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
		<script type='text/javascript' src='js/jquery.js'></script>	
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
		<script type='text/javascript'>
			function removeLoading()
			{
				identity = document.getElementById('loading'); 
				identity.className = 'noDisplay'; 
			}
		
			function backToMenu(){
			history.go(-2);
			}
		
			function alertDone(){
			alert('search complete');
			}
		</script>
		<script type="text/javascript" src="image.js?a=1"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
        <script src="http://maps.google.com/maps?file=api&v=2&key=ABQIAAAAtlwEWH8tzFF5uyFqqSonbBTtpCvlTKD2A10-zFBzoxXUpYfkABS6zp_03QuiNt5kpju0uKuwsn2bjw"type="text/javascript"></script>
		

<script src="./gremlins/gremlins.min.js"></script>
		<script>
		//gremlins.createHorde().unleash();
		</script>
		
		</head>
		<body>
			
			<div id="headerR"></div>
			<script type = "text/babel" src = "/fmr/react/header.js"></script>
	
_EndOfHTML3;
		echo $htmloutput;
}

function showPrequeryHeader(){
	
		$htmloutput =<<<_EndOfHTML
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>BYUFMR</title>
		<link rel="stylesheet" type="text/css" href="CSS/BYUFMR.css" />
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous"/>

		<!-- Latest compiled and minified JavaScript -->
			<script type="text/javascript" src="js/jquery.js"></script>	
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
	<script type="text/javascript" src="image.js?a=1"></script>	
	<script type="text/javascript">
		
		function showSpinner() { 
        document.getElementById('loading').showModal();
		
		setInterval(function(){
		
			var valeur = 0;
			valeur =  $('.progress-bar').attr('aria-valuenow');
			var max = $('.progress-bar').attr('aria-valuemax');
			if (valeur< max - 1)
			{
			valeur++;
			}
			
			var percent = (valeur / max)*100;
			
			$('.progress-bar').css('width', percent+'%').attr('aria-valuenow', valeur);  
			
		},5000);
		
				
		return true;
		}
	</script>
	
	</head>
	<body>
	
	<div id="headerR"></div>
	<script type = "text/babel" src = "/fmr/react/header.js"></script>
		<p class="userWelcome">Choose a place:</p>
		
_EndOfHTML;
	echo $htmloutput;
	
}


?>