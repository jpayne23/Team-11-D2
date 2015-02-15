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
	
	echo "<content><table><tr><td><label>Department Code</label></td><td>";
	
	$sql = "SELECT DeptCode, DeptName FROM DeptNames WHERE DeptCode = '".$deptCode."';";
	$res =& $db->query($sql);
	
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	while ($row = $res->fetchRow())
	{
		echo '<div id="deptCodeDiv" title="'.$row["deptcode"].'">'.$row["deptcode"].' - '.$row["deptname"].'</div>';
	}
	
	echo "</td></tr><tr><td><label>Part</label></td><td><div id='partAdhocDiv'></div></td></tr>";
	
	echo "<tr><td><label>Module Code</label></td><td><div id='modCodeAdhocDiv'></div></td></tr>";
		
	echo "<tr><td><label>Pick weeks</label></td><td colspan='2'><ol id='weekSelector'>";
	
	for ($i = 1; $i < 16; $i++)
	{
	
		if($i < 13)
		{
			echo "<li class='ui-state-default ui-selected'>$i</li>";
		}
		else
		{
			echo "<li class='ui-state-default'>$i</li>";
		}
	
	}
	
	echo "</ol></br></td></tr><tr><td><label>Session Type</label></td><td><select id='seshType'>";
					
	echo "<option>Feedback</option><option>Lecture</option><option>Practical</option>";
	
	echo "<option>Seminar</option><option>Tutorial</option></select></td></tr><tr><td>";
	
	echo "<label> Session Length </label></td><td><select id='seshLength'><option>1 Hour</option>";
	
	echo "<option>2 Hours</option><option>3 Hours</option><option>4 Hours</option><option>5 Hours</option>";
				
	echo "</select></td></tr><tr><td><label> Day </label></td><td><select id='day'><option>Monday</option>";
	
	echo "<option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option>";
	
	echo "</select></td></tr><tr><td><label> Start Time </label></td><td><select id='time'>";
	
	for ($i = 9; $i < 18; $i++)
	{
	
		echo "<option>0$i:00</option>";
	
	}

	echo "</select></td></tr><tr><td>Special Requirements:</td><td><textarea id='specialReq' cols='40' rows='3'></textarea>";

	echo "</td></tr><tr><td><label> Pick Date </label></td><td><input type='text' id='date'></td></tr>";

	echo "</content>";
?>