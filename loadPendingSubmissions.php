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
	
	$sql = "SELECT Request.RequestID, ModCode, Room, SessionType, SessionLength, Status FROM Request, RoomRequest ";
	$sql .= "JOIN RequestToRoom WHERE RequestToRoom.RequestID = Request.RequestID;";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table border='1' id='submissionsTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>";
	echo "<th>Request ID</th>";
	echo "<th>Module Code</th>";
	echo "<th>Room</th>";
	echo "<th>Session Type</th>";
	echo "<th>Session Length (Hours)</th>";
	echo "<th>Status</th>";
	
	// Populate the table with rows from database
	while ($row = $res->fetchRow())
	{
		echo "<tr>";
		echo "<td>" . $row["requestid"] . "</td>";
		echo "<td>" . $row["modcode"] . "</td>";
		echo "<td>" . $row["room"] . "</td>";
		echo "<td>" . $row["sessiontype"] . "</td>";
		echo "<td>" . $row["sessionlength"] . "</td>";
		echo "<td>" . $row["status"] . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
?>