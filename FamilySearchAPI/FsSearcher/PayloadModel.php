<?php

class PayloadModel
{
	private $payload = null;



	public function updatePayload($counter){
	
		if (count($payload)==0)
		{
			$totalResults = $counter + $searchCount-1;
				echo "<div class='alert alert-info' role='alert'><b>Only $totalResults matches found</b></div>";
				showSearchHelp();
		}
		else
		{
			array_pop($payload);
		}
	}
}
?>