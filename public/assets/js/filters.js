function filter() {
    // get the filters form
    const filtersForm = document.querySelector("#filters");

    // get the inputs of the form filters
    const inputs = document.querySelectorAll("#filters input");
    //var countries = [];
    
    // add event listener on each input
    inputs.forEach(input => {
        input.addEventListener("change", function() {
            // get the data of the form
            var form = new FormData(filtersForm);
            // create url string params
            const params = new URLSearchParams();
            // loop over the form
            form.forEach((value, key) => {
                params.append(key, value);
            });
            // get the current url
            const currentUrl = new URL(window.location.href);
            // get the ajax query ready
            // using fetch that return a promise
            // add "ajax = 1" so that the same index function in the controller can be used
            fetch(currentUrl.pathname + "?" + params.toString() + "&ajax=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response => response.json()
            ).then(data => {
                // get the div id filtered_content which is to be replaced by the response data
                const filteredContent = document.querySelector("#filtered_content");
                filteredContent.innerHTML = data.content;
                //const formFilteredContent = document.querySelector("#countryCollapse");

                const filteredAccQty = document.querySelectorAll("#accQtyCountry");
                
                var accQtyTab = data.accessionQtyCountry;
                //console.log("Acc Qty ", accQtyTab);
                
                filteredAccQty.forEach(element => {
                    if (accQtyTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        //element.parentElement.style.display = "block";
                        //element.style.display = "block";
                        //console.log("Ele ", element.parentElement, " key ", accQtyTab, "div Tog ", divToggle);
                        //document.querySelector(".tryyy div");
                        //$("#"+element.getAttribute('data-id')).children().prop('disabled',false);
                        
                        
                        //element.style.display = "block";
                        //element.className = "float-right badge badge-primary badge-pill";
                        //element.innerHTML = accQtyTab[element.getAttribute('data-id')]; 
                        //element.parentElement.appendChild(elExists);
                    } else {
                        //element.innerHTML = 0;
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                        //console.log("Else div Tog ", divToggle);
                        //element.style.display = "none";
                        //element.parentElement.style.display = "none";
                        //element.parentElement.remove();
                        //$("#"+element.getAttribute('data-id')).children().prop('disabled',true);
                        //element.className = "float-right badge badge-danger badge-pill";
                    }
                });

                // biological status
                const filteredBiologicalStatQty = document.querySelectorAll("#accQtyBiologicalStat");
                var accQtyBiologicalStatTab = data.accessionQtyBiologicalStatus;
                filteredBiologicalStatQty.forEach(element => {
                    if (accQtyBiologicalStatTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyBiologicalStatTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        //element.innerHTML = 0;
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // mls status
                const filteredMLSStatQty = document.querySelectorAll("#accQtyMLSStat");
                var accQtyMLSStatTab = data.accessionQtyMLSStatus;
                filteredMLSStatQty.forEach(element => {
                    if (accQtyMLSStatTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyMLSStatTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        //element.innerHTML = 0;
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // collecting mission
                const filteredColMission = document.querySelectorAll("#accQtyColMission");
                var accQtyColMissionTab = data.accessionQtyColMission;
                filteredColMission.forEach((element, key) => {
                    if (accQtyColMissionTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyColMissionTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        //element.innerHTML = 0;
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });
                
                // update the URL
                history.pushState({}, null, currentUrl.pathname + "?" + params.toString());
                //filter();
            }).catch(e => alert(e));
        });
    });
}

$(document).ready(filter());

