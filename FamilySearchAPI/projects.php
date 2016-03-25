<?php
error_reporting(E_ERROR | E_PARSE);
	/**************************************************************************
	* $data
	*     $project
	*         $file
	*             $id
	*             $name
	*/
	$data = array();

	$users = getUsers();
	$dir    = "data/v2/";
	$files = scandir($dir);
	$htmloutput ="";
	

	
//						<div id="accordion">
//EndOfHTML;


//Pull Projects table

//for each project
    foreach($users as $key => $projectId)
	{
	//append h3 with project name
	$project = array();
	$project['name']= $key;
	$project['id']= $projectId;
	$project['files'] = array();
	//$projectName =$key;
	//$parentItem = <<<PARENT
	//<div class="group">
	//<h3>$projectName</h3>
	//<div>
//PARENT;
	
	//pull files with project id
	$files = getFiles($projectId);
	//for each file
	    foreach($files as $name =>$path)
		{
		//add button with file name
		$file = array();
		$file['fileName'] = $name;
		$file['filePath'] = $path;
		array_push($project['files'],$file);
		//$buttonName = $name;
		//$button = <<<BUTTON
	//	<form action="BYUFMR.php" method="post" >	
	//	<Button type="submit" name="fileName" value="$path">$buttonName</Button></br>
	//	<input type="hidden" name="step" value="analyze" />
	//	<input type="hidden" name="analysisFile" value="none" />
	//	</form>
//BUTTON;
		//$parentItem.=$button;
		}
	/*$parentItem.=<<<HTML
	<button type="button" class="btn btn-default btn-lg" >
	<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add File
	</button>
	</div>
	</div>
HTML;
	
	$htmloutput.=$parentItem;*/
	array_push($data,$project);
	}
	
	
	



echo json_encode($data);


function getUsers()
{
	$users = array();
	$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
		
		
	$result = pg_query($pgConnection, "SELECT projectName, projectId FROM Project;");
	if (!$result) {
	  //echo "An error occurred.\n";
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
	  //echo "An error occurred.\n";
	  exit;
	}

	while ($row = pg_fetch_row($result)) {
	$files[$row[0]]= $row[1];
	}
	return $files;
	
	
}

?>