$(document).ready(function () {
    var timesRun = 0;
    document.getElementById("uploadFromFile").addEventListener("submit", function () {
        var interval = setInterval(function () {
            timesRun += 1;
            $('#dataUploadModalCenter').modal('show');
            if(timesRun === 3){
                clearInterval(interval);
                return;
            }
        }, 20000);
    });
});
