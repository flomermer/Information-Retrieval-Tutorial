<?php
header('Content-Type: application/json');

include('../classes/IR.php');

$keyword = $_GET["keyword"];
$ir = new IR();

if(preg_match('/[*,&,|,)]/i',$keyword) or true)
    $result = $ir->searchBool($keyword);
else
    $result = $ir->search($keyword);

$json=array();
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json[] = array(
            'documentID'    =>      $rs['document_id'],
            'name'          =>      $rs['name'],
            'author'        =>      $rs['author'],
            'times'         =>      $rs['total_hits'],
            'wordsCounter'  =>      $rs['wordsCounter'],
            'words'         =>      $rs['words'],
            'content'       =>      $rs['content']
        );
    }
}

echo json_encode($json);
?>