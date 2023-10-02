$(document).ready(function(){
    // get the filters form
    const filtersForm = document.querySelector("#filters");

    // get the inputs of the form filters
    const inputs = document.querySelectorAll("#filters input");
    
    // add event listener on each input
    inputs.forEach(input => {
        input.addEventListener("change", function() {
            // get the data of the form
            const form = new FormData(filtersForm);
            // create url string params
            const params = new URLSearchParams();
            // loop over the for
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

                // reinitialize the data table for type in filter / condition
                // client-side processing
                $('#datatable').DataTable();
                // update the URL
                history.pushState({}, null, currentUrl.pathname + "?" + params.toString());
            }).catch(e => alert(e));
        });
    });
})