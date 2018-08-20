<?php include('DB.php'); ?>
<?php
class IR{
    private $DB;
    function __construct(){
        $this->DB = new DB();
    }
    function initializeDB(){
        $this->DB->initialize();
    }
    private function isStopList($word){
        $db = $this->DB;
        $result = $db->query("SELECT * FROM stop_list WHERE word='$word'");
        if($result->num_rows>0)
            return true;
        return false;
    }

    function indexDocument($docName,$docAuthor,$filename){
        $fileContent = strtolower(file_get_contents("../documents/$filename"));
        $fileContent = preg_replace('/[^a-z\s]/i', '',$fileContent); //removes all characters which not letters from word

        $contentArr = preg_split('/\s+/', $fileContent);

        if($fileContent=='' || is_null($fileContent)){
            echo "cannot index $filename. file not exists or is empty<BR><BR>";
            return false;
        }

        $db = $this->DB;
        //insert new document if not exists
        $result = $db->query("SELECT * FROM documents WHERE filename='$filename'");
        if($result->num_rows==0){
            $document_id = $db->query("INSERT INTO documents (name,author,filename) VALUES ('$docName','$docAuthor','$filename')");
        } else {
            $rs = $result->fetch_assoc();
            $document_id = $rs['document_id'];
            $db->query("UPDATE documents SET name='$docName', author='$docAuthor' WHERE document_id=$document_id");
            $db->query("DELETE FROM word_in_document WHERE document_id=$document_id");
        }

        $pos=0;
        foreach($contentArr as $word){
            if($word=='' || strlen($word)<1)
                continue;

            //INSERT word TO words (only if not exists yet)
            $sql = "INSERT IGNORE INTO words (word) VALUES ('$word')";
            $db->query($sql);

            //index document,word in word_in_document
            $sql = "INSERT INTO word_in_document (word,document_id,pos) VALUES ('$word',$document_id,$pos)";
            $db->query($sql);
            $pos++;
        }

        return $document_id;
    }

    function search($keyword){

        /*
         * doubleQuates: search also on stopWords. return all records which contains ALL the words(AND)
         * regular: ignore stopWords. return all records which contain ONE word at least (OR)
         */
        $db = $this->DB;

        $isExact=false;
        if($keyword[0]=='"' && $keyword[strlen($keyword)-1]=='"'){ //exact phrase with quates
            $keyword = str_replace('"', "", $keyword);
            $isExact = true;
        }

        $words   = preg_split('/\s+/', $keyword);
        $total_words = count($words);

        $sqlWords = "";

        for($i=0;$i<$total_words;$i++){
            $word = $words[$i];

            $isWildCard=false;

            if($word[strlen($word)-1]=='*'){ //wildcard.
                $word = str_replace('*','',$word);
                $isWildCard = true;
            }
            if($isExact==false){ //stopList skip
                $result = $db->query("SELECT word FROM stop_list WHERE word='$word'");
                if($result->num_rows>0){
                    continue;
                }
            }

            if($isWildCard){
                $sqlWords .= "word LIKE '$word%'";
            } else {
                $sqlWords .= "word='$word'";
            }
            $sqlWords .= " or ";
        }
        $sqlWords = substr($sqlWords, 0, -4); //remove ' or ' - 4 characters
        if($isExact)
            $sqlOnlyExact = "AND wordsCounter>=$total_words";

        $sql = "
                SELECT t.* FROM
                    (SELECT document_id, docs.name, docs.author, docs.isHidden,
                            count(distinct word) as wordsCounter, GROUP_CONCAT(distinct word) as words,
                            count(word) as total_hits, GROUP_CONCAT(pos,'@@@',word separator '$$$') as positions
                    FROM word_in_document
                    NATURAL JOIN documents docs
                    WHERE ($sqlWords)
                    GROUP BY document_id) AS t
                WHERE isHidden=0 $sqlOnlyExact
                ORDER BY wordsCounter DESC, total_hits DESC
               ";

        return $db->query($sql);
    }

