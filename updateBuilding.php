<?php
	// Setting up connecting to the database
	require_once 'MDB2.php';			
	include "password.php"
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
	
	echo '<select name="building" id="building1" class="larger">';
	while ($row = $res->fetchRow())
	{
		echo '<option>' . $row["buildingcode"] . " - " . $row["building"] . '</option>';
	}
	echo '</select>';
?>