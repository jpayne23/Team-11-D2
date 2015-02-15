<?php 
	require_once 'MDB2.php';			
	include "/disks/diskh/teams/team11/passwords/password.php";
	$dsn = "mysql://$username:$password@$host/$dbName"; 
	$db =& MDB2::connect($dsn); 
	if(PEAR::isError($db)){ 
		die($db->getMessage());
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	$username1 = $_POST["username"];
	$password1 = $_POST["password"];
	$access = $_POST["access"];
	$username1 = stripslashes($username1);
	$password1 = stripslashes($password1);
	$username1 = mysql_real_escape_string($username1);
	$password1 = mysql_real_escape_string($password1);
	$sql = "SELECT `DeptCode` FROM `Users` WHERE `UserName` = '".$username1."' and `Password` = '".$password1."' ";			
	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	if($res -> numRows() == 0)
		echo "error";
	else
	{
		while ($row = $res->fetchRow())
		{
			session_start();
			$_SESSION["deptCode"] = $row["deptcode"];
			if($access == "on")
			{
				$_SESSION["access"] = "yes";
			}
			else
			{
				$_SESSION["access"] = "no";
			}
			header("location:../homepage.php");
		}
	}
?>