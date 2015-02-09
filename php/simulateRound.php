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
	
	$sql="SELECT RequestID FROM Request WHERE UserID = ".$userID." AND Status = 'Submitted';";
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
		}
		else
		{
			$sql2 = "UPDATE Request SET Status = 'Unsuccessful' WHERE RequestID = ".$row['requestid'].";";
			$res2 =& $db->query($sql2);
			if(PEAR::isError($re2s))
			{
				die($res2->getMessage());
			}
		}
	}
?>