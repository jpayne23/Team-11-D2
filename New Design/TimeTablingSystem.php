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
			$deptCode = $row["deptcode"];
			
			$url = "http://co-project.lboro.ac.uk/cgpw/teamproject/website/Current/homepage.php";
			$fields = array
			(
				'deptCode' => $deptCode
			);
			$postvars = http_build_query($fields);
			
			// build the urlencoded data
			$postvars = http_build_query($fields);

			// open connection
			$ch = curl_init();

			// set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
			// execute post
			$result = curl_exec($ch);

			// close connection
			curl_close($ch);
		}
	}
?>