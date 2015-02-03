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
	
	$modCode = $_REQUEST['modCode'];
	$selectedWeeks = $_REQUEST['selectedWeeks'];
	$sessionType = $_REQUEST['sessionType'];
	$sessionLength = $_REQUEST['sessionLength'];
	$specialReq = "";
	
	// Convert the selected weeks to the database weeks format
	$weeksArray = explode(",", $selectedWeeks);
	for ($i = 0; $i < count($weeksArray); $i++)
	{
		$dashPos = strpos($weeksArray[$i], "-");	// Find position of dash
		
		if ($dashPos === false)		// If dash is not found, then the entry is a single week
		{
			$weeksArray[$i] = $weeksArray[$i] . "-" . $weeksArray[$i];
		}
	}		
	print_r($weeksArray);
	
	/*$sql = "INSERT INTO Request (UserID,ModCode,SessionType,SessionLength,PriorityRequest,AdhocRequest,SpecialRequirements,RoundID,Status) ";
	$sql .= "VALUES (2," . $modCode . "," . $sessionType . "," . $sessionLength . ",1,0,". $specialReq .",1,Submitted)";
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}	
	
	echo $sql;*/
?>