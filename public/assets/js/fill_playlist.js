function fillPlaylist(myVids, vidTitles) {
    var ulPlaylist = document.getElementById("playlist");
    for (i = 0; i < myVids.length; i++) {
        var li = document.createElement("li");
        $(li).addClass('list-group-item');
        li.innerHTML = '<a href='+myVids[i]+'>'+vidTitles[i]+'</a>';
        ulPlaylist.appendChild(li);
    }
    
}

$(document).ready(fillPlaylist(myVids, vidTitles));