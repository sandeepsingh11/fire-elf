//
// ─── HANDLE THE QUILLJS EDITOR ──────────────────────────────────────────────────
//


// Initialize Quill editors
var toolbarOptions = [
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike', 'link'],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'align': [] }],
    ['image'],
    ['clean']
];



var fileObj_arr = [];



// initialize quill editor
var quill = new Quill('#editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});



// ===== prepare media library modal =====

// set 'X' button to exit modal
var closeBtn = document.getElementById('modal-close');
closeBtn.addEventListener('click', () => {
    var modal = document.getElementById('media-lib-modal');
    modal.classList.remove('show');
    modal.classList.add('hide');
});

// get media list value
var mediaListEle = document.getElementById('media-list');
var mediaList = mediaListEle.value;

// split string to array
mediaList = mediaList.split(',');

// prepare media url
var url = window.location;
var mediaUrl = url.protocol + '//' + url.host + '/media/';

// populate modal
var mediaLibEle = document.getElementById('media-lib');

mediaList.forEach(media => {
    var imgNode = document.createElement('img');

    imgNode.classList.add('media-lib-img');
    imgNode.setAttribute('src', mediaUrl + media);
    imgNode.addEventListener('click', (e) => {addImage(e.target.src)})

    mediaLibEle.appendChild(imgNode);
});

// ===== =====



// handle image upload within the media lib modal
var modalUploadEle = document.getElementById('media-lib-upload');
modalUploadEle.addEventListener('change', () => {
    var file = modalUploadEle.files[0];
    var reader = new FileReader();

    // triggered after readAsDataURL()
    reader.onloadend = () => {
        addImage(reader.result);
    }

    // convert to base64
    reader.readAsDataURL(file);

    // push new img name
    fileObj_arr.push(file.name);
});



// handle media library modal.
// triggered when quill's image button is clicked
function mediaLib() {
    var modal = document.getElementById('media-lib-modal');
    var displayState = modal.className;

    if (displayState == 'hide') {
        modal.classList.remove('hide');
        modal.classList.add('show');
    }
    else {
        modal.classList.remove('show');
        modal.classList.add('hide');
    }
}



// add image (from upload or media lib) to quill editor
function addImage(imgUrl) {
    // get quill cursor location
    var range = quill.getSelection();

    // insert to editor
    // * range could be null when user uploads and image
    // (if they have not clicked in the editor before, the cursor has not been set)
    quill.insertEmbed((range) ? range.index : 0, 'image', imgUrl);

    // close modal after image insert
    var modal = document.getElementById('media-lib-modal');
    modal.classList.remove('show');
    modal.classList.add('hide');
}



// register quill image listener
var toolbar = quill.getModule('toolbar');
toolbar.addHandler('image', mediaLib);



// on form submit, get quill delta (ops),
// then resume submit
$('#form').submit(function(e)  {
    e.preventDefault();


    // get quill delta
    var delta = quill.getContents();
    var delta_json = JSON.stringify(delta);
    delta_json = delta_json.replace(/'/g, '&#39;'); // convert "'"

    var inputOps = '<input type="hidden" name="ops" id="ops" value=\'' + delta_json + '\'>';

    // append to form for submission
    $('#id').after(inputOps);


    // build image names string
    var imgNames_str = "";
    var len = fileObj_arr.length;


    // there could be more image names pushed into fileObj_arr
    // than there are new base64 images since the user can delete
    // images after uploading, and there currently isn't an easy
    // way to implement an image deletion listener to pop the
    // deleted image name out of the array.

    // instead, use the latest pushed names (hence reversed for loop)
    for (var i = len - 1; i >= 0; i--) {
        imgNames_str += fileObj_arr[i] + ",";
    }

    // trim off last " "
    imgNames_str = imgNames_str.slice(0, -1);

    // append to form for submission
    var inputImgNames = '<input type="hidden" name="image-names" value="' + imgNames_str + '">';
    $('#id').after(inputImgNames);


    // resume form submit
    e.target.submit();
});