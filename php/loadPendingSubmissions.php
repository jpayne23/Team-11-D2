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
	$sortDirection = $_REQUEST['sortDirection'];
	
	$sql = "SELECT Request.RequestID, ModCode, Room, SessionType, SessionLength, Status ";
	$sql .= "FROM Request ";
	$sql .= "LEFT JOIN RequestToRoom ON Request.RequestID = RequestToRoom.RequestID ";		// Add rooms to the results
	$sql .= "LEFT JOIN RoomRequest ON RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";		// Add rooms to the results	
	$sql .= "WHERE Status = 'Pending' AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')";
	
	if ($sortDirection == "up")
	{				
		$sql .= "ORDER BY Request.RequestID DESC;";
	}
	else
	{
		$sql .= "ORDER BY Request.RequestID ASC;";
	}
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table border='1' id='submissionsTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>";	
	if ($sortDirection == "up")
	{				
		echo "<th>Request ID <img id='upArrow' src='img/upArrow.png'></th>";
	}
	else
	{
		echo "<th>Request ID <img id='downArrow' src='img/downArrow.png'></th>";
	}	
	echo "<th>Module Code</th>";
	echo "<th>Room</th>";
	echo "<th>Facilities</th>";
	echo "<th>Weeks</th>";
	echo "<th>Session Type</th>";
	echo "<th>Session Length (Hours)</th>";
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
			$commaPos = strpos($weekString, "-");	// Find position of dash
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
		echo "<td>" . $row["status"] . "</td>";
		echo "<td><img id='editIcon' name='editIcon" . $row["requestid"] . "' src='img/editIcon.png'></td>";
		echo "</tr>";
	}
	
	echo "</table>";
?>