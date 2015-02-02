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
		$sql = "SELECT Title FROM Module WHERE DeptCode = '".$deptCode."';";
	else
		$sql = "SELECT Title FROM Module WHERE DeptCode = '".$deptCode."' and Part = '".$part."';";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="modCode" id="modTitles" class="larger" style="width: 150px;" onchange="setModCode();">';
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["title"].'">' . $row["title"] . '</option>';
	}
	echo '</select>';
?>