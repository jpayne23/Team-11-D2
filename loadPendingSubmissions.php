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
	
	$sql = "SELECT RequestID, ModCode, SessionType, SessionLength, Status FROM Request";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table border='1' id='submissionsTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>";
	echo "<th>Request ID</th>";
	echo "<th>Mod Code</th>";
	echo "<th>Session Type</th>";
	echo "<th>SessionLength</th>";
	echo "<th>Status</th>";
	
	// Populate the table with rows from database
	while ($row = $res->fetchRow())
	{
		echo "<tr>";
		echo "<td>" . $row["requestid"] . "</td>";
		echo "<td>" . $row["modcode"] . "</td>";
		echo "<td>" . $row["sessiontype"] . "</td>";
		echo "<td>" . $row["sessionlength"] . "</td>";
		echo "<td>" . $row["status"] . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
?>