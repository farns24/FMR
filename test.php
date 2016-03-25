<?php
require_once('testing/testCase.php');
require_once('FamilySearchAPI/DAO.php');
require_once('FamilySearchAPI/placeUtils.php');
require_once('FamilySearchAPI/dataTester.php'); 
require_once('FamilySearchAPI/classPlace.php'); 


 //DAO Testing
	$testArray = array();
	$dao = new DAO();
	
	$testCase = $dao->testDao(getNewTestCase());
    $testCase2 = $dao->testDaoNullISO(getNewTestCase());
	array_push($testArray,$testCase,$testCase2);
   
//Name Matcher tests	
	$testCase3 = testMatch(getNewTestCase());
	array_push($testArray,$testCase3);

//Set Children test
	$testCase4 = testSetChildren(getNewTestCase());
	array_push($testArray,$testCase4);
	
//Test Place Classes (Using dependency injection	
	$testPlace = testPlace(getNewTestCase());
	array_push($testArray,$testPlace);
	
	
   echo json_encode($testArray);

?>