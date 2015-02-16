<?php
	/*
	A php script to load a list of parts for a given department from the database.
	We input the deptCode from the main page and output html code to create a select input element.
	This html is appended to a div in the main page. 
	Written by Prakash.
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
	
	$deptCode = $_REQUEST['deptCode'];
	
	$sql = "SELECT distinct Part FROM Module WHERE DeptCode = '".$deptCode."';";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="part" id="part" class="optionResize" title="Module Part" onchange="updateModCode();">';
	echo '<option id = "any">Any</option>';
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["part"].'">' . $row["part"] . '</option>';
	}
	echo '</select>';
?>