    function searchBool($keyword){
        global $db; $db = $this->DB;

        $keyword = str_replace(' ','',$keyword);

        global $_filter, $word, $words, $phrase_filter;
        $words=array(); $word=''; $_filter='';

        function setWord(){
            global $words, $word, $_filter, $phrase_filter, $db;

            if(strlen($word)<=0)
                return false;

            $orgWord = $word;
            $word = preg_replace('/[^a-z\s]/i', '',$orgWord);

            $result = $db->query("SELECT * FROM stop_list WHERE word='$word'");
            if($result->num_rows>0 && $orgWord[strlen($orgWord)-1]!='*' && $orgWord[0]!='"'){
                $_filter = substr($_filter, 0, -4);
                $word='';
                return false;
            }

            if($orgWord[0]=='^')
                $_filter .= "NOT ";

            $_filter .= "INSTR(words,'$word')";
            $words[] = $orgWord;
            $word = '';
            return true;
        }

        for($i=0;$i<strlen($keyword);$i++){
            $char = $keyword[$i];

            switch($char){
                case '(' :
                    setWord();

                    $_filter .= "(";
                    break;
                case ')' :
                    setWord();

                    $_filter .= ")";
                    break;
                case '&' :
                    setWord();
                        $_filter .= " && ";
                    break;
                case '|' :
                    setWord();
                        $_filter .= " || ";
                    break;
                default:
                    if(ctype_alpha($char) || $char=='^' || $char=='*' ||  $char=='-' || $char=='"')
                        $word .= $char;

                    if($i==strlen($keyword)-1) //last character
                        setWord();

            }
        }

        $words = array_unique($words);
        if(count($words)==0){
            return false;
        }
        for($i=0;$i<count($words);$i++){
            $word = $words[$i];

            $isWildCard=false;
            if($word[strlen($word)-1]=='*')
                $isWildCard=true;

            $word = preg_replace('/[^a-z\s]/i', '',$word); //remove all characters which not letters from $word

            if($this->isStopList($word)==true && !($isWildCard)){
                //$_filter = preg_replace("/INSTR[(]words[,]'".$word."['][)]\s&&/i", '',$_filter);
                //$_filter = preg_replace("/INSTR[(]words[,]'".$word."['][)]\s||/i", '',$_filter);
                //$_filter = preg_replace("/[|][|] INSTR[(]words[,]'".$word."['][)]/i", '',$_filter);
                //$_filter = preg_replace("/[&][&] INSTR[(]words[,]'".$word."['][)]/i", '',$_filter);
                //continue;
            }

            if($isWildCard){
                $sql_words .= "word LIKE '$word%'";
            }else if($isNot){

            }else {
                $sql_words .= "word='$word'";
            }

            if ($i<count($words)-1)
                $sql_words .= " || ";
        }

        $_filter = preg_replace('/([|][|]|[&][&])\s(^[A-Z]|[(][)]|[)])/', '',$_filter);

        $sql = "
                 SELECT GROUP_CONCAT(wd2.word separator ' ') as content ,t.* FROM
                    (SELECT document_id, docs.name, docs.author, docs.isHidden,
                            count(distinct word) as wordsCounter, GROUP_CONCAT(distinct word) as words,
                            count(word) as total_hits, GROUP_CONCAT(pos,'@@@',word separator '$$$') as positions
                    FROM word_in_document
                    NATURAL JOIN documents docs
                    WHERE ($sql_words)
                    GROUP BY document_id) AS t
                LEFT JOIN word_in_document wd2 ON wd2.document_id=t.document_id
                WHERE t.isHidden=0 AND $_filter
                GROUP BY document_id
                ORDER BY wordsCounter DESC, total_hits DESC
                ";

        //echo "<BR><BR>$_filter <BR><BR> $sql_words <BR><BR> $sql";
        return $db->query($sql);
    }
}
?>
