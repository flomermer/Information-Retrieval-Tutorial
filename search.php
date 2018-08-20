<?php include('classes/IR.php'); ?>
<?php
    header('Content-Type: application/json');

    $keyword = "let it be";
    $ir = new IR();

    $result = $ir->search("found sea");

    $json=array();
    if ($result->num_rows > 0) {
        while($rs = $result->fetch_assoc()) {
            $json[] = array(
                'documentID'    =>      $rs['document_id'],
                'name'          =>      $rs['name'],
                'author'        =>      $rs['author']
            );
        }
    }

    
    echo json_encode($json);
?>