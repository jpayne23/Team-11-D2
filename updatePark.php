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
	
	$sql = 'SELECT DISTINCT Park FROM Building';			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo '<select name="park" id="park1" onchange="updateBuilding(); updateRoom();" class="larger">';
	echo '<option>Any</option>';
	while ($row = $res->fetchRow())
	{
		echo '<option>' . $row["park"] . '</option>';
	}
	echo '</select>';
?>