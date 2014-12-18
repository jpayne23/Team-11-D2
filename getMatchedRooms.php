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
	
	$input = $_REQUEST['f'];
	$f = json_decode($input,true); //decode back into a PHP array
	
	//Make a string of the facilities to use in SQL Query
	$fac = "(";
	for($i=0;$i<sizeof($f) - 1;$i++)
	{
		$fac.= "'" .$f[$i]. "', ";
	}
	$fac.= "'" .$f[sizeof($f)-1]. "')";
	

	$sql = "";
    $sql .= "select f.room ";
    $sql .= "from `Facilities` f ";
   $sql .= "where f.facility in ". $fac ." "; //e.g. $fac = ('Computer', 'DVD Player')
   $sql .= "group by f.room ";
   $sql .= "having count(distinct f.facility) = ". sizeof($f) .";"; //$f = no. of facilities
    
    

	
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	$i = 0;
	while ($row = $res->fetchRow())
	{
		echo $row["room"]."</br>";
		$i++;
	}
?>