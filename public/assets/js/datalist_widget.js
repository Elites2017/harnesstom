$(document).ready(function(){
    // select all the datalist
    var myDatalists = document.querySelectorAll('input[list]');
    // loop over them to add an event listener on each
    for(var cntr = 0; cntr < myDatalists.length; cntr++) {
        myDatalists[cntr].addEventListener('input', function(e) {
            // get the target (the one clicked)
            // get the list attribute ofr the related input (the clicked one)
            // get the options
            // get the hidden input of the clicked input
            // get the value of the clicked input
            var input = e.target,
                list = input.getAttribute('list'),
                options = document.querySelectorAll('#' + list + ' option'),
                hiddenInput = document.getElementById(input.getAttribute('id') + '_hidden'),
                inputValue = input.value;
            // assign the value of visible input to the hidden one
            hiddenInput.value = inputValue;
            // now lop over the options to see if the text value of the option matches input value
            // which is the text shown in the datalist. If the text option matches that value,
            // assign to the hidden input value the data-value of the option. The hidden input
            // is the one sent to the controller 
            for(var i = 0; i < options.length; i++) {
                var option = options[i];
                // do not use innerText as it returns other caracter than the text value itself
                if(option.text === inputValue) {
                    hiddenInput.value = option.getAttribute('data-value');
                    break;
                }
            }
        });
    }
});