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
	
	$sql="SELECT UserID FROM Users WHERE DeptCode = '".$_SESSION["deptCode"]."';";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	$userID;
	while ($row = $res->fetchRow())
	{
		$userID = $row["userid"];
	}
	
	$sql="SELECT RequestID FROM Request WHERE UserID = ".$userID." AND Status = 'Submitted' AND AdhocRequest = '0'";
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	while ($row = $res->fetchRow())
	{
		$passfail = rand(0,1);
		if ($passfail>=0.5)
		{
			$sql2 = "UPDATE Request SET Status = 'Successful' WHERE RequestID = ".$row['requestid'].";";
			
			$res2 =& $db->query($sql2);
			if(PEAR::isError($res2))
			{
				die($res2->getMessage());
			}
		
			$rooms = array();
			$dayID = 0;
			$periodID = 0;
			
			$sql3 = "SELECT Room , DayID, PeriodID FROM Request JOIN RequestToRoom ON RequestToRoom.RequestID = Request.RequestID ";
			$sql3 .= "JOIN RoomRequest ON RoomRequest.RoomRequestID = RequestToRoom.RoomRequestID ";
			$sql3 .= "WHERE Request.RequestID = '".$row['requestid']."'";
			
			
			$res3 =& $db->query($sql3);
			if(PEAR::isError($res3))
			{
				die($res3->getMessage());
			}
			
			if ($res3->numRows() > 0)
			{
				while ($row3 = $res3->fetchRow())
				{
					$rooms[count($rooms)] = $row3['room'];
					$dayID = $row3['dayid'];
					$periodID = $row3['periodid'];
					
				}
				
				for ($i=0;$i<count($rooms);$i++)
				{
					$sql4 = "INSERT INTO AllocatedRooms (RequestID,Room,DayID,PeriodID) VALUES ('". $row['requestid'] ."','" . $rooms[$i] . "','" . $dayID . "','" . $periodID . "');";
					
					$res4 =& $db->query($sql4);
					if(PEAR::isError($res4))
					{
						die($res4->getMessage());
					}
				}
			}
		}
		else
		{
			$sql2 = "UPDATE Request SET Status = 'Unsuccessful' WHERE RequestID = ".$row['requestid']."";
			$res2 =& $db->query($sql2);
			if(PEAR::isError($res2))
			{
				die($res2->getMessage());
			}
		}
	}
?>