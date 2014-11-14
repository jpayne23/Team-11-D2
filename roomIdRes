<?php
//Connect to MySQL Server
require_once 'MDB2.php';
$username1 = 'team11';
$password1 = 'bcx94daw';
$host='co-project.lboro.ac.uk';
$dbName='team11';
$dsn = "mysql://$username1:$password1@$host/$dbName"; 
$db =& MDB2::connect($dsn); 
if(PEAR::isError($db)) 
	die($db->getMessage());
$db->setFetchMode(MDB2_FETCHMODE_ASSOC);


$capacity = $_GET['capacity'];
$sort = $_GET['sort'];
$input = $_GET['park'];
if ($input == "Any")
{
	$sql="SELECT * FROM Rooms WHERE capacity >= ".$capacity;
}
else
{
	$park = substr($input, 0, 1);
	$sql="SELECT * FROM Rooms WHERE park = '".$park."' and capacity > ".$capacity;
}
if ($sort == "true")
	$sql .= " order by -capacity;";
else
	$sql .= ";";
$res =& $db->query($sql);
if(PEAR::isError($res))
	die($res->getMessage());

$headings = array('roomid', 'capacity', 'park', 'lab');
echo '<table border = 1 >';
echo '<tr>';
echo '<th> Room Id </th>';
echo '<th> Capacity </th>';
echo '<th> Park </th>';
echo '<th> Lab </th>';
echo '</tr>';
while ($row = $res->fetchRow())
{
	/*echo "<pre>";
	print_r($row);
	echo "</pre>";*/
	echo '<tr>';
	for ($i=0;$i<4;$i++)
		echo '<td>'.$row[$headings[$i]].'</td>';
	echo '</tr>';
}
echo '</table>';
?>
