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
	
	$sql = "SELECT distinct Part FROM Module WHERE DeptCode = '".$deptCode."';";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="part" id="part" title="Module Part" class="optionResize" onchange="updateModCode();">';
	echo '<option id = "any">Any</option>';
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["part"].'">' . $row["part"] . '</option>';
	}
	echo '</select>';
?>