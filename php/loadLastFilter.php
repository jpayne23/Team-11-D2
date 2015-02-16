<?php

	/* 
	File generates the filter menu dynamically depending on what information is
	stored in the tables for last year. i.e. The filter menu will only let you select a
	facility which is in at least one of the requests in the table.
	
	Implemented by Joe.
	
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
	
	session_start();
	$deptCode = $_SESSION['deptCode'];
	
	//Tells script what table the filter menu is being generated for
	$source = $_REQUEST['source'];
		
	$status = "Status != 'Pending'";
	
	echo "<input type='button' class='filterButton' value='Close me!' onclick='closeDiv(\"filterDivLast\");'></input>";
	
	//Gets all modules which are part of a request
	$sql = "SELECT ModCode, Title FROM Module WHERE ModCode IN (SELECT ModCode FROM RequestHist WHERE " . $status;
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode'))";
	
	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	
	echo "<table id='facilitiesTable'><tr><td>ModCode</td><td>";
	
	echo "<select name='modCode' id='modCodesFilter'>";
	
	echo '<option id ="Any">Any</option>';
	
	while ($row = $res->fetchRow())
	{
		echo '<option class="modCode" id ="'.$row["modcode"].'">' . $row["modcode"] . ' - '.$row["title"].'</option>';
	}
	echo "</select></td></tr>";
	
	echo "<tr><td>Session Type</td><td>";
	
	//Gets all session types which are part of a request
	$sql = "SELECT DISTINCT SessionType FROM RequestHist WHERE " . $status;
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
	
	//Gets all days which are part of a request
	$sql = "SELECT DISTINCT Day FROM DayInfo WHERE DayID IN (SELECT DayID FROM RequestHist WHERE " . $status;
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
	
	//Gets all facilities which are part of a request
	$sql = "SELECT DISTINCT Facility FROM Facility WHERE FacilityID IN (SELECT FacilityID FROM FacilityRequestHist WHERE ";
	$sql .= "RequestIDHist IN (SELECT RequestIDHist FROM RequestHist WHERE " . $status;
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')))";
	
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
	
	echo "</select></td></tr>";
	
	echo "<tr><td>Status</td><td>";
	
	//Gets all status' which are part of a request
	$sql = "SELECT DISTINCT Status FROM RequestHist WHERE " . $status;
	$sql .= " AND UserID = (SELECT UserID FROM Users WHERE DeptCode = '$deptCode')";
	
	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	echo '<select name="status" id="statusFilter">';
	
	echo '<option id ="Any">Any</option>';
	
	while ($row = $res->fetchRow())
	{
		echo '<option id ="'.$row["status"].'">' . $row["status"].'</option>';
	}
	echo "</select></td></tr>";
	
	echo "</table>";
	
	echo "<input type='button' class='filterButton' value='Filter Away!' onclick='filterTable(\"$source\");'></input>";
	
?>