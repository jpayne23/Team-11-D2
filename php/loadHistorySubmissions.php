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
	
	$sql = "SELECT Request.RequestID, ModCode, Room, SessionType, SessionLength, Day, Period, Status ";
	$sql .= "FROM Request ";
	$sql .= "LEFT JOIN RequestToRoom ON Request.RequestID = RequestToRoom.RequestID ";		// Add rooms to the results
	$sql .= "LEFT JOIN RoomRequest ON RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";	// Add rooms to the results
	$sql .= "JOIN DayInfo ON DayInfo.DayID = Request.DayID ";
	$sql .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = Request.PeriodID ";
	$sql .= "WHERE Status = 'Submitted' AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table border='1' id='historyTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>";
	echo "<th>Request ID</th>";
	echo "<th>Module Code</th>";
	echo "<th>Room</th>";
	echo "<th>Facilities</th>";
	echo "<th>Weeks</th>";
	echo "<th>Session Type</th>";
	echo "<th>Session Length (Hours)</th>";
	echo "<th>Day</th>";
	echo "<th>Start Time</th>";
	echo "<th>Status</th>";
		
	// Populate the table with rows from database	
	while ($row = $res->fetchRow())
	{
		echo "<tr>";
		echo "<td>" . $row["requestid"] . "</td>";
		echo "<td>" . $row["modcode"] . "</td>";
		
		if ($row["room"] == "")	// If there are no rooms, return 'Any'
		{
			echo "<td>Any</td>";
		}
		else
		{
			echo "<td>" . $row["room"] . "</td>";
		}		
		
		// List facilities in one row instead of multiple						
		$sql2 = "SELECT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequest WHERE RequestID = ".$row["requestid"].")";
		$res2 =& $db->query($sql2);
		if(PEAR::isError($res2))
		{
			die($res2->getMessage());
		}
		
		// If there are no results, return 'Any'
		if ($res2->numRows() == 0)
		{
			echo "<td>Any</td>";
		}
		else 
		{
			echo "<td>";
			$facArray = array();
			while ($row2 = $res2->fetchRow())
			{		
				array_push($facArray, $row2["facility"]);					
			}
			echo implode(", ", $facArray);		// Concatenate the elements of the array of facilities with a comma
			echo "</td>";
		}		
		
		// List weeks in one row instead of multiple		
		$sql3 = "SELECT Weeks FROM WeekRequest WHERE RequestID = ".$row["requestid"].";";
		$res3 =& $db->query($sql3);
		if(PEAR::isError($res3))
		{
			die($res3->getMessage());
		}
		
		echo "<td>";
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
		echo implode(", ", $weekArray);			// Concatenate the elements of the array of weeks with a comma
		echo "</td>";
		
		echo "<td>" . $row["sessiontype"] . "</td>";
		echo "<td>" . $row["sessionlength"] . "</td>";
		
		echo "<td>" . $row["day"] . "</td>";
		echo "<td>" . $row["period"] . "</td>";
		
		echo "<td>" . $row["status"] . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
?>