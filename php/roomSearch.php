<?php 
	/*
	A php script that will find a room using the facilities and room capacity as 
	search criteria. It takes in the facilities selected in the html and the 
	capacity entered and will return an array containing all rooms that match the 
	criteria. 
	Written by Prakash and Bhavnit.
	*/
	
	// Setting up connecting to the database
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	$ticked = $_REQUEST['tickedfac'];
	//raw facilities entry from html
	$groupsize = $_REQUEST['groupSize'];
	//groupsize is validated to be numeric
	if(!is_numeric($groupsize))
	{
		echo "notNumeric";
		exit;
	}
	
	$facilities = explode(',', $ticked);
	
	
	$fac = "(";
	for($i=0;$i<sizeof($facilities)-1;$i++)
	{
		$fac.= "'";
		
		$sql = "";
		$sql .= "select FacilityID from Facility where Facility like '".$facilities[$i]."';";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die($res->getMessage().'first');
		}
		while ($row = $res->fetchRow())
		{
			$fac .= $row["facilityid"];
		}
		
		$fac.= "', ";
	}
	$fac.= "'";
	$sql = "";
	$sql .= "select FacilityID from Facility where Facility = '".$facilities[sizeof($facilities)-1]."';";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage().'second');
	}
	while ($row = $res->fetchRow())
	{
		$fac .= $row["facilityid"];
	}
	$fac .= "')";
	//list of facilities is converted into a useful format
	$sql = "";
	$sql .= "select distinct f.Room ";
	$sql .= "from `RoomFacilities` f ";
	$sql .= "where f.Facility in ".$fac." "; //e.g. $fac = ('1', '2')
	$sql .= "and f.Room in (select Room from Room where Capacity >= ".$groupsize.") ";
	$sql .= "group by f.Room;";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage().'third');
	}
	$ar = array();
	while ($row = $res->fetchRow())
	{
		$ar[sizeof($ar)] = $row['room'];
	}//run query
	//return results array to the javascript
	echo json_encode($ar);
?>