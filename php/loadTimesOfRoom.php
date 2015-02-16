<?php
/*
This script is called from requestscript.js --> fillTimetable() and its purpose
is to return a list of requests that have booked a room, including their days
and times and weeks booked.

Contributions by Bhavnit
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
	
	$roomNo = $_REQUEST['roomNo']; //Bring in the chosen room number

	//query to get the list of requests that match the room
	$sql= "SELECT a.RequestID, a.DayID, a.PeriodID, SessionLength FROM AllocatedRooms a join Request on Request.RequestID = a.RequestID where a.Room = '".$roomNo."'";
			
			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	//query to use each request from the above query and find the list of weeks
	//that it has been booked for.
	$sql = "select WeekRequest.RequestID, Weeks from WeekRequest where WeekRequest.RequestID in(
			SELECT a.RequestID FROM AllocatedRooms a join Request on Request.RequestID = a.RequestID where a.Room = '".$roomNo."')";
			

	
	
	$res2 =& $db->query($sql);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage()); //output the error message and end script
	}
	
	//local arrays for the booked weeks
	$a = array();
	$ar = array();
	$arr = array();
	while ($row = $res2->fetchRow())
	{
		//add the weeks for each request to an array
		$a[sizeof($a)] = $row["requestid"];
		$a[sizeof($a)] = $row["weeks"];
	}
	
	while ($row = $res->fetchRow()) //loop through each result
	{
		$weeks = "";
		for($i = 0;$i<sizeof($a);$i=$i+2)
		{
			if($a[$i] == $row["requestid"]){
				$weeks .= ":".$a[$i+1]; //append the weeks with a colon
			}
		}
		//create the id to be used in javascript
		$id = 'd'.$row["dayid"].'p'.$row["periodid"];
		
		//assign the attributes of the associative array
		$ar['id'] = $id;
		$ar['data-selected'] = '1';
		$ar['data-length'] = $row["sessionlength"];
		$ar['data-weeks'] = $weeks;
		$ar['data-available'] = '';
		$ar['data-display'] = '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]';
		$ar['class'] = 'timeslotbooked';
		$arr[sizeof($arr)] = $ar; //push the contents to the array
	}
	echo json_encode($arr); //return the encoded array to be used for the 
	//timetable
?>