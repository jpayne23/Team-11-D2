<?php
/*
Returns all of the information about the requests that the user pressed the edit
button for. It is then used to fill out the form.

Implemented by Jack
*/
	// Setting up connecting to the database
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	$requestID = $_REQUEST['requestID']; //pull request id from javascript
	
	$sql = "SELECT Request.RequestID, Request.ModCode, Title, Part, SessionType, SessionLength, Day, Period, PriorityRequest, SpecialRequirements, AdhocRequest, Semester ";
	$sql .= "FROM Request ";
	$sql .= "JOIN Module ON Request.ModCode = Module.ModCode ";
	$sql .= "JOIN DayInfo ON Request.DayID = DayInfo.DayID ";
	$sql .= "JOIN PeriodInfo ON Request.PeriodID = PeriodInfo.PeriodID ";
	$sql .= "WHERE Request.RequestID = '$requestID'";
	//build sql query
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
		array_push($results, $row['priorityrequest']);
		array_push($results, $row['adhocrequest']);
		array_push($results, $row['semester']);
	}
	//add easy to display fields in an array
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
	
	// List facilities in one row instead of multiple
	$sql3 = "SELECT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequest WHERE RequestID = '$requestID')";
	$res3 =& $db->query($sql3);
	if(PEAR::isError($res3))
	{
		die($res3->getMessage());
	}	
	
	$facilityArray = array();
	while ($row3 = $res3->fetchRow())
	{
		array_push($facilityArray, $row3['facility']);
	}
	array_push($results, $facilityArray);
	
	// List rooms in one row instead of multiple
	$sql4 = "SELECT Room, GroupSize FROM RoomRequest ";
	$sql4 .= "LEFT JOIN RequestToRoom ON '$requestID' = RequestToRoom.RequestID ";		
	$sql4 .= "WHERE RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";	
	$res4 =& $db->query($sql4);
	if(PEAR::isError($res4))
	{
		die($res4->getMessage());
	}
	
	$roomsArray = array();
	$groupSizeArray = array();
	while ($row4 = $res4->fetchRow())
	{
		array_push($roomsArray, $row4['room']);
		array_push($groupSizeArray, $row4['groupsize']);
	}
	array_push($results, $roomsArray);
	array_push($results, $groupSizeArray);
	
	echo json_encode($results);
?>