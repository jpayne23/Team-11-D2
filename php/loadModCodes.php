<?php
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
	$part = $_REQUEST['part'];
	
	if ($part == "any")
		$sql = "SELECT ModCode, Title FROM Module WHERE DeptCode = '".$deptCode."';";	
	else
		$sql = "SELECT ModCode, Title FROM Module WHERE DeptCode = '".$deptCode."' and Part = '".$part."';";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="modCode" id="modCodes" onclick="resetSelectedRooms();" style="width: 350px;">';
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["modcode"].'">' . $row["modcode"] . ' - '.$row["title"].'</option>';
	}
	echo '</select>';
?>