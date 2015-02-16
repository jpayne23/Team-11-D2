<?php
	/*
	A php script that will generate all information about a request. This is run when a request in the pending/history table is clicked.
	It takes in the requestID and will return all information pertaining to that request. 
	For a modified request where a different room was allocated than the one that was requested, 
	this script will generate an alert that shows what the request was and what was allocated. 
	Written by Prakash and Jack.
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
	
	$requestID = $_REQUEST['requestID']; //get request id from the javascript
	
	$sql = "SELECT Request.RequestID, Users.DeptCode, DeptNames.DeptName, Request.ModCode, Title, SessionType, SessionLength, Day, Request.PeriodID ,Period, PriorityRequest, AdhocRequest, SpecialRequirements, RoundID, Semester, Status ";
	$sql .= "FROM Request ";
	$sql .= "JOIN DayInfo ON DayInfo.DayID = Request.DayID ";
	$sql .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = Request.PeriodID ";	
	$sql .= "JOIN Module ON Module.ModCode = Request.ModCode ";	
	$sql .= "JOIN Users ON Users.UserID = Request.UserID ";	
	$sql .= "JOIN DeptNames ON DeptNames.DeptCode = Users.DeptCode ";
	$sql .= "WHERE Request.RequestID = '".$requestID."';";
	//build the sql
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}//run the query
	
	$results = array();
	while ($row = $res->fetchRow())
	{
		array_push($results, $row['requestid']);
		array_push($results, $row['modcode']);
		array_push($results, $row['title']);
		array_push($results, $row['sessiontype']);
		array_push($results, $row['sessionlength']);
		array_push($results, $row['day']);
		array_push($results, $row['period']);
		array_push($results, $row['specialrequirements']);
		array_push($results, $row['priorityrequest']);
		array_push($results, $row['status']);
		array_push($results, $row['semester']);
	}//add easy to display information to an array
	
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
	}//add arrays containing rooms and group size in each room
	array_push($results, $roomsArray);
	array_push($results, $groupSizeArray);
	
	$allocated = array();
	//if the request status is "Modified"
	if ($results[9] == "Modified")
	{
		$sql5 = "SELECT Room, Day, Period, Comments FROM AllocatedRooms ";
		$sql5 .= "JOIN DayInfo ON DayInfo.DayID = AllocatedRooms.DayID ";
		$sql5 .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = AllocatedRooms.PeriodID ";
		$sql5 .= "WHERE RequestID = $requestID ";
		
		$res5 =& $db->query($sql5);
		if(PEAR::isError($res5))
		{
			die($res5->getMessage());
		}		
		
		while ($row5 = $res5->fetchRow())
		{
			array_push($allocated, $row5['room']);
			array_push($allocated, $row5['day']);
			array_push($allocated, $row5['period']);
			array_push($allocated, $row5['comments']);
		}
	}
	array_push($results, $allocated);
	//send the results back to the javascript in an array
	echo json_encode($results);
?>