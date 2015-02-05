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
	
	$sql = "SELECT Request.RequestID, Request.ModCode, Title, Part, Room, SessionType, SessionLength, Day, Period, SpecialRequirements ";
	$sql .= "FROM Request ";
	$sql .= "JOIN Module ON Request.ModCode = Module.ModCode ";
	$sql .= "JOIN DayInfo ON Request.DayID = DayInfo.DayID ";
	$sql .= "JOIN PeriodInfo ON Request.PeriodID = PeriodInfo.PeriodID ";
	$sql .= "LEFT JOIN RequestToRoom ON Request.RequestID = RequestToRoom.RequestID ";		// Add rooms to the results
	$sql .= "LEFT JOIN RoomRequest ON RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";		// Add rooms to the results	
	$sql .= "WHERE Request.RequestID = '$requestID'";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	$results = array();	
	while ($row = $res->fetchRow())
	{
		array_push($results, $row['requestid']);
		array_push($results, $row['modcode']);
		array_push($results, $row['title']);
		array_push($results, $row['part']);
		array_push($results, $row['sessiontype']);
		array_push($results, $row['sessionlength']);
		array_push($results, $row['day']);
		array_push($results, $row['period']);
		array_push($results, $row['specialrequirements']);
	}
	
	// List weeks in one row instead of multiple		
	$sql2 = "SELECT Weeks FROM WeekRequest WHERE RequestID = '$requestID';";
	$res2 =& $db->query($sql2);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage());
	}	
	
	$weekArray = array();
	while ($row2 = $res2->fetchRow())
	{
		$weekString = $row2["weeks"];
		$weekString = str_replace(",", "-", $weekString);		// Replace the , with a -
		$weekString = str_replace("[", "", $weekString);		// Remove the opening bracket
		$weekString = str_replace("]", "", $weekString);		// Remove the closing bracket
		
		// Test for if the week start and end are the same Eg: 11-11
		$commaPos = strpos($weekString, "-");	// Find position of dash
		$leftSide = substr($weekString, 0, $commaPos);
		$rightSide = substr($weekString, $commaPos + 1);
		
		if ($leftSide == $rightSide)
		{
			$weekString = $leftSide;
		}
		
		array_push($weekArray, $weekString);
	}
	
	array_push($results, $weekArray);
	echo json_encode($results);
?>