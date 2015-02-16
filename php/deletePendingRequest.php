<!--
When the user clicks the delete icon, we remove any information relating to
that specific requestID.

Implemented by Jack.
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
	
	$requestID = $_REQUEST['requestID'];
	
	// Get roomRequestID for given requestID
	$sql = "SELECT RoomRequestID FROM RequestToRoom WHERE RequestID = " . $requestID . ";";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	$roomRequestIDs = array();
	while ($row = $res->fetchRow())
	{
		array_push($roomRequestIDs, $row['roomrequestid']);
	}
	
	// Delete the room requests for given request id and room request id
	for ($i = 0; $i < count($roomRequestIDs); $i++)
	{
		$sql = "DELETE FROM RoomRequest WHERE RoomRequestID = " . $roomRequestIDs[$i] . ";";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die($res->getMessage());
		}
	}
	
	// Delete main request
	$sql2 = "DELETE FROM Request WHERE RequestID = " . $requestID . ";";		
	$res2 =& $db->query($sql2);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage());
	}
?>