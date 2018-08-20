<?php
include('../../classes/DB.php');

$tableName = $_GET["tableName"];
$field = $_GET["field"];
$val = $_GET["val"];
$whereField = $_GET["whereField"];
$whereVal = $_GET["whereVal"];

//echo "$tableName - $field - $val - $whereField - $whereVal";

$db = new DB();

$sql = "UPDATE $tableName SET $field=$val WHERE $whereField=$whereVal";

$db->query($sql);
?>
