<?php 
$input1 = $_REQUEST['array1'];
$input2 = $_REQUEST['array2'];

$array1 = json_decode($input1, true);
$array2 = json_decode($input2, true);


/*
$array1 = array("a" => 1, 3, 5, 9);
$array2 = array("b" => 4, 1, 3, 6, 8, 9);
*/
$res = (array_intersect($array1, $array2));
echo json_encode($res);
?>