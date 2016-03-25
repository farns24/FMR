<?
include("functions.php");
	$fileName = "Salt+Lake+City,Salt+Lake,Utah~birth~1855~2009-06-19.fmr";
	$htmloutput =<<<EndOfHTML
	</head>
	<body>
		<p class="title">
			BYU Family Migration Research
			<br />
			<sub class="header">
				Modeling Large-Scale Historical Migration Patterns Using Family History Records
			</sub>
		</p>
		<p class="userWelcome">Raw Data Display: $fileName</p>
EndOfHTML;
	echo $htmloutput;
	$xml = xmlpp($fileName, TRUE);
	echo "<pre>".$xml."</pre>";

?>