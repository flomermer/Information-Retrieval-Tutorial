<?php
include('../classes/IR.php');

$filename = $_GET["filename"];
$name = $_GET["name"];
$author = $_GET["author"];

$ir = new IR();

$document_id = $ir->indexDocument($name, $author, $filename);

echo $document_id;
?>