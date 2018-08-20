<?php

$conn->query("SET NAMES 'utf8'");

dropTables($conn);
createTables($conn);
initializeValues($conn);

$conn->close();


function createTables($conn){
    $tables = array();

    $tables[] = "CREATE TABLE stop_list (
                                word VARCHAR(50) NOT NULL,
                                PRIMARY KEY (word)
                            ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE words (
                                word VARCHAR(50) NOT NULL,
                                PRIMARY KEY (word)
                            ) ENGINE=InnoDB";

    $tables[] = "CREATE TABLE documents (
                                document_id INT UNSIGNED AUTO_INCREMENT,
                                name VARCHAR(50),
                                author VARCHAR(50),
                                filename VARCHAR(200) NOT NULL,
                                format VARCHAR(50),
                                isHidden SMALLINT(1) NOT NULL DEFAULT 0,

                                PRIMARY KEY (document_id)
                            ) ENGINE=InnoDB";

    /*
    $tables[] = "CREATE TABLE word_in_document (
                                word VARCHAR(50) NOT NULL,
                                document_id INT UNSIGNED NOT NULL,
                                times INT UNSIGNED NOT NULL,

                                PRIMARY KEY (word,document_id),

                                FOREIGN KEY (word) REFERENCES words (word)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE,

                                FOREIGN KEY (document_id) REFERENCES documents (document_id)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE

                            ) ENGINE=InnoDB";
    */
    $tables[] = "CREATE TABLE word_in_document (
                                auto_id INT UNSIGNED AUTO_INCREMENT,
                                word VARCHAR(50) NOT NULL,
                                document_id INT UNSIGNED NOT NULL,
                                pos INT UNSIGNED NOT NULL,

                                PRIMARY KEY (auto_id),

                                FOREIGN KEY (word) REFERENCES words (word)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE,

                                FOREIGN KEY (document_id) REFERENCES documents (document_id)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE

                            ) ENGINE=InnoDB";

    foreach($tables as $table){
        createTable($table,$conn);
    }
}

function createTable($sql,$conn){
    $arr = explode(' ',trim($sql));
    $tableName = $arr[2];
    if ($conn->query($sql)==TRUE)
        echo "$tableName TABLE created successfully" . "<BR>";
    else
        echo $conn->error . "<BR><BR>";
}

function dropTables($conn){
    $sql = "DROP TABLE IF EXISTS
            word_in_document,
            test,
            stop_list,
            words,
            documents
            ";
    if($conn->query($sql)==FALSE)
        echo "drop failed: " . $conn->error;

    echo "all tables deleted successfully<BR><BR>";
}

function initializeValues($conn){
    $sql = array();

    $sql[] = "INSERT INTO stop_list (word) VALUES
                    ('the'),('is'),('a'),('an'), ('and'), ('or'), ('of'),
                    ('i'), ('we'), ('you'), ('he'), ('she'), ('it')
             ";

    echo "<BR><BR>";
    foreach($sql as $q){
        if($conn->query($q)==FALSE){
            echo $conn->error;
        } else {
            $arr = explode(' ',trim($q));
            $tableName = $arr[2];
            echo "insert to $tableName TABLE successfully" . "<BR>";
        }
    }
}
?>