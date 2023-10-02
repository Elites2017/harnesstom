function filter() {
    // get the filters form
    const filtersForm = document.querySelector("#filters");

    // get the inputs of the form filters
    const inputs = document.querySelectorAll("#filters input");

    // get the remove all filter btn
    const btnFilterRemove = document.querySelector("#btnFilterRemove");
    btnFilterRemove.addEventListener("click", function(){
        // create a change event that is to fired
        // when the remove filter btn is clicked
        const event = new Event("change");

        inputs.forEach(input => {
            // if the input is checked as there are only checkboxes in the form
            if (input.checked) {
                input.checked = false;
                input.dispatchEvent(event);
            }
        });
            
    });
    
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
                
                // country
                const filteredAccQty = document.querySelectorAll("#accQtyCountry");
                // accessionQtyCountry coming from the controller to update the number
                // display in the bagde. AccQtyTab is a key value data list [id => val]
                var accQtyTab = data.accessionQtyCountry;
                // get the badges to check which parent (objects) satifying the criteria or not.
                // loop over list of elements (badges...)
                // check if the data exists in the key value data array
                // for each data-id of the elements (each one).
                // if the data exists, update the badge value coming from the data array 
                // the object should be shown to the user
                // otherwise do not show the object(s) not satisfying the filters 
                filteredAccQty.forEach(element => {
                    if (accQtyTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyTab[element.getAttribute('data-id')];
                        // important to add the d-flex class as removed when the object doesn't exist
                        element.parentElement.classList.add("d-flex");
                    } else {
                        // important to remove the d-flex class as it's ovveriden the display none prop
                        // by some boostrap files
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
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
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // collecting source
                const filteredColSource = document.querySelectorAll("#accQtyColSource");
                var accQtyColSourceTab = data.accessionQtyColSource;
                filteredColSource.forEach(element => {
                    if (accQtyColSourceTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyColSourceTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // taxonomy
                const filteredTaxonomy = document.querySelectorAll("#accQtyTaxonomy");
                var accQtyTaxonomyTab = data.accessionQtyTaxonomy;
                filteredTaxonomy.forEach(element => {
                    if (accQtyTaxonomyTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyTaxonomyTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // maintaining institute
                const filteredMainInstitute = document.querySelectorAll("#accQtyMainInstitute");
                var accQtyMainInstituteTab = data.accessionQtyMainInstitute;
                filteredMainInstitute.forEach(element => {
                    if (accQtyMainInstituteTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyMainInstituteTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // donor institute
                const filteredDonorInstitute = document.querySelectorAll("#accQtyDonorInstitute");
                var accQtyDonorInstituteTab = data.accessionQtyDonorInstitute;
                filteredDonorInstitute.forEach(element => {
                    if (accQtyDonorInstituteTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyDonorInstituteTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // bred institute
                const filteredBredInstitute = document.querySelectorAll("#accQtyBredInstitute");
                var accQtyBredInstituteTab = data.accessionQtyBredInstitute;
                filteredBredInstitute.forEach(element => {
                    if (accQtyBredInstituteTab[element.getAttribute('data-id')]) {
                        element.innerHTML = accQtyBredInstituteTab[element.getAttribute('data-id')];
                        element.parentElement.classList.add("d-flex");
                        
                    } else {
                        element.parentElement.classList.remove("d-flex");
                        element.parentElement.style.display = "none";
                    }
                });

                // reinitialize the data table for type in filter / condition
                // client-side processing
                $('#datatable').DataTable();
                // update the URL
                history.pushState({}, null, currentUrl.pathname + "?" + params.toString());
            }).catch(e => alert(e));
        });
    });
}

$(document).ready(filter());
