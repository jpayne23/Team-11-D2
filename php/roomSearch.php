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
	
	$ticked = $_REQUEST['tickedfac'];
	$groupsize = $_REQUEST['groupSize'];
	
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
	}
	echo json_encode($ar);
?>