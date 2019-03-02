# Information-Retrieval-Tutorial
IR Engine - Scan .txt files and store it as inverted index. GUI to search \ Admin to insert new .txt files to vocabulary

to run it on your localhost:

1. clone the repo on your machine
2. on classes->dbDetails.php change the mySQL details to allow connection into your local mySQL (ip: localhost, username: ___, password: ____)
3. after that, please run server/createDB/initialize.php on your machine to initialize the DB into yours mySQL database.

if you reached here, a connection to mySQL has already established, and the IR DB already created into your mySQL schemas.

4. for the client GUI -> run index.html
5. for the admin  GUI -> run admin.php

* note for admin * :
to scan new txt files, make sure you put them into ./documents folder at first.

enjoy :)
