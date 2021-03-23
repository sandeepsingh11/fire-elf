// handle button events





// handle delete button confirmation

// get delete forms
var deleteForms = Array.from(document.getElementsByClassName('form-delete'));

// add submit listener for each delete form
deleteForms.forEach((ele) => {
    ele.addEventListener('submit', (e) => {
        // prevent submit
        e.preventDefault();

        // get entry's title name (from (grand)child input[name='entry-name'])
        if (e.target.children['entry-name'] === undefined) {
            var newParent = e.target.children[1];
            var entryName = newParent.children['entry-name'].value;
        }
        else {
            var entryName = e.target.children['entry-name'].value;
        }

        // confirm delete request to the user
        if (confirm('Are you sure you want to delete "' + entryName + '"?')) {
            // if confirmed, delete
            e.target.submit();
        } 
    });
});