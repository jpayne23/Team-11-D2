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
	
	$sql = "SELECT Request.RequestID, ModCode, Room, SessionType, SessionLength, DayID, PeriodID, Status ";
	$sql .= "FROM Request ";
	$sql .= "LEFT JOIN RequestToRoom ON Request.RequestID = RequestToRoom.RequestID ";		// Add rooms to the results
	$sql .= "LEFT JOIN RoomRequest ON RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";		// Add rooms to the results
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
		$sql2 = "SELECT Facility FROM FacilityRequest WHERE RequestID = ".$row["requestid"].";";
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
		echo implode(", ", $weekArray);			// Concatenate the elements of the array of weeks with a comma
		echo "</td>";
		
		echo "<td>" . $row["sessiontype"] . "</td>";
		echo "<td>" . $row["sessionlength"] . "</td>";
		
		$sql4 = "SELECT Day FROM DayInfo WHERE DayID = ".$row["dayid"].";";
		$res4 =& $db->query($sql4);
		if(PEAR::isError($res4))
		{
			die($res4->getMessage());
		}

		while ($row4 = $res4->fetchRow())
		{
			echo "<td>" . $row4["day"] . "</td>";
			
		}
		
		$sql5 = "SELECT Period FROM PeriodInfo WHERE PeriodID = " . $row["periodid"] . ";";
		$res5 =& $db->query($sql5);
		if(PEAR::isError($res5))
		{
			die($res5->getMessage());
		}

		while ($row5 = $res5->fetchRow())
		{
			echo "<td>" . $row5["period"] . "</td>";
			
		}
		
		echo "<td>" . $row["status"] . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
?>