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
	
	$roomNo = $_REQUEST['roomNo'];
	//echo $roomNo;
	$sql = "SELECT Facility FROM Facility;";	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	$facList = $res->fetchAll();
	$sql = "SELECT Facility FROM RoomFacilities WHERE Room = '".$roomNo."' ;";	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	$sql = "SELECT * FROM Room WHERE Room = '".$roomNo."' ;";	
	$res2 =& $db->query($sql);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage());
	}
	
	while ($row = $res2->fetchRow())
	{
		echo "<b>Capacity:</b>".$row["capacity"]."</br>";
		if ($row["lab"] == 0)
		{
			echo "<b>Type:</b> Lecture Room";
		}
		else
		{
			echo "<b>Type:</b> Lab";
		}
		echo "</br>";
		echo "<img src='".$row['url']."' alt='http://co-project.lboro.ac.uk/team11/Prakash/img/noImg.jpg' height='150' width='150'>";
		echo "</br>";
	}
	echo "<b>Facilities:<b> </br>";
	while ($row = $res->fetchRow())
	{
		echo $facList[$row["facility"]-1]["facility"]."</br>";
	}
?>