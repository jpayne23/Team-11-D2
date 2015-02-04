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
	$selectedWeeks = $_REQUEST['selectedWeeks'];
	$facilities = $_REQUEST['facilities'];
	$sessionType = $_REQUEST['sessionType'];
	$sessionLength = $_REQUEST['sessionLength'];
	$sessionLength = (int)$sessionLength;
	$specialReq = "a";
		
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
	
	$sql = "INSERT INTO Request (UserID,ModCode,SessionType,SessionLength,DayID, PeriodID,PriorityRequest,AdhocRequest,SpecialRequirements,RoundID,Status) ";
	$sql .= "VALUES ((SELECT UserID FROM Users WHERE DeptCode = '$deptCode'),'$modCode','$sessionType',$sessionLength,1,1,1,0,'',1,'Pending')";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}	

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
	
	for ($l = 0; $l < count($facilities); $l++)
	{
		if ($facilities != "null")
		{
			$sql3 = "INSERT INTO FacilityRequest (RequestID, Facility) ";
			$sql3 .= "VALUES ((SELECT MAX(RequestID) From Request), '$facilities[$l]')"; 

			$res3 =& $db->query($sql3);
			if(PEAR::isError($res3))
			{
				die($res3->getMessage());
			}
		}
	}
?>