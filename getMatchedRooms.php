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
	$f = json_decode($input,true);
	$fac = "(";
	for($i=0;$i<sizeof($f) - 1;$i++)
	{
		$fac.= "'" .$f[$i]. "', ";
	}
	$fac.= "'" .$f[sizeof($f)-1]. "')";
	

	$sql = "";
	/*
	for ($j = 0; $j < sizeof($f)-1; $j++){
		$sql .= "SELECT `Room` FROM `Facilities` WHERE `Facility` = '". $f[0]. "' UNION ";

	}
	$sql .= "SELECT `Room` FROM `Facilities` WHERE `Facility` = '".$f[sizeof($f)-1]."' ";
    $sql .= "group by `Room` having count(distinct `Room`) = ".sizeof($f).";";
	*/
    
    $sql .= "select f.room ";
    $sql .= "from `Facilities` f ";
   $sql .= "where f.facility in ". $fac ." ";
   $sql .= "group by f.room ";
   $sql .= "having count(distinct f.facility) = ". sizeof($f) .";";
    
    

	
	
	$res =& $db->query($sql);
	if(PEAR::isError($res))
	{
		die($res->getMessage());
	}
	$i = 0;
	while ($row = $res->fetchRow())
	{
		/*echo "<pre>";
		print_r($row);
		echo "</pre>";*/
		//echo '<input type="checkbox" id="c'.$i.'" name="'.$row["facility"].'" value="'.$row["facility"].'">'.$row["facility"].'</input></br>';
		echo $row["room"]."</br>";
		$i++;
	}
?>