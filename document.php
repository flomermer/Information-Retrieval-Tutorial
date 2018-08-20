<?php include('classes/DB.php');?>
<?php
$document_id = $_GET["document_id"];
$words = $_GET["words"];

$db = new DB();
$result = $db->query("SELECT * FROM documents WHERE document_id=$document_id");
$rs = $result->fetch_assoc();

$filename = $rs["filename"];
$docName = $rs["name"];
$author = $rs["author"];

$file = fopen("documents/$filename", "r") or die("Unable to open file!");
echo "<h2>$docName - $author</h2>";
echo "<div id='document'>";
    while(!feof($file)) {
        echo fgets($file) . "<br>";
    }
echo "</div>";
fclose($file);
?>
<style>
    .highlight{color:red;}
</style>

<input type="hidden" id="words" value="<?php echo $words?>" />
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="includes/plugins/highlight/highlight.js"></script>
<script src="includes/document.js"></script>
