<!--  
Loads all adhoc requests and this file is also used  to filter these requests with a flag to determine whether it was a general request or not.
Presents all this in a table in a pop up which becomes visible when selected.

General request retrieval by Jack, filtering implemented by Joe
-->


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
	
	//Determines if request is from the filter menu
	
	if ($flag == 1){ 
		//From filter menu
		$modCode = $_REQUEST['modCode'];
		$sessionType = $_REQUEST['sessionType'];
		$day = $_REQUEST['day'];
		$facility = $_REQUEST['facility'];
		$status = $_REQUEST['status'];
	}
	else{
		//General request
		$modCode = 'Any';
		$sessionType = 'Any';	
		$day = 'Any';
		$facility = 'Any';
		$status = 'Any';
	}
	
	//Retrieve all required information about a request
	$sql = "SELECT Request.RequestID, ModCode, Room, SessionType, SessionLength, Day, Period, Status ";
	$sql .= "FROM Request ";
	$sql .= "LEFT JOIN RequestToRoom ON Request.RequestID = RequestToRoom.RequestID ";		// Add rooms to the results
	$sql .= "LEFT JOIN RoomRequest ON RequestToRoom.RoomRequestID = RoomRequest.RoomRequestID ";	// Add rooms to the results
	$sql .= "JOIN DayInfo ON DayInfo.DayID = Request.DayID ";
	$sql .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = Request.PeriodID ";
	$sql .= "WHERE UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode') AND AdhocRequest = 1";
	
	//Additional conditions for filtering
	if ($modCode != "Any")
	{				
		$sql .= " AND ModCode = '" . $modCode . "'";
	}
	if ($sessionType != "Any")
	{
		$sql .= " AND SessionType = '" . $sessionType . "'";
	}
	if ($day != "Any")
	{				
		$sql .= " AND Request.DayID = (SELECT DayID FROM DayInfo WHERE Day = '" . $day . "')";
	}
	if ($facility != 'Any')
	{
		$sql .= " AND Request.RequestID IN (SELECT FacilityRequest.RequestID FROM FacilityRequest)";
	}
	if ($status != 'Any')
	{
		$sql .= " AND Status = '". $status ."'";
	}
	//When results are sorted by a column
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
			case "Status":
				$sql .= " ORDER BY case Status when 'Unsuccessful' then 1 when 'Modified' then 2 when 'Successful' then 3 else 4 end;";
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
			case "Status":
				$sql .= " ORDER BY case Status when 'Submitted' then 1 when 'Successful' then 2 when 'Modified' then 3 else 4 end;";
				break;
		}	
	}
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	//All table headers
	echo "<table border='1' class = 'loadTable' id='submissionsTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>";	
	//Determines direction of sort arrow
	if ($sortDirection == "up")
	{				
		echo "<th>Request ID <img id='upArrow' name='RequestID' src='img/upArrow.png'></th>";
		echo "<th>Module Code <img id='upArrow' name= 'ModuleCode' src='img/upArrow.png'></th>";
		echo "<th>Room</th>";
		echo "<th>Facilities</th>";
		echo "<th>Weeks</th>";
		echo "<th>Session Type <img id='upArrow' name='SessionType' src='img/upArrow.png'></th>";
		echo "<th>Session Length <img id='upArrow' name='SessionLength' src='img/upArrow.png'></th>";
		echo "<th>Day <img id='upArrow' name='Day' src='img/upArrow.png'></th>";
		echo "<th>Start Time <img id='upArrow' name='Period' src='img/upArrow.png'></th>";
		echo "<th>Status <img id='upArrow' name='Status' src='img/upArrow.png'></th>";
	}
	else
	{
		echo "<th>Request ID <img id='downArrow' name='RequestID' src='img/downArrow.png'></th>";
		echo "<th>Module Code <img id='downArrow' name= 'ModuleCode' src='img/downArrow.png'></th>";
		echo "<th>Room</th>";
		echo "<th>Facilities</th>";
		echo "<th>Weeks</th>";
		echo "<th>Session Type <img id='downArrow' name='SessionType' src='img/downArrow.png'></th>";
		echo "<th>Session Length <img id='downArrow' name='SessionLength' src='img/downArrow.png'></th>";
		echo "<th>Day <img id='downArrow' name='Day' src='img/downArrow.png'></th>";
		echo "<th>Start Time <img id='downArrow' name='Period' src='img/downArrow.png'></th>";
		echo "<th>Status <img id='downArrow' name='Status' src='img/downArrow.png'></th>";
	}
	
	$modCodes = array();
	$fill = True;
		
	// Populate the table with rows from database	
	while ($row = $res->fetchRow())
	{
		//Checks if a request contains the chosen facility before request gets added to table 
		if ($facility != 'Any'){
		
			$sql2 = "SELECT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequest WHERE RequestID = ".$row["requestid"].")";
			$sql2 .= " AND '$facility' IN (SELECT Facility FROM Facility WHERE FacilityID IN ";
			$sql2 .= " (SELECT FacilityID FROM FacilityRequest WHERE RequestID = '".$row['requestid']."'));";
			
			$res2 =& $db->query($sql2);
			if(PEAR::isError($res2))
			{
				die($res2->getMessage());
			}
			//If the request doesn't contain the chosen facility
			if ($res2->numRows() == 0)
			{
				$fill = False;
			}
			//If the request does contain the chosen facility
			else
			{
				$fill = True;
			}
		}
		//Fill in request information if the request fits the criteria
		if ($fill){
			
			//Colour codes request ID depending on status 
			echo "<tr id='historyRow' class='clickable' name ='".$row["requestid"]."'>";
			
			if ($row['status'] == 'Unsuccessful')
			{				
				echo "<td class='unsuccessful'><b>" . $row["requestid"] . "</b></td>";
			}
			else if ($row['status'] == 'Successful')
			{
				echo "<td class='successful'>" . $row["requestid"] . "</td>";
			}
			else
			{
				echo "<td>" . $row["requestid"] . "</td>";
			}
			
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
			if ($facility == 'Any')
			{
				$sql2 = "SELECT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequest WHERE RequestID = ".$row["requestid"].")";
				
				$res2 =& $db->query($sql2);
				if(PEAR::isError($res2))
				{
					die($res2->getMessage());
				}
			}
			
			// If there are no results and no facility was specified
			if ($res2->numRows() == 0 && $facility == 'Any')
			{
				echo "<td>No preference</td>";
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

			//Colour codes status 
			if ($row['status'] == 'Unsuccessful')
			{
				echo "<td class='unsuccessful'><b>" . $row["status"] . "</b></td>";	
				echo "<td id='edittd'><img id='editIcon' name='editIcon" . $row["requestid"] . "' src='img/editIcon.png'></td>";
			}
			else if ($row['status'] == 'Successful')
			{
				echo "<td class='successful'>" . $row["status"] . "</td>";	
			}
			else
			{
				echo "<td>" . $row["status"] . "</td>";	
			}		
			echo "<td id='deletetd'><img id='deleteIcon' name='deleteIcon" . $row["requestid"] . "' src='img/deleteIcon.png'></td>";
			echo "</tr>";
		}
	}
	
	echo "</table>";
?>