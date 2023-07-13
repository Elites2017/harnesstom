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

                const filteredAccQty = document.querySelectorAll("#accQtyCtry");

                var accQtyTab = data.accessionQtyCountry;
                console.log(accQtyTab);

                filteredAccQty.forEach(element => {
                    if (accQtyTab[element.getAttribute('data-id')]) {
                        var elExists = document.getElementById(element.getAttribute('data-id'));
                        // console.log("Pap + ", element.parentElement);
                        // if (elExists) {
                        //     console.log("Hello ", elExists);
                        // } else {
                        //     console.log("NOTHING");
                        // }
                        element.className = "float-right badge badge-primary badge-pill";
                        element.innerHTML = accQtyTab[element.getAttribute('data-id')]; 
                    } else {
                        element.innerHTML = 0;
                        //element.parentElement.remove();
                        element.parentElement.disabled = "disabled";
                        element.className = "float-right badge badge-danger badge-pill";
                    }
                });
                //console.log(filteredAccQty);
                // filteredAccQty.getAttribute('data-id').forEach(element => {
                //     console.log(element);
                // });
                
                // console.log("Hello ", filteredAccQty, "id ", filteredAccQty.getAttribute('data-id'));
                // // filteredAccQty.innerHTML = 9999;

                // var accQtyTab = data.accessionQtyCountry;
                // console.log("Acc Qty Lits Returned ", accQtyTab);
                // accQtyTab.forEach(element => {
                //     if (element.id == filteredAccQty.getAttribute('data-id'))
                //     filteredAccQty.innerHTML = element.accQty;
                // });
                
                
                // if (params.get('countries[]')) {
                //     countries.push(params.get('countries[]'));
                // }
                // //console.log("For Country ", formFilteredContent, " Params ", params.get('countries[]'), " Countries ", countries, " URL ", currentUrl);
                //formFilteredContent.innerHTML = data.ctyFil;
                
                // update the URL
                history.pushState({}, null, currentUrl.pathname + "?" + params.toString());
                filter();
            }).catch(e => alert(e));
        });
    });
}

$(document).ready(filter());

