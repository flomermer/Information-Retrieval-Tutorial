<!DOCTYPE html>
<html>
    <head>
        <title>Information Retrieval - ADMIN</title>

        <!-- Jquery CDN-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <!-- Bootstrap CSS -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
        <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>

        <script src="includes/admin.js"></script>
        <link rel="stylesheet" type="text/css" href="includes/style.css">
    </head>
    <body>
        <main class="container">
            <div class="page-header">
                <div class="row">
                    <div class="col-10">
                        <h1>I.R Task - ADMIN</h1>
                    </div>
                    <div class="col-2 text-right">
                        <button class="btn btn-lg" id="btnUpload">New Document  <i class="fas fa-plus"></i></button>
                        <input type="file" name="file" id="inputUpload" />
                    </div>
                </div>
                <br />
            </div>
            <div id="documentsList">
                <?php
                include('classes/DB.php');
                $db = new DB();

                $result = $db->query("SELECT * FROM documents ORDER BY document_id ASC");
                while($rs = $result->fetch_assoc()){
                    $document_id = $rs["document_id"];
                    $name = $rs["name"];
                    $author = $rs["author"];
                    $filename = $rs["filename"];
                    $isHidden = $rs["isHidden"];

                    echo "<div class='document' data-id='$document_id' data-name='$name' data-author='$author' data-hidden='$isHidden' data-filename='$filename'>
                            $document_id. $name - $author - $filename
                         </div>";
                }
                ?>
            </div>
        </main>

        <div class="modal fade" id="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <label></label>
                        <span id="eye"><i class="fas fa-2x"></i></span>
                    </div>
                    <div class="modal-body">
                        <div class="content">
                            <form class="form" id="formDocument" method="post">
                                <input type="hidden" name="documentID" />
                                <input type="hidden" name="filename" />
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" placeholder="File Name..." required />
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="author" placeholder="Author" required />
                                </div>
                                <div class="form-group text-center">
                                    <button class="btn btn-success btn-submit"></button>
                                    <i class="loading fas fa-spinner fa-pulse fa-3x"></i>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer fixed-bottom text-center">
            Tomer Flom - 303015671 &nbsp; & &nbsp; Adiel Perez - 308101062
        </footer>
    </body>
</html>
