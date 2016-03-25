<?php

function preAccessToken(&$credentials)
{
		if (!isset($credentials['accessToken']))
	{
		if (isset($_COOKIE['accessToken']))
		{
			$credentials['accessToken'] = $_COOKIE['accessToken'];
			
		}
		else
		{
		echo <<<HTML
			<div class="alert alert-info" role="alert">
			<h2>
				Session expired
			</h2>
			<p>
				Oops! Looks like the session has expired. <a href="http://pintura.byu.edu/fmr" class = "btn btn-info">Click here to login</a>
			</p>
			</div>
		
HTML;
		exit("Missing credentials");
		}
	}	
	
}


?>