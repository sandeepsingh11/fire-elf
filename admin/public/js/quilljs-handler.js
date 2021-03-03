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

// Initialize quill-image-uploader module
Quill.register("modules/imageUploader", ImageUploader);
var fileObj_arr = [];



var quillEle = new Quill('#editor', {
    modules: {
        toolbar: toolbarOptions,
        imageUploader: {
            upload: file => {
                return new Promise((resolve, reject) => {
                    // push new img name
                    fileObj_arr.push(file.name);
                    
                    // *** delay required ***
                    // jquery will not find new img node without delay
                    setTimeout(() => {
                        var imgs = $('#editor img');
                        
                        // get current time as string
                        // used as a unique identifier for new editor images
                        var date = new Date();
                        var now = 
                            date.getHours().toString() + 
                            date.getMinutes().toString() + 
                            date.getSeconds().toString();

                        // new entries pushed to the front of array, so [0]
                        // set img-name attr
                        $(imgs[0]).attr('data-img-name', file.name);
                        
                    }, 250);
                });
            }
        }
    },
    theme: 'snow'
});





// on form submit, get quill delta (ops),
// then resume submit
$('#form').submit(function(e)  {
    e.preventDefault();


    // get quill delta
    var delta = quillEle.getContents();
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