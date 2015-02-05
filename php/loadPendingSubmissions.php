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
	$sortColumn = $_REQUEST['sortColumn'];
	$flag = $_REQUEST['flag'];
	
	if ($flag == 1){
		$modCode = $_REQUEST['modCode'];
		$sessionType = $_REQUEST['sessionType'];
	}
	else{
		
		$modCode = 'Any';
		$sessionType = 'Any';		
	}
	
	$sql = "SELECT Request.RequestID, ModCode, Room, SessionType, SessionLength, Day, Period, Status ";
	$sql .= "FROM Request ";
	$sql .= "LEFT JOIN RequestToRoom ON Request.RequestID = RequestToRoom.RequestID ";		// Add rooms to the results
	$sql .= "LEFT JOIN RoomRequest ON RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";		// Add rooms to the results	
	$sql .= "JOIN DayInfo ON DayInfo.DayID = Request.DayID ";
	$sql .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = Request.PeriodID ";
	$sql .= "WHERE Status = 'Pending'";
	
	if ($modCode != "Any")
	{				
		$sql .= " AND ModCode = '" . $modCode . "'";
	}
	if ($sessionType != "Any")
	{
		$sql .= " AND SessionType = '" . $sessionType . "'";
	}
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')";
	
	if ($sortDirection == "up")
	{				
		switch ($sortColumn)
		{
			case "RequestID":
				$sql .= " ORDER BY Request.RequestID DESC;";
				break;
			case "ModuleCode":
				$sql .= " ORDER BY ModCode DESC;";
				break;
			case "Room":
				$sql .= " ORDER BY Room DESC;";
				break;
			case "SessionType":
				$sql .= " ORDER BY Request.SessionType DESC;";
				break;
			case "SessionLength":
				$sql .= " ORDER BY Request.SessionLength DESC;";
				break;
			case "Day":
				$sql .= " ORDER BY Request.DayID DESC;";
				break;
			case "Period":
				$sql .= " ORDER BY Request.PeriodID DESC;";
				break;
		}		
	}
	else
	{
		switch ($sortColumn)
		{
			case "RequestID":
				$sql .= " ORDER BY Request.RequestID ASC;";
				break;
			case "ModuleCode":
				$sql .= " ORDER BY ModCode ASC;";
				break;
			case "Room":
				$sql .= " ORDER BY Room ASC;";
				break;
			case "SessionType":
				$sql .= " ORDER BY Request.SessionType ASC;";
				break;
			case "SessionLength":
				$sql .= " ORDER BY Request.SessionLength ASC;";
				break;
			case "Day":
				$sql .= " ORDER BY Request.DayID ASC;";
				break;
			case "Period":
				$sql .= " ORDER BY Request.PeriodID ASC;";
				break;
		}	
	}
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table border='1' id='submissionsTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>";	
	if ($sortDirection == "up")
	{				
		echo "<th>Request ID <img id='upArrow' name='RequestID' src='img/upArrow.png'></th>";
		echo "<th>Module Code <img id='upArrow' name= 'ModuleCode' src='img/upArrow.png'></th>";
		echo "<th>Room <img id='upArrow' name='Room' src='img/upArrow.png'></th>";
		echo "<th>Facilities</th>";
		echo "<th>Weeks</th>";
		echo "<th>Session Type <img id='upArrow' name='SessionType' src='img/upArrow.png'></th>";
		echo "<th>Session Length <img id='upArrow' name='SessionLength' src='img/upArrow.png'></th>";
		echo "<th>Day <img id='upArrow' name='Day' src='img/upArrow.png'></th>";
		echo "<th>Start Time <img id='upArrow' name='Period' src='img/upArrow.png'></th>";
	}
	else
	{
		echo "<th>Request ID <img id='downArrow' name='RequestID' src='img/downArrow.png'></th>";
		echo "<th>Module Code <img id='downArrow' name= 'ModuleCode' src='img/downArrow.png'></th>";
		echo "<th>Room <img id='downArrow' name='Room' src='img/downArrow.png'></th>";
		echo "<th>Facilities</th>";
		echo "<th>Weeks</th>";
		echo "<th>Session Type <img id='downArrow' name='SessionType' src='img/downArrow.png'></th>";
		echo "<th>Session Length <img id='downArrow' name='SessionLength' src='img/downArrow.png'></th>";
		echo "<th>Day <img id='downArrow' name='Day' src='img/downArrow.png'></th>";
		echo "<th>Start Time <img id='downArrow' name='Period' src='img/downArrow.png'></th>";
	}
	
	echo "<th>Status</th>";
	
	$modCodes = array();
		
	// Populate the table with rows from database	
	while ($row = $res->fetchRow())
	{
		echo "<tr>";
		echo "<td>" . $row["requestid"] . "</td>";
		echo "<td>" . $row["modcode"] . "</td>";
		
		//$modCodes[$modCodes.size] = $row["modcode"];
		
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
		
		echo "<td>" . $row["day"] . "</td>";
		echo "<td>" . $row["period"] . "</td>";
		
		echo "<td>" . $row["status"] . "</td>";
		echo "<td><img id='editIcon' name='editIcon" . $row["requestid"] . "' src='img/editIcon.png'></td>";
		echo "<td><img id='deleteIcon' name='deleteIcon" . $row["requestid"] . "' src='img/deleteIcon.png'></td>";
		echo "</tr>";
	}
	
	echo "</table>";
?>