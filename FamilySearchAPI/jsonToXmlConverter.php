<?php
	require_once('getFSXMLResponse.php');
	require_once('appState.php');
	require_once('fmrFactory.php');

	function convertToXml($json,$credentials,$mainURL,$direction)
	{
	//echo $direction;
	//echo '<h1>json</h1>'.json_encode($json);
	//Build Writer
	$w=new XMLWriter();
	$w->openMemory();
	
	//Person tag
	$w->startElement("person");
    $w->writeAttribute("id", $json["id"]);
	
		$w->startElement("assertions");
			$w->startElement("names");
				$w->startElement("name");
					$w->startElement("value");
						$w->writeAttribute("type","Name");
							$w->startElement("forms");
								$w->startElement("form");
									$w->writeElement("fullText",$json["names"][0]["nameForms"][0]["fullText"]);
								
								$w->endElement();
							$w->endElement();
						$w->endElement();
					$w->endElement();
				$w->endElement();
			
		
	
			
				$w->startElement("genders");
					$w->startElement("gender");
						if ($json["gender"]["type"]=="http://gedcomx.org/Male")
						{
							$w->writeElement("value","Male");
						}
						else
						{
							$w->writeElement("value","Female");
						}
					$w->endElement();
				$w->endElement();
				
				$w->startElement("events");
					//birth
					//TODO Set christening to be birth
					$i = 0;
					$birthFound = false;
					$deathFound = false;
					foreach ($json["facts"] as $fact){
						if ($fact["type"]=="http://gedcomx.org/Birth")
						{
							$birthFound = true;
							writeEvent($w,"Birth",$i,$json,$credentials);
						}
						//Christening
						else if ($fact["type"]=="http://gedcomx.org/Christening" and !$birthFound)
						{
							writeEvent($w,"Birth",$i,$json,$credentials);
						}
						//Death
						else if ($fact["type"]=="http://gedcomx.org/Death")
						{
						    $deathFound = true;
							writeEvent($w,"Death",$i,$json,$credentials);
						}
						//Burial
						else if ($fact["type"]=="http://gedcomx.org/Burial" and !$deathFound)
						{
							writeEvent($w,"Death",$i,$json,$credentials);
						}
						
						
						$i++;
					}
					if (count($json["facts"])==1)
					{
						writeDummyEvent($w);
					}
					
				$w->endElement();
			$w->endElement();
			//Get Parents data
			if ($direction =='TRUE' or $direction=="Backward")//Backwards Search
				{
					loadParents($w,$credentials,$json,$mainURL);
				}
				else if ($direction =='FALSE' or $direction=="Forward")
				{
					loadChildren($w,$credentials,$json,$mainURL);
				}
				else
				{
				    echo "<h2>Direction undefined</h2>$direction";	
				}
				
		$w->endElement();
	$w->endElement();	
	 return $w->outputMemory(true);
	}
	
	/**
	*
	*
	*/
	function loadChildren($w,$credentials,$json,$mainURL)
	{
		$fsConnect = FmrFactory::createFsConnect();
		//form url for parents request
		//echo "<br>PersonResponse [".json_encode($json)."]<br>";
		if (isset($json) && isset($json['id']))
		{
			$childrenLinkUrl = $mainURL."platform/tree/persons/".$json['id']."/children";
			//$childrenLinkUrl = $json["links"]["children"]["href"];
			//echo "<br>parentUrl [".$parentLinkUrl."]<br>";
			//echo "<h1>Get Children</h1>".$childrenLinkUrl;
			$childrenStructure = $fsConnect->getFSXMLResponse($credentials, $childrenLinkUrl);
			
			//echo "<br>parentResponse [".json_encode($parentStructure)."]<br>";
			//echo "<br><br>";
			//echo json_encode($childrenStructure);
			//echo "<br><br>";
			if (isset($childrenStructure))
			{
			$w->startElement("children");
			foreach($childrenStructure["persons"] as $child)
			{
				$w->startElement("child");
				
				if ($child["gender"]["type"]=="http://gedcomx.org/Male")
				{

						$w->writeAttribute("gender","Male");
						$w->writeAttribute("id",$child["id"]);
				}		
				else 			
				{
			
				//mother
		
						$w->writeAttribute("gender","Female");
						$w->writeAttribute("id",$child["id"]);
				}
					$w->endElement();	
			}
				$w->endElement();
			}
			else
			{
				appState::$treeEnd++;
				
			}
		}
	}
	/**
	*Pulls the information on the parents, gathers the parent's pids, then returns the formatted xml 
	* portion dealling with parents.
	* @param $w - xml writer instance 
	* @param $credentials - saved information on the state of the program. holds information on Authorization Codes,
	* main url, and so forth.
	*
	*/
	function loadParents($w,$credentials,$json,$mainURL)
	{
		$fsConnect = FmrFactory::createFsConnect();
		//form url for parents request
		//echo "<br>PersonResponse [".json_encode($json)."]<br>";
		if (isset($json) && isset($json['id']))
		{
			$parentLinkUrl = $mainURL."platform/tree/persons/".$json['id']."/parents";//$json["links"]["parents"]["href"];
			//echo "<h1>parentUrl</h1> [".$parentLinkUrl."]";
			$parentStructure = $fsConnect->getFSXMLResponse($credentials, $parentLinkUrl);
			
			if (isset($parentStructure))
			{
			$w->startElement("parents");
				$w->startElement("couple");
				//father 
					$w->startElement("parent");
					if (isset($parentStructure["childAndParentsRelationships"][0]["father"]))
					{
						$w->writeAttribute("gender","Male");
						$w->writeAttribute("id",$parentStructure["childAndParentsRelationships"][0]["father"]["resourceId"]);
						
							
						$w->endElement();
					}
			
				//mother
				if (isset($parentStructure["childAndParentsRelationships"][0]["mother"]))
					{
					$w->startElement("parent");
						$w->writeAttribute("gender","Female");
						$w->writeAttribute("id",$parentStructure["childAndParentsRelationships"][0]["mother"]["resourceId"]);
						$w->endElement();
					$w->endElement();
				}
					else
					{
						appState::$treeEnd++;
					}
				$w->endElement();
			}
		}
	}
	/**
	* Writes life events to the xml structure
	*
	*/
	function writeEvent($w,$type,$index,$json,$credentials){
	
	$facts = $json["facts"][$index];
	$place = $facts["place"];
	$placeNorm = "";
	
	$w->startElement("event");
		$w->startElement("value");
			$w->writeAttribute("type",$type);
				$w->startElement("date");
					if (isset($facts["date"]["original"]))
					{
						$w->writeElement("original",$facts["date"]["original"]);
					}
					//$w->endElement();
					$w->writeElement("normalized",$facts["date"]["normalized"][0]["value"]);
					//$w->endElement();
					
					
					$w->endElement();
					//Birth place
					$w->startElement("place");
						try{
						if (isset($place))
						{
							if (isset($place["normalized"]))
							{
								foreach($place["normalized"] as $value)
								{
									if (!empty($value["value"]))
									{
										$placeNorm = $value["value"];
										break;
									}
								}
							}
							else
							{
								$placeNorm = $place["original"];
							}
						
							$pid = getPlaceId($placeNorm,$credentials);
							$w->writeElement("original",$placeNorm);
							$w->startElement("normalized");
							$w->writeAttribute("id",$pid);
						}
						} catch (Exception $e) {
							echo 'Caught exception: ',  $e->getMessage(), "\n";
							echo "<div class ='well'>";
							var_dump($place);
							echo"</div>";
						}
					$w->endElement();
			$w->endElement();
		$w->endElement();		
	$w->endElement();
	}
	
	function writeDummyEvent($w)
	{
		
	$w->startElement("event");
		$w->startElement("value");
			$w->writeAttribute("type","dummy");
				$w->startElement("date");

				
					
				$w->endElement();
		$w->endElement();		
	$w->endElement();
	}
	
	//pulls a useful id for the location
	function getPlaceId($placeName,$credentials)
	{
		return FmrFactory::getFacade()->getId($placeName,$credentials);
	}
	
?>
