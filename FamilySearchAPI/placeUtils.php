<?php


function namesMatch($name, $pName)
{
	//Strip off white space
	$pName = trim($pName);
	$name = trim($name);
	
	//
	if (!isset($pName)||!isset($name))
	{
		return true;
	}
	
	if ($pName==""|| $name=="")
	{
		
		return true;
	}
	else if (strpos($name,$pName)!==false || strpos($pName,$name)!==false)
	{
		return true;
	}
	
	return false;
	
}

function testMatch($test)
{
	$test["name"] = "Name Matcher test";
	
	//Test one
	if (namesMatch("Provo","Provo,Utah, Utah"))
	{
		array_push($test["info"],"Passed Provo => Provo, Utah, Utah");
	}
	else
	{
		array_push($test["info"],"Failed Provo => Provo, Utah, Utah");
	}
	
	//Test one
	if (namesMatch("Provo,Utah, Utah","Provo"))
	{
		array_push($test["info"],"Passed Provo, Utah, Utah => Provo");
	}
	else
	{
		array_push($test["info"],"Failed Provo, Utah, Utah => Provo");
	}
	
	//Beijing test
	if (namesMatch("Provo,Utah, Utah","Beijing"))
	{
		array_push($test["info"],"Failed Provo, Utah, Utah !=> Beijing");
	}
	else
	{
		array_push($test["info"],"Passed Provo, Utah, Utah !=> Beijing");
	}
	
	//Whitespace test
	if (namesMatch("Provo,Utah, Utah"," Provo"))
	{
		array_push($test["info"],"Passed ' Provo' =>'Provo Utah, Utah'");
	}
	else
	{
		array_push($test["info"],"Failed ' Provo' =>'Provo Utah, Utah'");
	}
	
	
	
	return $test;
}


?>