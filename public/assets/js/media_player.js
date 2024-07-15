function mediaPlayer(myVids) {
    var i = 0;
    var prevBtn = document.getElementById('prev');
    var nextBtn = document.getElementById('next');
    var videoPlayer = document.getElementById('videoPlayer');
    videoPlayer.src = myVids[0]+"#t=0.7";

    // for the ul li playlist. start of the ul li playlist tracks
    var playlist = document.getElementById("playlist");
    // get the tracks
    var tracks = playlist.getElementsByTagName('a');
    // have a loop on all the tracks to listen to the clicked one
    Array.from(tracks).forEach((track, index) => {
        track.addEventListener('click', function(e){
            e.preventDefault();
            // clicked source
            videoPlayer.src = this.getAttribute('href');
            // add a catch in case the play return an error
            videoPlayer.play().catch((e) => {
            /* error handler */
            })
            i = index;
        });
    });
    // end of the ul li playlist tracks

    videoPlayer.onended = function(){
        videoPlayer.src = myVids[i+1];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i++;
        if (i >= myVids.length) {
        i = 0;
        videoPlayer.src = myVids[i];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i++;
        }  
    }

    // prev button 
    prevBtn.addEventListener('click',function(){
        if (i <= 0) {
        videoPlayer.src = myVids[myVids.length-1];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i = myVids.length-1;
        } 
        else {
        videoPlayer.src = myVids[i-1];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i--;
        }
    },false);
    
    // next button 
    nextBtn.addEventListener('click',function(){
        if (i >= myVids.length) {
        videoPlayer.src = myVids[1];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i = 1;
        } else if (i + 1 == myVids.length) {
        videoPlayer.src = myVids[0];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i++;
        }
        else {
        videoPlayer.src = myVids[i+1];
        // add a catch in case the play return an error
        videoPlayer.play().catch((e) => {
            /* error handler */
            })
        i++;
        }
    },false);
}

$(document).ready(mediaPlayer(myVids));