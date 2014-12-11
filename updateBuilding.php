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
	
	echo '<select name="building" id="building1" class="larger" onchange="updateRoom()">';
	echo '<option>Any</option>';
	if($park != 'Any')
	{
		while ($row = $res->fetchRow())
		{
			echo '<option id ="'.$row["buildingcode"].'">' . $row["buildingcode"] . " - " . $row["building"] . '</option>';
		}
	}
	else 
	{
		echo '<option>Any</option>';
	}	
	echo '</select>';
?>