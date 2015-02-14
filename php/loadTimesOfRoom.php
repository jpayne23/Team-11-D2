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

	$sql = "SELECT RequestIDHist, DayID, PeriodID, SessionLength FROM `RequestHist` where RequestIDHist in
			(select RequestIDHist from RequestToRoomHist where RoomRequestIDHist in
			(select RoomRequestIDHist from RoomRequestHist where Room = '".$roomNo."'));";
			
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	$sql = "select RequestIDHist, Weeks from WeekRequestHist where RequestIDHist in(
			SELECT RequestIDHist FROM RequestHist where RequestIDHist in
			(select RequestIDHist from RequestToRoomHist where RoomRequestIDHist in
			(select RoomRequestIDHist from RoomRequestHist where Room = '".$roomNo."')));";

	
	
	$res2 =& $db->query($sql);
	if(PEAR::isError($res2))
	{
		die($res2->getMessage());
	}
	
	$a = array();
	$ar = array(array());
	$arr = array();
	while ($row = $res2->fetchRow())
	{
		//add the weeks for each request to an array
		$a[sizeof($a)] = $row["requestidhist"];
		$a[sizeof($a)] = $row["weeks"];
	}
	
	while ($row = $res->fetchRow())
	{
		$weeks = "";
		for($i = 0;$i<sizeof($a);$i=$i+2)
		{
			if($a[$i] == $row["requestidhist"]){
				$weeks .= ":".$a[$i+1];
			}
		}
		$id = 'd'.$row["dayid"].'p'.$row["periodid"];
		
		$ar['id'] = $id;
		$ar['data-selected'] = '1';
		$ar['data-length'] = $row["sessionlength"];
		$ar['data-weeks'] = $weeks;
		$ar['data-available'] = '';
		$ar['data-display'] = '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]';
		$ar['class'] = 'timeslotbooked';
		$arr[sizeof($arr)] = $ar;
		
		
		//echo "<td id='".$id."' data-selected='1' data-length='".$row["sessionlength"]."' data-weeks='".$weeks."' data-available='' class='timeslotbooked'>Available</td>,,"; 
	}
	echo json_encode($arr);
?>