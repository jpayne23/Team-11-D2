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
	
	$requestIDHist = $_REQUEST['requestIDHist'];
	$round = (int)$_REQUEST['round'];
	
	for ($i = 0; $i < count($requestIDHist); $i++)
	{
		// Copy the main request from history to be submitted
		$sql = "INSERT INTO Request (UserID, ModCode, SessionType, SessionLength, DayID, PeriodID, PriorityRequest, SpecialRequirements, Semester, RoundID, Status) ";
		$sql .= "(SELECT UserID, ModCode, SessionType, SessionLength, DayID, PeriodID, PriorityRequest, SpecialRequirements, Semester, $round, \"Submitted\" FROM RequestHist ";
		$sql .= "WHERE RequestHist.RequestIDHist = " . $requestIDHist[$i] . ");";			
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die("Main: " . $res->getMessage());
		}
		
		// Get the weeks from each requestIDHist
		$weeks = array();
		$sql = "SELECT Weeks FROM WeekRequestHist WHERE WeekRequestHist.RequestIDHist = " . $requestIDHist[$i] . ";";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die("weeks: " . $res->getMessage());
		}
		while ($row = $res->fetchRow())
		{
			array_push($weeks, $row['weeks']);
		}
		
		// Copy the weeks from history to be submitted
		if (count($weeks) > 0)
		{
			for ($j = 0; $j < count($weeks); $j++)
			{
				$sql = "INSERT INTO WeekRequest (RequestID, Weeks) ";
				$sql .= "VALUES ((SELECT MAX(RequestID) From Request), '" . $weeks[$j] . "');";
				$res =& $db->query($sql);
				if(PEAR::isError($res))
				{
					die("Weeksloop: " . $res->getMessage());
				}
			}
		}
		
		// Get the facilities from each requestIDHist
		$facilities = array();
		$sql = "SELECT FacilityID FROM FacilityRequestHist WHERE FacilityRequestHist.RequestIDHist = " . $requestIDHist[$i] . ";";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die("facilities: " . $res->getMessage());
		}
		while ($row = $res->fetchRow())
		{
			array_push($facilities, $row['facilityid']);
		}
		
		// Copy the facilities from history to be submitted
		if (count($facilities) > 0)
		{
			for ($j = 0; $j < count($facilities); $j++)
			{				
				$sql = "INSERT INTO FacilityRequest (RequestID, FacilityID) ";
				$sql .= "VALUES ((SELECT MAX(RequestID) From Request), " . $facilities[$j] . ");";
				
				$res =& $db->query($sql);
				if(PEAR::isError($res))
				{
					die("facilitiesloop: " . $res->getMessage());
				}
			}
		}
		
		// Copy the rooms from history to be submitted
		$sql = "INSERT INTO RoomRequest (Room, GroupSize) ";
		$sql .= "(SELECT Room, GroupSize FROM RoomRequestHist WHERE RoomRequestHist.RoomRequestIDHist = " . $requestIDHist[$i] . ");";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die("rooms: " . $res->getMessage());
		}
		
		$sql = "INSERT INTO RequestToRoom (RequestID, RoomRequestID) ";
		$sql .= "VALUES ((SELECT MAX(RequestID) FROM Request), (SELECT MAX(RoomRequestID) FROM RoomRequest))";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die("rooms2: " . $res->getMessage());
		}
	}
?>