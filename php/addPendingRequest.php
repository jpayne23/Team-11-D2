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
	
	session_start();
	$deptCode = $_SESSION['deptCode'];	
	$modCode = $_REQUEST['modCode'];
	$rooms = $_REQUEST['rooms'];
	$groupSizes = $_REQUEST['groupSizes'];
	$selectedWeeks = $_REQUEST['selectedWeeks'];
	$facilities = $_REQUEST['facilities'];
	$sessionType = $_REQUEST['sessionType'];
	$sessionLength = $_REQUEST['sessionLength'];
	$sessionLength = (int)$sessionLength;
	$specialReq = $_REQUEST['specialReq'];
	$day = $_REQUEST['day'];
	$time = $_REQUEST['time'];
	$round = $_REQUEST['round'];
	
	// Convert the selected weeks to the database weeks format
	$weeksArray = array();
	if ($selectedWeeks != "")
	{
		$weeksArray = explode(",", $selectedWeeks);
		for ($i = 0; $i < count($weeksArray); $i++)
		{
			$dashPos = strpos($weeksArray[$i], "-");	// Find position of dash
			
			if ($dashPos === false)		// If dash is not found, then the entry is a single week
			{
				$weeksArray[$i] = $weeksArray[$i] . "-" . $weeksArray[$i];
			}			
		}	

		for ($j = 0; $j < count($weeksArray); $j++)
		{
			$dashPos = strpos($weeksArray[$j], "-");
			$leftSide = substr($weeksArray[$j], 0, $dashPos);
			$rightSide = substr($weeksArray[$j], $dashPos + 1);
			
			$weeksArray[$j] = "[" . $leftSide . "," . $rightSide . "]";
		}
	}
	
	$sql = "INSERT INTO Request (UserID,ModCode,SessionType,SessionLength,DayID,PeriodID,PriorityRequest,AdhocRequest,SpecialRequirements,RoundID,Status) ";
	$sql .= "VALUES ((SELECT UserID FROM Users WHERE DeptCode = '$deptCode'),'$modCode','$sessionType',$sessionLength,$day,$time,1,0,'$specialReq',$round,'Pending')";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}	
	
	// Add selected weeks to the database
	for ($k = 0; $k < count($weeksArray); $k++)
	{
		$sql2 = "INSERT INTO WeekRequest (RequestID, Weeks) ";
		$sql2 .= "VALUES ((SELECT MAX(RequestID) From Request), '$weeksArray[$k]')"; 

		$res2 =& $db->query($sql2);
		if(PEAR::isError($res2))
		{
			die($res2->getMessage());
		}
	}
	
	// Get FacilityID from the submitted facility names
	$facilityIDs = array();
	for ($l = 0; $l < count($facilities); $l++)
	{
		if ($facilities != "null")
		{			
			$sql3 = "SELECT FacilityID FROM Facility WHERE Facility = '$facilities[$l]'";
			
			$res3 =& $db->query($sql3);
			if(PEAR::isError($res3))
			{
				die($res3->getMessage());
			}
			
			while ($row3 = $res3->fetchRow())
			{
				array_push($facilityIDs, $row3['facilityid']);
			}			
		}
	}	
	
	// Add selected facilities to the database
	for ($m = 0; $m < count($facilityIDs); $m++)
	{
		$sql4 = "INSERT INTO FacilityRequest (RequestID, FacilityID) ";
		$sql4 .= "VALUES ((SELECT MAX(RequestID) From Request), '$facilityIDs[$m]')"; 	

		$res4 =& $db->query($sql4);
		if(PEAR::isError($res4))
		{
			die($res4->getMessage());
		}
	}
	
	
	// Add rooms to the database
	for ($n = 0; $n < count($rooms); $n++)
	{
		if($rooms != "null")
		{
			$sql5 = "INSERT INTO RoomRequest (Room, GroupSize) ";
			$sql5 .= "VALUES ('$rooms[$n]', " . (int)$groupSizes[$n] . ")";
			
			$res5 =& $db->query($sql5);
			if(PEAR::isError($res5))
			{
				die($res5->getMessage());
			}
			
			$sql6 = "INSERT INTO RequestToRoom (RequestID, RoomRequestID) ";
			$sql6 .= "VALUES ((SELECT MAX(RequestID) FROM Request), (SELECT MAX(RoomRequestID) FROM RoomRequest))";
			
			$res6 =& $db->query($sql6);
			if(PEAR::isError($res6))
			{
				die($res6->getMessage());
			}			
		}		
	}
?>