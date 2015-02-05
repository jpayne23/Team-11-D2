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
	
	/*$deptCode = $_REQUEST['deptCode'];
	$part = $_REQUEST['part'];
	*/
	
	$sql = "SELECT ModCode, Title FROM Module WHERE ModCode IN (SELECT ModCode FROM Request WHERE Status = 'Pending')";	

	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<input type='button' value='Close me!' onclick='closeDiv(\"filterDiv\");'></input>";
	
	echo "<table id='facilitiesTable'><tr><td>ModCode</td><td>";
	
	echo "<select name='modCode' id='modCodesFilter'>";
	
	echo '<option id ="Any">Any</option>';
	
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["modcode"].'">' . $row["modcode"] . ' - '.$row["title"].'</option>';
	}
	echo "</select></td></tr>";
	
	echo "<tr><td>Session Type</td><td>";
	
	$sql = "SELECT DISTINCT SessionType FROM Request WHERE Status = 'Pending'";	

	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="sessionType" id="sessionTypeFilter">';
	
	echo '<option id ="Any">Any</option>';
	
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["sessiontype"].'">' . $row["sessiontype"].'</option>';
	}
	echo "</select></td></tr></table>";
	
	echo "<input type='button' value='Filter Away!' onclick='filterTable()'></input>";
	
?>