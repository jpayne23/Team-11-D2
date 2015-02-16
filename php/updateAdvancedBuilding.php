<!--
Retrieves a list of buildings that are associated with the park that the user has selected.
This is then written to the park content div.

Contribution from Bhavnit, Daniel
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
	
	$park = $_REQUEST['park'];
	$sql = "SELECT buildingcode, building FROM `Building` WHERE park = '$park'";			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo '<table id="buildingContent" class="contenttable">';
	while ($row = $res->fetchRow())
	{
		if(($row['buildingcode'] == 'EAST') || ($row['buildingcode'] == 'WEST') || ($row['buildingcode'] == 'CENTRAL'))
			continue;
		
		echo '<tr class = "contentrows clickable" id ="'.$row["buildingcode"].'"> <td id ="'.$row["buildingcode"].'" onclick=" updateAdvancedRoom(this.id); clearBuildingContent();">'. $row["buildingcode"] . " - " . $row["building"] . '</td></tr>';
		//echo '<td> </br> </td>';
			
	}
	echo '</table>';
	//}
	
?>