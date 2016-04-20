<?php

class HeaderShower{
	/**
	* Command Patturn used to show header
	*
	* Note: There is an easter egg hidden here. When you click on the 'family history' text of the sub header, it opens a quote on family history
	*/
	public function show()
	{
		echo <<<HTML
	<p class="title">
			BYU Family Migration Research
			<br />
			<sub class="header">
				Modeling Large-Scale Historical Migration Patterns Using <a href="/fmr/img/quote-bednar-temple.jpg" target="_blank" style="color:white; background-color: none;text-decoration:none;">Family History</a> Records
			</sub>
			<div id="logout">
				<form action="BYUFMR.php" method="post">
					<input type="hidden" name="step" value="logout" />
					<button type="submit" value="Logout">Logout</button>
				</form>
			</div>
		</p>	
HTML;

	}
}
?>