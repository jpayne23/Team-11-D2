<?php
/*This script is called from script.js --> updateModCode() and its purpose is to
update the drop down list on the main page with the list of modules available
in the database.

Contributions by Prakash 
*/

	// Setting up connecting to the database
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	//input the department code and part
	$deptCode = $_REQUEST['deptCode'];
	$part = $_REQUEST['part'];
	
	if ($part == "any") //get all modcodes if part = any
		$sql = "SELECT ModCode, Title FROM Module WHERE DeptCode = '".$deptCode."';";	
	else //get all modcodes from the user chosen part
		$sql = "SELECT ModCode, Title FROM Module WHERE DeptCode = '".$deptCode."' and Part = '".$part."';";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage()); //give error message
	}
	//output html which is a list of module codes and names
	echo '<select class= "optionResize" name="modCode" title="Module Codes and Titles" id="modCodes" onclick="resetSelectedRooms();" style="width: 350px;">';
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["modcode"].'">' . $row["modcode"] . ' - '.$row["title"].'</option>';
	}
	echo '</select>';
?>