<html>

    <body>

    <p>Iteration ID: <input type="text" id="iterationid" value='181221'></p>

    <p><button onclick="getbasicinfo();">basic info</button></p>
    <p><button onclick="getdetailedhistory();">detailed history</button></p>
    <p><button onclick="getdependencies();">dependencies</button></p>
    <p><span id="status"/></p>

    <hr>

    <textarea style="width:80%" rows="10" id="export-contents" wrap="off">
    </textarea>

    </body>

    <script src="js/apihandler.js"></script>
    <script src="js/main.js"></script>


</html>

<?php

