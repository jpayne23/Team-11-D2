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
	
	//$sql = "SELECT *  FROM Request WHERE RequestID = '".$requestID."';";		
	$sql = "SELECT Request.RequestID, Users.DeptCode, DeptNames.DeptName, Request.ModCode, Title, SessionType, SessionLength, Day, Request.PeriodID ,Period, PriorityRequest, AdhocRequest, SpecialRequirements, RoundID, Status ";
	$sql .= "FROM Request ";
	$sql .= "JOIN DayInfo ON DayInfo.DayID = Request.DayID ";
	$sql .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = Request.PeriodID ";	
	$sql .= "JOIN Module ON Module.ModCode = Request.ModCode ";	
	$sql .= "JOIN Users ON Users.UserID = Request.UserID ";	
	$sql .= "JOIN DeptNames ON DeptNames.DeptCode = Users.DeptCode ";
	$sql .= "WHERE Request.RequestID = '".$requestID."';";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	while ($row = $res->fetchRow())
	{
		// List facilities in one row instead of multiple						
		$sql2 = "SELECT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequest WHERE RequestID = ".$row["requestid"].")";
		$res2 =& $db->query($sql2);
		if(PEAR::isError($res2))
		{
			die($res2->getMessage());
		}
		$facilities = "";
		// If there are no results, return 'Any'
		if ($res2->numRows() == 0)
		{
			$facilities .= "Any";
		}
		else 
		{
			$facArray = array();
			while ($row2 = $res2->fetchRow())
			{		
				array_push($facArray, $row2["facility"]);					
			}
			$facilities = implode(", ", $facArray);		// Concatenate the elements of the array of facilities with a comma
		}		
		
		// List weeks in one row instead of multiple		
		$sql3 = "SELECT Weeks FROM WeekRequest WHERE RequestID = ".$row["requestid"].";";
		$res3 =& $db->query($sql3);
		if(PEAR::isError($res3))
		{
			die($res3->getMessage());
		}
		$weekArray = array();
		while ($row3 = $res3->fetchRow())
		{
			$weekString = $row3["weeks"];
			$weekString = str_replace(",", "-", $weekString);		// Replace the , with a -
			$weekString = str_replace("[", "", $weekString);		// Remove the opening bracket
			$weekString = str_replace("]", "", $weekString);		// Remove the closing bracket
			
			// Test for if the week start and end are the same Eg: 11-11
			$commaPos = strpos($weekString, "-");	// Find position of comma
			$leftSide = substr($weekString, 0, $commaPos);
			$rightSide = substr($weekString, $commaPos + 1);
			
			if ($leftSide == $rightSide)
			{
				$weekString = $leftSide;
			}
			
			array_push($weekArray, $weekString);
		}
		// Sort the weeks by ascending order
		for ($i = 0; $i < count($weekArray); $i++)
		{
			// Get the left hand side of the currently selected week
			// Set it to the lowest value
			$currentWeek = $weekArray[$i];			
			$currentDashPos = strpos($currentWeek, "-");	// Find position of dash
			if ($currentDashPos === false)
			{
				$currentLeftSide = $currentWeek;
			}
			else
			{
				$currentLeftSide = substr($currentWeek, 0, $currentDashPos);
			}
			
			$lowestWeekIndex = $i;
			
			for ($j = $i + 1; $j < count($weekArray); $j++)
			{
				// Get the left hand side of the next selected week, compare it to the lowest week
				// Swap if the next selected week is lower
				$nextWeek = $weekArray[$j];
				$nextDashPos = strpos($nextWeek, "-");	// Find position of dash
				if ($nextDashPos === false)
				{
					$nextLeftSide = $nextWeek;
				}
				else
				{
					$nextLeftSide = substr($nextWeek, 0, $nextDashPos);
				}			

				$lowestDashPos = strpos($weekArray[$lowestWeekIndex], "-");
				if ($lowestDashPos === false)
				{
					$lowestLeftSide = $weekArray[$lowestWeekIndex];
				}
				else
				{
					$lowestLeftSide = substr($weekArray[$lowestWeekIndex], 0, $lowestDashPos);
				}	
				
				if ($nextLeftSide < $lowestLeftSide)
				{
					$lowestWeekIndex = $j;	
				}
				
			}			
			
			$tempWeek = $weekArray[$lowestWeekIndex];			
			$weekArray[$lowestWeekIndex] = $weekArray[$i];			
			$weekArray[$i] = $tempWeek;	
		}		
		$weeks = implode(", ", $weekArray);			// Concatenate the elements of the array of weeks with a comma
		
		// List rooms in one row instead of multiple
		$sql4 = "SELECT Room FROM RoomRequest ";
		$sql4 .= "LEFT JOIN RequestToRoom ON " . $row["requestid"]  . " = RequestToRoom.RequestID ";		
		$sql4 .= "WHERE RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";	
		$res4 =& $db->query($sql4);
		if(PEAR::isError($res4))
		{
			die($res4->getMessage());
		}
		
		$rooms = "";
		// If there are no results, return 'Any'
		if ($res4->numRows() == 0)
		{
			$rooms = "Any";
		}
		else 
		{
			$roomArray = array();
			while ($row4 = $res4->fetchRow())
			{
				array_push($roomArray, $row4['room']);
			}

			$rooms .= implode(", ", $roomArray);

		}	
		
		if ($row["priorityrequest"] == 0)
		{
			$priority = "No";
		}
		else
		{
			$priority = "Yes";
		}
		
		if ($row["adhocrequest"] == 0)
		{
			$adhoc = "No";
		}
		else
		{
			$adhoc = "Yes";
		}
		
		if ($row["sessionlength"] == 1)
		{
			$hours = " hour";
		}
		else
		{
			$hours = " hours";
		}
		
		if ($row["specialrequirements"] == "")
		{
			$spReq = "N/A";
		}
		else
		{
			$spReq  = $row["specialrequirements"];
		}
		
		echo "Request ID: ".$row["requestid"]."\n";
		echo "Department: ".$row["deptname"]."\n";
		echo "Department Code: ".$row["deptcode"]."\n";
		echo "Module Code: ".$row["modcode"]."\n";
		echo "Module Title: ".$row["title"]."\n";
		echo "Session Type: ".$row["sessiontype"]."\n";
		echo "Session Length: ".$row["sessionlength"].$hours."\n";
		echo "Day: ".$row["day"]."\n";
		echo "Period: ".$row["periodid"]." starting at ".$row["period"]."\n";
		echo "Priority Request: ".$priority."\n";
		echo "Ad-Hoc Request: ".$adhoc."\n";
		echo "Special Requirements: ".$spReq."\n";
		echo "Round Number: ".$row["roundid"]."\n";
		echo "Facilities Requested: ".$facilities."\n";
		echo "Weeks Requested: ".$weeks."\n";
		echo "Room: ".$rooms."\n";
		echo "Status: ".$row["status"]."\n";
		
		//print_r($row);
	}
	
?>