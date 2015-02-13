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
	
	/*($row = $res->fetchRow());
	echo '<tr id ="'.$row["buildingcode"].'"> <td>'. $row["buildingcode"] . " - " . $row["building"] . '</td></tr>';
	*/
	//echo '<select name="building" id="building1" onchange="updateRoom()">';
	while ($row = $res->fetchRow())
		{
			echo '<tr class = "contentrows" id ="'.$row["buildingcode"].'"> <td id ="'.$row["buildingcode"].'" onclick=" updateAdvancedRoom(this.id); clearBuildingContent();">'. $row["buildingcode"] . " - " . $row["building"] . '</td></tr>';
			echo '<td> </br> </td>';
		}
	//}
	
?>