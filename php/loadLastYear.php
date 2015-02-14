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
		$day = $_REQUEST['day'];
		$facility = $_REQUEST['facility'];
	}
	else{
		
		$modCode = 'Any';
		$sessionType = 'Any';	
		$day = 'Any';
		$facility = 'Any';
	}
	
	$sql = "SELECT RequestHist.RequestIDHist, ModCode, SessionType, SessionLength, Day, Period, Status ";
	$sql .= "FROM RequestHist ";
	$sql .= "JOIN DayInfo ON DayInfo.DayID = RequestHist.DayID ";
	$sql .= "JOIN PeriodInfo ON PeriodInfo.PeriodID = RequestHist.PeriodID ";
		
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
		$sql .= " AND RequestHist.DayID = (SELECT DayID FROM DayInfo WHERE Day = '" . $day . "')";
	}
	if ($facility != 'Any')
	{
		$sql .= " AND RequestHist.RequestIDHist IN (SELECT FacilityRequestHist.RequestIDHist FROM FacilityRequestHist)";
	}
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')";
	
	if ($sortDirection == "up")
	{				
		switch ($sortColumn)
		{
			case "RequestID":
				$sql .= " ORDER BY RequestHist.RequestIDHist DESC;";
				break;
			case "ModuleCode":
				$sql .= " ORDER BY ModCode DESC;";
				break;
			case "SessionType":
				$sql .= " ORDER BY RequestHist.SessionType DESC;";
				break;
			case "SessionLength":
				$sql .= " ORDER BY RequestHist.SessionLength DESC;";
				break;
			case "Day":
				$sql .= " ORDER BY RequestHist.DayID DESC;";
				break;
			case "Period":
				$sql .= " ORDER BY RequestHist.PeriodID DESC;";
				break;
		}		
	}
	else
	{
		switch ($sortColumn)
		{
			case "RequestID":
				$sql .= " ORDER BY RequestHist.RequestIDHist ASC;";
				break;
			case "ModuleCode":
				$sql .= " ORDER BY ModCode ASC;";
				break;
			case "SessionType":
				$sql .= " ORDER BY RequestHist.SessionType ASC;";
				break;
			case "SessionLength":
				$sql .= " ORDER BY RequestHist.SessionLength ASC;";
				break;
			case "Day":
				$sql .= " ORDER BY RequestHist.DayID ASC;";
				break;
			case "Period":
				$sql .= " ORDER BY RequestHist.PeriodID ASC;";
				break;
		}	
	}
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table border='1' id='lastYearTable' style='width:100%; margin-left:auto; margin-right:auto;'>";	
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
	}
	
	echo "<th>Status</th>";
	echo "<th>Select/Deselect All<br><input type='checkbox' id='requestCheckboxMaster'</th>";
	
	$modCodes = array();
	$fill = True;
	
	// Populate the table with rows from database	
	while ($row = $res->fetchRow())
	{
		if ($facility != 'Any'){			
			$sql2 = "SELECT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequestHist WHERE RequestIDHist = ".$row["requestidhist"].")";
			$sql2 .= " AND '$facility' IN (SELECT Facility FROM Facility WHERE FacilityID IN ";
			$sql2 .= " (SELECT FacilityID FROM FacilityRequestHist WHERE RequestIDHist = '".$row['requestidhist']."'));";
			
			$res2 =& $db->query($sql2);
			if(PEAR::isError($res2))
			{
				die($res2->getMessage());
			}
			
			if ($res2->numRows() == 0)
			{
				$fill = False;
			}
			else
			{
				$fill = True;
			}
		}
		
		if($fill){
			echo "<tr id='pendingRow' name='".$row["requestidhist"]."'>";
			echo "<td>" . $row["requestidhist"] . "</td>";
			echo "<td>" . $row["modcode"] . "</td>";
			
			//$modCodes[$modCodes.size] = $row["modcode"];
			
			// List rooms in one row instead of multiple
			$sql4 = "SELECT Room FROM RoomRequestHist ";
			$sql4 .= "LEFT JOIN RequestToRoomHist ON " . $row["requestidhist"]  . " = RequestToRoomHist.RequestIDHist ";		
			$sql4 .= "WHERE RequestToRoomHist.RoomRequestIDHist = RoomRequestHist.RoomRequestIDHist ";	
			$res4 =& $db->query($sql4);
			if(PEAR::isError($res4))
			{
				die($res4->getMessage());
			}
			
			// If there are no results, return 'Any'
			if ($res4->numRows() == 0)
			{
				echo "<td>Any</td>";
			}
			else 
			{
				$roomArray = array();
				while ($row4 = $res4->fetchRow())
				{
					array_push($roomArray, $row4['room']);
				}
				echo "<td>";
				echo implode(", ", $roomArray);
				echo "</td>";
			}		
			
			// List facilities in one row instead of multiple	
			if ($facility == 'Any')
			{
				$sql2 = "SELECT Facility FROM Facility WHERE FacilityID IN ";
				$sql2 .= "(SELECT FacilityID FROM FacilityRequestHist WHERE RequestIDHist = ".$row["requestidhist"].")";
				
				$res2 =& $db->query($sql2);
				if(PEAR::isError($res2))
				{
					die($res2->getMessage());
				}
			}
			
			// If there are no results, return 'Any'
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
			$sql3 = "SELECT Weeks FROM WeekRequestHist WHERE RequestIDHist = ".$row["requestidhist"].";";
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
			echo "<td><input type='checkbox' id='requestCheckbox" . $row['requestidhist'] . "'/></td>";
			echo "</tr>";
		}
	}
	
	echo "</table>";
?>