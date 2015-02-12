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
	$deptCode = $_SESSION['deptCode'];
	
	//$part = $_REQUEST['part'];
	
	$add = " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode'))";
	
	$sql = "SELECT ModCode, Title FROM Module WHERE ModCode IN (SELECT ModCode FROM Request WHERE Status = 'Pending'";	
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode'))";
	
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
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')";

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
	echo "</select></td></tr>";
	
	echo "<tr><td>Day</td><td>";
	
	$sql = "SELECT DISTINCT Day FROM DayInfo WHERE DayID IN (SELECT DayID FROM Request WHERE Status='Pending'";
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode'))";
	
	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="day" id="dayFilter">';
	
	echo '<option id ="Any">Any</option>';
	
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["day"].'">' . $row["day"].'</option>';
	}
	echo "</select></td></tr>";
	
	echo "<tr><td>Facility</td><td>";
	
	$sql = "SELECT DISTINCT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequest WHERE ";
	$sql .= "RequestID IN (SELECT RequestID FROM Request WHERE Status='Pending' ";
	$sql .= "AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')))";
	
	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<select name='facilities' id='facilitiesFilter'>";
	
	echo '<option id ="Any">Any</option>';
	
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["facility"].'">' . $row["facility"].'</option>';
	}
	
	echo "</select></td></tr></table>";
	
	echo "<input type='button' value='Filter Away!' onclick='filterTable()'></input>";
	
?>