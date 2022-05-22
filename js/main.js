const endpoint_url = "api.php";
const api = new ApiFetch(endpoint_url);

api.setErrorHandler( function (error) {
    console.log ("Ops...");
    console.log (error)
});

function setStatus(text) {
    document.getElementById("status").innerHTML = text;
}

function getbasicinfo() {

    setStatus ("Loading data...");
    display("");
    api.getText({
        action: 'basicinfo',
        iterationid: document.getElementById("iterationid").value
    })
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
    api.getText({
        action: 'detailedhistory',
        iterationid: document.getElementById("iterationid").value
    })
        .then(
            function(contents) {
                display(contents);
                setStatus ("Success!");
            }
        );
}

function display(contents) {
    document.getElementById("export-contents").innerHTML = contents;
}