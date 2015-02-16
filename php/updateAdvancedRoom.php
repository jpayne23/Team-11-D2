<!--
Retrieves a list of rooms that are associated with the building which the user has selected.
This is then written into the building content div.

Contribution Daniel
-->

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
	
	$building = $_REQUEST['building'];
	$sql = "SELECT room FROM `Room` WHERE buildingcode = '".$building."';";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
		
	$sql = "SELECT building from Building where buildingcode = (SELECT distinct buildingcode FROM `Room` WHERE buildingcode = '".$building."');";
	$res2 =& $db->query($sql);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage());
	}	
	
	$row = $res2->fetchRow();
	$buildingName = $row['building'];
	
	echo '<table id ="roomContent" class="contenttable">';
	while ($row = $res->fetchRow())
	{
		
		echo '<tr id ="'.$row["room"].'" class = "contentrows clickable" data-building="'.$buildingName.'" onclick="updateAdvancedRoomFacility(this.id);"> <td>'.$row["room"].'</td></tr>';
		
	}
	echo '</table>';

	
?>