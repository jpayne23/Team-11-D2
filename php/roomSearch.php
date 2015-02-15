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
	
	$facilities = $_POST['facilities'];
	$groupsize = $_POST['groupsize'];
	
	$fac = "(";
	for($i=0;$i<sizeof($facilities)-1;$i++)
	{
		$fac.= "'";
		
		$sql = "";
		$sql .= "select FacilityID from Facility where Facility like '".$facilities[$i]."';";
		$res =& $db->query($sql);
		if(PEAR::isError($res))
		{
			die($res->getMessage());
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
		die($res->getMessage());
	}
	while ($row = $res->fetchRow())
	{
		$fac .= $row["facilityid"];
	}
	$fac .= "')";
	
	$sql = "";
	$sql .= "select distinct f.room ";
	$sql .= "from `RoomFacilities` f ";
	$sql .= "where f.facility in $fac "; //e.g. $fac = ('1', '2')
	$sql .= "and f.room in(select room from Room where capacity >= $groupsize) ";
	$sql .= "group by f.room;";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	while ($row = $res->fetchRow())
	{
		print_r($row);
	}
?>