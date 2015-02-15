<?php

	function returnBookedWeeks($start,$end) //return the booked weeks as an inclusive string
	{
		$str = "";
		$intstart = intval($start); //convert given numbers into integers
		$intend = intval($end);
		
		for($i = $intstart; $i <= $intend; $i++){
			$str .= $i . ",";
		}
		return $str;
	}

	function getWeeks($room,$dayID,$periodID,$semester) //get the booked weeks for a speific room, day and time
	{
		require_once 'MDB2.php';			
		include "/disks/diskh/teams/team11/passwords/password.php";
		$dsn = "mysql://$username:$password@$host/$dbName"; 
		$db =& MDB2::connect($dsn); 
		if(PEAR::isError($db)){ 
			die($db->getMessage());
		}
		$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
		
		$sql = "SELECT DISTINCT WeekRequest.Weeks FROM WeekRequest ";
		$sql .= "JOIN Request ON Request.RequestID = WeekRequest.RequestID ";
		$sql .= "JOIN AllocatedRooms ON AllocatedRooms.RequestID = WeekRequest.RequestID ";
		$sql .= "WHERE AllocatedRooms.Room = '" . $room . "' AND Request.DayID = '". $dayID ."' ";
		$sql .= "AND Request.PeriodID = '". $periodID ."' AND Semester = $semester";

		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die($res->getMessage()." Function");
		}
		
		$a = array();
		
		while ($row = $res->fetchRow())
		{
			//add the weeks for each request to an array
			$a[sizeof($a)] = $row["weeks"]; 
		}
		
		$str ="";
		for($i=0;$i<count($a);$i++)
		{
			$len = strlen($a[$i]);
			switch($len){ //return the booked weeks as a string depending on the length of the string
				case 5:
					$str .= returnBookedWeeks(substr($a[$i],1,1),substr($a[$i],3,1));
					break;
				case 6:
					if($a[$i].substr(2,1) == ","){
						$str .= returnBookedWeeks(substr($a[$i],1,1),substr($a[$i],3,2));
					} else{
						$str .= returnBookedWeeks(substr($a[$i],1,2),substr($a[$i],4,1));
					}
					break;
				case 7:
					$str .= returnBookedWeeks(substr($a[$i],1,2), substr($a[$i],3,2));
					break;
			} //end switch
		}
		$weeks = explode(",",$str); //turn comma delimited string into an array
		
		return $weeks;
	}


	
	// Setting up connecting to the database
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	//load variables
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
	$semester = $_REQUEST['semester'];
	$adhoc = $_REQUEST['adhoc'];
	$priority = $_REQUEST['priority'];
	
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
	
	$sql = "INSERT INTO Request (UserID,ModCode,SessionType,SessionLength,DayID,PeriodID,PriorityRequest,AdhocRequest,SpecialRequirements,Semester,RoundID,Status) ";
	$sql .= "VALUES ((SELECT UserID FROM Users WHERE DeptCode = '$deptCode'),'$modCode','$sessionType',$sessionLength,$day,$time,$priority,$adhoc,'$specialReq',$semester,$round,'Pending')";
	
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
	
	$match = false;
	
	if($adhoc == 1){ //instant feedback of an adhoc request		
		
		if($rooms != "null")
		{
			$sql = "SELECT Room, AllocatedRooms.DayID, AllocatedRooms.PeriodID, SessionLength FROM AllocatedRooms JOIN Request ON AllocatedRooms.RequestID = Request.RequestID";
			$sql .= " WHERE Semester = $semester";
			
			$res =& $db->query($sql);
			if(PEAR::isError($res))
			{
				die($res->getMessage() . "1st");
			}
			while ($row = $res->fetchRow())
			{
				//check a match for the day and time in the allocated rooms table
				if($row['dayid'] == $day && ($row['periodid'] + $row['sessionlength'] - 1) == $time)
				{
					for($i = 0; $i < count($rooms); $i++) //loop through each room found
					{
						if($rooms[$i] == $row['room'])
						{
							//return array of weeks booked for a given room, day and time
							$taken = getWeeks($row['room'],$row['dayid'],($row['periodid'] + $row['sessionlength'] - 1), $semester);
							
							for($j=0;$j<count($taken);$j++)
							{
								//check any week in the booked weeks matches the given ad hoc week
								if($taken[$j] == $selectedWeeks)
								{
									$match = true;
									break;
								}
							}
						}
					}
				}
			}
			
			if(!$match) //run this if there is no match i.e. the room is free for an ad hoc booking
			{
				for($i = 0; $i < count($rooms); $i++)
				{
					$sql = "INSERT INTO AllocatedRooms (RequestID,Room,DayID,PeriodID) ";
					$sql .= "VALUES ((SELECT MAX(RequestID) FROM Request),'$rooms[$i]','$day', '$time')";

					$res =& $db->query($sql);
					if(PEAR::isError($res))
					{
						die($res->getMessage() . "2nd");
					}
				}
				
				$sql = "UPDATE Request SET Status = 'Successful' WHERE RequestID = (SELECT MAX(RequestID) FROM WeekRequest)";
				
				$res =& $db->query($sql);
				if(PEAR::isError($res))
				{
					die($res->getMessage()."3rd");
				}
				
				echo "Request Successful";
			}
			else //else if the room isnt available at given day and time, alert the user that the request is unsuccessful
			{
				$sql = "UPDATE Request SET Status = 'Unsuccessful' WHERE RequestID = (SELECT MAX(RequestID) FROM WeekRequest)";
				
				$res =& $db->query($sql);
				if(PEAR::isError($res))
				{
					die($res->getMessage()."4th");
				}
				
				echo "Request Unsuccessful";
			}
		}
		else
		{
			$sql = "UPDATE Request SET Status = 'Successful' WHERE RequestID = (SELECT MAX(RequestID) FROM WeekRequest)";
			
			$res =& $db->query($sql);
			if(PEAR::isError($res))
			{
				die($res->getMessage()."5th");
			}
			
			echo "Request Successful";
		}
	}
?>