$(document).ready(function(){
    var treeCard = $(".treeView");
    var listCard = $(".listView");
    listCard.hide();
    document.getElementById("optionToShow").addEventListener("click", function(e){
        var option = e.target;
        if (option.text === "View tree") {
        option.text = "View list";
        treeCard.show();
        listCard.hide();
        } else {
            option.text = "View tree";
            treeCard.hide();
            listCard.show();
        }
    });
});

//  function myFunc(){
// 	var treeCard = $(".treeView");
// 	var listCard = $(".listView");
// 	var option = document.getElementById("optionToShow");
// 	if (option.text === "View tree") {
// 		option.text = "View list";
// 		treeCard.show();
// 		listCard.hide();
// 	} else {
// 		option.text = "View tree";
// 		treeCard.hide();
// 		listCard.show();
// 	}
// }