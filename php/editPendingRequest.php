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
	$requestID = $_REQUEST['requestID'];
	$modCode = $_REQUEST['modCode'];
	$selectedWeeks = $_REQUEST['selectedWeeks'];
	$facilities = $_REQUEST['facilities'];
	$sessionType = $_REQUEST['sessionType'];
	$sessionLength = $_REQUEST['sessionLength'];
	$sessionLength = (int)$sessionLength;
	$specialReq = $_REQUEST['specialReq'];
	$day = $_REQUEST['day'];
	$time = $_REQUEST['time'];
	
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
	
	$sql = "UPDATE Request ";
	$sql .= "SET ModCode = '$modCode', SessionType = '$sessionType', SessionLength = $sessionLength, ";
	$sql .= "DayID = $day, PeriodID = $time, SpecialRequirements = '$specialReq' ";
	$sql .= "WHERE RequestID = $requestID";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	// Delete the weeks currently in the table
	$sql2 = "DELETE FROM WeekRequest ";
	$sql2 .= "WHERE RequestID = $requestID";
	
	$res2 =& $db->query($sql2);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage());
	}
	
	// Insert new weeks back in
	for ($k = 0; $k < count($weeksArray); $k++)
	{
		$sql3 = "INSERT INTO WeekRequest (RequestID, Weeks) ";
		$sql3 .= "VALUES ($requestID, '$weeksArray[$k]')"; 

		$res3 =& $db->query($sql3);
		if(PEAR::isError($res3))
		{
			die($res3->getMessage());
		}
	}
	
	// Get FacilityID from the submitted facility names
	$facilityIDs = array();
	for ($l = 0; $l < count($facilities); $l++)
	{
		if ($facilities != "null")
		{			
			$sql4 = "SELECT FacilityID FROM Facility WHERE Facility = '$facilities[$l]'";
			
			$res4 =& $db->query($sql4);
			if(PEAR::isError($res4))
			{
				die($res4->getMessage());
			}
			
			while ($row4 = $res4->fetchRow())
			{
				array_push($facilityIDs, $row4['facilityid']);
			}			
		}
	}	
	
	// Delete the facilities currently in the table
	$sql5 = "DELETE FROM FacilityRequest ";
	$sql5 .= "WHERE RequestID = $requestID";
	
	$res5 =& $db->query($sql5);
	if(PEAR::isError($res5))
	{
		die($res5->getMessage());
	}
	
	// Add selected facilities to the database
	for ($m = 0; $m < count($facilityIDs); $m++)
	{
		$sql6 = "INSERT INTO FacilityRequest (RequestID, FacilityID) ";
		$sql6 .= "VALUES ($requestID, '$facilityIDs[$m]')"; 	

		$res6 =& $db->query($sql6);
		if(PEAR::isError($res6))
		{
			die($res6->getMessage());
		}
	}
?>