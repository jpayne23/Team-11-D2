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
	$sessionType = $_REQUEST['sessionType'];
	$sessionLength = $_REQUEST['sessionLength'];
	$sessionLength = (int)$sessionLength;
	$specialReq = $_REQUEST['specialReq'];
	
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
	$sql .= "SpecialRequirements = '$specialReq' ";
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
?>