<html>

    <body>

<pre>Example queries:
team:"F1 Team"
epic:153141
</pre>

    <!-- <p>Query: <input type="text" id="query" value='epic:153141'></p> -->
    <p>Iteration ID: <input type="text" id="iterationid" value='149839'></p>
    <p><button onclick="getbasicinfo();">basic info</button></p>
    <p><button onclick="getdetailedhistory();">detailed history</button></p>
    <p><span id="status"/></p>

    <hr>

    <textarea style="width:80%" rows="10" id="export-contents" wrap="off"></textarea>

    </body>

    <script>

        function setStatus(text) {
            document.getElementById("status").innerHTML = text;
        }

        function getbasicinfo() {

            setStatus ("Loading data...");
            display("");

            const variables = {
                //query: document.getElementById("query").value
                action: 'basicinfo',
                iterationid: document.getElementById("iterationid").value
            }

            fetch("api.php?" + new URLSearchParams(variables))
            .then(data => data.text())
            .then(
                function(contents) {
                    display(contents);
                    setStatus ("Success!");
                }
            )
        }

        function getdetailedhistory() {

            setStatus ("Loading data...");
            display("");

            const variables = {
                //query: document.getElementById("query").value
                action: 'detailedhistory',
                iterationid: document.getElementById("iterationid").value
            }

            fetch("api.php?" + new URLSearchParams(variables))
                .then(data => data.text())
                .then(
                    function(contents) {
                        display(contents);
                        setStatus ("Success!");
                    }
                )
        }

        function display(contents) {
            document.getElementById("export-contents").innerHTML = contents;
        }

    </script>


</html>

<?php

